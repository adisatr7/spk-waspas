<?php

use Illuminate\Support\Facades\Route;

// API Controllers
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\Staff\InfluencerController as ApiStaffInfluencerController;
use App\Http\Controllers\Api\Staff\CriteriaController as ApiStaffCriteriaController;
use App\Http\Controllers\Api\Staff\WaspasController as ApiStaffWaspasController;
use App\Http\Controllers\Api\Staff\SelectedInfluencerController as ApiStaffSelectedInfluencerController;
use App\Http\Controllers\Api\Manager\StaffController as ApiManagerStaffController;
use App\Http\Controllers\Api\Manager\WaspasResultController as ApiManagerWaspasResultController;
use App\Http\Controllers\Api\Manager\EndorseHistoryController as ApiManagerEndorseHistoryController;

// ============== PUBLIC ==============

Route::get('/test', function () {
    return response()->json([
        'ok'      => true,
        'message' => 'API Connected',
        'time'    => now()->toDateTimeString(),
    ]);
});

// Auth (token)
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [ApiAuthController::class, 'me'])->middleware('auth:sanctum');

// ============== PROTECTED (auth:sanctum) ==============

Route::middleware('auth:sanctum')->group(function () {

    // -------- STAFF AREA --------
    Route::prefix('staff')->name('api.staff.')->group(function () {

        // Dashboard statistik staff
        Route::get('/dashboard', [ApiStaffWaspasController::class, 'dashboard']);

        // CRUD Influencer
        Route::get('/influencers', [ApiStaffInfluencerController::class, 'index']);
        Route::post('/influencers', [ApiStaffInfluencerController::class, 'store']);
        Route::get('/influencers/{id}', [ApiStaffInfluencerController::class, 'show']);
        Route::put('/influencers/{id}', [ApiStaffInfluencerController::class, 'update']);
        Route::delete('/influencers/{id}', [ApiStaffInfluencerController::class, 'destroy']);

        // CRUD Criteria
        Route::get('/criteria', [ApiStaffCriteriaController::class, 'index']);
        Route::post('/criteria', [ApiStaffCriteriaController::class, 'store']);
        Route::get('/criteria/{id}', [ApiStaffCriteriaController::class, 'show']);
        Route::put('/criteria/{id}', [ApiStaffCriteriaController::class, 'update']);
        Route::delete('/criteria/{id}', [ApiStaffCriteriaController::class, 'destroy']);

        // WASPAS histories (read only di API, perhitungan utama dari web controller)
        Route::get('/waspas', [ApiStaffWaspasController::class, 'index']);
        Route::get('/waspas/{id}', [ApiStaffWaspasController::class, 'show']);

        // Toggle selected influencer + list selected
        Route::post('/waspas/item/{id}/toggle', [ApiStaffSelectedInfluencerController::class, 'toggle']);
        Route::get('/selected-influencers', [ApiStaffSelectedInfluencerController::class, 'selectedList']);
    });

    // -------- MANAGER AREA --------
    Route::prefix('manager')->name('api.manager.')->group(function () {

        // Dashboard manager
        Route::get('/dashboard', [ApiManagerWaspasResultController::class, 'dashboard']);

        // CRUD Staff
        Route::get('/staff', [ApiManagerStaffController::class, 'index']);
        Route::post('/staff', [ApiManagerStaffController::class, 'store']);
        Route::get('/staff/{id}', [ApiManagerStaffController::class, 'show']);
        Route::put('/staff/{id}', [ApiManagerStaffController::class, 'update']);
        Route::delete('/staff/{id}', [ApiManagerStaffController::class, 'destroy']);

        // Hasil WASPAS (semua)
        Route::get('/waspas', [ApiManagerWaspasResultController::class, 'index']);
        Route::get('/waspas/{id}', [ApiManagerWaspasResultController::class, 'show']);

        // Riwayat endorse
        Route::get('/endorse-history', [ApiManagerEndorseHistoryController::class, 'index']);
    });
});
