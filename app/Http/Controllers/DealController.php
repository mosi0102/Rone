<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\DealData;

class DealController extends Controller
{
    use DealData;

    public function deposit($id)
    {
        if ($r = $this->requireVerified()) return $r;
        return view('pages.deals.deposit', ['car' => (object)[
            'id' => $id, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $this->img('dealer-stock-1.jpg'), 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'price' => 985000000,
            'amount' => 20000000, 'fee' => 200000, 'url' => route('listings.show', $id),
            'dealer' => ['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true],
        ]]);
    }

    public function show($id)
    {
        $all = $this->dealStore();
        $d = $all[(int)$id] ?? $all[101];
        if ($st = request('state')) {
            $d['status'] = $st;
            if ($st !== 'held') $d['decided_at'] = now()->subHours(2);
            if ($st === 'review') {
                $d['reason'] = 'خودرو با توضیحات آگهی تفاوت داشت';
                $d['reason_note'] = 'رنگ و وضعیت بدنه با آگهی فرق داشت.';
            }
            if ($st === 'refunded') $d['reason'] = 'خودرو با توضیحات آگهی تفاوت داشت';
        }
        return view('pages.deals.show', ['deal' => (object)$d]);
    }

    public function decide($id)
    {
        $all = $this->dealStore();
        $d = $all[(int)$id] ?? $all[101];
        return view('pages.deals.decide', ['deal' => (object)$d]);
    }

    public function index()
    {
        $all = collect($this->dealStore())->only([101, 102, 103, 104])->values()->all();
        return view('pages.deals.index', ['deals' => $all]);
    }
}
