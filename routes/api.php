<?php

use App\Http\Controllers\API\Admin\CurriculumController;
use App\Http\Controllers\API\Admin\LaporanController;
use App\Http\Controllers\API\ArtikelController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KuisController;
use App\Http\Controllers\API\MateriController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public Read Content
    Route::get('/materi', [MateriController::class, 'index']);
    Route::get('/materi/{slug}', [MateriController::class, 'show']);
    Route::get('/artikel', [ArtikelController::class, 'index']);
    Route::get('/artikel/{slug}', [ArtikelController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum Auth Required)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Authenticated User Info
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Warga Quiz Endpoint
    Route::get('/kuis/materi/{materiId}', [KuisController::class, 'getKuisByMateri']);
    Route::post('/kuis/submit', [KuisController::class, 'submitKuis']);

    // Admin Endpoints
    Route::prefix('admin')->group(function () {
        Route::post('/outcomes', [CurriculumController::class, 'storeOutcome']);
        Route::post('/materi', [CurriculumController::class, 'storeMateri']);
        Route::post('/kuis', [CurriculumController::class, 'storeKuis']);
        Route::post('/artikel', [CurriculumController::class, 'storeArtikel']);
        Route::get('/laporan/literasi', [LaporanController::class, 'getAnalitikLiterasi']);
    });
});
