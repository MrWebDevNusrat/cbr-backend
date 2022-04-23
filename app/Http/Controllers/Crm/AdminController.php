<?php

namespace App\Http\Controllers\Crm;

use App\Models\Crm\Config;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:crm_admin_index',   ['only' => 'index']);
        $this->middleware('permission:crm_admin_store',   ['only' => 'store', 'show']);
        $this->middleware('permission:crm_admin_update',  ['only' => 'update', 'show']);
        $this->middleware('permission:crm_admin_show',    ['only' => 'show']);
        $this->middleware('permission:crm_admin_destroy', ['only' => 'destroy']);
    }

    public function index(Request $request)
    {
        $admins = User::leftJoin('permission_groups', 'permission_groups.id', '=', 'users.permission_group_id')
            ->where('role', User::ROLE_ADMIN)
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
                'profile_img',
                'permission_groups.name as permission_group_name'
            );

        $admins = $admins->paginate($request->get('limit', Config::key('grid-pagination-limit')));

        return $this->successResponse($admins);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:255|unique:users|email:rfc,dns',
            'password' => 'required|string|min:6|max:255',
            'status' => 'required|in:1,0',
            'firstname' => 'required|string|max:20',
            'birth_date' => 'required|date|date_format:d.m.Y',
            'surname' => 'required|string|max:20',
            'patronymic' => 'required|string|max:20',
            'permission_group_id' => 'required|integer|exists:permission_groups,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $admin = User::create([
            'created_by' => auth()->id(),
            'username' => $request->username,
            'firstname' => $request->firstname,
            'surname' => $request->surname,
            'patronymic' => $request->patronymic,
            'birth_date' => $request->birth_date,
            'password' => bcrypt($request->password),
            'role' => User::ROLE_ADMIN,
            'status' => $request->status,
            'permission_group_id' => $request->permission_group_id
        ]);

        if ($request->profile_img) {
            $admin->syncMedia($request->profile_img, ['avatar']);
            $this->media->moveFolderImage($request->profile_img, $admin->id, 'Profile');
        }

        return $this->view($admin->id);

    }

    public function update(Request $request, User $admin)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:20',
            'surname' => 'required|string|max:20',
            'patronymic' => 'required|string|max:20',
            'birth_date' => 'required|date|date_format:d.m.Y',
            'username' => 'required|string|min:4|max:255|email:rfc,dns|unique:users,username,' . $admin->id,
            'password' => 'nullable|min:6|max:255',
            'status' => 'required|in:1,0',
            'permission_group_id' => 'required|integer|exists:permission_groups,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $admin->update([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'surname' => $request->surname,
            'patronymic' => $request->patronymic,
            'birth_date' => $request->birth_date,
            'password' => isset($request->password) ? bcrypt($request->password) : $admin->password,
            'status' => $request->status,
            'permission_group_id' => $request->permission_group_id,
            'updated_by' => auth()->id()
        ]);

        return $this->view($admin->id);
    }

    public function show(User $admin)
    {
        if ($admin->role != User::ROLE_ADMIN)
            return $this->errorResponse('Admin not found',404);

        return $this->view($admin->id);
    }

    public function destroy(User $admin)
    {
        if ($admin->role != User::ROLE_ADMIN)
            return $this->errorResponse('Admin not found',404);

        $admin->update(['deleted_by' => auth()->id()]);
        $admin->delete();

        return $this->successResponse('Admin deleted successfully.');
    }

    public function view($id)
    {
        $admin = User::select(
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
            ->where('id', $id)->with('permission_group:id,name')->get();

        return $this->successResponse($admin);
    }

    public function lists(Request $request)
    {
        $admins = User::where('deleted_by', '=', null)
            ->where('role', '=', User::ROLE_ADMIN)
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
            ->where(function ($query) use ($request) {
                if ($request->language)
                    $query->where('users.username', '=', $request->username);
                if ($request->name)
                    $query->where('users.status', 'LIKE', "%{$request->status}%");

            })->get();
        return $this->successResponse($admins);
    }
}
