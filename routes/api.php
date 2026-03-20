<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua Controller
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\JadwalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rute Login (Bebas diakses)
Route::post('/login', [AuthController::class, 'login']);

// SAFE METHODS (GET: index & show) - Bebas diakses
Route::apiResource('/users', UserController::class)->only(['index', 'show']);
Route::apiResource('/guru', GuruController::class)->only(['index', 'show']);
Route::apiResource('/mapel', MapelController::class)->only(['index', 'show']);
Route::apiResource('/kelas', KelasController::class)->parameters(['kelas' => 'kelas'])->only(['index', 'show']);
Route::apiResource('/siswa', SiswaController::class)->only(['index', 'show']);
Route::apiResource('/jadwal', JadwalController::class)->only(['index', 'show']);

// UNSAFE METHODS (POST, PUT, DELETE) - Wajib menggunakan Token JWT
Route::group(['middleware' => ['jwt.verify']], function() {
    
    // Rute cek token
    Route::get('/cek-token', [UserController::class, 'cek_token']); 

    // Rute yang dikunci
    Route::apiResource('/users', UserController::class)->except(['index', 'show']);
    Route::apiResource('/guru', GuruController::class)->except(['index', 'show']);
    Route::apiResource('/mapel', MapelController::class)->except(['index', 'show']);
    Route::apiResource('/kelas', KelasController::class)->parameters(['kelas' => 'kelas'])->except(['index', 'show']);
    Route::apiResource('/siswa', SiswaController::class)->except(['index', 'show']);
    Route::apiResource('/jadwal', JadwalController::class)->except(['index', 'show']);
});