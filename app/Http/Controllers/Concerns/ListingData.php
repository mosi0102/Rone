<?php

namespace App\Http\Controllers\Concerns;

/** داده و فیلتر نمایشی آگهی‌های ملک (مشترک بین جست‌وجو، رادار و ...) */
trait ListingData
{
    use MockAsset;

    /**
     * دسته‌های اصلی و نحوه‌ی قیمت‌گذاری هر دسته.
     * price_mode: sale = قیمت کل • rent = رهن + اجاره‌ی ماهانه • short = اجاره‌ی هر شب • project = قیمت هر متر
     *
     * @return array<string, array{title: string, icon: string, price_mode: string, types: array<int, string>}>
     */
    protected function propertyCategories(): array
    {
        return [
            'sale_res' => ['title' => 'فروش مسکونی', 'short' => 'فروش مسکونی', 'icon' => 'home', 'price_mode' => 'sale', 'types' => ['آپارتمان', 'خانه و ویلا', 'زمین و کلنگی']],
            'rent_res' => ['title' => 'اجاره مسکونی', 'short' => 'اجاره مسکونی', 'icon' => 'key', 'price_mode' => 'rent', 'types' => ['آپارتمان', 'خانه و ویلا']],
            'sale_com' => ['title' => 'فروش اداری و تجاری', 'short' => 'فروش تجاری', 'icon' => 'briefcase', 'price_mode' => 'sale', 'types' => ['دفتر کار، اتاق اداری و مطب', 'مغازه و غرفه', 'صنعتی، کشاورزی و تجاری']],
            'rent_com' => ['title' => 'اجاره اداری و تجاری', 'short' => 'اجاره تجاری', 'icon' => 'store', 'price_mode' => 'rent', 'types' => ['دفتر کار، اتاق اداری و مطب', 'مغازه و غرفه', 'صنعتی، کشاورزی و تجاری']],
            'project' => ['title' => 'پروژه‌های ساخت‌وساز', 'short' => 'پروژه‌ها', 'icon' => 'hammer', 'price_mode' => 'project', 'types' => ['مشارکت در ساخت', 'پیش‌فروش']],
            'short' => ['title' => 'اجاره کوتاه‌مدت', 'short' => 'کوتاه‌مدت', 'icon' => 'calendar', 'price_mode' => 'short', 'types' => ['آپارتمان و سوئیت', 'ویلا و باغ', 'دفتر کار و فضای آموزشی']],
        ];
    }

    /**
     * مختصات تقریبی مرکز محله‌ها (نمایشی؛ بک‌اند: lat/lng واقعی هر آگهی از نقشه‌ی فرم ثبت آگهی).
     *
     * @return array<string, array<string, array{0: float, 1: float}>>
     */
    protected function districtCoords(): array
    {
        return [
            'تهران' => ['سعادت‌آباد' => [35.7800, 51.3780], 'پونک' => [35.7620, 51.3330], 'ونک' => [35.7570, 51.4100], 'نیاوران' => [35.8130, 51.4700], 'جنت‌آباد' => [35.7550, 51.3000], 'تهرانپارس' => [35.7330, 51.5300], 'شهرک غرب' => [35.7580, 51.3700]],
            'مشهد' => ['وکیل‌آباد' => [36.3170, 59.5150], 'احمدآباد' => [36.2950, 59.5800], 'سجاد' => [36.3200, 59.5500], 'هاشمیه' => [36.3250, 59.5300], 'الهیه' => [36.3400, 59.5300]],
            'اصفهان' => ['چهارباغ' => [32.6500, 51.6680], 'مرداویج' => [32.6120, 51.6560], 'جلفا' => [32.6370, 51.6550], 'شیخ صدوق' => [32.6290, 51.6430]],
            'شیراز' => ['معالی‌آباد' => [29.6370, 52.4900], 'قصردشت' => [29.6280, 52.5100], 'زرهی' => [29.6200, 52.5250]],
            'تبریز' => ['ولیعصر' => [38.0630, 46.3300], 'باغمیشه' => [38.0750, 46.3500], 'رشدیه' => [38.0400, 46.3700]],
            'کرج' => ['عظیمیه' => [35.8350, 50.9900], 'گوهردشت' => [35.8150, 50.9400], 'مهرشهر' => [35.8200, 50.9000]],
            'سبزوار' => ['توحید شهر' => [36.2050, 57.6900], 'بیهق' => [36.2150, 57.6700], 'اسرار' => [36.2100, 57.6550]],
            'قم' => ['پردیسان' => [34.6200, 50.8500], 'صفاشهر' => [34.6600, 50.8800]],
            'اهواز' => ['کیانپارس' => [31.3150, 48.6950], 'گلستان' => [31.3400, 48.6600]],
            'رشت' => ['گلسار' => [37.3000, 49.5900], 'منظریه' => [37.2800, 49.6000]],
        ];
    }

