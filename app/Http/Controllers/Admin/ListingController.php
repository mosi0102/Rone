<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ListingController extends Controller
{
    public function index()
    {
        $desc = 'خودرو تک‌مالک، سرویس‌ها به‌موقع انجام شده و آماده‌ی بازدید حضوری با هماهنگی قبلی در نمایشگاه است.';
        $specsFor = fn($cat, $year, $km, $color, $extra) => match ($cat) {
            'heavy' => [['سال ساخت', $year], ['کارکرد', number_format($km) . ' کیلومتر'], ['سوخت', 'دیزل'], ['رنگ', $color], ['نوع', $extra], ['وضعیت بدنه', 'سالم']],
            'bike' => [['سال ساخت', $year], ['کارکرد', number_format($km) . ' کیلومتر'], ['حجم موتور', $extra], ['رنگ', $color], ['گیربکس', 'دنده‌ای'], ['بیمه', 'تا اسفند ۱۴۰۵']],
            'bicycle' => [['سال ساخت', $year], ['نوع', $extra], ['سایز رینگ', '۲۷٫۵ اینچ'], ['رنگ', $color], ['دنده', '۲۱ سرعته'], ['وضعیت', 'کم‌کارکرد']],
            default => [['سال ساخت', $year], ['کارکرد', number_format($km) . ' کیلومتر'], ['گیربکس', $extra], ['رنگ', $color], ['سوخت', 'بنزین'], ['بیمه', 'تا اسفند ۱۴۰۵']],
        };
        // [کد، عنوان، دسته، قیمت، نوع فروش، وضعیت، نمایشگاه، شهر، زمان ثبت، تصویر، سال، کارکرد، رنگ، مشخصه‌ی اضافه، دلیل رد]
        $seed = [
            [24817, 'پژو ۲۰۷ اتوماتیک', 'car', 985000000, 'normal', 'pending', 'اتوگالری هالیوود', 'تهران', '۲ ساعت پیش', 'car-207.jpg', '۱۴۰۲', 24000, 'سفید', 'اتوماتیک'],
            [24818, 'ولوو FH500 کشنده', 'heavy', 9800000000, 'auction', 'pending', 'آریا خودروهای تجاری', 'تهران', '۵ ساعت پیش', 'truck-fh.jpg', '۱۳۹۹', 320000, 'سفید', 'کشنده'],
            [24819, 'یاماها NMAX 155', 'bike', 385000000, 'instant', 'pending', 'آریا موتور', 'مشهد', 'دیروز', 'yamaha.jpg', '۱۴۰۳', 4500, 'آبی', '۱۵۵ سی‌سی'],
            [24820, 'سمند LX EF7', 'car', 520000000, 'instant', 'pending', 'نمایشگاه پارس', 'اصفهان', 'دیروز', 'dealer-stock-1.jpg', '۱۴۰۱', 41000, 'نقره‌ای', 'دنده‌ای'],
            [24821, 'هیوندای النترا', 'car', 1850000000, 'normal', 'active', 'نمایشگاه نگین', 'مشهد', '۲ روز پیش', 'detail-main.jpg', '۱۴۰۰', 62000, 'مشکی', 'اتوماتیک'],
            [24822, 'کامیون بنز ۲۶۳۱', 'heavy', 6200000000, 'auction', 'active', 'سنگین‌خودرو البرز', 'شیراز', '۲ روز پیش', 'truck-fh-a.jpg', '۱۳۹۵', 380000, 'زرد', 'ده‌چرخ'],
            [24823, 'موتور هوندا CBR 250', 'bike', 390000000, 'normal', 'active', 'موتور سیتی', 'تبریز', '۳ روز پیش', 'dealer-stock-3.jpg', '۱۳۹۹', 18000, 'قرمز', '۲۵۰ سی‌سی'],
            [24824, 'تیبا ۲ صفر', 'car', 390000000, 'instant', 'active', 'اتوگالری هالیوود', 'کرج', '۴ روز پیش', 'detail-t1.jpg', '۱۴۰۳', 0, 'سفید', 'دنده‌ای'],
            [24825, 'پژو ۲۰۷ پانوراما', 'car', 940000000, 'normal', 'rejected', 'نمایشگاه پارس', 'اصفهان', '۵ روز پیش', 'car-207-a.jpg', '۱۴۰۱', 52000, 'مشکی', 'اتوماتیک', 'تصاویر نامناسب یا ناکافی'],
            [24826, 'نیسان زامیاد', 'heavy', 1450000000, 'normal', 'suspended', 'سنگین‌خودرو البرز', 'شیراز', '۶ روز پیش', 'dealer-stock-2.jpg', '۱۳۹۸', 210000, 'سفید', 'وانت'],
            [24827, 'پراید ۱۳۱ SE', 'car', 280000000, 'normal', 'sold', 'نمایشگاه نگین', 'مشهد', 'هفته‌ی پیش', 'detail-t2.jpg', '۱۳۹۷', 98000, 'سفید', 'دنده‌ای'],
            [24829, 'دوچرخه کوهستان جاینت ATX', 'bicycle', 38000000, 'instant', 'pending', 'دوچرخه‌ی آرمان', 'تهران', '۳ ساعت پیش', 'dealer-stock-3.jpg', '۱۴۰۳', 0, 'مشکی', 'کوهستان'],
            [24828, 'رنو تندر ۹۰', 'car', 610000000, 'instant', 'expired', 'نمایشگاه پارس', 'اصفهان', '۲ هفته پیش', 'detail-t3.jpg', '۱۳۹۴', 140000, 'نقره‌ای', 'دنده‌ای'],
        ];
        $listings = array_map(function ($r) use ($desc, $specsFor) {
            [$code, $title, $cat, $price, $sale, $status, $dealer, $city, $posted, $image, $year, $km, $color, $extra] = $r;
            return [
                'id' => $code, 'code' => $code, 'title' => $title, 'cat' => $cat, 'price' => $price, 'sale' => $sale, 'status' => $status,
                'dealer' => $dealer, 'city' => $city, 'posted' => $posted, 'reason' => $r[14] ?? '', 'desc' => $desc,
                'images' => [$this->img($image), $this->img('detail-t1.jpg'), $this->img('detail-t2.jpg'), $this->img('detail-t3.jpg')],
                'specs' => $specsFor($cat, $year, $km, $color, $extra),
            ];
        }, $seed);

        return view('admin.pages.listings.index', ['listings' => $listings]);

    }
}
