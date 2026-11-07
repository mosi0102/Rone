<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class AuctionController extends Controller
{
    use ListingData;

    public function index()
    {
        $list = collect($this->auctionStore())->filter(fn ($a) => $a['status'] !== 'ended')->map(function ($a) {
            $a['agency_id'] = $a['agency']['id'];
            $a['agency'] = $a['agency_name'];

            return $a;
        })->sortBy(fn ($a) => $a['status'] === 'live' ? 0 : 1)->values()->all();

        return view('pages.auctions.index', ['auctions' => $list]);
    }

    public function show($id)
    {
        $all = $this->auctionStore();
        $a = $all[(int) $id] ?? $all[11];
        if ($st = request('state')) {                     // نمایشی: تغییر وضعیت
            $a['status'] = $st;
            if ($st === 'live') {
                $a['starts_at'] = now()->subHours(1);
                $a['ends_at'] = now()->addHours(1)->addMinutes(11)->addSeconds(36);
                $a['top_bid'] = 18900000000;
                $a['bids_count'] = 9;
                $a['bids'] = [['amount' => 18900000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 18800000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 18700000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]];
                $a['top_bid'] = 18900000000;
                $a['my_last_bid'] = 18800000000;
            }
            if ($st === 'ended') {
                $a['ends_at'] = now()->subHours(3);
                $a['top_bid'] = $a['top_bid'] ?: 19200000000;
                $a['bids_count'] = max(9, $a['bids_count']);
                $a['contract_deadline'] = now()->addHours(47);
                $a['bids'] = $a['bids'] ?: [['amount' => 19200000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false]];
            }
            if ($st === 'scheduled') {
                $a['starts_at'] = now()->addHours(2)->addMinutes(11)->addSeconds(36);
                $a['top_bid'] = null;
                $a['bids'] = [];
            }
        }
        if (request('paid') !== null) {
            $a['paid'] = request('paid') === '1';
        }
        if (request('won') === '1') {
            $a['won'] = true;
            $a['paid'] = true;
        }

        return view('pages.auctions.show', ['auction' => (object) $a]);
    }

    public function entry($id)
    {
        if ($r = $this->requireVerified()) {
            return $r;
        }
        $all = $this->auctionStore();
        $a = $all[(int) $id] ?? $all[11];
        if ($st = request('state')) {
            $a['status'] = $st;
        }

        return view('pages.auctions.entry', ['auction' => (object) $a]);
    }

    public function create()
    {
        return view('pages.panel.auction-form', [
            'edit' => request('edit'),
            'selected' => request('listing'),
            'create_fee' => 2000000,
            // فقط آگهی‌های فروش (مسکونی، تجاری، پروژه) قابل مزایده‌اند
            'listings' => collect($this->listingStore())->where('agency_id', 8)->whereIn('cat', ['sale_res', 'sale_com', 'project'])->take(4)->map(fn ($r) => ['id' => $r['id'], 'title' => $r['title'], 'image' => $r['image'], 'price' => $r['price'], 'area' => $r['area'], 'rooms' => $r['rooms'], 'year' => $r['year']])->values()->all(),
        ]);
    }

    /** مزایده‌های نمایشی (فقط آگهی‌های فروش). برنده بیعانه نمی‌دهد؛ تا contract_deadline برای قرارداد به آژانس مراجعه می‌کند. */
    protected function auctionStore()
    {
        $rows = collect($this->listingStore())->keyBy('id');
        $gallery = [['src' => $this->img('detail-main.jpg'), 'thumb' => $this->img('detail-t1.jpg')], ['src' => $this->img('detail-t1.jpg'), 'thumb' => $this->img('detail-t2.jpg')], ['src' => $this->img('detail-t2.jpg'), 'thumb' => $this->img('detail-t3.jpg')]];
        $bid = fn ($top, $step) => [
            ['amount' => $top, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false],
            ['amount' => $top - $step, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true],
            ['amount' => $top - 2 * $step, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false],
        ];
        // [id، کد آگهی، وضعیت، قیمت پایه، گام، بالاترین پیشنهاد، تعداد پیشنهاد، شرکت‌کننده، شروع، پایان]
        $seed = [
            [11, 31001, 'scheduled', 17500000000, 100000000, null, 0, 14, now()->addHours(2)->addMinutes(11)->addSeconds(36), now()->addHours(4)->addMinutes(11)],
            [12, 31008, 'scheduled', 9000000000, 50000000, null, 0, 3, now()->addDays(3), now()->addDays(3)->addHours(2)],
            [13, 31003, 'live', 20000000000, 100000000, 20600000000, 9, 7, now()->subHours(1), now()->addHours(2)->addMinutes(11)->addSeconds(36)],
            [15, 31012, 'live', 140000000, 1000000, 146000000, 9, 7, now()->subHours(1), now()->addHours(2)->addMinutes(11)->addSeconds(36)],
            [14, 31004, 'ended', 6500000000, 50000000, 7150000000, 11, 11, now()->subDays(1)->subHours(2), now()->subDays(1)],
        ];
        $out = [];
        foreach ($seed as [$id, $lid, $st, $start, $step, $top, $count, $people, $from, $to]) {
            $r = $rows[$lid];
            $out[$id] = [
                'id' => $id, 'status' => $st, 'category' => $r['cat'], 'title' => $r['title'], 'short_title' => $r['type'].' '.$this->fa($r['area']).' متری',
                'subtitle' => $r['type'].' • '.$this->fa($r['area']).' متر • '.$r['district'], 'area' => $r['area'], 'rooms' => $r['rooms'], 'year' => $r['cat'] === 'project' ? null : $r['year'], 'extra' => $r['district'],
                'image' => $r['image'], 'images' => $gallery, 'listing_id' => $lid, 'listing_url' => route('listings.show', $lid),
                'agency' => ['id' => $r['agency_id'], 'name' => $r['agency'], 'area' => $r['location'], 'active_count' => 12, 'verified' => true, 'cover' => $this->img('detail-banner.jpg'), 'url' => route('agencies.show', $r['agency_id']), 'phone' => '۰۲۱۲۲۳۴۵۶۷۸'],
                'agency_name' => $r['agency'], 'verified' => true, 'price_label' => $this->priceLabel($r['cat']),
                'start_price' => $start, 'original_price' => $r['price'], 'entry_fee' => 500000, 'min_step' => $step, 'top_bid' => $top, 'bids_count' => $count, 'participants' => $people,
                'starts_at' => $from, 'ends_at' => $to, 'paid' => false, 'won' => false,
                'contract_deadline' => $st === 'ended' ? now()->addHours(47) : null,
                'bids' => $top ? $bid($top, $step) : [], 'my_last_bid' => $top && $st === 'live' ? $top - $step : null,
            ];
        }

        return $out;
    }
}
