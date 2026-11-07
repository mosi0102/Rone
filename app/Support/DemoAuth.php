<?php

namespace App\Support;

/**
 * کاربر نمایشی (فقط برای پیش‌نمایش فرانت؛ بک‌اندکار با کاربر واقعی جایگزین کند).
 *
 * سه نوع کاربر: مهمان · کاربر (وارد شده) · مشاور املاک (همه‌ی امکانات کاربر + پنل آژانس).
 * پیش‌نمایش: ?as=guest|user|agency  و  وضعیت احراز هویت: ?profile=none|pending|approved|rejected
 * عملیات پرداختی (ورود به مزایده، برداشت) فقط با profile_status = approved مجاز است.
 */
class DemoAuth
{
    public static function get(): object
    {
        static $auth = null;
        if ($auth) {
            return $auth;
        }

        // پیش‌نمایش (?as / ?profile) فقط در حالت debug؛ در محیط واقعی نادیده گرفته می‌شود (بک‌اندکار کل این کلاس را با کاربر واقعی جایگزین کند)
        $demo = (bool) config('app.debug');
        $as = $demo ? (request('as') ?: session('demo_as', 'user')) : 'guest';
        if (! in_array($as, ['guest', 'user', 'agency'], true)) {
            $as = 'user';
        }
        session(['demo_as' => $as]);

        $profile = $demo ? (request('profile') ?: session('demo_profile', 'approved')) : 'none';
        if (! in_array($profile, ['none', 'pending', 'approved', 'rejected'], true)) {
            $profile = 'approved';
        }
        session(['demo_profile' => $profile]);

        $agency = $as === 'agency' ? (object) ['id' => 8, 'name' => 'املاک پارسیان', 'verified' => true, 'plan_days_left' => 41, 'plan_total_days' => 90, 'plan_name' => 'اشتراک ۳ ماهه'] : null;

        return $auth = (object) [
            'check' => $as !== 'guest',
            'name' => $as === 'agency' ? 'ابولفضل پناهی' : 'علی محمدی',
            'phone' => '۰۹۱۲۳۴۵۶۷۸۹',
            'wallet' => 120000000,
            'is_agency' => (bool) $agency,
            'agency' => $agency,
            'profile_status' => $profile,
            'verified' => $profile === 'approved',
        ];
    }
}
