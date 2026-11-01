<?php

namespace App\Http\Controllers;

class AuctionController extends Controller
{
    public function index()
    {
        $list = collect($this->auctionStore())->filter(fn($a) => $a['status'] !== 'ended')->map(function ($a) {
            $a['dealer_id'] = $a['dealer']['id'];
            $a['dealer'] = $a['dealer_name'];
            return $a;
        })->sortBy(fn($a) => $a['status'] === 'live' ? 0 : 1)->values()->all();
        return view('pages.auctions.index', ['auctions' => $list]);
    }

    public function show($id)
    {
        $all = $this->auctionStore();
        $a = $all[(int)$id] ?? $all[11];
        if ($st = request('state')) {                     // نمایشی: تغییر وضعیت
            $a['status'] = $st;
            if ($st === 'live') {
                $a['starts_at'] = now()->subHours(1);
                $a['ends_at'] = now()->addHours(1)->addMinutes(11)->addSeconds(36);
                $a['top_bid'] = 970000000;
                $a['bids_count'] = 9;
                $a['bids'] = [['amount' => 975000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 970000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 965000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]];
                $a['top_bid'] = 975000000;
                $a['my_last_bid'] = 970000000;
            }
            if ($st === 'ended') {
                $a['ends_at'] = now()->subHours(3);
                $a['top_bid'] = $a['top_bid'] ?: 990000000;
                $a['bids_count'] = max(9, $a['bids_count']);
                $a['deposit_deadline'] = now()->addHours(23);
                $a['bids'] = $a['bids'] ?: [['amount' => 990000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false]];
            }
            if ($st === 'scheduled') {
                $a['starts_at'] = now()->addHours(2)->addMinutes(11)->addSeconds(36);
                $a['top_bid'] = null;
                $a['bids'] = [];
            }
        }
        if (request('paid') !== null) $a['paid'] = request('paid') === '1';
        if (request('won') === '1') {
            $a['won'] = true;
            $a['paid'] = true;
        }
        return view('pages.auctions.show', ['auction' => (object)$a]);
    }

    public function entry($id)
    {
        if ($r = $this->requireVerified()) return $r;
        $all = $this->auctionStore();
        $a = $all[(int)$id] ?? $all[11];
        if ($st = request('state')) $a['status'] = $st;
        return view('pages.auctions.entry', ['auction' => (object)$a]);
    }

