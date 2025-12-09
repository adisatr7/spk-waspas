<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Manager\StaffController;
use App\Http\Controllers\Staff\InfluencerController;
use App\Http\Controllers\Staff\CriteriaController;
use App\Http\Controllers\Staff\WaspasController;
use App\Http\Controllers\Manager\WaspasResultController;
use App\Http\Controllers\Staff\SelectedInfluencerController;
use App\Http\Controllers\Manager\EndorseHistoryController;

// Models
use App\Models\Influencer;
use App\Models\Criterion;
use App\Models\WaspasHistory;
use App\Models\WaspasHistoryItem;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman awal: redirect sesuai role
Route::get('/', function (Request $request) {
    /** @var \App\Models\User|null $user */
    $user = $request->user();

    if ($user) {
        if ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($user->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }
    }

    return redirect()->route('login');
});

// ================== AUTH ==================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================== PROFILE (AKUN) ==================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ================== GROUP MANAGER ==================
Route::middleware('auth')
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        Route::get('/dashboard', function (Request $request) {
            /** @var \App\Models\User|null $user */
            $user = $request->user();

            if (!$user || $user->role !== 'manager') {
                abort(403, 'Akses ditolak. Hanya manager yang boleh mengakses dashboard ini.');
            }

            // Statistik global
            $totalInfluencers = Influencer::count();
            $totalCriteria    = Criterion::where('is_active', true)->count();
            $totalHistories   = WaspasHistory::count(); // semua perhitungan dari semua staff
            $staffCount       = User::where('role', 'staff')->count();

            return view('manager.dashboard', compact(
                'user',
                'totalInfluencers',
                'totalCriteria',
                'totalHistories',
                'staffCount'
            ));
        })->name('dashboard');

        // CRUD staff (role dicek di StaffController::ensureManager)
        Route::resource('staff', StaffController::class)
            ->parameters(['staff' => 'id']);

        // ===== Hasil Perhitungan WASPAS (READ-ONLY) =====
        Route::get('waspas', [WaspasResultController::class, 'index'])
            ->name('waspas.index');

        Route::get('waspas/{id}', [WaspasResultController::class, 'show'])
            ->name('waspas.show');

        // Riwayat influencer terpilih (endorse history)
        Route::get('endorse-history', [EndorseHistoryController::class, 'index'])
            ->name('endorse.index');
    });

// ================== GROUP STAFF ==================
Route::middleware('auth')
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        // DASHBOARD STAFF dengan statistik & data chart
        Route::get('/dashboard', function (Request $request) {
            /** @var \App\Models\User|null $user */
            $user = $request->user();

            if (!$user || $user->role !== 'staff') {
                abort(403, 'Akses ditolak. Hanya staff yang boleh mengakses dashboard ini.');
            }

            // Statistik dasar
            $totalInfluencers = Influencer::count();
            $totalCriteria    = Criterion::where('is_active', true)->count();
            $totalHistories   = WaspasHistory::where('user_id', $user->id)->count();
            $totalSelected    = WaspasHistoryItem::where('selected', true)
                ->whereHas('history', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->count();

            // Riwayat WASPAS terakhir (untuk grafik)
            $recentHistories = WaspasHistory::withCount([
                    'items as selected_count' => function ($q) {
                        $q->where('selected', true);
                    },
                    'items as influencer_count',
                ])
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return view('staff.dashboard', compact(
                'user',
                'totalInfluencers',
                'totalCriteria',
                'totalHistories',
                'totalSelected',
                'recentHistories'
            ));
        })->name('dashboard');

        // CRUD Influencer (tanpa show)
        Route::resource('influencers', InfluencerController::class)
            ->parameters(['influencers' => 'id'])
            ->except(['show']);

        // CRUD Kriteria & Bobot (tanpa show)
        Route::resource('criteria', CriteriaController::class)
            ->parameters(['criteria' => 'id'])
            ->except(['show']);

        // Perhitungan WASPAS
        Route::get('waspas', [WaspasController::class, 'index'])->name('waspas.index');
        Route::get('waspas/create', [WaspasController::class, 'create'])->name('waspas.create');
        Route::post('waspas', [WaspasController::class, 'store'])->name('waspas.store');
        Route::get('waspas/{id}', [WaspasController::class, 'show'])->name('waspas.show');

        // Toggle selected / batal selected influencer pada hasil WASPAS
        Route::post('waspas/item/{id}/toggle', [SelectedInfluencerController::class, 'toggle'])
            ->name('waspas.toggle');

        // Daftar influencer terpilih endorse (untuk staff)
        Route::get('selected-influencers', [SelectedInfluencerController::class, 'selectedList'])
            ->name('waspas.selected');
    });
