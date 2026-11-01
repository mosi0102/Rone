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
        $u = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.users.show', ['u' => $u]);

    }

    protected function userData()
    {
        $min = fn($m) => now()->addMinutes($m)->toIso8601String();
        // [id، نام، موبایل، شهر، وضعیت، عضویت(روز قبل)، کیف پول، بیعانه‌ی در امانت، مزایده‌ی شرکت‌کرده، محرومیت از مزایده(روز|null)، درخواست خرید فعال]
        $seed = [
            [201, 'علی رضایی', '09121110011', 'تهران', 'active', 300, 2400000, 19700000, 6, null, 1],
            [202, 'مریم احمدی', '09122220022', 'مشهد', 'active', 220, 850000, 11550000, 3, null, 1],
            [203, 'حسین کریمی', '09123330033', 'تبریز', 'active', 180, 0, 147000000, 9, null, 1],
            [204, 'سارا موسوی', '09124440044', 'اصفهان', 'active', 90, 15000000, 9600000, 2, null, 0],
            [205, 'رضا نوری', '09125550055', 'تهران', 'active', 60, 300000, 87000000, 5, null, 1],
            [206, 'نیلوفر صادقی', '09126660066', 'کرج', 'active', 45, 0, 19800000, 1, 27, 0],
            [207, 'پدرام شریفی', '09127770077', 'شیراز', 'banned', 400, 0, 0, 4, null, 0],
        ];
        return array_map(function ($r) use ($min) {
            [$id, $name, $phone, $city, $st, $days, $wallet, $held, $auc, $ban, $req] = $r;
            return [
                'id' => $id, 'name' => $name, 'phone' => $phone, 'city' => $city, 'status' => $st, 'created_at' => $min(-$days * 1440),
                'wallet' => $wallet, 'held' => $held, 'auctions' => $auc, 'auction_ban_days' => $ban, 'requests' => $req,
                'reason' => $st === 'banned' ? 'انتشار اطلاعات تماس در گفتگو' : null,
            ];
        }, $seed);
    }
}
