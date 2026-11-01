<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RequestController extends Controller
{
    public function index()
    {
        return view('admin.pages.requests.index', ['requests' => $this->requestData()]);
    }

    public function show($id)
    {
        $all = $this->requestData();
        $r = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.requests.show', ['r' => $r]);

    }

    protected function requestData()
    {
        $min = fn($m) => now()->addMinutes($m)->toIso8601String();
        $users = [
            ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
            ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
            ['name' => 'رضا نوری', 'phone' => '09125550055'],
        ];
        $pool = [ // آگهی‌های نمایشی برای نمایش تطابق: [عنوان، تصویر، قیمت، شهر، نمایشگاه، کد]
            ['پژو ۲۰۷ اتوماتیک', 'car-207.jpg', 985000000, 'تهران', 'اتوگالری هالیوود', 24817],
            ['پژو ۲۰۷ پانوراما', 'car-207-a.jpg', 940000000, 'اصفهان', 'نمایشگاه پارس', 24825],
            ['هیوندای النترا', 'detail-main.jpg', 1850000000, 'مشهد', 'نمایشگاه نگین', 24821],
            ['سمند LX EF7', 'dealer-stock-1.jpg', 520000000, 'اصفهان', 'نمایشگاه پارس', 24820],
            ['تیبا ۲ صفر', 'detail-t1.jpg', 390000000, 'کرج', 'اتوگالری هالیوود', 24824],
            ['یاماها NMAX 155', 'yamaha.jpg', 385000000, 'مشهد', 'آریا موتور', 24819],
            ['موتور هوندا CBR 250', 'dealer-stock-3.jpg', 390000000, 'تبریز', 'موتور سیتی', 24823],
            ['دوچرخه کوهستان جاینت ATX', 'dealer-stock-3.jpg', 38000000, 'تهران', 'دوچرخه‌ی آرمان', 24829],
        ];
        // [id، کاربر، دسته، خلاصه، برند/مدل، سال از، سقف قیمت، سقف کارکرد، شهر، وضعیت، ثبت(روز قبل)، [[ایندکس آگهی، درصد]…]]
        $seed = [
            [501, 0, 'car', 'پژو ۲۰۷ اتوماتیک، سفید', 'پژو ۲۰۷', '۱۴۰۱', 1000000000, '۵۰٬۰۰۰', 'تهران', 'active', 2, [[0, 96], [1, 82], [4, 41]]],
            [502, 1, 'car', 'خودرو شاسی‌بلند اقتصادی', 'هر مدل', '۱۴۰۲', 1900000000, '۸۰٬۰۰۰', 'مشهد', 'active', 9, [[2, 68], [3, 44]]],
            [503, 2, 'bike', 'موتور ۱۵۰ تا ۲۵۰ سی‌سی', 'هر مدل', '۱۴۰۰', 400000000, '۲۵٬۰۰۰', 'تبریز', 'active', 5, [[6, 91], [5, 77]]],
            [504, 3, 'heavy', 'کشنده‌ی ولوو یا بنز', 'ولوو / بنز', '۱۳۹۸', 10000000000, '۳۵۰٬۰۰۰', 'تهران', 'active', 14, []],
            [505, 4, 'bicycle', 'دوچرخه‌ی کوهستان ۲۷٫۵ اینچ', 'هر مدل', '۱۴۰۲', 45000000, '—', 'تهران', 'active', 1, [[7, 88]]],
            [506, 1, 'car', 'سمند یا پژو ۴۰۵ دنده‌ای', 'سمند / ۴۰۵', '۱۳۹۹', 600000000, '۹۰٬۰۰۰', 'اصفهان', 'cancelled', 20, [[3, 73]]],
            [507, 2, 'car', 'خودروی صفر کیلومتر زیر ۴۰۰ میلیون', 'تیبا / ساینا', '۱۴۰۳', 400000000, '۵٬۰۰۰', 'کرج', 'suspended', 6, [[4, 94]]],
        ];
        return array_map(function ($r) use ($users, $pool, $min) {
            [$id, $u, $cat, $summary, $model, $year, $budget, $km, $city, $st, $days, $mt] = $r;
            $matches = array_map(function ($m) use ($pool) {
                $l = $pool[$m[0]];
                return ['title' => $l[0], 'image' => $this->img($l[1]), 'price' => $l[2], 'city' => $l[3], 'dealer' => $l[4], 'code' => $l[5], 'percent' => $m[1]];
            }, $mt);
            usort($matches, fn($a, $b) => $b['percent'] <=> $a['percent']);
            return [
                'id' => $id, 'code' => 'RQ-' . (1000 + $id), 'user' => $users[$u], 'cat' => $cat, 'summary' => $summary, 'model' => $model, 'year_from' => $year,
                'budget' => $budget, 'km_max' => $km, 'city' => $city, 'status' => $st, 'created_at' => $min(-$days * 1440),
                'matches' => $matches, 'best' => $matches ? $matches[0]['percent'] : 0,
                'cancelled_at' => $st === 'cancelled' ? $min(-2 * 1440) : null,
            ];
        }, $seed);
    }
}