    /** مرکز تقریبی هر شهر (میانگین محله‌ها) */
    protected function cityCenters(): array
    {
        return array_map(function ($d) {
            $p = array_values($d);

            return [round(array_sum(array_column($p, 0)) / count($p), 4), round(array_sum(array_column($p, 1)) / count($p), 4)];
        }, $this->districtCoords());
    }

    /** برچسب قیمت اصلی بر اساس نحوه‌ی قیمت‌گذاری */
    protected function priceLabel(string $cat): string
    {
        return ['sale' => 'قیمت کل', 'rent' => 'رهن', 'short' => 'اجاره هر شب', 'project' => 'قیمت هر متر'][$this->propertyCategories()[$cat]['price_mode'] ?? 'sale'];
    }

    protected function lf(): array
    {
        $cats = $this->propertyCategories();
        $residential = ['sale_res', 'rent_res'];
        $built = ['sale_res', 'rent_res', 'sale_com', 'rent_com'];

        return [
            'categories' => array_map(fn ($c) => $c['title'], $cats),
            'short_titles' => array_map(fn ($c) => $c['short'], $cats),
            'icons' => array_map(fn ($c) => $c['icon'], $cats),
            // نوع ملک وابسته به دسته (جایگزین برند)
            'types' => array_map(fn ($c) => $c['types'], $cats),
            'cities' => ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'],
            // محله وابسته به شهر (جایگزین مدل)
            'districts' => [
                'تهران' => ['سعادت‌آباد', 'پونک', 'ونک', 'نیاوران', 'جنت‌آباد', 'تهرانپارس', 'شهرک غرب'],
                'مشهد' => ['وکیل‌آباد', 'احمدآباد', 'سجاد', 'هاشمیه', 'الهیه'],
                'اصفهان' => ['چهارباغ', 'مرداویج', 'جلفا', 'شیخ صدوق'],
                'شیراز' => ['معالی‌آباد', 'قصردشت', 'زرهی'],
                'تبریز' => ['ولیعصر', 'باغمیشه', 'رشدیه'],
                'کرج' => ['عظیمیه', 'گوهردشت', 'مهرشهر'],
                'سبزوار' => ['توحید شهر', 'بیهق', 'اسرار'],
                'قم' => ['پردیسان', 'صفاشهر'],
                'اهواز' => ['کیانپارس', 'گلستان'],
                'رشت' => ['گلسار', 'منظریه'],
            ],
            'district_coords' => $this->districtCoords(),
            'city_centers' => $this->cityCenters(),
            'price_labels' => array_map(fn ($c) => ['sale' => 'قیمت کل', 'rent' => 'رهن', 'short' => 'اجاره هر شب', 'project' => 'قیمت هر متر'][$c['price_mode']], $cats) + ['' => 'قیمت'],
            'max_price' => ['sale_res' => 100000000000, 'rent_res' => 5000000000, 'sale_com' => 150000000000, 'rent_com' => 10000000000, 'project' => 300000000, 'short' => 20000000, '' => 150000000000],
            // فیلدهای مشخصات؛ cats = دسته‌هایی که فیلد برایشان نمایش داده می‌شود (خالی = همه)
            'specs' => [
                ['name' => 'area_min', 'label' => 'متراژ از (متر)', 'type' => 'number', 'half' => true, 'ph' => '۵۰'],
                ['name' => 'area_max', 'label' => 'متراژ تا (متر)', 'type' => 'number', 'half' => true, 'ph' => '۵۰۰'],
                ['name' => 'rent_max', 'label' => 'حداکثر اجاره ماهانه (تومان)', 'type' => 'number', 'ph' => '۳۰,۰۰۰,۰۰۰', 'cats' => ['rent_res', 'rent_com']],
                ['name' => 'rooms', 'label' => 'تعداد اتاق', 'type' => 'select', 'half' => true, 'cats' => ['sale_res', 'rent_res', 'short'], 'options' => ['بدون اتاق', '۱', '۲', '۳', '۴ و بیشتر']],
                ['name' => 'year_from', 'label' => 'ساخت از سال', 'type' => 'year', 'half' => true, 'cats' => $built],
                ['name' => 'deed', 'label' => 'نوع سند', 'type' => 'select', 'half' => true, 'cats' => ['sale_res', 'sale_com', 'project'], 'options' => ['تک‌برگ', 'منگوله‌دار', 'قولنامه‌ای', 'اوقافی']],
                ['name' => 'stage', 'label' => 'مرحله‌ی ساخت', 'type' => 'select', 'half' => true, 'cats' => ['project'], 'options' => ['پروانه ساخت', 'فونداسیون', 'سفت‌کاری', 'نازک‌کاری', 'آماده تحویل']],
                ['name' => 'capacity', 'label' => 'ظرفیت (حداقل نفر)', 'type' => 'select', 'half' => true, 'cats' => ['short'], 'options' => ['۲', '۴', '۶', '۸', '۱۰']],
                ['name' => 'parking', 'label' => 'پارکینگ', 'type' => 'check', 'half' => true, 'cats' => $built],
                ['name' => 'elevator', 'label' => 'آسانسور', 'type' => 'check', 'half' => true, 'cats' => $built],
                ['name' => 'storage', 'label' => 'انباری', 'type' => 'check', 'half' => true, 'cats' => $residential],
            ],
        ];
    }

