<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

// گفتگوی خریدار ↔ نمایشگاه‌دار
Route::get('/chats', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chats/start', [ChatController::class, 'start'])->name('chat.start');
Route::get('/chats/{id}', [ChatController::class, 'show'])->whereNumber('id')->name('chat.show');
Route::post('/chats/{id}/messages', [ChatController::class, 'send'])->whereNumber('id')->name('chat.send');
Route::post('/chats/{id}/report', [ChatController::class, 'report'])->whereNumber('id')->name('chat.report');
