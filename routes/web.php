<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\StasiunController;

// 1. Route Halaman Utama (Redirect ke Preview Monitor)
Route::get('/', function () {
    return redirect()->route('monitor.index');
});

// 2. Route Resource Stasiun
// Menangani: stasiun.index, stasiun.store, stasiun.destroy, dll.
Route::resource('stasiun', StasiunController::class);

// 3. Route Monitoring (AJAX Data)
Route::get('/monitor/data', [MonitorController::class, 'getTableData'])->name('monitor.data');

// 4. Route Resource Monitoring (Preview)
Route::resource('preview', MonitorController::class)->names([
    'index' => 'monitor.index',
    'create' => 'monitor.create',
    'store' => 'monitor.store',
    'edit' => 'monitor.edit',
    'update' => 'monitor.update',
    'destroy' => 'monitor.destroy',
]);