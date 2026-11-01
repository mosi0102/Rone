<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MockAsset;
use App\Support\DemoAuth;

abstract class Controller
{
    use MockAsset;

    /**
     * مقصد بازگشت (next) فقط داخل همین سایت پذیرفته می‌شود؛ آدرس خارجی/جاوااسکریپت ← مقصد پیش‌فرض (جلوگیری از Open Redirect).
     */
    protected function safeNext(?string $url, ?string $default = null): string
    {
        $default = $default ?: route('home');
        if (!$url || str_contains($url, '\\') || preg_match('/[\x00-\x1f]/', $url)) return $default;
        $p = parse_url($url);
        if ($p === false) return $default;
        if (isset($p['scheme']) || isset($p['host'])) {                              // آدرس کامل: فقط http(s) و همین دامنه
            return (in_array($p['scheme'] ?? '', ['http', 'https'], true) && ($p['host'] ?? '') === request()->getHost()) ? $url : $default;
        }
        return ($url[0] === '/' && !str_starts_with($url, '//')) ? $url : $default;   // مسیر نسبی
    }

    /** پاسخ HTML که برای درخواست AJAX و عادی فرق می‌کند؛ Vary جلوی کش‌شدن اشتباه (مثلاً دکمه‌ی برگشت مرورگر) را می‌گیرد. */
    protected function varied($view, array $data = [])
    {
        return response()->view($view, $data)->header('Vary', 'X-Requested-With');
    }

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