    public function create()
    {
        return view('pages.panel.auction-form', [
            'edit' => request('edit'),
            'selected' => request('listing'),
            'create_fee' => 600000,
            'listings' => [
                ['id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $this->img('dealer-stock-1.jpg'), 'price' => 985000000, 'year' => 1402, 'km' => 24000],
                ['id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $this->img('dealer-stock-2.jpg'), 'price' => 9800000000, 'year' => 1399, 'km' => 320000],
                ['id' => 24819, 'title' => 'یاماها NMAX 155', 'image' => $this->img('dealer-stock-3.jpg'), 'price' => 385000000, 'year' => 1403, 'km' => 4500],
            ],
        ]);
    }

    protected function auctionStore()
    {
        $hollywood = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'verified' => true, 'cover' => $this->img('detail-banner.jpg'), 'url' => route('dealers.show', 8)];
        $gallery = [['src' => $this->img('detail-main.jpg'), 'thumb' => $this->img('detail-t1.jpg')], ['src' => $this->img('detail-t1.jpg'), 'thumb' => $this->img('detail-t2.jpg')], ['src' => $this->img('detail-t2.jpg'), 'thumb' => $this->img('detail-t3.jpg')]];
        $bids = [
            ['amount' => 975000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false],
            ['amount' => 970000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true],
            ['amount' => 965000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false],
        ];
        return [
            11 => ['id' => 11, 'status' => 'scheduled', 'category' => 'car', 'title' => 'پژو ۲۰۷ اتوماتیک', 'short_title' => 'پژو ۲۰۷', 'subtitle' => 'مدل ۱۴۰۲ • سقف شیشه‌ای • بدون رنگ', 'year' => 1402, 'km' => 24000, 'extra' => 'سفید',
                'image' => $this->img('dealer-stock-1.jpg'), 'images' => $gallery, 'listing_id' => 24817, 'listing_url' => route('listings.show', 24817), 'dealer' => $hollywood, 'dealer_name' => 'اتوگالری هالیوود', 'verified' => true,
                'start_price' => 950000000, 'original_price' => 985000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => null, 'bids_count' => 0, 'participants' => 14,
                'starts_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36), 'ends_at' => now()->addHours(4)->addMinutes(11), 'paid' => false, 'bids' => [], 'my_last_bid' => null],
            12 => ['id' => 12, 'status' => 'scheduled', 'category' => 'heavy', 'title' => 'ولوو FH500 کشنده', 'short_title' => 'ولوو FH500', 'subtitle' => 'مدل ۱۳۹۹ • دیزل • تک‌مالک', 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل',
                'image' => $this->img('dealer-stock-2.jpg'), 'images' => $gallery, 'listing_id' => 24818, 'listing_url' => route('listings.show', 24818), 'dealer' => array_merge($hollywood, ['id' => 22, 'name' => 'آریا خودروهای تجاری', 'url' => route('dealers.show', 22)]), 'dealer_name' => 'آریا خودروهای تجاری', 'verified' => true,
                'start_price' => 9500000000, 'original_price' => 9800000000, 'entry_fee' => 50000, 'min_step' => 50000000, 'top_bid' => null, 'bids_count' => 0, 'participants' => 3,
                'starts_at' => now()->addDays(3), 'ends_at' => now()->addDays(3)->addHours(2), 'paid' => false, 'bids' => [], 'my_last_bid' => null],
            13 => ['id' => 13, 'status' => 'live', 'category' => 'bike', 'title' => 'یاماها NMAX 155', 'short_title' => 'یاماها NMAX', 'subtitle' => 'مدل ۱۴۰۳ • آبی • بدون رنگ', 'year' => 1403, 'km' => 4500, 'extra' => 'آبی',
                'image' => $this->img('dealer-stock-3.jpg'), 'images' => $gallery, 'listing_id' => 24819, 'listing_url' => route('listings.show', 24819), 'dealer' => array_merge($hollywood, ['id' => 21, 'name' => 'آریا موتور', 'url' => route('dealers.show', 21)]), 'dealer_name' => 'آریا موتور', 'verified' => true,
                'start_price' => 350000000, 'original_price' => 385000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => 370000000, 'bids_count' => 9, 'participants' => 7,
                'starts_at' => now()->subHours(1), 'ends_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36), 'paid' => false,
                'bids' => [['amount' => 370000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 365000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 360000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]], 'my_last_bid' => 365000000],
            15 => ['id' => 15, 'status' => 'live', 'category' => 'bicycle', 'title' => 'دوچرخه کوهستان جاینت ATX', 'short_title' => 'جاینت ATX', 'subtitle' => 'مدل ۱۴۰۳ • مشکی • فریم آلومینیوم', 'year' => 1403, 'km' => 800, 'extra' => 'مشکی',
                'image' => $this->img('dealer-stock-3.jpg'), 'images' => $gallery, 'listing_id' => 24830, 'listing_url' => route('listings.show', 24830), 'dealer' => array_merge($hollywood, ['id' => 21, 'name' => 'آریا موتور', 'url' => route('dealers.show', 21)]), 'dealer_name' => 'آریا موتور', 'verified' => true,
                'start_price' => 24000000, 'original_price' => 28000000, 'entry_fee' => 50000, 'min_step' => 500000, 'top_bid' => 26500000, 'bids_count' => 9, 'participants' => 7,
                'starts_at' => now()->subHours(1), 'ends_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36), 'paid' => false,
                'bids' => [['amount' => 26500000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 26000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 25500000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]], 'my_last_bid' => 26000000],
            14 => ['id' => 14, 'status' => 'ended', 'category' => 'car', 'title' => 'پژو ۲۰۶ تیپ ۲', 'short_title' => 'پژو ۲۰۶', 'subtitle' => 'مدل ۱۴۰۰ • سفید', 'year' => 1400, 'km' => 61000, 'extra' => 'سفید',
                'image' => $this->img('dealer-stock-1.jpg'), 'images' => $gallery, 'listing_id' => 24820, 'listing_url' => route('listings.show', 24820), 'dealer' => $hollywood, 'dealer_name' => 'اتوگالری هالیوود', 'verified' => true,
                'start_price' => 500000000, 'original_price' => 540000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => 562000000, 'bids_count' => 11, 'participants' => 11,
                'starts_at' => now()->subDays(1)->subHours(2), 'ends_at' => now()->subDays(1), 'paid' => false, 'won' => false, 'deposit_deadline' => now()->addHours(23), 'bids' => $bids, 'my_last_bid' => null],
        ];
    }
}
