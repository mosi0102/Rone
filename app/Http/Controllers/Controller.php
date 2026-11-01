<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MockAsset;
use App\Support\DemoAuth;

abstract class Controller
{
    use MockAsset;

    /**
     * عملیات پرداختی (بیعانه، ورود به مزایده، برداشت) فقط برای کاربر احراز‌شده.
     * اگر کاربر وارد شده ولی پروفایلش تأیید نشده باشد، به صفحه‌ی تکمیل اطلاعات می‌رود.
     * بک‌اندکار: همین شرط را روی میدل‌ور بگذارد.
     */
    protected function requireVerified()
    {
        $auth = DemoAuth::get();
        if ($auth->check && !$auth->verified) {
            return redirect()->route('account.profile', ['need' => 1, 'next' => request()->fullUrl()]);
        }
        return null;
    }
}
