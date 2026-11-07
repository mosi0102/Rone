<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ListingData;
use App\Http\Controllers\Controller;

class RequestController extends Controller
{
    use ListingData;

    public function index()
    {
        return view('admin.pages.requests.index', ['requests' => $this->requestData()]);
    }

    public function show($id)
    {
        $all = $this->requestData();
        $r = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];

        return view('admin.pages.requests.show', ['r' => $r]);

    }

    protected function requestData()
    {
        $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
        $users = [
            ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
            ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
            ['name' => 'رضا نوری', 'phone' => '09125550055'],
        ];
        $pool = array_values($this->listingStore()); // آگهی‌های نمایشی برای نمایش تطابق
        // [id، کاربر، دسته، خلاصه، نوع ملک، محله، سقف بودجه، حداقل متراژ، شهر، وضعیت، ثبت(روز قبل)، [[ایندکس آگهی، درصد]…]]
        $seed = [
            [501, 0, 'sale_res', 'آپارتمان ۱۰۰ متری نوساز', 'آپارتمان', 'سعادت‌آباد', 20000000000, 100, 'تهران', 'active', 2, [[0, 96], [16, 82], [3, 51]]],
            [502, 1, 'rent_res', 'اجاره‌ی آپارتمان دوخوابه', 'آپارتمان', 'پونک', 700000000, 85, 'تهران', 'active', 9, [[4, 88], [20, 74]]],
            [503, 2, 'sale_com', 'مغازه بر خیابان اصلی', 'مغازه و غرفه', 'ونک', 10000000000, 30, 'تهران', 'active', 5, [[7, 91], [23, 77]]],
            [504, 3, 'project', 'پیش‌فروش آپارتمان در برج', 'پیش‌فروش', '', 150000000, 100, 'تهران', 'active', 14, []],
            [505, 4, 'short', 'ویلای شمال برای تعطیلات', 'ویلا و باغ', 'گلسار', 7000000, 150, 'رشت', 'active', 1, [[13, 92]]],
            [506, 1, 'rent_com', 'دفتر کار ۶۰ تا ۸۰ متر', 'دفتر کار، اتاق اداری و مطب', '', 400000000, 60, 'تبریز', 'cancelled', 20, [[9, 73]]],
            [507, 2, 'sale_res', 'خانه‌ی کلنگی برای ساخت', 'زمین و کلنگی', 'احمدآباد', 25000000000, 200, 'مشهد', 'suspended', 6, [[2, 94]]],
        ];

        return array_map(function ($r) use ($users, $pool, $min) {
            [$id, $u, $cat, $summary, $ptype, $district, $budget, $area, $city, $st, $days, $mt] = $r;
            $matches = array_map(function ($m) use ($pool) {
                $l = $pool[$m[0]];

                return ['title' => $l['title'], 'image' => $l['image'], 'price' => $l['price'], 'city' => $l['city'], 'agency' => $l['agency'], 'code' => $l['id'], 'percent' => $m[1]];
            }, $mt);
            usort($matches, fn ($a, $b) => $b['percent'] <=> $a['percent']);

            return [
                'id' => $id, 'code' => 'RQ-'.(1000 + $id), 'user' => $users[$u], 'cat' => $cat, 'summary' => $summary, 'ptype' => $ptype, 'district' => $district ?: 'مهم نیست',
                'budget' => $budget, 'area_min' => $area, 'city' => $city, 'status' => $st, 'created_at' => $min(-$days * 1440),
                'matches' => $matches, 'best' => $matches ? $matches[0]['percent'] : 0,
                'cancelled_at' => $st === 'cancelled' ? $min(-2 * 1440) : null,
            ];
        }, $seed);
    }
}
