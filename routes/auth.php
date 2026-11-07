<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ورود، ثبت‌نام و خوش‌آمدگویی
Route::get('/welcome', [AuthController::class, 'welcome'])->name('welcome');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register/agency', [AuthController::class, 'registerAgency'])->name('agency.register');
