<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RadarController;

// رادار خودرو
Route::get('/radar', [RadarController::class, 'index'])->name('radar.index');
Route::get('/radar/create', [RadarController::class, 'create'])->name('radar.create');
Route::get('/radar/preview', [RadarController::class, 'preview'])->name('radar.preview');
Route::post('/radar', [RadarController::class, 'store'])->name('radar.store');
Route::get('/radar/{id}', [RadarController::class, 'show'])->whereNumber('id')->name('radar.show');
Route::delete('/radar/{id}', [RadarController::class, 'destroy'])->whereNumber('id')->name('radar.destroy');
Route::get('/panel/radar', [RadarController::class, 'panel'])->name('panel.radar');
Route::post('/panel/radar/{id}/offer', [RadarController::class, 'offer'])->whereNumber('id')->name('panel.radar.offer');
