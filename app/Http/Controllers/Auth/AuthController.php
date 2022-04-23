<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Common\SmsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\FileController;
use App\Models\Crm\PermissionGroup;
use Illuminate\Support\Facades\Mail;
use App\Models\Crm\PhoneCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Mail\MyRecoverMail;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->user = auth()->user();

        $this->media = new FileController();

        $this->middleware('auth.refresh', ['only' => ['refresh']]);
    }

    /**
     * Login
     * @param Request $request
     * @return mixed
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:255|email:rfc,dns|unique:users',
            'firstname' => 'required|string|max:20',
            'surname' => 'required|string|max:20',
            'patronymic' => 'required|string|max:20',
            'profile_img' => 'exists:media,id',
            'birth_date' => 'required|date|date_format:d.m.Y',
            'password' => 'min:6|string|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success' => false, 'error' => $error]);
        }

        $verify_code_expire = now();
        $username = $request->username;
        $verification_code = $this->str_random(30); //Generate verification code
        $user = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'surname' => $request->surname,
            'patronymic' => $request->patronymic,
            'birth_date' => $request->birth_date,
            'password' => bcrypt($request->password),
            'show_password' => $request->password,
            'verify_code' => $verification_code,
            'status' => USER::STATUS_NOT_CONFIRMED,
            'permission_group_id' => 2,
            'role' => USER::ROLE_USER,
            'verify_code_expire' => $verify_code_expire
        ]);

        if ($request->profile_img) {
            $user->syncMedia($request->profile_img, ['avatar']);
            $this->media->moveFolderImage($request->profile_img, $user->id, 'Profile');
        }

        $details = $user;

        \Mail::to($user->username)->send(new \App\Mail\MyTestMail($details));

        return response()->json(['success' => true, 'message' => 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    public function recover(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['username' => $error_message]], 401);
        }


        try {
            $verification_code = $this->str_random(30); //Generate verification code
            $username = $user->username;
            $user->update([
                'verify_code' => $verification_code,
                'verify_code_expire' => now()
            ]);
            $subject = "Please verify your email address.";

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }

        $details = $user;

        \Mail::to('nusratakhmadjonovich@gmail.com')->send(new \App\Mail\MyRecoverMail($details));

//        $data = ['message' => 'This is a test!'];


//        Mail::to('nusratakhmadjonovich@gmail.com')->send(new MyRecoverMail($details));

//        $user = User::find(1)->toArray();
//
//        Mail::send('email.reset', $user, function($message) use ($user) {
//            $message->to('nusratakhmadjonovich@gmail.com');
//            $message->subject('Sendgrid Testing');
//        });
//        dd('Mail Send Successfully');

        return response()->json([
            'success' => true, 'data' => ['msg' => 'A reset email has been sent! Please check your email.']
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:255|email:rfc,dns',
            'password' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        if (User::where('username', $request->username)->where('status', User::STATUS_NOT_CONFIRMED)->first()) {
            return $this->errorResponse('This user not confirmed email', 422);
        }

        if (!$token = auth('api')->attempt(['username' => $request->username, 'password' => $request->password, 'status' => User::STATUS_ACTIVE])) {
            return $this->errorResponse('The credentionals error', 401);
        }

        return $this->respondWithToken($token);
    }

    public function verifyEmail($verify_code)
    {
        $user = User::where([['verify_code', $verify_code], ['role', User::ROLE_USER]])->first();

        if (!$user || $user->verify_code_expire > date('Y-m-d H:i:s'))
            return $this->errorResponse("Verification code error.", 401);

        $user->update([
            'updated_by' => auth()->id(),
            'status' => USER::STATUS_ACTIVE
        ]);

        header("Location: https://dev.unilibrary.uz/auth/login?status=1");
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verify_code' => 'required',
            'password' => 'min:6|string|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);
        $user = User::where([['verify_code', $request->verify_code], ['role', User::ROLE_USER]])->first();

        if (!$user || $user->verify_code_expire > date('Y-m-d H:i:s'))
            return $this->errorResponse("Verification code error.", 401);

        $user->update([
            'password' => bcrypt($request->password),
            'show_password' => $request->password,
            'updated_by' => auth()->id(),
            'status' => 1,
            'verify_code' => null
        ]);

        $result = 'User password is changed';
        return $this->successResponse($result);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', new ExistsUser(User::ROLE_USER)],
            'password' => 'min:6|string|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $field = 'username';

        $user = User::where([[$field, $request->username], ['role', User::ROLE_ADMIN]])->first();

        if ($user->status != User::STATUS_ACTIVE)
            return $this->errorResponse('This administrator is inactive.', 422);

        $verify_code = Str::random(60);
        $user->update([
            'verify_code' => $verify_code,
            'verify_code_expire' => Carbon::now()->addSeconds(User::VERIFY_TIME)
        ]);

        $result = 'Successfully send sms';
        return $this->successResponse($result);
    }

    public function logout(Request $request)
    {
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');
        // Invalidate the token
        try {
            auth('api')->invalidate($token);
            return $this->successResponse('Successfully logged out');
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->errorResponse('Failed to logout, please try again.', 500);
        }
    }

    public function refresh()
    {
        $access_token = auth('api')->refresh(true, true);
        auth()->setToken($access_token);

        return $this->respondWithToken($access_token);
    }

    protected function respondWithToken($access_token)
    {
        return $this->successResponse([
            'access_token' => $access_token,
            'token_type' => 'bearer',
            'access_expires_in' => auth()->factory()->getTTL(),
            'refresh_token' => auth()
                ->claims([
                    'xtype' => 'refresh',
                    'xpair' => auth()->payload()->get('jti')
                ])
                ->setTTL(auth()->factory()->getTTL() * 24)
                ->tokenById(auth()->user()->id),
            'refresh_expires_in' => auth()->factory()->getTTL()
        ]);
    }

    private static function str_random($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
