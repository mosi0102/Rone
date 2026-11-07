<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class HomeController extends Controller
{
    use ListingData;

    public function index()
    {
        $rows = collect($this->listingStore());
        $counts = ['sale_res' => 3240, 'rent_res' => 2810, 'sale_com' => 640, 'rent_com' => 520, 'project' => 96, 'short' => 310];

        return view('pages.home', [
            'city' => 'سبزوار',
            // مزایده‌های در آستانه شروع (status: scheduled|live)
            'auctions' => [
                ['id' => 11, 'title' => 'مزایده آپارتمان ۱۲۰ متری نوساز', 'url' => route('auctions.show', 11), 'image' => $this->img('agency-stock-1.jpg'), 'price' => 17500000000, 'area' => 120, 'rooms' => 2, 'year' => 1402, 'extra' => 'سعادت‌آباد', 'status' => 'scheduled', 'starts_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36)],
                ['id' => 13, 'title' => 'مزایده خانه کلنگی ۲۴۰ متر', 'url' => route('auctions.show', 13), 'image' => $this->img('detail-main.jpg'), 'price' => 20600000000, 'area' => 240, 'rooms' => 3, 'year' => null, 'extra' => 'احمدآباد', 'status' => 'live', 'ends_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36)],
            ],
            'auctionsCount' => 4,
            'categories' => collect($this->propertyCategories())->map(fn ($c, $slug) => ['slug' => $slug, 'title' => $c['short'], 'icon' => $c['icon'], 'count' => $counts[$slug]])->values()->all(),
            'ad' => ['image' => $this->img('ad-1.jpg'), 'label' => 'تبلیغات', 'title' => 'خانه‌ی رویایی‌تان نزدیک است', 'subtitle' => 'وام خرید مسکن • مشاوره‌ی رایگان', 'cta' => 'بیشتر بدانید', 'url' => '#'],
            'instant' => $rows->where('sale', 'instant')->take(5)->map(fn ($r) => $this->listingCardItem($r))->values()->all(),
            'latest' => $rows->reject(fn ($r) => $r['featured'])->take(4)->map(fn ($r) => $this->listingCardItem($r))->values(),
        ]);
    }

    public function styleguide()
    {
        return view('pages.styleguide', ['items' => collect($this->listingStore())->take(6)->map(fn ($r) => $this->listingCardItem($r))->values()]);
    }
}
