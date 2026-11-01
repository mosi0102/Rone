<?php

namespace App\Support;

/**
 * کاربر نمایشی (فقط برای پیش‌نمایش فرانت؛ بک‌اندکار با کاربر واقعی جایگزین کند).
 *
 * سه نوع کاربر: مهمان · کاربر (وارد شده) · نمایشگاه‌دار (همه‌ی امکانات کاربر + پنل نمایشگاه).
 * پیش‌نمایش: ?as=guest|user|dealer  و  وضعیت احراز هویت: ?profile=none|pending|approved|rejected
 * عملیات پرداختی (بیعانه، ورود به مزایده، برداشت) فقط با profile_status = approved مجاز است.
 */
class DemoAuth
{
    public static function get(): object
    {
        static $auth = null;
        if ($auth) return $auth;

        $as = request('as') ?: session('demo_as', 'user');
        session(['demo_as' => $as]);

        $profile = request('profile') ?: session('demo_profile', 'approved');
        if (!in_array($profile, ['none', 'pending', 'approved', 'rejected'], true)) $profile = 'approved';
        session(['demo_profile' => $profile]);

        $dealer = $as === 'dealer' ? (object)['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true, 'plan_days_left' => 41, 'plan_total_days' => 90, 'plan_name' => 'اشتراک ۳ ماهه'] : null;
        return $auth = (object)[
            'check' => $as !== 'guest',
            'name' => $as === 'dealer' ? 'ابولفضل پناهی' : 'علی محمدی',
            'phone' => '۰۹۱۲۳۴۵۶۷۸۹',
            'wallet' => 120000000,
            'is_dealer' => (bool)$dealer,
            'dealer' => $dealer,
            'profile_status' => $profile,
            'verified' => $profile === 'approved',
        ];
    }
}