    protected function fa($s)
    {
        return strtr((string) $s, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    protected function digits($v)
    {
        return (int) preg_replace('/\D/', '', strtr((string) $v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']));
    }

    protected function human($n)
    {
        if ($n >= 1000000000) {
            return $this->fa(rtrim(rtrim(number_format($n / 1e9, 1), '0'), '.')).' میلیارد';
        }
        if ($n >= 1000000) {
            return $this->fa(number_format($n / 1e6, 0)).' میلیون';
        }

        return $this->fa(number_format($n));
    }

    /** خلاصه‌ی مشخصات برای کارت‌ها (متراژ، اتاق، سال ساخت یا ویژگی دسته) */
    protected function listingExtra(array $r): ?string
    {
        return match ($r['cat']) {
            'project' => $r['stage'],
            'short' => 'تا '.$this->fa($r['capacity']).' نفر',
            default => $r['parking'] ? 'پارکینگ' : null,
        };
    }

    /** آیتم کارت آگهی از ردیف داده */
    protected function listingCardItem(array $r): array
    {
        return [
            'id' => $r['id'], 'title' => $r['title'], 'url' => route('listings.show', $r['id']), 'image' => $r['image'],
            'price' => $r['price'], 'rent' => $r['rent'], 'price_label' => $this->priceLabel($r['cat']), 'old_price' => $r['old'],
            'area' => $r['area'], 'rooms' => $r['rooms'], 'year' => $r['cat'] === 'project' ? null : $r['year'], 'extra' => $this->listingExtra($r),
            'agency' => $r['agency'], 'verified' => true, 'location' => $r['location'], 'posted' => $r['posted'], 'favorited' => false,
            'sale_type' => $r['sale'], 'featured' => $r['featured'], 'agency_id' => $r['agency_id'], 'category' => $r['cat'],
            'lat' => $r['lat'], 'lng' => $r['lng'], 'city' => $r['city'],
        ];
    }

    protected function listingStore()
    {
        // cat, type, title, price(رهن/قیمت کل/هر شب/هر متر), rent(ماهانه), area, rooms, year, floor, city, district, agency, agency_id, image, deed, sale, featured, old
        $base = [
            ['sale_res', 'آپارتمان', 'آپارتمان ۱۲۰ متری نوساز', 18500000000, null, 120, 2, 1402, 4, 'تهران', 'سعادت‌آباد', 'املاک پارسیان', 8, 'agency-stock-1.jpg', 'تک‌برگ', 'instant', true, 19800000000],
            ['sale_res', 'خانه و ویلا', 'ویلای دوبلکس با استخر', 42000000000, null, 380, 4, 1398, 0, 'تهران', 'نیاوران', 'املاک الهیه', 22, 'agency-stock-2.jpg', 'تک‌برگ', null, false, null],
            ['sale_res', 'زمین و کلنگی', 'خانه کلنگی ۲۴۰ متر شمالی', 21000000000, null, 240, 3, 1365, 0, 'مشهد', 'احمدآباد', 'املاک آریا', 24, 'detail-main.jpg', 'منگوله‌دار', 'auction', false, null],
            ['sale_res', 'آپارتمان', 'آپارتمان ۸۵ متری فول امکانات', 6900000000, null, 85, 2, 1400, 3, 'مشهد', 'وکیل‌آباد', 'املاک آریا', 24, 'detail-t1.jpg', 'تک‌برگ', null, false, null],
            ['rent_res', 'آپارتمان', 'اجاره آپارتمان ۹۰ متری', 600000000, 25000000, 90, 2, 1399, 2, 'تهران', 'پونک', 'املاک پارسیان', 8, 'detail-t2.jpg', null, 'instant', true, null],
            ['rent_res', 'آپارتمان', 'رهن کامل آپارتمان ۷۰ متری', 1500000000, 0, 70, 1, 1395, 5, 'کرج', 'عظیمیه', 'املاک البرز', 25, 'detail-t3.jpg', null, null, false, null],
            ['rent_res', 'خانه و ویلا', 'اجاره خانه ویلایی حیاط‌دار', 400000000, 18000000, 180, 3, 1390, 0, 'اصفهان', 'جلفا', 'املاک نقش جهان', 26, 'agency-stock-3.jpg', null, null, false, null],
            ['sale_com', 'مغازه و غرفه', 'مغازه ۳۵ متری بر خیابان اصلی', 9500000000, null, 35, 0, 1397, 0, 'تهران', 'ونک', 'املاک پارسیان', 8, 'agency-thumb-1.jpg', 'تک‌برگ', 'instant', true, 10200000000],
            ['sale_com', 'دفتر کار، اتاق اداری و مطب', 'دفتر اداری ۶۰ متری موقعیت اداری', 7800000000, null, 60, 2, 1401, 6, 'شیراز', 'معالی‌آباد', 'املاک ارم', 27, 'agency-thumb-2.jpg', 'تک‌برگ', 'auction', false, null],
            ['rent_com', 'دفتر کار، اتاق اداری و مطب', 'اجاره مطب ۷۵ متری', 300000000, 40000000, 75, 3, 1400, 2, 'تبریز', 'ولیعصر', 'املاک ائل‌گلی', 21, 'agency-thumb-3.jpg', null, null, false, null],
            ['rent_com', 'صنعتی، کشاورزی و تجاری', 'اجاره سوله ۱۲۰۰ متری', 1000000000, 90000000, 1200, 0, 1394, 0, 'کرج', 'گوهردشت', 'املاک البرز', 25, 'agency-cover.jpg', null, null, false, null],
            ['project', 'پیش‌فروش', 'پیش‌فروش برج مسکونی ۱۴ طبقه', 145000000, null, 110, 2, 1405, 0, 'تهران', 'جنت‌آباد', 'املاک الهیه', 22, 'hero.jpg', 'تک‌برگ', 'instant', true, null],
            ['project', 'مشارکت در ساخت', 'مشارکت در ساخت زمین ۵۰۰ متری', 95000000, null, 500, 0, 1405, 0, 'مشهد', 'الهیه', 'املاک آریا', 24, 'banner-agencies-1.jpg', 'تک‌برگ', null, false, null],
            ['short', 'ویلا و باغ', 'ویلای ساحلی اجاره روزانه', 6500000, null, 220, 3, 1399, 0, 'رشت', 'گلسار', 'املاک گیلان', 28, 'banner-agencies-2.jpg', null, 'instant', true, 7500000],
            ['short', 'آپارتمان و سوئیت', 'سوئیت مبله نزدیک حرم', 1800000, null, 55, 1, 1401, 3, 'مشهد', 'هاشمیه', 'املاک آریا', 24, 'detail-banner.jpg', null, null, false, null],
            ['short', 'دفتر کار و فضای آموزشی', 'کلاس آموزشی ساعتی/روزانه', 2500000, null, 80, 2, 1398, 1, 'تهران', 'ونک', 'املاک پارسیان', 8, 'agency-thumb-2.png', null, null, false, null],
        ];
        $posted = ['۲ ساعت پیش', '۵ ساعت پیش', 'دیروز', '۲ روز پیش', '۳ روز پیش'];
        $stages = ['پروانه ساخت', 'فونداسیون', 'سفت‌کاری', 'نازک‌کاری', 'آماده تحویل'];
        $capacity = ['۲', '۴', '۶', '۸', '۱۰'];
        $coords = $this->districtCoords();
        $streets = ['خیابان اصلی', 'بلوار', 'کوچه‌ی ۱۲', 'خیابان دوم', 'نبش میدان'];
        $out = [];
        $id = 31000;
        $n = count($base);
        foreach ([0, 1, 2] as $v) {
            foreach ($base as $k => $r) {
                $id++;
                $i = $v * $n + $k;
                $grow = 1 + 0.04 * $v;
                $out[] = [
                    'id' => $id, 'cat' => $r[0], 'type' => $r[1], 'title' => $r[2].($v ? ' ('.['', 'موقعیت عالی', 'قابل معاوضه'][$v].')' : ''),
                    'price' => (int) round($r[3] * $grow / 100000) * 100000, 'rent' => is_null($r[4]) ? null : (int) round($r[4] * $grow / 100000) * 100000,
                    'area' => $r[5] + 5 * $v, 'rooms' => $r[6], 'year' => $r[7] - $v, 'floor' => $r[8], 'city' => $r[9], 'district' => $r[10],
                    'agency' => $r[11], 'agency_id' => $r[12], 'image' => $this->img($r[13]), 'deed' => $r[14], 'sale' => $r[15], 'featured' => $v ? false : $r[16], 'old' => $v ? null : $r[17],
                    'parking' => $i % 3 !== 2, 'elevator' => $r[8] > 1 || $i % 2 === 0, 'storage' => $i % 4 !== 3,
                    'stage' => $stages[$i % 5], 'capacity' => $capacity[$i % 5], 'posted' => $posted[$i % 5], 'location' => $r[9].'، '.$r[10],
                    // مختصات: مرکز محله + جابه‌جایی کوچک ثابت (نمایشی)
                    'lat' => round($coords[$r[9]][$r[10]][0] + (($id * 37) % 13 - 6) * 0.0011, 6), 'lng' => round($coords[$r[9]][$r[10]][1] + (($id * 53) % 13 - 6) * 0.0013, 6),
                    'address' => $r[9].'، '.$r[10].'، '.$streets[$i % 5].'، پلاک '.$this->fa(10 + $i * 3),
                ];
            }
        }

        return $out;
    }

    /** تعداد اتاق مطابق گزینه‌ی فیلتر؟ */
    protected function roomsMatch(int $rooms, string $opt): bool
    {
        if ($opt === 'بدون اتاق') {
            return $rooms === 0;
        }
        $n = $this->digits($opt);

        return str_contains($opt, 'بیشتر') ? $rooms >= $n : $rooms === $n;
    }

    protected function listingFilter(array $rows, array $q)
    {
        $c = collect($rows);
        if (! empty($q['q'])) {
            $t = trim($q['q']);
            $c = $c->filter(fn ($r) => mb_stripos($r['title'].' '.$r['type'].' '.$r['agency'].' '.$r['city'].' '.$r['district'], $t) !== false);
        }
        foreach (['category' => 'cat', 'ptype' => 'type', 'city' => 'city', 'district' => 'district', 'deed' => 'deed', 'stage' => 'stage'] as $p => $f) {
            if (! empty($q[$p])) {
                $c = $c->filter(fn ($r) => $r[$f] === $q[$p]);
            }
        }
        foreach (['parking', 'elevator', 'storage'] as $p) {
            if (! empty($q[$p])) {
                $c = $c->filter(fn ($r) => $r[$p]);
            }
        }
        if (! empty($q['rooms'])) {
            $c = $c->filter(fn ($r) => $this->roomsMatch($r['rooms'], $q['rooms']));
        }
        if (! empty($q['capacity'])) {
            $c = $c->filter(fn ($r) => $this->digits($r['capacity']) >= $this->digits($q['capacity']));
        }
        if (! empty($q['price_min'])) {
            $c = $c->filter(fn ($r) => $r['price'] >= $this->digits($q['price_min']));
        }
        if (! empty($q['price_max'])) {
            $c = $c->filter(fn ($r) => $r['price'] <= $this->digits($q['price_max']));
        }
        if (! empty($q['rent_max'])) {
            $c = $c->filter(fn ($r) => ! is_null($r['rent']) && $r['rent'] <= $this->digits($q['rent_max']));
        }
        if (! empty($q['area_min'])) {
            $c = $c->filter(fn ($r) => $r['area'] >= $this->digits($q['area_min']));
        }
        if (! empty($q['area_max'])) {
            $c = $c->filter(fn ($r) => $r['area'] <= $this->digits($q['area_max']));
        }
        if (! empty($q['year_from'])) {
            $c = $c->filter(fn ($r) => $r['year'] >= $this->digits($q['year_from']));
        }
        if (! empty($q['with_photo'])) {
            $c = $c->filter(fn ($r) => ! empty($r['image']));
        }
        if (! empty($q['sale_type'])) {
            $c = $c->filter(fn ($r) => $r['sale'] === $q['sale_type']);
        }
        if (! empty($q['agency'])) {
            $c = $c->filter(fn ($r) => (int) $r['agency_id'] === (int) $q['agency']);
        }
        if (! empty($q['mine'])) {
            $c = $c->filter(fn ($r) => (int) $r['agency_id'] === 8);
        }
        $sort = $q['sort'] ?? 'new';
        $c = match ($sort) {
            'cheap' => $c->sortBy('price'),
            'expensive' => $c->sortByDesc('price'),
            'big' => $c->sortByDesc('area'),
            'new_build' => $c->sortByDesc('year'),
            default => $c,
        };

        return $c->values();
    }

    protected function listingPage(string $mode)
    {
        $lf = $this->lf();
        $q = request()->query();
        $embed = ! empty($q['embed']);                       // داخل لایه‌ی جست‌وجو: کادر جست‌وجوی خودش را ندارد
        unset($q['embed']);
        if (isset($q['type']) && in_array($q['type'], ['instant', 'auction'], true)) {
            $q['sale_type'] = $q['type'];
            unset($q['type']);
        } // لینک «مشاهده همه» فوری / مزایده
        $q = array_filter($q, fn ($v) => $v !== null && $v !== '');
        $name = $mode === 'search' ? 'search' : 'listings.index';
        $all = $this->listingFilter($this->listingStore(), $q);
        $total = $all->count();
        $perPage = 6;
        $page = max(1, (int) ($q['page'] ?? 1));
        $items = $all->slice(0, $page * $perPage)->map(fn ($r) => $this->listingCardItem($r))->values()->all();
        $without = fn (array $keys) => route($name, array_diff_key(array_diff_key($q, array_flip($keys)), ['page' => 1]));
        // چیپ‌های فیلتر فعال (دسته در تب‌ها دیده می‌شود)
        $chips = [];
        $add = function (string $label, array $keys) use (&$chips, $without) {
            $chips[] = ['label' => $label, 'url' => $without($keys)];
        };
        foreach (['ptype', 'city', 'district', 'deed', 'stage'] as $k) {
            if (! empty($q[$k])) {
                $add($q[$k], [$k]);
            }
        }
        foreach (['parking' => 'پارکینگ', 'elevator' => 'آسانسور', 'storage' => 'انباری'] as $k => $t) {
            if (! empty($q[$k])) {
                $add($t, [$k]);
            }
        }
        if (! empty($q['rooms'])) {
            $add($q['rooms'] === 'بدون اتاق' ? 'بدون اتاق' : $q['rooms'].' اتاق', ['rooms']);
        }
        if (! empty($q['capacity'])) {
            $add('حداقل '.$q['capacity'].' نفر', ['capacity']);
        }
        $priceName = $lf['price_labels'][$q['category'] ?? ''] ?? 'قیمت';
        if (! empty($q['price_min']) || ! empty($q['price_max'])) {
            $add($priceName.' '.(! empty($q['price_min']) ? 'از '.$this->human($this->digits($q['price_min'])) : '').(! empty($q['price_max']) ? ' تا '.$this->human($this->digits($q['price_max'])) : ''), ['price_min', 'price_max']);
        }
        if (! empty($q['rent_max'])) {
            $add('اجاره تا '.$this->human($this->digits($q['rent_max'])), ['rent_max']);
        }
        if (! empty($q['area_min']) || ! empty($q['area_max'])) {
            $add('متراژ '.($q['area_min'] ?? '…').' تا '.($q['area_max'] ?? '…'), ['area_min', 'area_max']);
        }
        if (! empty($q['year_from'])) {
            $add('ساخت از '.$q['year_from'], ['year_from']);
        }
        if (! empty($q['with_photo'])) {
            $add('فقط دارای عکس', ['with_photo']);
        }
        if (! empty($q['sale_type'])) {
            $add($q['sale_type'] === 'auction' ? 'مزایده' : 'فوری', ['sale_type']);
        }
        if (! empty($q['agency'])) {
            $add('یک آژانس', ['agency']);
        }
        if (! empty($q['mine'])) {
            $add('آگهی‌های آژانس من', ['mine']);
        }
        $catBound = ['category', 'ptype', 'rooms', 'rent_max', 'deed', 'stage', 'capacity', 'parking', 'elevator', 'storage', 'year_from', 'price_min', 'price_max', 'page'];
        $tabs = [];
        foreach ($lf['short_titles'] as $k => $t) {
            $on = ($q['category'] ?? '') === $k;
            $tabs[] = ['title' => $t, 'on' => $on, 'url' => route($name, array_merge(array_diff_key($q, array_flip($catBound)), $on ? [] : ['category' => $k]))];
        }
        $sorts = ['new' => 'جدیدترین', 'cheap' => 'ارزان‌ترین', 'expensive' => 'گران‌ترین', 'big' => 'بیشترین متراژ', 'new_build' => 'نوسازترین'];
        $nextQ = array_merge($q, ['page' => $page + 1]);
        $data = [
            'embed' => $embed, 'mode' => $mode, 'q' => $q['q'] ?? '', 'query' => $q, 'items' => $items, 'total' => $total, 'chips' => $chips, 'tabs' => $tabs, 'sorts' => $sorts, 'sort' => $q['sort'] ?? 'new',
            'filter_count' => count($chips),
            'filters_url' => route('listings.filters', $q), 'clear_url' => route($name, array_intersect_key($q, array_flip(['q', 'category']))),
            'has_more' => $total > $page * $perPage, 'next_url' => route($name, $nextQ), 'lf' => $lf,
            'title' => $mode === 'search' ? 'جست‌وجوی ملک' : 'آگهی‌ها', 'subtitle' => $mode === 'search' ? 'آگهی‌های مورد نظر خودتان را پیدا کنید.' : 'همه آگهی‌های فعال',
            'city' => 'سبزوار',
        ];

        // درخواست AJAX (مرورگر آگهی در js/listing-browser.js): فقط HTML مرورگر، بدون صفحه‌ی کامل
        return $this->varied(request()->ajax() ? 'pages.listings._browser' : 'pages.listings.index', $data);
    }
}
