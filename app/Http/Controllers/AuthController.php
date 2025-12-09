<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampil form login
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user && $user->role === 'manager') {
                return redirect()->route('manager.dashboard');
            }

            if ($user && $user->role === 'staff') {
                return redirect()->route('staff.dashboard');
            }
        }

        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user && $user->role === 'manager') {
                return redirect()->route('manager.dashboard');
            }

            return redirect()->route('staff.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }



    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
