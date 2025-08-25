<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $cred = $request->validate([
            'username' => ['required', 'exists:users'],
            'password' => ['required']
        ]);

        if (Auth::attempt($cred, $request->remember)) {
            return redirect('/');
        }
        return back()->withErrors([
            'password' => 'Password yang diberikan salah.',
        ])->onlyInput('password');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
