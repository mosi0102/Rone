<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.pages.users.index', ['users' => $this->userData()]);
    }

    public function show($id)
    {
        $all = $this->userData();
        $u = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];

        return view('admin.pages.users.show', ['u' => $u]);

    }

    protected function userData()
    {
        $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
        // [id، نام، موبایل، شهر، وضعیت، عضویت(روز قبل)، کیف پول، جمع ورودی مزایده‌ها، مزایده‌ی شرکت‌کرده، درخواست ملک فعال]
        $seed = [
            [201, 'علی رضایی', '09121110011', 'تهران', 'active', 300, 2400000, 3000000, 6, 1],
            [202, 'مریم احمدی', '09122220022', 'مشهد', 'active', 220, 850000, 1500000, 3, 1],
            [203, 'حسین کریمی', '09123330033', 'تبریز', 'active', 180, 0, 4500000, 9, 1],
            [204, 'سارا موسوی', '09124440044', 'اصفهان', 'active', 90, 15000000, 1000000, 2, 0],
            [205, 'رضا نوری', '09125550055', 'تهران', 'active', 60, 300000, 2500000, 5, 1],
            [206, 'نیلوفر صادقی', '09126660066', 'کرج', 'active', 45, 0, 500000, 1, 0],
            [207, 'پدرام شریفی', '09127770077', 'شیراز', 'banned', 400, 0, 2000000, 4, 0],
        ];

        return array_map(function ($r) use ($min) {
            [$id, $name, $phone, $city, $st, $days, $wallet, $entries, $auctions, $req] = $r;

            return [
                'id' => $id, 'name' => $name, 'phone' => $phone, 'city' => $city, 'status' => $st, 'created_at' => $min(-$days * 1440),
                'wallet' => $wallet, 'entries' => $entries, 'auctions' => $auctions, 'requests' => $req,
                'reason' => $st === 'banned' ? 'انتشار اطلاعات تماس در گفتگو' : null,
            ];
        }, $seed);
    }
}
