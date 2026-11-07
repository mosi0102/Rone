<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $queues = [
            ['label' => 'احراز هویت', 'count' => 4, 'icon' => 'user-check', 'tone' => 'sky', 'route' => 'admin.agencies.index'],
            ['label' => 'آگهی در انتظار', 'count' => 12, 'icon' => 'list-checks', 'tone' => 'peach', 'route' => 'admin.listings.index'],
            ['label' => 'برنده‌ی بدون مراجعه', 'count' => 2, 'icon' => 'alert-triangle', 'tone' => 'rose', 'route' => 'admin.auctions.index'],
            ['label' => 'درخواست برداشت', 'count' => 5, 'icon' => 'banknote', 'tone' => 'sage', 'route' => 'admin.settlements.index'],
            ['label' => 'گفتگوی گزارش‌شده', 'count' => 2, 'icon' => 'flag', 'tone' => 'sand', 'route' => 'admin.chats.index'],
        ];

        return view('admin.pages.dashboard', [
            'queues' => $queues,
            'pendingTotal' => collect($queues)->sum('count'),
            // آگهی‌های امروز که بررسی شده‌اند
            'today' => ['percent' => 68, 'done' => 17, 'total' => 25],
            'liveCount' => 3,         // مزایده‌ی زنده
            'auctionEntries' => 46,  // ورودی‌های پرداخت‌شده‌ی امروز
            'requestsCount' => 286, // درخواست‌های ملک فعال
            'kpi' => [
                'users' => 12840, 'users_delta' => 8,
                'agencies' => 312, 'agencies_delta' => 3,
                'listings' => 1668, 'listings_delta' => 5,
                'revenue' => 1260, 'revenue_delta' => -2,   // میلیون تومان
            ],
            // درآمد روزانه (میلیون تومان): a = فروش اشتراک، b = آگهی فوری و مزایده (ساخت + ورودی)
            'revenue' => [
                ['day' => 'ش', 'a' => 82, 'b' => 61], ['day' => 'ی', 'a' => 74, 'b' => 48], ['day' => 'د', 'a' => 91, 'b' => 70],
                ['day' => 'س', 'a' => 66, 'b' => 52], ['day' => 'چ', 'a' => 40, 'b' => 31], ['day' => 'پ', 'a' => 88, 'b' => 94], ['day' => 'ج', 'a' => 55, 'b' => 42],
            ],
            'revTotal' => 496,
            'revExtra' => 398,
            'pending' => [
                ['title' => 'آپارتمان ۱۲۰ متری نوساز', 'meta' => 'فروش • سعادت‌آباد • ۴ طبقه', 'agency' => 'املاک پارسیان', 'price' => 18500000000, 'posted' => '۲ ساعت پیش', 'image' => $this->img('agency-stock-1.jpg')],
                ['title' => 'اجاره آپارتمان ۹۰ متری', 'meta' => 'رهن و اجاره • پونک • ۲ اتاق', 'agency' => 'املاک پارسیان', 'price' => 600000000, 'posted' => '۵ ساعت پیش', 'image' => $this->img('detail-t2.jpg')],
                ['title' => 'مغازه ۳۵ متری بر خیابان اصلی', 'meta' => 'فروش تجاری • ونک', 'agency' => 'املاک پارسیان', 'price' => 9500000000, 'posted' => 'دیروز', 'image' => $this->img('agency-thumb-1.jpg')],
            ],
            'weekMarks' => '1,3,5',
            // مزایده‌های پیش‌رو
            'upcoming' => [
                ['title' => 'مزایده‌ی آپارتمان ۱۲۰ متری', 'agency' => 'املاک پارسیان', 'icon' => 'home', 'tone' => 'sage', 'live' => true, 'at' => now()->addMinutes(42)->toIso8601String()],
                ['title' => 'مزایده‌ی مغازه ۳۵ متری', 'agency' => 'املاک پارسیان', 'icon' => 'briefcase', 'tone' => 'peach', 'live' => false, 'at' => now()->addHours(5)->addMinutes(12)->toIso8601String()],
                ['title' => 'مزایده‌ی خانه کلنگی ۲۴۰ متر', 'agency' => 'املاک آریا', 'icon' => 'trees', 'tone' => 'sky', 'live' => false, 'at' => now()->addHours(26)->toIso8601String()],
            ],
            'activity' => [
                ['title' => 'آژانس جدید ثبت‌نام کرد', 'sub' => 'املاک آرش', 'icon' => 'store', 'tone' => 'sky', 'ago' => '۱۰ دقیقه'],
                ['title' => 'مزایده‌ی جدید ساخته شد', 'sub' => 'خانه کلنگی ۲۴۰ متر • املاک آریا', 'icon' => 'gavel', 'tone' => 'sage', 'ago' => '۳۲ دقیقه'],
                ['title' => 'برنده برای قرارداد مراجعه نکرد', 'sub' => 'مزایده‌ی شماره‌ی ۱۶', 'icon' => 'alert-triangle', 'tone' => 'rose', 'ago' => '۱ ساعت'],
                ['title' => 'درخواست برداشت', 'sub' => 'آژانس پارس • ۱۸۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'banknote', 'tone' => 'peach', 'ago' => '۲ ساعت'],
            ],
        ]);

    }
}
