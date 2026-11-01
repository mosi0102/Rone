<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{

    public function logout()
    {
        return redirect('/?as=guest');
    }

    public function welcome()
    {
        return view('pages.auth.welcome', ['next' => $this->safeNext(request('next'))]);
    }

    public function login()
    {
        return view('pages.auth.login', ['next' => $this->safeNext(request('next'))]);
    }

    public function registerDealer()
    {
        return view('pages.auth.dealer');
    }
}
