<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IcdxController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('icd')->group(function () {
    Route::get('/search', [IcdxController::class, 'searchApi']);
    Route::get('/detail/{code}', [IcdxController::class, 'detailApi']);
});
