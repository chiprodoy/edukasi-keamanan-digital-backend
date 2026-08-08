<?php

use App\Http\Controllers\API\Admin\CurriculumController;
use App\Http\Controllers\API\Admin\LaporanController;
use App\Http\Controllers\API\ArtikelController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KuisController;
use App\Http\Controllers\API\MateriController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\KategoriArtikelController;
use App\Http\Controllers\API\WargaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OutcomeController;
use App\Http\Controllers\API\RubrikPenilaianController;
use App\Http\Controllers\API\SoalKuisController;
use App\Http\Controllers\API\UserDashboardController;
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
    //Route::get('/kuis/materi/{materiId}', [KuisController::class, 'getKuisByMateri']);
    Route::get('/kuis/{kuisId}', [KuisController::class, 'show']);
    Route::post('/kuis/{kuisId}/submit', [KuisController::class, 'submitKuis']);

    Route::get('/materi', [MateriController::class, 'index']);
    Route::get('/materi/{slug}', [MateriController::class, 'show']);
    Route::get('/artikel', [ArtikelController::class, 'index']);
    Route::get('/artikel/{slug}', [ArtikelController::class, 'show']);

    // Admin Endpoints
    Route::prefix('admin')->group(function () {
            // Endpoint Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index']);
        //Route::post('/outcomes', [CurriculumController::class, 'storeOutcome']);

        Route::get('/materi', [MateriController::class, 'index']);
        Route::post('/materi', [CurriculumController::class, 'storeMateri']);

        // Dropdown materi untuk form kuis
        Route::get('/materi-options', [KuisController::class, 'materiOptions']);

        Route::apiResource('/kuis', KuisController::class);
        Route::get('/kuis/{id}/questions', [KuisController::class, 'questionsByKuis']);
        Route::apiResource('/question', SoalKuisController::class);

        Route::apiResource('kategori-artikel', KategoriArtikelController::class);
        Route::apiResource('/artikel', ArtikelController::class);
        Route::apiResource('warga', WargaController::class);

        // Menyediakan otomatis route: GET, POST, GET {id}, PUT/PATCH {id}, DELETE {id}
        Route::apiResource('outcomes', OutcomeController::class);
        Route::apiResource('rubrik-penilaian', RubrikPenilaianController::class);

        Route::get('/laporan/literasi', [LaporanController::class, 'getAnalitikLiterasi']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index']);

    });
});
