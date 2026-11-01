<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\DealData;

class PanelController extends Controller
{
    use DealData;

    public function dashboard()
    {
        $listings = [
            ['id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $this->img('dealer-stock-1.jpg'), 'price' => 985000000, 'status' => 'active', 'sale_type' => 'instant', 'views' => 326, 'saves' => 18, 'reserves' => 2, 'expires' => 'تا ۴۱ روز دیگر'],
            ['id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $this->img('dealer-stock-2.jpg'), 'price' => 9800000000, 'status' => 'active', 'sale_type' => null, 'views' => 148, 'saves' => 9, 'reserves' => 0, 'expires' => 'تا ۴۱ روز دیگر'],
            ['id' => 24819, 'title' => 'یاماها NMAX 155', 'image' => $this->img('dealer-stock-3.jpg'), 'price' => 385000000, 'status' => 'auction', 'sale_type' => 'auction', 'views' => 412, 'saves' => 31, 'reserves' => 0, 'expires' => 'مزایده جاری'],
            ['id' => 24820, 'title' => 'پژو ۲۰۶ تیپ ۲', 'image' => null, 'price' => 540000000, 'status' => 'draft', 'sale_type' => null, 'views' => 0, 'saves' => 0, 'reserves' => 0, 'expires' => 'پیش‌نویس'],
            ['id' => 24821, 'title' => 'سمند سورن', 'image' => null, 'price' => 610000000, 'status' => 'expired', 'sale_type' => null, 'views' => 97, 'saves' => 4, 'reserves' => 1, 'expires' => 'منقضی شده'],
        ];
        return view('pages.panel.dashboard', [
            'deals' => collect($this->dealStore())->only([101, 104, 105])->values()->all(),
            'tab' => request('tab', 'overview'),
            'kpis' => [
                ['icon' => 'eye', 'label' => 'بازدید ۷ روز اخیر', 'value' => 1240, 'delta' => 12, 'up' => true],
                ['icon' => 'bookmark', 'label' => 'نشان‌شده‌ها', 'value' => 62, 'delta' => 5, 'up' => true],
                ['icon' => 'shield', 'label' => 'بیعانه‌های جدید', 'value' => 3, 'delta' => 1, 'up' => false],
                ['icon' => 'trending-up2', 'label' => 'نرخ تبدیل', 'value' => '۲٫۴٪', 'delta' => 0.3, 'up' => true],
            ],
            'series' => ['labels' => ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'], 'values' => [120, 188, 150, 232, 205, 260, 85]],
            'funnel' => [['label' => 'بازدید', 'value' => 1240], ['label' => 'نشان‌شده', 'value' => 62], ['label' => 'بیعانه', 'value' => 9], ['label' => 'معامله', 'value' => 5]],
            'activity' => [
                ['icon' => 'shield', 'text' => 'بیعانه جدید برای «پژو ۲۰۷ اتوماتیک»', 'time' => '۲۵ دقیقه پیش', 'tone' => 'ok'],
                ['icon' => 'gavel', 'text' => 'پیشنهاد جدید ۳۹۰٬۰۰۰٬۰۰۰ تومان در مزایده «یاماها NMAX»', 'time' => '۲ ساعت پیش', 'tone' => 'info'],
                ['icon' => 'users', 'text' => '۳ کاربر جدید در مزایده شرکت کردند', 'time' => 'دیروز', 'tone' => 'info'],
                ['icon' => 'alert', 'text' => 'آگهی «سمند سورن» منقضی شد', 'time' => 'دیروز', 'tone' => 'warn'],
            ],
            'listings' => $listings,
            'auctions' => [
                ['id' => 13, 'title' => 'یاماها NMAX 155', 'image' => $this->img('dealer-stock-3.jpg'), 'status' => 'live', 'base' => 380000000, 'top' => 395000000, 'bidders' => 7, 'ends_at' => now()->addHours(3)],
                ['id' => 12, 'title' => 'ولوو FH500 کشنده', 'image' => $this->img('dealer-stock-2.jpg'), 'status' => 'scheduled', 'base' => 9500000000, 'top' => null, 'bidders' => 2, 'ends_at' => now()->addDays(2)],
                ['id' => 14, 'title' => 'پژو ۲۰۷ پانوراما', 'image' => null, 'status' => 'ended', 'base' => 900000000, 'top' => 962000000, 'bidders' => 11, 'ends_at' => now()->subDays(3)],
            ],
            'finance' => [
                'available' => 18500000, 'pending' => 87000000, 'month_income' => 215000000, 'iban' => 'IR12 **** **** **** **** 4521',
                'tx' => [
                    ['title' => 'آزادسازی بیعانه «پژو ۲۰۷»', 'amount' => 29550000, 'in' => true, 'date' => now()->subHours(5)],
                    ['title' => 'خرید فروش فوری (۷ روزه)', 'amount' => 450000, 'in' => false, 'date' => now()->subDays(1)],
                    ['title' => 'هزینه ساخت مزایده', 'amount' => 600000, 'in' => false, 'date' => now()->subDays(2)],
                    ['title' => 'تسویه به حساب بانکی', 'amount' => 120000000, 'in' => false, 'date' => now()->subDays(6)],
                ],
            ],
            'dealer' => (object)['name' => 'اتوگالری هالیوود', 'short' => 'گالری هالیوود', 'manager' => 'ابولفضل پناهی', 'phone' => '۰۲۱۲۲۳۴۵۶۷۸', 'about' => 'فروش تخصصی سواری، سنگین و موتورسیکلت؛ بازدید حضوری و کارشناسی مستقل با هماهنگی.', 'address' => 'تهران، سعادت‌آباد، بین سعادتی ۲۰ و ۲۲، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰', 'cover' => $this->img('dealer-cover.jpg')],
        ]);
    }

    public function listingCreate()
    {
        return view('pages.panel.listing-form', ['listing' => null, 'opt' => $this->formOptions()]);
    }

    public function listingEdit($id)
    {
        return view('pages.panel.listing-form', ['listing' => (object)['id' => $id, 'category' => 'car', 'brand' => 'پژو', 'model' => '۲۰۷ اتوماتیک', 'year' => 1402, 'km' => 24000, 'color' => 'سفید', 'gearbox' => 'auto', 'fuel' => 'بنزین', 'price' => 985000000, 'negotiable' => true, 'description' => 'خودرو تک‌مالک، سرویس‌ها به‌موقع.', 'sale_type' => 'instant', 'images' => [asset('img/mock/detail-main.jpg'), asset('img/mock/detail-t1.jpg')]], 'opt' => $this->formOptions()]);
    }

    protected function formOptions()
    {
        return [
            'brands' => ['پژو', 'سمند', 'تیبا', 'هیوندای', 'کیا', 'ولوو', 'بنز', 'یاماها', 'هوندا'],
            'colors' => ['سفید', 'مشکی', 'نقره‌ای', 'خاکستری', 'آبی', 'قرمز', 'بدون رنگ'],
            'conditions' => ['سالم و پلمپ', 'سالم', 'رنگ‌شده', 'ضربه خورده', 'تعویض شده'],
        ];
    }

    /** سابقه‌ی درخواست‌های تسویه‌ی نمایشگاه‌دار */
    public function settlements()
    {
        return view('pages.panel.settlements', [
            'iban' => 'IR12 **** **** **** **** 4521',
            'items' => [
                ['id' => 3, 'amount' => 120000000, 'status' => 'pending', 'at' => now()->subHours(6), 'note' => null],
                ['id' => 2, 'amount' => 80000000, 'status' => 'paid', 'at' => now()->subDays(6), 'note' => 'شناسه‌ی پیگیری بانک: ۷۴۱۹۵۲۶'],
                ['id' => 1, 'amount' => 45000000, 'status' => 'rejected', 'at' => now()->subDays(15), 'note' => 'نام صاحب حساب با مدارک همخوانی نداشت.'],
            ],
        ]);
    }
}
