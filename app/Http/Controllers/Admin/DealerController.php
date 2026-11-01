<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DealerController extends Controller
{
    public function index()
    {
        return view('admin.pages.dealers.index', ['dealers' => $this->dealerData()]);
    }

    public function show($id)
    {
        $all = $this->dealerData();
        $d = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.dealers.show', ['d' => $d]);

    }

    protected function dealerData()
    {
        $min = fn($m) => now()->addMinutes($m)->toIso8601String();
        // [id، نام نمایشگاه، مدیر، موبایل، شهر، وضعیت، ثبت(روز قبل)، آگهی فعال، فروش، کیف پول، اشتراک(روز مانده|null)، لوگو]
        $seed = [
            [101, 'نمایشگاه ستاره', 'امیر حسینی', '09131000101', 'اصفهان', 'pending', 1, 0, 0, 0, null, 'dealer-thumb-1.jpg'],
            [102, 'اتوگالری مهر', 'کامران یزدانی', '09132000202', 'شیراز', 'pending', 2, 0, 0, 0, null, 'dealer-thumb-2.jpg'],
            [103, 'دوچرخه‌ی نیما', 'نیما قاسمی', '09133000303', 'تهران', 'pending', 3, 0, 0, 0, null, 'dealer-thumb-3.jpg'],
            [104, 'موتور پلاس', 'سعید فرهادی', '09134000404', 'مشهد', 'pending', 4, 0, 0, 0, null, 'dealer-thumb-1.jpg'],
            [105, 'اتوگالری هالیوود', 'بهرام اسدی', '09121005005', 'تهران', 'verified', 120, 14, 31, 184000000, 34, 'dealer-thumb-1.jpg'],
            [106, 'نمایشگاه پارس', 'آرش مرادی', '09122006006', 'اصفهان', 'verified', 200, 9, 22, 96000000, 12, 'dealer-thumb-2.jpg'],
            [107, 'آریا خودروهای تجاری', 'پویا نصیری', '09123007007', 'تهران', 'verified', 90, 6, 8, 540000000, 61, 'dealer-thumb-3.jpg'],
            [108, 'نمایشگاه نگین', 'مهدی کیانی', '09124008008', 'مشهد', 'suspended', 150, 0, 12, 40000000, null, 'dealer-thumb-2.jpg'],
            [109, 'موتور سیتی', 'کیوان رحمانی', '09125009009', 'تبریز', 'rejected', 18, 0, 0, 0, null, 'dealer-thumb-3.jpg'],
        ];
        return array_map(function ($r) use ($min) {
            [$id, $name, $owner, $phone, $city, $st, $days, $ads, $sales, $wallet, $sub, $logo] = $r;
            return [
                'id' => $id, 'name' => $name, 'owner' => $owner, 'phone' => $phone, 'city' => $city, 'status' => $st,
                'created_at' => $min(-$days * 1440), 'ads' => $ads, 'sales' => $sales, 'wallet' => $wallet, 'sub_days' => $sub, 'logo' => $this->img($logo),
                'license' => $this->img('detail-t1.jpg'), 'id_card' => $this->img('detail-t2.jpg'), 'national_id' => '00' . (12000000 + $id * 137),
                'address' => 'خیابان نمایشگاه‌ها، پلاک ' . (10 + $id % 40), 'reason' => $st === 'rejected' ? 'تصویر مجوز ناخوانا بود' : ($st === 'suspended' ? 'شکایت مکرر خریداران' : null),
            ];
        }, $seed);
    }
}
