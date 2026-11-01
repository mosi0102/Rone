<?php

namespace App\Providers;

use App\View\Composers\AdminComposer;
use App\View\Composers\DemoAuthComposer;
use App\View\Composers\RadarDashboardComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // متغیر سراسری $auth (نمایشی؛ بک‌اندکار با کاربر واقعی جایگزین کند)
        View::composer('*', DemoAuthComposer::class);

        // داده‌ی مشترک پنل ادمین
        View::composer('admin.*', AdminComposer::class);

        // درخواست‌های رادار مرتبط در داشبورد نمایشگاه‌دار
        View::composer('pages.panel.dashboard', RadarDashboardComposer::class);
    }
}
