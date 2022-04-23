<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\FileController;
use App\Mail\ActivateMail;
use App\Models\Common\Student;
use App\Models\Crm\PermissionGroup;
use App\Models\Resources\Media;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->media = new FileController();
        $this->user = auth()->user();

//        $this->middleware('permission:common_profile_update', ['only' => 'updateProfile']);
//        $this->middleware('permission:common_profile_change_phone', ['only' => 'changePhone']);
//        $this->middleware('permission:common_profile_change_password', ['only' => 'changePassword']);
//        $this->middleware('permission:common_profile_show', ['only' => 'show']);
//        $this->middleware('permission:common_profile_get_permission_group', ['only' => 'getPermissionGroup']);
//        $this->middleware('permission:common_profile_photo', ['only' => 'photo']);
    }

    public function updateProfile(Request $request)
    {
        if (!$user = User::where('id', $this->user->id)->first())
            abort(404);

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:20',
            'surname' => 'required|string|max:20',
            'patronymic' => 'required|string|max:20',
            'birth_date' => 'required|date|date_format:d.m.Y',
            'username' => 'required|string|min:4|max:255|email:rfc,dns|unique:users,username,'.$user->id,
            'profile_img' => 'exists:media,id',
        ]);

        if ($validator->fails())
            return $this->errorResponse($validator->messages(), 422);

        $user->update([
            'updated_by' => auth()->id(),
            'firstname' => $request->firstname,
            'surname' => $request->surname,
            'patronymic' => $request->patronymic,
            'birth_date' => $request->birth_date,
            'username' => $request->username,
        ]);

        if ($request->profile_img) {
            $user->syncMedia($request->profile_img, ['avatar']);
            $this->media->moveFolderImage($request->profile_img, $user->id, 'Profile');
        }

        return $this->successResponse('User profile changed successfully');
    }

    public function changePassword(Request $request)
    {
        if (!$user = User::where('id', $this->user->id)->first())
            abort(404);
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required|string|max:20',
            'password' => 'required|string|max:20',
            'passwordRepeat' => 'same:password',
        ]);

        if ($validator->fails())
            return $this->errorResponse($validator->messages(), 422);

        if (!Hash::check($request->currentPassword, $user->password)) {
            return $this->errorResponse('Return error with current password is not match', 422);
        } else {
            $user->update([
                'updated_by' => auth()->id(),
                'password' => bcrypt($request->password),
            ]);
            return $this->successResponse('Password changed successfully');
        }
    }


    public function show(Request $request)
    {
        $user = User::
        where([['users.id', $this->user->id]])
            ->with('permission_group.permissions')
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
            ->get();

        $user = User::mediaUrl($user);

        return $this->successResponse($user);
    }

    public function getPermissionGroup($id)
    {
        if (!$permission_group = PermissionGroup::select('id', 'name')
            ->with('permissions.permission')
            ->where('id', $id)
            ->first()) {
            return $this->errorResponse('I18nSource not found', 404);
        }

        return $this->successResponse($permission_group);

    }

    public function photo(Request $request)
    {
        if (!$user = User::where('id', $this->user->id)->first())
            abort(404);

        $validator = Validator::make($request->all(), [
            'profile_img' => 'exists:media,id'
        ]);

        if ($validator->fails())
            return $this->errorResponse($validator->messages(), 422);

        $user->update([
            'profile_img' => $request->profile_img ? $request->profile_img : $user->profile_img,
            'updated_by' => auth()->id()
        ]);

        if ($request->profile_img) {
            $user->syncMedia($request->profile_img, ['avatar']);
            $this->media->moveFolderImage($request->profile_img, $user->id, 'Profile');
        }

        return $this->successResponse('Photo removed successfully');

    }
}
