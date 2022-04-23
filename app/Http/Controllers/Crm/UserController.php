<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\FileController;
use App\Models\Crm\UserUniversity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function __construct()
    {
        $this->user = auth()->user();

        $this->media = new FileController();
        $this->middleware('permission:crm_users_index', ['only' => 'index']);
        $this->middleware('permission:crm_update_role', ['only' => 'updateRole']);
        $this->middleware('permission:crm_users_show', ['only' => 'show']);
    }

    public function index(Request $request)
    {
        $users = User::select(
            'users.id',
            'users.role',
            'users.username',
            'firstname',
            'surname',
            'patronymic',
            'birth_date',
            'users.permission_group_id',
            'users.status',
            'profile_img'
        )->where('role', User::ROLE_USER)
           ;

        if ($request->id)
            $users->where('users.id', 'LIKE', "%{$request->get('id')}%");


        if ($request->username)
            $users->where('users.username', 'LIKE', "%{$request->get('username')}%");

        $users = $users->paginate($request->get('limit', 50));

        $users = User::mediaUrl($users);

        return $this->successResponse($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:255|email:rfc,dns|unique:users',
            'firstname' => 'required|string|max:20',
            'surname' => 'required|string|max:20',
            'patronymic' => 'required|string|max:20',
            'profile_img' => 'exists:media,id',
            'birth_date' => 'required|date|date_format:d.m.Y',
            'password' => 'min:6|string|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6',
            'university_ids' => 'nullable|array',
            'university_ids.*' => 'nullable|integer|distinct|exists:universities,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
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
            'verify_code' => $verification_code,
            'status' => 0,
            'permission_group_id' => 1,
            'role' => USER::ROLE_USER,
            'verify_code_expire' => $verify_code_expire
        ]);

        if ($request->profile_img) {
            $user->syncMedia($request->profile_img, ['avatar']);
            $this->media->moveFolderImage($request->profile_img, $user->id, 'Profile');
        }

        if (isset($request->university_ids)) {
            foreach ($request->university_ids as $university_id) {
                $user_university = UserUniversity::create([
                    'user_id' => $user->id,
                    'university_id' => $university_id
                ]);
            }
        }


        return $this->view($user->id, $request);
    }

    public function view($id, $request)
    {

        $user = User::
        where([['users.id', $id]])
            ->where('role', User::ROLE_USER)
            ->select(
                'users.id',
                'users.role',
                'users.username',
                'firstname',
                'surname',
                'patronymic',
                'birth_date',
                'users.permission_group_id',
                'users.status',
                'profile_img'
            )
            ->with('university')
            ->get();

        $user = User::mediaUrl($user);


        return $this->successResponse($user);
    }

    public function show(Request $request, $id)
    {
        return $this->view(intval($id), $request);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:20',
            'surname' => 'required|string|max:20',
            'patronymic' => 'required|string|max:20',
            'birth_date' => 'required|date|date_format:d.m.Y',
            'username' => 'required|string|min:4|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6|max:255',
            'status' => 'required|in:1,0',
            'university_ids' => 'nullable|array',
            'university_ids.*' => 'nullable|integer|distinct|exists:universities,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user->update([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'surname' => $request->surname,
            'patronymic' => $request->patronymic,
            'password' => isset($request->password) ? bcrypt($request->password) : $user->password,
            'birth_date' => $request->birth_date,
            'status' => $request->status,
            'updated_by' => auth()->id()
        ]);

        if (isset($request->university_ids)) {
            UserUniversity::where('user_id', $user->id)->delete();

            foreach ($request->university_ids as $university_id) {
                UserUniversity::create([
                    'user_id' => $user->id,
                    'university_id' => $university_id
                ]);
            }

        }

        return $this->view($user->id, $user);
    }

    public function updateRole(Request $request, $id)
    {
        if (!$user = User::where('id', intval($id))->first())
            abort(404);

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:admin,user',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user->update([
            'role' => $request->role
        ]);

        return $this->view(intval($id), $request);
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

    public function lists(Request $request)
    {
        $users = User::where('deleted_by', '=', null)
            ->where('role', '=', User::ROLE_USER)
            ->select(
                'users.id',
                'users.role',
                'users.username',
                'firstname',
                'surname',
                'patronymic',
                'birth_date',
                'users.permission_group_id',
                'users.status',
                'profile_img'
            )
            ->where('users.status', 1)
            ->where(function ($query) use ($request) {
                if ($request->language)
                    $query->where('users.username', '=', $request->username);
                if ($request->name)
                    $query->where('users.status', 'LIKE', "%{$request->status}%");

            })->get();
        return $this->successResponse($users);
    }

    public function destroy(User $user)
    {
        if ($user->role != User::ROLE_USER)
            return $this->errorResponse('User not found',404);

        $user->update(['deleted_by' => auth()->id()]);
        $user->delete();

        return $this->successResponse('User deleted successfully.');
    }
}
