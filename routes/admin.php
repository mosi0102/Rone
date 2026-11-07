<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\AuctionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// پنل ادمین (بک‌اندکار: میدل‌ور احراز هویت/دسترسی ادمین را روی گروه بدون /login بگذارد)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [RequestController::class, 'show'])->whereNumber('id')->name('requests.show');
    Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
    Route::get('/auctions/{id}', [AuctionController::class, 'show'])->whereNumber('id')->name('auctions.show');
    Route::get('/agencies', [AgencyController::class, 'index'])->name('agencies.index');
    Route::get('/agencies/{id}', [AgencyController::class, 'show'])->whereNumber('id')->name('agencies.show');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->whereNumber('id')->name('users.show');
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/settlements', [FinanceController::class, 'settlements'])->name('settlements.index');
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/catalog', [ContentController::class, 'catalog'])->name('catalog.index');
    Route::get('/content', [ContentController::class, 'pages'])->name('content.index');
    Route::get('/logs', [ContentController::class, 'logs'])->name('logs.index');
});
