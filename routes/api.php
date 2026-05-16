<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IcdxController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobilePresensiController;
use App\Http\Controllers\Api\MobileCutiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('icd')->group(function () {
    Route::get('/search', [IcdxController::class, 'searchApi']);
    Route::get('/detail/{code}', [IcdxController::class, 'detailApi']);
});

// ── Mobile App API ──────────────────────────────────────
Route::prefix('mobile')->group(function () {

    // Auth (tidak butuh token)
    Route::post('/login', [MobileAuthController::class, 'login']);

    // Routes yang butuh token Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/me', [MobileAuthController::class, 'me']);

        // Presensi
        Route::get('/presensi', [MobilePresensiController::class, 'index']);
        Route::post('/presensi/clock-in', [MobilePresensiController::class, 'clockIn']);
        Route::post('/presensi/clock-out', [MobilePresensiController::class, 'clockOut']);

        // Cuti / Izin / Sakit
        Route::get('/cuti', [MobileCutiController::class, 'index']);
        Route::get('/cuti/quota', [MobileCutiController::class, 'quota']);
        Route::post('/cuti', [MobileCutiController::class, 'store']);
    });
});

