<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaduanController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| USER (MASYARAKAT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Pengaduan - USER
    Route::post('/pengaduan', [PengaduanController::class, 'store']);
    Route::get('/pengaduan-saya', [PengaduanController::class, 'myPengaduan']);
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/admin/pengaduan', [PengaduanController::class, 'index']);
    Route::put('/admin/pengaduan/{id}', [PengaduanController::class, 'update']);
    Route::delete('/admin/pengaduan/{id}', [PengaduanController::class, 'destroy']);
});
