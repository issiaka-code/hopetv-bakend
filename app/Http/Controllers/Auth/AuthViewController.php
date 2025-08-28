<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthViewController extends Controller
{
    public function createLogin()
    {
        return view('auth.login');
    }

        // public function creatRegister(): View
        // {
        //     return view('auth.register');
        // }
}
