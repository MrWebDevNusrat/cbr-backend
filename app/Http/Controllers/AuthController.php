<?php

namespace App\Http\Controllers;

use App\Models\Crm\PermissionGroup;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = array('phone' => '974116408', 'password' => 'string');


        if (Auth::guard('web')->attempt($credentials, false)){

            return redirect()->route('send-push.notificaiton');
        }
        return redirect()->route('home');
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

}
