<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'admin' && $password === 'admin') {
            session(['admin_logged_in' => true]);
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('login');
    }
}