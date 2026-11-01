<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ورود، ثبت‌نام و خوش‌آمدگویی
Route::get('/welcome', [AuthController::class, 'welcome'])->name('welcome');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register/dealer', [AuthController::class, 'registerDealer'])->name('dealer.register');
