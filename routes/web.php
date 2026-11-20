<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Belum login: login, register, guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');

    Route::post('/guest', [AuthController::class, 'guest'])->name('guest');
});

// Sudah login: semua fitur utama
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

    Route::get('/scan', [ScanController::class, 'index'])->name('scan');
    Route::post('/scan', [ScanController::class, 'store'])->name('scan.store');
    Route::get('/scan/{scan}', [ScanController::class, 'show'])->name('scan.show');

    Route::get('/tantangan', [ChallengeController::class, 'index'])->name('challenges');

    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history');

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

    Route::get('/pencapaian', [AchievementController::class, 'index'])->name('achievements');

    Route::get('/hadiah', [RewardController::class, 'index'])->name('rewards');
    Route::post('/hadiah/{reward}/tukar', [RewardController::class, 'redeem'])->name('rewards.redeem');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/scan/detect-preview', [ScanController::class, 'detectPreview'])
    ->name('scan.detect');


});
