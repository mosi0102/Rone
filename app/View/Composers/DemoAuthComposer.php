<?php

namespace App\View\Composers;

use App\Support\DemoAuth;
use Illuminate\View\View;

/** متغیر سراسری $auth در همه‌ی viewها (نمایشی؛ بک‌اندکار با کاربر واقعی جایگزین کند) */
class DemoAuthComposer
{
    public function compose(View $view): void
    {
        $view->with('auth', DemoAuth::get());
    }
}
