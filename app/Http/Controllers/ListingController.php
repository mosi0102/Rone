<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class ListingController extends Controller
{
    use ListingData;

    public function show($id)
    {
        return view('pages.listings.show', [
            'wallet' => 120000000, // موجودی کیف پول کاربر (برای تست حالت کمبود موجودی عدد را کم کنید)
            'car' => (object)[
                'id' => $id, 'code' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'subtitle' => 'مدل ۱۴۰۲ • سقف شیشه‌ای • بدون رنگ', 'sale_type' => 'instant',
                'price' => 985000000, 'price_note' => 'قیمت نقدی • قابل مذاکره', 'location' => 'تهران، سعادت‌آباد', 'views' => 326, 'favorited' => false,
                'deposit_percent' => 2, 'deposit_amount' => 20000000, 'service_fee' => 200000,
                'images' => [
                    ['src' => $this->img('detail-main.jpg'), 'thumb' => $this->img('detail-t1.jpg')],
                    ['src' => $this->img('detail-t1.jpg'), 'thumb' => $this->img('detail-t2.jpg')],
                    ['src' => $this->img('detail-t2.jpg'), 'thumb' => $this->img('detail-t3.jpg')],
                    ['src' => $this->img('detail-t3.jpg'), 'thumb' => $this->img('detail-t3.jpg')],
                ],
                'specs' => [
                    ['icon' => 'calendar', 'label' => 'سال ساخت', 'value' => '۱۴۰۲'],
                    ['icon' => 'gauge', 'label' => 'کارکرد', 'value' => '۲۴٬۰۰۰ کیلومتر'],
                    ['icon' => 'gearbox', 'label' => 'گیربکس', 'value' => 'اتوماتیک'],
                    ['icon' => 'car-front', 'label' => 'وضعیت بدنه', 'value' => 'سالم و بی‌رنگ'],
                    ['icon' => 'palette', 'label' => 'رنگ', 'value' => 'سفید'],
                    ['icon' => 'shield', 'label' => 'بیمه', 'value' => '۱۴۰۵ تا مهر'],
                ],
                'details' => [
                    ['label' => 'سوخت و حجم موتور', 'value' => 'بنزین • ۱٬۶۰۰ سی‌سی'],
                    ['label' => 'رنگ کابین', 'value' => 'پارچه مشکی'],
                    ['label' => 'موتور', 'value' => 'سالم', 'status' => 'ok'],
                    ['label' => 'شاسی جلو', 'value' => 'سالم و پلمپ', 'status' => 'ok'],
                    ['label' => 'شاسی عقب', 'value' => 'ضربه خورده', 'status' => 'bad'],
                    ['label' => 'گیربکس', 'value' => 'سالم و پلمپ', 'status' => 'ok'],
                ],
                'description' => 'خودرو تک‌مالک، سرویس‌ها به‌موقع انجام شده و بیمه تا اسفند ۱۴۰۵ معتبر است. آماده کارشناسی در مرکز مورد اعتماد شما؛ بازدید با هماهنگی قبلی.',
                'dealer' => ['id' => 8, 'name' => 'اتوگالری هالیوود', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'verified' => true, 'cover' => $this->img('detail-banner.jpg'), 'url' => route('dealers.show', 8)],
            ],
            'similar' => [
                ['id' => 41, 'title' => 'پژو ۲۰۷ پانوراما', 'url' => route('listings.show', 24817), 'image' => $this->img('car-207.jpg'), 'price' => 940000000, 'year' => 1401, 'km' => 52000, 'extra' => 'مشکی', 'dealer' => 'نمایشگاه پارس', 'verified' => true, 'sale_type' => null],
            ],
        ]);
    }

    public function search()
    {
        if (request('scope') === 'dealers') return redirect(route('dealers.index', array_filter(['q' => request('q'), 'city' => request('city')])));
        return $this->listingPage('search');
    }

    public function index()
    {
        return $this->listingPage('list');
    }

    public function filters()
    {
        $lf = $this->lf();
        $q = array_filter(request()->query(), fn($v) => $v !== null && $v !== '');
        return view('pages.listings.filters', ['f' => $q, 'lf' => $lf, 'count' => $this->listingFilter($this->listingStore(), $q)->count(), 'back' => route(isset($q['q']) ? 'search' : 'listings.index', $q)]);
    }

    public function count()
    {
        // شمارش زنده برای دکمه «نمایش N آگهی»
         $q = array_filter(request()->query(), fn($v) => $v !== null && $v !== '');
         return response()->json(['count' => $this->listingFilter($this->listingStore(), $q)->count()]);
    }
}
