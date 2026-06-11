<?php

use App\Http\Controllers\AdminMatchController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/', fn () => redirect()->route('matches.index'));

Route::middleware(['auth'])->group(function () {
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::post('/matches/prediction/all', [PredictionController::class, 'storeAll'])->name('predictions.storeAll');
    Route::post('/matches/{matchGame}/prediction', [PredictionController::class, 'store'])->name('predictions.store');
    Route::get('/my-predictions', [PredictionController::class, 'index'])->name('predictions.index');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('matches', AdminMatchController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
