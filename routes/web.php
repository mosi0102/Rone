<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PanelController;

/*
| فقط آدرس‌ها؛ داده و منطق در کنترلرها (app/Http/Controllers) است.
| نام مسیرها (route name) را تغییر ندهید؛ ناوبری به آن‌ها وابسته است.
*/

// صفحه‌ها
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/styleguide', [HomeController::class, 'styleguide'])->name('styleguide');

// نمایشگاه‌ها
Route::get('/dealers', [DealerController::class, 'index'])->name('dealers.index');
Route::get('/dealers/search', [DealerController::class, 'search'])->name('dealers.search');
Route::get('/dealers/join', [DealerController::class, 'join'])->name('dealer.join');
Route::get('/dealers/status', [DealerController::class, 'status'])->name('dealer.status');
Route::get('/dealers/{id}', [DealerController::class, 'show'])->whereNumber('id')->name('dealers.show');

// آگهی‌ها و جست‌وجو
Route::get('/search', [ListingController::class, 'search'])->name('search');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/filters', [ListingController::class, 'filters'])->name('listings.filters');
Route::get('/listings/count', [ListingController::class, 'count'])->name('listings.count');
Route::get('/listings/{id}', [ListingController::class, 'show'])->whereNumber('id')->name('listings.show');

// ورود/خروج و حالت کاربر
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// پنل نمایشگاه‌دار
Route::get('/panel', [PanelController::class, 'dashboard'])->name('panel');
Route::get('/panel/settlements', [PanelController::class, 'settlements'])->name('panel.settlements');
Route::get('/panel/listings/create', [PanelController::class, 'listingCreate'])->name('listings.create');
Route::get('/panel/listings/{id}/edit', [PanelController::class, 'listingEdit'])->whereNumber('id')->name('listings.edit');

// مزایده‌ها
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/panel/auctions/create', [AuctionController::class, 'create'])->name('auctions.create');
Route::get('/auctions/{id}', [AuctionController::class, 'show'])->whereNumber('id')->name('auctions.show');
Route::get('/auctions/{id}/entry', [AuctionController::class, 'entry'])->whereNumber('id')->name('auctions.entry');

// بیعانه و معامله
Route::get('/listings/{id}/deposit', [DealController::class, 'deposit'])->whereNumber('id')->name('listings.deposit');
Route::get('/deals/{id}', [DealController::class, 'show'])->whereNumber('id')->name('deals.show');
Route::get('/deals/{id}/decide', [DealController::class, 'decide'])->whereNumber('id')->name('deals.decide');
Route::get('/account/deals', [DealController::class, 'index'])->name('account.reservations');

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
require __DIR__ . '/auth.php';
require __DIR__ . '/radar.php';
require __DIR__ . '/chat.php';
require __DIR__ . '/admin.php';
