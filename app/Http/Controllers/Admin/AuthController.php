<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.pages.login');
    }

    public function login()
    {
        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        return redirect()->route('admin.login');
    }
}
