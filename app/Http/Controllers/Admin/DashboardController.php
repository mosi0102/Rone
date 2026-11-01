<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $queues = [
            ['label' => 'احراز هویت', 'count' => 4, 'icon' => 'user-check', 'tone' => 'sky', 'route' => 'admin.dealers.index'],
            ['label' => 'آگهی در انتظار', 'count' => 12, 'icon' => 'list-checks', 'tone' => 'peach', 'route' => 'admin.listings.index'],
            ['label' => 'اختلاف‌ها', 'count' => 3, 'icon' => 'alert-triangle', 'tone' => 'rose', 'route' => 'admin.disputes.index'],
            ['label' => 'درخواست برداشت', 'count' => 5, 'icon' => 'banknote', 'tone' => 'sage', 'route' => 'admin.settlements.index'],
            ['label' => 'گفتگوی گزارش‌شده', 'count' => 2, 'icon' => 'flag', 'tone' => 'sand', 'route' => 'admin.chats.index'],
        ];

        return view('admin.pages.dashboard', [
            'queues' => $queues,
            'pendingTotal' => collect($queues)->sum('count'),
            'today' => ['percent' => 68, 'done' => 17, 'total' => 25],
            'escrow' => 48200000000,
            'escrowCount' => 14,
            'liveCount' => 3,
            'kpi' => [
                'users' => 12840, 'users_delta' => 8,
                'dealers' => 312, 'dealers_delta' => 3,
                'listings' => 1668, 'listings_delta' => 5,
                'revenue' => 1260, 'revenue_delta' => -2,   // میلیون تومان
            ],
            // درآمد روزانه (میلیون تومان): a = فروش اشتراک، b = مزایده و فروش فوری
            'revenue' => [
                ['day' => 'ش', 'a' => 82, 'b' => 61], ['day' => 'ی', 'a' => 74, 'b' => 48], ['day' => 'د', 'a' => 91, 'b' => 70],
                ['day' => 'س', 'a' => 66, 'b' => 52], ['day' => 'چ', 'a' => 40, 'b' => 31], ['day' => 'پ', 'a' => 88, 'b' => 94], ['day' => 'ج', 'a' => 55, 'b' => 42],
            ],
            'revTotal' => 496,
            'revAuction' => 398,
            'pending' => [
                ['title' => 'پژو ۲۰۷ اتوماتیک', 'meta' => 'مدل ۱۴۰۲ • ۲۴٬۰۰۰ کیلومتر', 'dealer' => 'اتوگالری هالیوود', 'price' => 985000000, 'posted' => '۲ ساعت پیش', 'image' => $this->img('car-207.jpg')],
                ['title' => 'ولوو FH500 کشنده', 'meta' => 'مدل ۱۳۹۹ • ۳۲۰٬۰۰۰ کیلومتر', 'dealer' => 'آریا خودروهای تجاری', 'price' => 9800000000, 'posted' => '۵ ساعت پیش', 'image' => $this->img('truck-fh.jpg')],
                ['title' => 'یاماها NMAX 155', 'meta' => 'مدل ۱۴۰۳ • ۴٬۵۰۰ کیلومتر', 'dealer' => 'آریا موتور', 'price' => 385000000, 'posted' => 'دیروز', 'image' => $this->img('yamaha.jpg')],
            ],
            'weekMarks' => '1,3,5',
            'upcoming' => [
                ['title' => 'مزایده‌ی پژو ۲۰۷', 'dealer' => 'اتوگالری هالیوود', 'icon' => 'car', 'tone' => 'sage', 'live' => true, 'at' => now()->addMinutes(42)->toIso8601String()],
                ['title' => 'مزایده‌ی ولوو FH500', 'dealer' => 'آریا خودروهای تجاری', 'icon' => 'truck', 'tone' => 'peach', 'live' => false, 'at' => now()->addHours(5)->addMinutes(12)->toIso8601String()],
                ['title' => 'مزایده‌ی یاماها NMAX', 'dealer' => 'آریا موتور', 'icon' => 'bike', 'tone' => 'sky', 'live' => false, 'at' => now()->addHours(26)->toIso8601String()],
            ],
            'activity' => [
                ['title' => 'نمایشگاه جدید ثبت‌نام کرد', 'sub' => 'اتوگالری آرش', 'icon' => 'store', 'tone' => 'sky', 'ago' => '۱۰ دقیقه'],
                ['title' => 'بیعانه پرداخت شد', 'sub' => 'سمند LX • ۲۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'shield-check', 'tone' => 'sage', 'ago' => '۳۲ دقیقه'],
                ['title' => 'اختلاف جدید ثبت شد', 'sub' => 'معامله‌ی شماره‌ی ۱۰۴', 'icon' => 'alert-triangle', 'tone' => 'rose', 'ago' => '۱ ساعت'],
                ['title' => 'درخواست برداشت', 'sub' => 'نمایشگاه پارس • ۱۸۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'banknote', 'tone' => 'peach', 'ago' => '۲ ساعت'],
            ],
        ]);

    }
}
