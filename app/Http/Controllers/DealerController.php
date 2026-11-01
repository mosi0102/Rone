<?php

namespace App\Http\Controllers;

class DealerController extends Controller
{
    public function index()
    {
        $cities = $this->cities();
        $hasht = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'manager' => 'ابولفضل پناهی', 'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'cover' => $this->img('dealer-cover.jpg'), 'url' => route('dealers.show', 8), 'verified' => true, 'featured' => true,
            'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']]];
        return view('pages.dealers.index', [
            'city' => 'سبزوار', 'cities' => $cities,
            'banners' => [['image' => $this->img('banner-dealers-1.jpg'), 'alt' => 'جزو بهترین‌ها باش؛ به‌صورت رایگان به خانواده بزرگ آروان بپیوندید', 'url' => route('dealer.join')]],
            'categories' => [
                ['slug' => 'car', 'title' => 'نمایشگاه سواری', 'icon' => 'car', 'count' => 50],
                ['slug' => 'heavy', 'title' => 'نمایشگاه سنگین', 'icon' => 'truck', 'count' => 16],
                ['slug' => 'bike', 'title' => 'نمایشگاه موتور', 'icon' => 'bike', 'count' => 23],
                ['slug' => 'bicycle', 'title' => 'نمایشگاه دوچرخه', 'icon' => 'bicycle', 'count' => 11],
            ],
            'featured' => [$hasht, array_merge($hasht, ['id' => 9, 'name' => 'اتوگالری آرش', 'manager' => 'عباس آرمانجو', 'featured' => false, 'url' => route('dealers.show', 9)])],
            'latest' => collect((request('q') || request('city')) ? $this->dealerFilter($this->dealerStore(), request('q'), request('city')) : [
                ['id' => 31, 'name' => 'اتوگالری آرش', 'manager' => 'عباس آرمانجو', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'image' => $this->img('dealer-thumb-1.jpg'), 'url' => route('dealers.show', 9)],
                ['id' => 32, 'name' => 'اتوگالری کرمی', 'manager' => 'یار رمضانی', 'area' => 'سبزوار، چهل متری', 'active_count' => 21, 'image' => $this->img('dealer-thumb-2.jpg'), 'url' => route('dealers.show', 8)],
                ['id' => 33, 'name' => 'اتوگالری علیپور', 'manager' => 'علی علیپور', 'area' => 'سبزوار، چهار راه فرودگاه', 'active_count' => 21, 'image' => $this->img('dealer-thumb-3.jpg'), 'url' => route('dealers.show', 8)],
            ]),
        ]);
    }

    /** جست‌وجوی زنده‌ی نمایشگاه‌ها (AJAX از لایه‌ی جست‌وجو): q، city، category ← فقط HTML فهرست */
    public function search()
    {
        if (!request()->ajax()) return redirect(route('dealers.index', array_filter(['q' => request('q'), 'city' => request('city')])));
        $items = $this->dealerFilter($this->dealerStore(), request('q'), request('city'), request('category'));
        return $this->varied('pages.dealers._results', ['items' => $items]);
    }

    /** نمایشگاه‌های نمونه (از بک‌اند: name, manager, area, city, cat[], active_count, image, url) */
    protected function dealerStore(): array
    {
        $r = fn($id, $n, $m, $city, $area, $cat, $cnt, $im) => ['id' => $id, 'name' => $n, 'manager' => $m, 'city' => $city, 'area' => $city . '، ' . $area, 'cat' => $cat, 'active_count' => $cnt, 'image' => $this->img($im), 'url' => route('dealers.show', $id)];
        return [
            $r(8, 'اتوگالری هالیوود', 'ابولفضل پناهی', 'تهران', 'سعادت‌آباد', ['car', 'heavy', 'bike'], 12, 'dealer-thumb-1.jpg'),
            $r(9, 'اتوگالری آرش', 'عباس آرمانجو', 'تهران', 'سعادت‌آباد', ['car'], 12, 'dealer-thumb-1.jpg'),
            $r(32, 'اتوگالری کرمی', 'یار رمضانی', 'سبزوار', 'چهل متری', ['car'], 21, 'dealer-thumb-2.jpg'),
            $r(33, 'اتوگالری علیپور', 'علی علیپور', 'سبزوار', 'چهار راه فرودگاه', ['car', 'bike'], 21, 'dealer-thumb-3.jpg'),
            $r(22, 'آریا خودروهای تجاری', 'سعید آریایی', 'مشهد', 'بلوار وکیل‌آباد', ['heavy'], 9, 'dealer-thumb-2.jpg'),
            $r(21, 'آریا موتور', 'مهدی آریایی', 'مشهد', 'احمدآباد', ['bike', 'bicycle'], 14, 'dealer-thumb-3.jpg'),
            $r(41, 'دوچرخه‌فروشی پدال', 'نیما صالحی', 'اصفهان', 'چهارباغ', ['bicycle'], 8, 'dealer-thumb-1.jpg'),
        ];
    }

    protected function dealerFilter(array $rows, $q = null, $city = null, $cat = null): array
    {
        $q = trim((string) $q);
        return array_values(array_filter($rows, fn($d) =>
            ($q === '' || mb_stripos($d['name'] . ' ' . $d['manager'] . ' ' . $d['area'], $q) !== false)
            && (!$city || $d['city'] === $city)
            && (!$cat || in_array($cat, $d['cat'], true))));
    }

    public function show($id)
    {
        $row = fn($i, $t, $p, $y, $k, $x, $d, $im, $cat, $st = null) => ['id' => $i, 'title' => $t, 'url' => route('listings.show', 24817), 'image' => $this->img($im), 'price' => $p, 'year' => $y, 'km' => $k, 'extra' => $x, 'dealer' => $d, 'verified' => true, 'sale_type' => $st, 'category' => $cat];
        return view('pages.dealers.show', [
            'dealer' => (object)[
                'id' => $id, 'name' => 'اتوگالری هالیوود', 'short_name' => 'گالری هالیوود', 'manager' => 'ابولفضل پناهی', 'verified' => true, 'cover' => $this->img('dealer-cover.jpg'),
                'video' => asset('video/intro.mp4'), // آدرس ویدیو معرفی (mp4)؛ خالی = بخش ویدیو نمایش داده نمی‌شود
                'about' => 'فروش تخصصی سواری، سنگین و موتورسیکلت در شعب آریا؛ بازدید حضوری و کارشناسی مستقل با هماهنگی.',
                'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰',
                'map_url' => 'https://maps.google.com/?q=35.78,51.37', 'active_count' => 12,
                'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'موجودی فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']],
            ],
            'tabs' => [['slug' => 'all', 'title' => 'همه', 'count' => 12], ['slug' => 'car', 'title' => 'سواری', 'count' => 7], ['slug' => 'heavy', 'title' => 'سنگین', 'count' => 2], ['slug' => 'bike', 'title' => 'موتور', 'count' => 3], ['slug' => 'bicycle', 'title' => 'دوچرخه', 'count' => 2]],
            'stock' => [
                $row(1, 'پژو ۲۰۷ اتوماتیک', 985000000, 1402, 24000, 'سفید', 'نمایشگاه آریا', 'dealer-stock-1.jpg', 'car', 'instant'),
                $row(2, 'ولوو FH500 کشنده', 9800000000, 1399, 320000, 'دیزل', 'آریا خودروهای تجاری', 'dealer-stock-2.jpg', 'heavy'),
                $row(3, 'یاماها NMAX 155', 385000000, 1403, 4500, 'آبی', 'آریا موتور', 'dealer-stock-3.jpg', 'bike', 'auction'),
                $row(4, 'دوچرخه کوهستان جاینت ATX', 28000000, 1403, 800, 'مشکی', 'آریا موتور', 'dealer-stock-3.jpg', 'bicycle', 'instant'),
            ],
        ]);
    }

    public function join()
    {
        return view('pages.dealers.join');
    }

    protected function cities(): array
    {
        return ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'];
    }

    /** وضعیت بررسی ثبت‌نام نمایشگاه (پس از ثبت‌نام). پیش‌نمایش: ?status=pending|approved|rejected */
    public function status()
    {
        $st = in_array(request('status'), ['pending', 'approved', 'rejected'], true) ? request('status') : 'pending';
        return view('pages.dealers.status', [
            'status' => $st,
            'name' => 'اتوگالری هالیوود',
            'ref' => '۴۸۲۱۹',
            'reason' => $st === 'rejected' ? 'تصویر پروانه‌ی کسب خوانا نیست. لطفاً تصویر واضح‌تری ارسال کنید.' : null,
            'steps' => [
                ['title' => 'ثبت درخواست', 'text' => 'اطلاعات و مدارک نمایشگاه دریافت شد.', 'done' => true],
                ['title' => 'بررسی مدارک', 'text' => 'کارشناسان پروانه‌ی کسب و هویت مدیر را بررسی می‌کنند.', 'done' => $st !== 'pending', 'current' => $st === 'pending'],
                ['title' => 'فعال‌سازی نمایشگاه', 'text' => 'پس از تأیید، اشتراک بخرید و آگهی ثبت کنید.', 'done' => $st === 'approved'],
            ],
        ]);
    }
}
