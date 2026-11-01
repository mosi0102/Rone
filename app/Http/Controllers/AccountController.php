<?php

namespace App\Http\Controllers;

use App\Support\DemoAuth;

class AccountController extends Controller
{
    /** حساب من: خلاصه‌ی حساب، وضعیت احراز هویت و میان‌برها */
    public function index()
    {
        return view('pages.account.index', ['pending_deals' => 1, 'unread_chats' => 1]);
    }

    /** اطلاعات کاربری و احراز هویت (شخصی + مدارک + بانکی) */
    public function profile()
    {
        $auth = DemoAuth::get();
        return view('pages.account.profile', [
            'need' => (bool)request('need'),                                   // از عملیات پرداختی به اینجا هدایت شده
            'next' => request('next') ?: route('home'),
            'sent' => (bool)request('sent'),
            'reject_reason' => $auth->profile_status === 'rejected' ? 'تصویر سلفی همراه کارت ملی خوانا نبود. لطفاً دوباره و در نور کافی ارسال کنید.' : null,
            // اطلاعات ثبت‌شده (بک‌اندکار: مقدارهای واقعی؛ کد ملی/شبا/کارت را ماسک‌شده برگرداند)
            'p' => (object)[
                'first_name' => 'علی', 'last_name' => 'محمدی', 'national_code' => '۰۰۱****۸۹۲', 'birth_date' => '۱۳۷۰/۰۵/۱۲',
                'holder' => 'علی محمدی', 'iban' => 'IR12 **** **** **** **** **52 14', 'card' => '۶۰۳۷ **** **** ۴۵۲۱',
            ],
        ]);
    }

    /**
     * ثبت مدارک (نمایشی). بک‌اندکار: اعتبارسنجی و ذخیره‌ی فایل‌ها، وضعیت را pending کند.
     * قواعد: national_code (۱۰ رقم + رقم کنترلی)، iban (IR + ۲۴ رقم)، card_number (۱۶ رقم)،
     * national_card و selfie_with_card (تصویر jpg/png/webp حداکثر ۵ مگابایت)، نام صاحب حساب = نام کاربر.
     */
    public function saveProfile()
    {
        session(['demo_profile' => 'pending']);
        return redirect()->route('account.profile', ['sent' => 1]);
    }
}
