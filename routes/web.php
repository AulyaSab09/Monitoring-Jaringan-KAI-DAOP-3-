<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\HistoryController;

Route::get('/', function () {
    return redirect()->route('preview');
});

Route::middleware('auth')->group(function () {

    // PREVIEW / DASHBOARD
    Route::get('/preview', [MonitorController::class, 'index'])->name('preview');

    // MONITOR CRUD
    Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor.index');
    Route::get('/monitor/create', [MonitorController::class, 'create'])->name('monitor.create');
    Route::post('/monitor', [MonitorController::class, 'store'])->name('monitor.store');
    Route::delete('/monitor/{id}', [MonitorController::class, 'destroy'])
    ->name('monitor.destroy');

    // AUTO REFRESH DATA
    Route::get('/monitor/data', [MonitorController::class, 'data'])->name('monitor.data');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
});

require __DIR__.'/auth.php';
