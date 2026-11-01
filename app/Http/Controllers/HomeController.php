<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $car = route('listings.show', 24817);

        // مزایده‌های در آستانه شروع (status: scheduled|live)
        $auctions = [
            ['id' => 11, 'title' => 'مزایده پژو ۲۰۷ اتوماتیک', 'url' => route('auctions.show', 11), 'image' => $this->img('car-207-a.jpg'), 'price' => 1985000000, 'year' => 1402, 'km' => 24000, 'extra' => 'مشکی', 'status' => 'scheduled', 'starts_at' => now()->addHours(42)->addMinutes(23)->addSeconds(15)],
            ['id' => 12, 'title' => 'مزایده ولوو FH500 کشنده', 'url' => route('auctions.show', 12), 'image' => $this->img('truck-fh-a.jpg'), 'price' => 9800000000, 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'status' => 'scheduled', 'starts_at' => now()->addHours(42)->addMinutes(23)->addSeconds(15)],
        ];

        $instant = [
            ['id' => 1, 'title' => 'پژو ۲۰۷ اتوماتیک', 'url' => $car, 'image' => $this->img('car-207.jpg'), 'price' => 920000000, 'old_price' => 985000000, 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'dealer' => 'نمایشگاه آریا', 'verified' => true, 'location' => 'تهران، سعادت‌آباد', 'posted' => '۲ ساعت پیش', 'favorited' => false],
            ['id' => 2, 'title' => 'ولوو FH500 کشنده', 'url' => $car, 'image' => $this->img('truck-fh.jpg'), 'price' => 9800000000, 'old_price' => null, 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'dealer' => 'آریا خودروهای تجاری', 'verified' => true, 'location' => 'تهران، شهریار', 'posted' => '۵ ساعت پیش', 'favorited' => false],
            ['id' => 3, 'title' => 'یاماها NMAX 155', 'url' => $car, 'image' => $this->img('yamaha.jpg'), 'price' => 385000000, 'old_price' => 420000000, 'year' => 1403, 'km' => 4500, 'extra' => 'آبی', 'dealer' => 'آریا موتور', 'verified' => true, 'location' => 'مشهد', 'posted' => 'دیروز', 'favorited' => true],
        ];

        $latest = [
            ['id' => 21, 'title' => 'ولوو FH500 کشنده', 'url' => $car, 'image' => $this->img('truck-fh.jpg'), 'price' => 9800000000, 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'dealer' => 'آریا خودروهای تجاری', 'verified' => true, 'sale_type' => null],
            ['id' => 22, 'title' => 'یاماها NMAX 155', 'url' => $car, 'image' => $this->img('yamaha.jpg'), 'price' => 385000000, 'year' => 1403, 'km' => 4500, 'extra' => 'آبی', 'dealer' => 'آریا موتور', 'verified' => true, 'sale_type' => null],
        ];

        return view('pages.home', [
            'city' => 'سبزوار',
            'auctions' => $auctions,
            'auctionsCount' => 3,
            'categories' => [
                ['slug' => 'car', 'title' => 'سواری', 'icon' => 'car', 'count' => 1240],
                ['slug' => 'heavy', 'title' => 'سنگین', 'icon' => 'truck', 'count' => 182],
                ['slug' => 'bike', 'title' => 'موتورسیکلت', 'icon' => 'bike', 'count' => 246],
                ['slug' => 'bicycle', 'title' => 'دوچرخه', 'icon' => 'bicycle', 'count' => 98],
            ],
            'ad' => ['image' => $this->img('ad-1.jpg'), 'label' => 'تبلیغات', 'title' => 'همراه مسیر زندگی شما', 'subtitle' => 'لیزینگ • فروش انواع ماشین سنگین', 'cta' => 'بازدید از نمایشگاه', 'url' => '#'],
            'instant' => $instant,
            'latest' => collect($latest),
        ]);
    }

    public function styleguide()
    {
        return view('pages.styleguide', ['items' => collect($this->mock())]);
    }

    protected function mock()
    {
        return [
            ['id' => 1, 'title' => 'پژو ۲۰۷ پانوراما دنده‌ای', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 685000000, 'year' => 1402, 'km' => 24000, 'city' => 'تهران', 'dealer' => 'نمایشگاه آریا', 'sale_type' => 'instant', 'favorited' => false],
            ['id' => 2, 'title' => 'سمند LX EF7', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 520000000, 'year' => 1401, 'km' => 41000, 'city' => 'اصفهان', 'dealer' => 'نمایشگاه پارس', 'sale_type' => 'auction', 'ends_at' => now()->addHours(5), 'favorited' => true],
            ['id' => 3, 'title' => 'هیوندای النترا', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 1850000000, 'year' => 1400, 'km' => 62000, 'city' => 'مشهد', 'dealer' => 'نمایشگاه نگین', 'sale_type' => null, 'favorited' => false],
            ['id' => 4, 'title' => 'کامیون بنز ۲۶۳۱', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 6200000000, 'year' => 1395, 'km' => 380000, 'city' => 'شیراز', 'dealer' => 'سنگین‌خودرو البرز', 'sale_type' => 'auction', 'ends_at' => now()->addMinutes(4), 'favorited' => false],
            ['id' => 5, 'title' => 'موتور هوندا CBR 250', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 390000000, 'year' => 1399, 'km' => 18000, 'city' => 'تبریز', 'dealer' => 'موتور سیتی', 'sale_type' => 'instant', 'favorited' => false],
            ['id' => 6, 'title' => 'تیبا ۲ صفر', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 390000000, 'year' => 1403, 'km' => 0, 'city' => 'کرج', 'dealer' => 'نمایشگاه آریا', 'sale_type' => null, 'favorited' => false],
        ];
    }
}
