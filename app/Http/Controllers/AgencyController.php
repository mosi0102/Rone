<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class AgencyController extends Controller
{
    use ListingData;

    public function index()
    {
        $cities = $this->cities();
        $hasht = ['id' => 8, 'name' => 'املاک پارسیان', 'manager' => 'ابولفضل پناهی', 'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'cover' => $this->img('agency-cover.jpg'), 'url' => route('agencies.show', 8), 'verified' => true, 'featured' => true,
            'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']]];
        return view('pages.agencies.index', [
            'city' => 'سبزوار', 'cities' => $cities,
            'banners' => [['image' => $this->img('banner-agencies-1.jpg'), 'alt' => 'جزو بهترین‌ها باش؛ به‌صورت رایگان به خانواده بزرگ رون بپیوندید', 'url' => route('agency.join')]],
            'categories' => [
                ['slug' => 'res', 'title' => 'مسکونی', 'icon' => 'home', 'count' => 184],
                ['slug' => 'com', 'title' => 'اداری و تجاری', 'icon' => 'briefcase', 'count' => 62],
                ['slug' => 'project', 'title' => 'پروژه‌ها', 'icon' => 'hammer', 'count' => 23],
                ['slug' => 'short', 'title' => 'کوتاه‌مدت', 'icon' => 'calendar', 'count' => 31],
            ],
            'featured' => [$hasht, array_merge($hasht, ['id' => 9, 'name' => 'املاک آرش', 'manager' => 'عباس آرمانجو', 'featured' => false, 'url' => route('agencies.show', 9)])],
            'latest' => collect((request('q') || request('city')) ? $this->agencyFilter($this->agencyStore(), request('q'), request('city')) : [
                ['id' => 31, 'name' => 'املاک آرش', 'manager' => 'عباس آرمانجو', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'image' => $this->img('agency-thumb-1.jpg'), 'url' => route('agencies.show', 9)],
                ['id' => 32, 'name' => 'املاک کرمی', 'manager' => 'یار رمضانی', 'area' => 'سبزوار، چهل متری', 'active_count' => 21, 'image' => $this->img('agency-thumb-2.jpg'), 'url' => route('agencies.show', 8)],
                ['id' => 33, 'name' => 'املاک علیپور', 'manager' => 'علی علیپور', 'area' => 'سبزوار، چهار راه فرودگاه', 'active_count' => 21, 'image' => $this->img('agency-thumb-3.jpg'), 'url' => route('agencies.show', 8)],
            ]),
        ]);
    }

    /** جست‌وجوی زنده‌ی آژانس‌ها (AJAX از لایه‌ی جست‌وجو): q، city، category ← فقط HTML فهرست */
    public function search()
    {
        if (!request()->ajax()) return redirect(route('agencies.index', array_filter(['q' => request('q'), 'city' => request('city')])));
        $items = $this->agencyFilter($this->agencyStore(), request('q'), request('city'), request('category'));
        return $this->varied('pages.agencies._results', ['items' => $items]);
    }

    /** آژانس‌های نمونه (از بک‌اند: name, manager, area, city, cat[] = تخصص: res|com|project|short, active_count, image, url) */
    protected function agencyStore(): array
    {
        $r = fn($id, $n, $m, $city, $area, $cat, $cnt, $im) => ['id' => $id, 'name' => $n, 'manager' => $m, 'city' => $city, 'area' => $city . '، ' . $area, 'cat' => $cat, 'active_count' => $cnt, 'image' => $this->img($im), 'url' => route('agencies.show', $id)];
        return [
            $r(8, 'املاک پارسیان', 'ابولفضل پناهی', 'تهران', 'سعادت‌آباد', ['res', 'com', 'short'], 12, 'agency-thumb-1.jpg'),
            $r(9, 'املاک آرش', 'عباس آرمانجو', 'تهران', 'سعادت‌آباد', ['res'], 12, 'agency-thumb-1.jpg'),
            $r(32, 'املاک کرمی', 'یار رمضانی', 'سبزوار', 'چهل متری', ['res'], 21, 'agency-thumb-2.jpg'),
            $r(33, 'املاک علیپور', 'علی علیپور', 'سبزوار', 'چهار راه فرودگاه', ['res', 'project'], 21, 'agency-thumb-3.jpg'),
            $r(22, 'املاک آریا تجاری', 'سعید آریایی', 'مشهد', 'بلوار وکیل‌آباد', ['com'], 9, 'agency-thumb-2.jpg'),
            $r(21, 'املاک آریا', 'مهدی آریایی', 'مشهد', 'احمدآباد', ['res', 'short'], 14, 'agency-thumb-3.jpg'),
            $r(41, 'املاک پدیده', 'نیما صالحی', 'اصفهان', 'چهارباغ', ['project'], 8, 'agency-thumb-1.jpg'),
        ];
    }

    protected function agencyFilter(array $rows, $q = null, $city = null, $cat = null): array
    {
        $q = trim((string) $q);
        return array_values(array_filter($rows, fn($d) =>
            ($q === '' || mb_stripos($d['name'] . ' ' . $d['manager'] . ' ' . $d['area'], $q) !== false)
            && (!$city || $d['city'] === $city)
            && (!$cat || in_array($cat, $d['cat'], true))));
    }

    public function show($id)
    {
        $stock = collect($this->listingStore())->where('agency_id', 8)->values();
        $tabs = [['slug' => 'all', 'title' => 'همه', 'count' => $stock->count()]];
        foreach ($this->propertyCategories() as $slug => $c) {
            if ($n = $stock->where('cat', $slug)->count()) {
                $tabs[] = ['slug' => $slug, 'title' => $c['short'], 'count' => $n];
            }
        }

        return view('pages.agencies.show', [
            'agency' => (object)[
                'id' => $id, 'name' => 'املاک پارسیان', 'short_name' => 'پارسیان', 'manager' => 'ابولفضل پناهی', 'verified' => true, 'cover' => $this->img('agency-cover.jpg'),
                'video' => asset('video/intro.mp4'), // آدرس ویدیو معرفی (mp4)؛ خالی = بخش ویدیو نمایش داده نمی‌شود
                'about' => 'خرید، فروش و اجاره‌ی آپارتمان، ویلا و املاک تجاری در شمال تهران؛ بازدید با هماهنگی قبلی و تنظیم قرارداد با کد رهگیری.',
                'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰',
                'map_url' => 'https://maps.google.com/?q=35.78,51.37', 'active_count' => 12,
                'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'موجودی فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']],
            ],
            'tabs' => $tabs,
            'stock' => $stock->map(fn ($r) => $this->listingCardItem($r))->all(),
        ]);
    }

    public function join()
    {
        return view('pages.agencies.join');
    }

    protected function cities(): array
    {
        return ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'];
    }

    /** وضعیت بررسی ثبت‌نام آژانس (پس از ثبت‌نام). پیش‌نمایش: ?status=pending|approved|rejected */
    public function status()
    {
        $st = in_array(request('status'), ['pending', 'approved', 'rejected'], true) ? request('status') : 'pending';
        return view('pages.agencies.status', [
            'status' => $st,
            'name' => 'املاک پارسیان',
            'ref' => '۴۸۲۱۹',
            'reason' => $st === 'rejected' ? 'تصویر پروانه‌ی کسب خوانا نیست. لطفاً تصویر واضح‌تری ارسال کنید.' : null,
            'steps' => [
                ['title' => 'ثبت درخواست', 'text' => 'اطلاعات و مدارک آژانس دریافت شد.', 'done' => true],
                ['title' => 'بررسی مدارک', 'text' => 'کارشناسان پروانه‌ی کسب و هویت مدیر را بررسی می‌کنند.', 'done' => $st !== 'pending', 'current' => $st === 'pending'],
                ['title' => 'فعال‌سازی آژانس', 'text' => 'پس از تأیید، اشتراک بخرید و آگهی ثبت کنید.', 'done' => $st === 'approved'],
            ],
        ]);
    }
}
