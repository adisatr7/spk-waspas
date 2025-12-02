<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;           // ⬅️ TAMBAH INI
use App\Http\Controllers\AuthController;

// Halaman utama
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user && $user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($user && $user->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }
    }

    return redirect()->route('login');
});

// AUTH
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// DASHBOARD (sementara)
Route::middleware('auth')->group(function () {
    Route::get('/manager/dashboard', function () {
        $user = Auth::user();

        if (!$user || $user->role !== 'manager') {
            abort(403, 'Anda bukan manager');
        }

        return view('manager.dashboard');
    })->name('manager.dashboard');

    Route::get('/staff/dashboard', function () {
        $user = Auth::user();

        if (!$user || $user->role !== 'staff') {
            abort(403, 'Anda bukan staff');
        }

        return view('staff.dashboard');
    })->name('staff.dashboard');
});
