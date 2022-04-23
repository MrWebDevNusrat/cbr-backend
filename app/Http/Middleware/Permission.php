<?php

namespace App\Http\Middleware;

use App\Models\Crm\PermissionGroupPermission;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class Permission
{
    protected $auth;

    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if(Auth::user()) {
            $can = false;
            $user = User::find(Auth::user()->id);
            $group_permissions = PermissionGroupPermission::where('permission_group_id', $user->permission_group_id)->get();
            foreach ($group_permissions as $item) {
                $permission = \App\Models\Crm\Permission::where('id', $item->permission_id)->first();
                foreach(explode('|', $permissions) as $i){
                    if($permission->name == $i)
                        $can = true;
                }
            }
            if(!$can) abort(403, 'You are unauthorized to access this resource');
        }

        return $next($request);
    }
}
