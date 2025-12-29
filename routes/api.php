<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaduanController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // --- FITUR MASYARAKAT ---
    Route::post('/pengaduan', [PengaduanController::class, 'store']);
    Route::get('/pengaduan-saya', [PengaduanController::class, 'myPengaduan']);

    // Route Baru untuk Update Masyarakat (Handle Foto & Teks)
    Route::post('/pengaduan-update/{id}', [PengaduanController::class, 'updateMasyarakat']);

    // --- FITUR ADMIN ---
    Route::get('/pengaduan', [PengaduanController::class, 'index']);
    Route::put('/pengaduan/{id}', [PengaduanController::class, 'update']); // Untuk Admin update status

    // --- FITUR UMUM ---
    Route::delete('/pengaduan/{id}', [PengaduanController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
