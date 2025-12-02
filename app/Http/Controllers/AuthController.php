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

    // Tampil form register
    public function showRegisterForm()
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

        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'min:6', 'confirmed'],
            'role'                  => ['required', 'in:manager,staff'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        Auth::login($user);

        if ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('staff.dashboard');
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
