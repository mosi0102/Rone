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
        $a = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.auctions.show', ['a' => $a]);

    }

    protected function auctionData()
    {
        $users = [
            ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
            ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
            ['name' => 'رضا نوری', 'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
        ];
        $min = fn($m) => now()->addMinutes($m)->toIso8601String();

        // [id، عنوان، دسته، نمایشگاه، تصویر، قیمت پایه، گام، ورودی، وضعیت، شروع(دقیقه)، پایان(دقیقه)، تعداد پیشنهاد، شرکت‌کننده، برنده، مهلت پرداخت(دقیقه)، وضعیت پرداخت]
        $seed = [
            [11, 'پژو ۲۰۷ اتوماتیک', 'car', 'اتوگالری هالیوود', 'car-207.jpg', 850000000, 5000000, 200000, 'live', -90, 42, 14, 6, null, null, null],
            [12, 'ولوو FH500 کشنده', 'heavy', 'آریا خودروهای تجاری', 'truck-fh.jpg', 9200000000, 50000000, 500000, 'live', -30, 312, 8, 4, null, null, null],
            [13, 'یاماها NMAX 155', 'bike', 'آریا موتور', 'yamaha.jpg', 350000000, 2000000, 100000, 'scheduled', 1560, 1680, 0, 0, null, null, null],
            [14, 'سمند LX EF7', 'car', 'نمایشگاه پارس', 'dealer-stock-1.jpg', 480000000, 3000000, 150000, 'scheduled', 4320, 4440, 0, 0, null, null, null],
            [15, 'کامیون بنز ۲۶۳۱', 'heavy', 'سنگین‌خودرو البرز', 'truck-fh-a.jpg', 5800000000, 50000000, 500000, 'awaiting_pay', -240, -120, 22, 9, 2, 300, 'waiting'],
            [16, 'هیوندای النترا', 'car', 'نمایشگاه نگین', 'detail-main.jpg', 1700000000, 10000000, 300000, 'awaiting_pay', -2000, -1800, 17, 7, 0, -60, 'overdue'],
            [17, 'موتور هوندا CBR 250', 'bike', 'موتور سیتی', 'dealer-stock-3.jpg', 360000000, 2000000, 100000, 'defaulted', -5000, -4800, 11, 5, 4, -2900, 'banned'],
            [18, 'تیبا ۲ صفر', 'car', 'اتوگالری هالیوود', 'detail-t1.jpg', 370000000, 2000000, 100000, 'ended', -6200, -6000, 19, 8, 1, -5000, 'paid'],
            [20, 'دوچرخه کوهستان جاینت ATX', 'bicycle', 'آریا موتور', 'dealer-stock-3.jpg', 24000000, 500000, 50000, 'live', -20, 200, 5, 3, null, null, null],
            [19, 'رنو تندر ۹۰', 'car', 'نمایشگاه پارس', 'detail-t3.jpg', 560000000, 3000000, 150000, 'cancelled', -300, -180, 0, 0, null, null, null],
        ];

        return array_map(function ($r) use ($users, $min) {
            [$id, $title, $cat, $dealer, $image, $base, $step, $entry, $status, $startM, $endM, $n, $parts, $winner, $payM, $pay] = $r;
            $bids = [];
            for ($i = 1; $i <= $n; $i++) {
                $bids[] = ['n' => $i, 'user' => $users[($i * 7 + $id) % 6], 'amount' => $base + $step * $i, 'at' => $min($endM - ($n - $i) * 6 - 1)];
            }
            $top = $n ? end($bids) : null;
            return [
                'id' => $id, 'title' => $title, 'cat' => $cat, 'dealer' => $dealer, 'image' => $this->img($image), 'code' => 24800 + $id,
                'base' => $base, 'step' => $step, 'entry' => $entry, 'status' => $status, 'bids_count' => $n, 'participants' => $parts,
                'top' => $top ? $top['amount'] : 0, 'leader' => $top ? $top['user']['name'] : null,
                'starts_at' => $min($startM), 'ends_at' => $min($endM), 'bids' => array_reverse($bids), 'users' => $users,
                'winner' => $winner !== null ? $users[$winner] : null, 'pay' => $pay, 'pay_deadline' => $payM !== null ? $min($payM) : null,
                'banned_until' => $pay === 'banned' ? $min(27 * 1440) : null,
                'cancel_reason' => $status === 'cancelled' ? 'درخواست نمایشگاه‌دار' : null,
                'deposit_percent' => 2, 'service_fee' => 200000,
            ];
        }, $seed);
    }
}
