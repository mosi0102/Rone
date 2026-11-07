<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuctionController extends Controller
{
    public function index()
    {
        return view('admin.pages.auctions.index', ['auctions' => $this->auctionData()]);
    }

    public function show($id)
    {
        $all = $this->auctionData();
        $a = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];

        return view('admin.pages.auctions.show', ['a' => $a]);

    }

    protected function auctionData()
    {
        $users = [
            ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
            ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
            ['name' => 'رضا نوری', 'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
        ];
        $min = fn ($m) => now()->addMinutes($m)->toIso8601String();

        // [id، عنوان، دسته، آژانس، تصویر، قیمت پایه، گام، ورودی، وضعیت، شروع(دقیقه)، پایان(دقیقه)، تعداد پیشنهاد، شرکت‌کننده، برنده، مهلت مراجعه برای قرارداد(دقیقه)، وضعیت قرارداد(waiting|overdue|banned|contracted)]
        $seed = [
            [11, 'آپارتمان ۱۲۰ متری نوساز', 'sale_res', 'املاک پارسیان', 'agency-stock-1.jpg', 17500000000, 100000000, 500000, 'live', -90, 42, 14, 6, null, null, null],
            [12, 'مغازه ۳۵ متری بر خیابان اصلی', 'sale_com', 'املاک پارسیان', 'agency-thumb-1.jpg', 9000000000, 50000000, 500000, 'live', -30, 312, 8, 4, null, null, null],
            [13, 'خانه کلنگی ۲۴۰ متر شمالی', 'sale_res', 'املاک آریا', 'detail-main.jpg', 20000000000, 100000000, 500000, 'scheduled', 1560, 1680, 0, 0, null, null, null],
            [14, 'پیش‌فروش برج مسکونی ۱۴ طبقه', 'project', 'املاک الهیه', 'hero.jpg', 140000000, 1000000, 300000, 'scheduled', 4320, 4440, 0, 0, null, null, null],
            [15, 'ویلای دوبلکس با استخر', 'sale_res', 'املاک الهیه', 'agency-stock-2.jpg', 40000000000, 200000000, 1000000, 'awaiting_contract', -240, -120, 22, 9, 2, 2580, 'waiting'],
            [16, 'آپارتمان ۸۵ متری فول امکانات', 'sale_res', 'املاک آریا', 'detail-t1.jpg', 6500000000, 50000000, 500000, 'awaiting_contract', -4000, -3800, 17, 7, 0, -60, 'overdue'],
            [17, 'دفتر اداری ۶۰ متری موقعیت اداری', 'sale_com', 'املاک ارم', 'agency-thumb-2.jpg', 7500000000, 50000000, 500000, 'defaulted', -8000, -7800, 11, 5, 4, -4900, 'banned'],
            [18, 'زمین ۵۰۰ متری مشارکتی', 'project', 'املاک آریا', 'banner-agencies-1.jpg', 90000000, 1000000, 300000, 'ended', -9200, -9000, 19, 8, 1, -6200, 'contracted'],
            [20, 'آپارتمان ۸۵ متری (موقعیت عالی)', 'sale_res', 'املاک آریا', 'detail-t2.jpg', 6800000000, 50000000, 500000, 'live', -20, 200, 5, 3, null, null, null],
            [19, 'مغازه ۳۵ متری (قابل معاوضه)', 'sale_com', 'املاک پارسیان', 'detail-t3.jpg', 9300000000, 50000000, 500000, 'cancelled', -300, -180, 0, 0, null, null, null],
        ];

        return array_map(function ($r) use ($users, $min) {
            [$id, $title, $cat, $agency, $image, $base, $step, $entry, $status, $startM, $endM, $n, $parts, $winner, $payM, $pay] = $r;
            $bids = [];
            for ($i = 1; $i <= $n; $i++) {
                $bids[] = ['n' => $i, 'user' => $users[($i * 7 + $id) % 6], 'amount' => $base + $step * $i, 'at' => $min($endM - ($n - $i) * 6 - 1)];
            }
            $top = $n ? end($bids) : null;

            return [
                'id' => $id, 'title' => $title, 'cat' => $cat, 'agency' => $agency, 'image' => $this->img($image), 'code' => 24800 + $id,
                'base' => $base, 'step' => $step, 'entry' => $entry, 'status' => $status, 'bids_count' => $n, 'participants' => $parts,
                'top' => $top ? $top['amount'] : 0, 'leader' => $top ? $top['user']['name'] : null,
                'starts_at' => $min($startM), 'ends_at' => $min($endM), 'bids' => array_reverse($bids), 'users' => $users,
                'winner' => $winner !== null ? $users[$winner] : null, 'pay' => $pay, 'pay_deadline' => $payM !== null ? $min($payM) : null,
                'banned_until' => $pay === 'banned' ? $min(27 * 1440) : null,
                'cancel_reason' => $status === 'cancelled' ? 'درخواست مشاور املاک' : null,
                'agency_phone' => '۰۲۱۲۲۳۴۵۶۷۸',
            ];
        }, $seed);
    }
}
