<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class PanelController extends Controller
{
    use ListingData;

    public function dashboard()
    {
        $listings = [
            ['id' => 31001, 'title' => 'آپارتمان ۱۲۰ متری نوساز', 'image' => $this->img('agency-stock-1.jpg'), 'price' => 18500000000, 'rent' => null, 'status' => 'active', 'sale_type' => 'instant', 'views' => 326, 'saves' => 18, 'reserves' => 2, 'expires' => 'تا ۴۱ روز دیگر'],
            ['id' => 31005, 'title' => 'اجاره آپارتمان ۹۰ متری', 'image' => $this->img('detail-t2.jpg'), 'price' => 600000000, 'rent' => 25000000, 'status' => 'active', 'sale_type' => null, 'views' => 148, 'saves' => 9, 'reserves' => 0, 'expires' => 'تا ۴۱ روز دیگر'],
            ['id' => 31008, 'title' => 'مغازه ۳۵ متری بر خیابان اصلی', 'image' => $this->img('agency-thumb-1.jpg'), 'price' => 9500000000, 'rent' => null, 'status' => 'active', 'sale_type' => 'instant', 'views' => 412, 'saves' => 31, 'reserves' => 1, 'expires' => 'تا ۱۲ روز دیگر'],
            ['id' => 31016, 'title' => 'کلاس آموزشی ساعتی/روزانه', 'image' => null, 'price' => 2500000, 'rent' => null, 'status' => 'draft', 'sale_type' => null, 'views' => 0, 'saves' => 0, 'reserves' => 0, 'expires' => 'پیش‌نویس'],
            ['id' => 31020, 'title' => 'اجاره آپارتمان ۹۰ متری (موقعیت عالی)', 'image' => null, 'price' => 620000000, 'rent' => 26000000, 'status' => 'expired', 'sale_type' => null, 'views' => 97, 'saves' => 4, 'reserves' => 1, 'expires' => 'منقضی شده'],
        ];

        return view('pages.panel.dashboard', [
            'tab' => request('tab', 'overview'),
            'kpis' => [
                ['icon' => 'eye', 'label' => 'بازدید ۷ روز اخیر', 'value' => 1240, 'delta' => 12, 'up' => true],
                ['icon' => 'bookmark', 'label' => 'نشان‌شده‌ها', 'value' => 62, 'delta' => 5, 'up' => true],
                ['icon' => 'phone', 'label' => 'تماس‌های جدید', 'value' => 23, 'delta' => 4, 'up' => true],
                ['icon' => 'trending-up2', 'label' => 'نرخ تبدیل', 'value' => '۲٫۴٪', 'delta' => 0.3, 'up' => true],
            ],
            'series' => ['labels' => ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'], 'values' => [120, 188, 150, 232, 205, 260, 85]],
            'funnel' => [['label' => 'بازدید', 'value' => 1240], ['label' => 'نشان‌شده', 'value' => 62], ['label' => 'تماس', 'value' => 23], ['label' => 'قرارداد', 'value' => 5]],
            'activity' => [
                ['icon' => 'gavel', 'text' => 'پیشنهاد جدید ۲۰٬۶۰۰٬۰۰۰٬۰۰۰ تومان در مزایده «خانه کلنگی ۲۴۰ متر»', 'time' => '۲۵ دقیقه پیش', 'tone' => 'ok'],
                ['icon' => 'message', 'text' => 'درخواست بازدید برای «مغازه ۳۵ متری بر خیابان اصلی»', 'time' => '۲ ساعت پیش', 'tone' => 'info'],
                ['icon' => 'sparkles', 'text' => '۳ مشتری دنبال آپارتمانی شبیه آگهی‌های شما هستند', 'time' => 'دیروز', 'tone' => 'info'],
                ['icon' => 'alert', 'text' => 'آگهی «اجاره آپارتمان ۹۰ متری» منقضی شد', 'time' => 'دیروز', 'tone' => 'warn'],
            ],
            'listings' => $listings,
            'auctions' => [
                ['id' => 13, 'title' => 'خانه کلنگی ۲۴۰ متر شمالی', 'image' => $this->img('detail-main.jpg'), 'status' => 'live', 'base' => 20000000000, 'top' => 20600000000, 'bidders' => 7, 'ends_at' => now()->addHours(3)],
                ['id' => 11, 'title' => 'آپارتمان ۱۲۰ متری نوساز', 'image' => $this->img('agency-stock-1.jpg'), 'status' => 'scheduled', 'base' => 17500000000, 'top' => null, 'bidders' => 14, 'ends_at' => now()->addDays(2)],
                ['id' => 14, 'title' => 'مغازه ۳۵ متری (قابل معاوضه)', 'image' => null, 'status' => 'ended', 'base' => 9000000000, 'top' => 9650000000, 'bidders' => 11, 'ends_at' => now()->subDays(3)],
            ],
            // پرداخت‌های آژانس: اشتراک، آگهی فوری ۲۴ ساعته، ساخت مزایده (سایت پول ملک را دریافت نمی‌کند)
            'finance' => [
                'balance' => 18500000, 'month_spent' => 7450000,
                'tx' => [
                    ['title' => 'ساخت مزایده «خانه کلنگی ۲۴۰ متر»', 'amount' => 2000000, 'in' => false, 'date' => now()->subHours(5)],
                    ['title' => 'آگهی فوری ۲۴ ساعته «آپارتمان ۱۲۰ متری»', 'amount' => 450000, 'in' => false, 'date' => now()->subDays(1)],
                    ['title' => 'شارژ کیف پول', 'amount' => 10000000, 'in' => true, 'date' => now()->subDays(2)],
                    ['title' => 'تمدید اشتراک ۳ ماهه', 'amount' => 4000000, 'in' => false, 'date' => now()->subDays(6)],
                ],
            ],
            'agency' => (object) ['name' => 'املاک پارسیان', 'short' => 'پارسیان', 'manager' => 'ابولفضل پناهی', 'phone' => '۰۲۱۲۲۳۴۵۶۷۸', 'about' => 'خرید، فروش و اجاره‌ی آپارتمان، ویلا و املاک تجاری؛ بازدید با هماهنگی و تنظیم قرارداد با کد رهگیری.', 'address' => 'تهران، سعادت‌آباد، بین سعادتی ۲۰ و ۲۲، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰', 'cover' => $this->img('agency-cover.jpg')],
        ]);
    }

    public function listingCreate()
    {
        return view('pages.panel.listing-form', ['listing' => null, 'opt' => $this->formOptions()]);
    }

    public function listingEdit($id)
    {
        $listing = (object) [
            'id' => $id, 'category' => 'rent_res', 'type' => 'آپارتمان', 'city' => 'تهران', 'district' => 'پونک', 'address' => 'تهران، پونک، بلوار عدل، کوچه‌ی ۱۲، پلاک ۸', 'lat' => 35.7631, 'lng' => 51.3345,
            'area' => 90, 'rooms' => '۲', 'year' => 1399, 'floor' => '۲', 'units' => 2, 'price' => 600000000, 'rent' => 25000000, 'convertible' => true, 'negotiable' => true,
            'amenities' => ['parking', 'elevator', 'storage', 'balcony'],
            'description' => 'آپارتمان نورگیر و کم‌واحد، کابینت‌های نو، دسترسی عالی به مترو.', 'sale_type' => 'instant',
            'images' => [asset('img/mock/detail-main.jpg'), asset('img/mock/detail-t1.jpg')],
        ];

        return view('pages.panel.listing-form', ['listing' => $listing, 'opt' => $this->formOptions()]);
    }

    protected function formOptions()
    {
        $lf = $this->lf();

        return [
            'categories' => $this->propertyCategories(),
            'cities' => $lf['cities'],
            'districts' => $lf['districts'],
            'district_coords' => $lf['district_coords'],
            'city_centers' => $lf['city_centers'],
            'price_labels' => $lf['price_labels'],
            'amenities' => [
                'parking' => ['پارکینگ', 'car'], 'elevator' => ['آسانسور', 'elevator'], 'storage' => ['انباری', 'box'],
                'balcony' => ['بالکن', 'home'], 'heating' => ['شوفاژ / پکیج', 'sparkles'], 'cooling' => ['کولر / اسپلیت', 'compass'],
                'renovated' => ['بازسازی‌شده', 'hammer'], 'furnished' => ['مبله', 'bed'], 'guard' => ['نگهبانی / لابی', 'shield'],
            ],
        ];
    }
}
