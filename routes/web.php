<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('preview');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // PREVIEW / DASHBOARD
    Route::get('/preview', [MonitorController::class, 'index'])->name('preview');

    // MONITOR CRUD
    Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor.index');
    Route::get('/monitor/create', [MonitorController::class, 'create'])->name('monitor.create');
    Route::get('/monitor/data', [MonitorController::class, 'data'])->name('monitor.data');
    Route::post('/monitor', [MonitorController::class, 'store'])->name('monitor.store');
    Route::get('/monitor/{id}/edit', [MonitorController::class, 'edit'])->name('monitor.edit');
    Route::put('/monitor/{id}', [MonitorController::class, 'update'])->name('monitor.update');
    Route::delete('/monitor/{id}', [MonitorController::class, 'destroy'])->name('monitor.destroy');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // HISTORY
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
});

require __DIR__.'/auth.php';

