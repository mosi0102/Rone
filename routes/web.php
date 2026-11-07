<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
| فقط آدرس‌ها؛ داده و منطق در کنترلرها (app/Http/Controllers) است.
| نام مسیرها (route name) را تغییر ندهید؛ ناوبری به آن‌ها وابسته است.
*/

// صفحه‌ها
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/styleguide', [HomeController::class, 'styleguide'])->name('styleguide');

// آژانس‌ها
Route::get('/agencies', [AgencyController::class, 'index'])->name('agencies.index');
Route::get('/agencies/search', [AgencyController::class, 'search'])->name('agencies.search');
Route::get('/agencies/join', [AgencyController::class, 'join'])->name('agency.join');
Route::get('/agencies/status', [AgencyController::class, 'status'])->name('agency.status');
Route::get('/agencies/{id}', [AgencyController::class, 'show'])->whereNumber('id')->name('agencies.show');

// آگهی‌ها و جست‌وجو
Route::get('/search', [ListingController::class, 'search'])->name('search');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/filters', [ListingController::class, 'filters'])->name('listings.filters');
Route::get('/listings/count', [ListingController::class, 'count'])->name('listings.count');
Route::get('/listings/map', [ListingController::class, 'map'])->name('listings.map');
Route::get('/listings/map/data', [ListingController::class, 'mapData'])->name('listings.map.data');
Route::get('/listings/{id}', [ListingController::class, 'show'])->whereNumber('id')->name('listings.show');

// ورود/خروج و حالت کاربر
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// پنل مشاور املاک
Route::get('/panel', [PanelController::class, 'dashboard'])->name('panel');
Route::get('/panel/listings/create', [PanelController::class, 'listingCreate'])->name('listings.create');
Route::get('/panel/listings/{id}/edit', [PanelController::class, 'listingEdit'])->whereNumber('id')->name('listings.edit');

// مزایده‌ها (پرداخت‌ها: ساخت مزایده توسط آژانس و ورودی شرکت در مزایده)
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/panel/auctions/create', [AuctionController::class, 'create'])->name('auctions.create');
Route::get('/auctions/{id}', [AuctionController::class, 'show'])->whereNumber('id')->name('auctions.show');
Route::get('/auctions/{id}/entry', [AuctionController::class, 'entry'])->whereNumber('id')->name('auctions.entry');

// حساب: کیف پول، نشان‌شده‌ها، راهنما، اعلان‌ها
Route::get('/account', [AccountController::class, 'index'])->name('account');
Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
Route::post('/account/profile', [AccountController::class, 'saveProfile'])->name('account.profile.save');
Route::get('/account/wallet', [WalletController::class, 'index'])->name('account.wallet');
Route::post('/account/wallet/charge', [WalletController::class, 'charge'])->name('account.wallet.charge');
Route::post('/account/wallet/withdraw', [WalletController::class, 'withdraw'])->name('account.wallet.withdraw');
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
Route::get('/help', [HelpController::class, 'index'])->name('help');
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

Route::view('/offline', 'pages.offline')->name('offline');

// فایل‌های مسیر جدا
require __DIR__.'/auth.php';
require __DIR__.'/radar.php';
require __DIR__.'/chat.php';
require __DIR__.'/admin.php';
