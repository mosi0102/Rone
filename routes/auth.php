<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| مسیرهای ورود، ثبت‌نام و خوش‌آمدگویی (فایل مستقل)
|--------------------------------------------------------------------------
| اتصال: در routes/web.php مسیر قدیمی `/login` را حذف و در انتها این خط را اضافه کنید:
|     require __DIR__ . '/auth.php';
| نام مسیر `login` (که بقیه‌ی صفحه‌ها به آن لینک می‌دهند) همان می‌ماند.
|
| بک‌اندکار: درخواست‌های OTP/رمز را در js/auth.js بخش `Auth.api` وصل کند (فعلاً شبیه‌سازی است).
*/

Route::get('/welcome', fn () => view('pages.auth.welcome', ['next' => request('next', route('home'))]))->name('welcome');
Route::get('/login', fn () => view('pages.auth.login', ['next' => request('next', route('home'))]))->name('login');
Route::get('/register/dealer', fn () => view('pages.auth.dealer'))->name('dealer.register');
