<?php

namespace App\Http\Controllers\Concerns;

/** داده و فیلتر نمایشی آگهی‌ها (مشترک بین جست‌وجو، رادار و ...) */
trait ListingData
{
    use MockAsset;

    protected function lf(): array
    {
        return [
            'categories' => ['car' => 'سواری', 'heavy' => 'سنگین', 'bike' => 'موتورسیکلت', 'bicycle' => 'دوچرخه'],
            'brands' => [
                'car' => ['پژو' => ['۲۰۷', '۲۰۶', 'پارس'], 'سمند' => ['LX', 'سورن'], 'تیبا' => ['۱', '۲'], 'هیوندای' => ['النترا', 'سوناتا'], 'کیا' => ['سراتو', 'اپتیما']],
                'heavy' => ['ولوو' => ['FH500', 'FH460', 'FM'], 'بنز' => ['اکتروس', 'آتگو'], 'اسکانیا' => ['R450', 'G410']],
                'bike' => ['یاماها' => ['NMAX', 'MT-07'], 'هوندا' => ['CBR 250', 'PCX'], 'سوزوکی' => ['جیکسر']],
                'bicycle' => ['جاینت' => ['ATX', 'Escape'], 'مریدا' => ['Big Nine', 'Scultura'], 'اسکات' => ['Aspect', 'Speedster'], 'المپیا' => ['۲۶ کوهستان', '۲۸ شهری']],
            ],
            'cities' => ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'],
            'max_price' => ['car' => 5000000000, 'heavy' => 15000000000, 'bike' => 1000000000, 'bicycle' => 200000000, '' => 15000000000],
            // فیلدهای فنی؛ cats = دسته‌هایی که فیلد برایشان نمایش داده می‌شود (خالی = همه)
            'specs' => [
                ['name' => 'year_from', 'label' => 'از سال', 'type' => 'year', 'half' => true],
                ['name' => 'year_to', 'label' => 'تا سال', 'type' => 'year', 'half' => true],
                ['name' => 'km_max', 'label' => 'حداکثر کارکرد (کیلومتر)', 'type' => 'number', 'ph' => '۵۰۰,۰۰۰', 'cats' => ['car', 'heavy', 'bike', 'bicycle']],
                ['name' => 'axle', 'label' => 'آرایش محور', 'type' => 'select', 'half' => true, 'cats' => ['heavy'], 'options' => ['۴×۲', '۶×۲', '۶×۴', '۸×۴']],
                ['name' => 'gearbox', 'label' => 'گیربکس', 'type' => 'select', 'half' => true, 'cats' => ['car', 'heavy'], 'options' => ['اتوماتیک', 'دنده‌ای']],
                ['name' => 'weight', 'label' => 'وزن ناخالص (تن)', 'type' => 'select', 'half' => true, 'cats' => ['heavy'], 'options' => ['۱۸', '۲۶', '۴۴']],
                ['name' => 'fuel', 'label' => 'سوخت', 'type' => 'select', 'half' => true, 'cats' => ['car', 'heavy'], 'options' => ['بنزین', 'دیزل', 'دوگانه‌سوز', 'هیبرید', 'برقی']],
                ['name' => 'color', 'label' => 'رنگ', 'type' => 'select', 'half' => true, 'cats' => ['car', 'bike', 'bicycle'], 'options' => ['سفید', 'مشکی', 'نقره‌ای', 'خاکستری', 'آبی', 'قرمز']],
                ['name' => 'body', 'label' => 'وضعیت بدنه', 'type' => 'select', 'half' => true, 'cats' => ['car'], 'options' => ['سالم و بی‌رنگ', 'رنگ‌شده', 'ضربه‌خورده']],
                ['name' => 'cc', 'label' => 'حجم موتور (سی‌سی)', 'type' => 'select', 'half' => true, 'cats' => ['bike'], 'options' => ['۱۲۵', '۱۵۰', '۲۵۰', '۴۰۰']],
                ['name' => 'type', 'label' => 'نوع موتور', 'type' => 'select', 'half' => true, 'cats' => ['bike'], 'options' => ['اسکوتر', 'اسپرت', 'کلاسیک', 'کویر']],
                ['name' => 'btype', 'label' => 'نوع دوچرخه', 'type' => 'select', 'half' => true, 'cats' => ['bicycle'], 'options' => ['کوهستان', 'جاده', 'شهری', 'برقی']],
                ['name' => 'wheel', 'label' => 'سایز طوقه (اینچ)', 'type' => 'select', 'half' => true, 'cats' => ['bicycle'], 'options' => ['۲۶', '۲۷٫۵', '۲۹']],
            ],
        ];
    }

    protected function fa($s)
    {
        return strtr((string)$s, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    protected function digits($v)
    {
        return (int)preg_replace('/\D/', '', strtr((string)$v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']));
    }

    protected function human($n)
    {
        if ($n >= 1000000000) return $this->fa(rtrim(rtrim(number_format($n / 1e9, 1), '0'), '.')) . ' میلیارد';
        if ($n >= 1000000) return $this->fa(number_format($n / 1e6, 0)) . ' میلیون';
        return $this->fa(number_format($n));
    }

    protected function listingStore()
    {
        // cat, brand, model, title, price, year, km, gearbox, fuel, city, dealer, dealer_id, image, color, sale, featured, old
        $base = [
            ['heavy', 'ولوو', 'FH500', 'ولوو FH500 کشنده', 9800000000, 1399, 320000, 'اتوماتیک', 'دیزل', 'تهران', 'آریا خودروهای تجاری', 22, 'dealer-stock-2.jpg', 'آبی', 'instant', true, 10000000000],
            ['heavy', 'ولوو', 'FH460', 'ولوو FH460 کشنده', 8450000000, 1398, 410000, 'دنده‌ای', 'دیزل', 'تهران', 'نمایشگاه پارس دیزل', 23, 'truck-fh.jpg', 'سفید', null, false, null],
            ['heavy', 'ولوو', 'FH500', 'ولوو FH500 فول آپشن', 12200000000, 1401, 180000, 'اتوماتیک', 'دیزل', 'مشهد', 'نمایشگاه آریا', 24, 'truck-fh-a.jpg', 'آبی', null, false, null],
            ['heavy', 'بنز', 'اکتروس', 'بنز اکتروس ۱۸۴۳', 9100000000, 1400, 260000, 'اتوماتیک', 'دیزل', 'تهران', 'آریا خودروهای تجاری', 22, 'dealer-stock-2.jpg', 'سفید', 'auction', false, null],
            ['car', 'پژو', '۲۰۷', 'پژو ۲۰۷ اتوماتیک', 985000000, 1402, 24000, 'اتوماتیک', 'بنزین', 'تهران', 'اتوگالری هالیوود', 8, 'dealer-stock-1.jpg', 'سفید', 'instant', true, 1070000000],
            ['car', 'پژو', '۲۰۶', 'پژو ۲۰۶ تیپ ۲', 540000000, 1400, 61000, 'دنده‌ای', 'بنزین', 'کرج', 'اتوگالری هالیوود', 8, 'car-207.jpg', 'نقره‌ای', null, false, null],
            ['car', 'سمند', 'LX', 'سمند LX EF7', 520000000, 1401, 41000, 'دنده‌ای', 'دوگانه‌سوز', 'اصفهان', 'نمایشگاه پارس', 25, 'car-207-a.jpg', 'سفید', 'auction', false, null],
            ['car', 'هیوندای', 'النترا', 'هیوندای النترا', 1850000000, 1400, 62000, 'اتوماتیک', 'بنزین', 'مشهد', 'نمایشگاه نگین', 26, 'detail-main.jpg', 'مشکی', null, false, null],
            ['car', 'کیا', 'سراتو', 'کیا سراتو', 1650000000, 1401, 48000, 'اتوماتیک', 'بنزین', 'تهران', 'اتوگالری هالیوود', 8, 'dealer-stock-1.jpg', 'خاکستری', 'instant', false, 1720000000],
            ['bike', 'یاماها', 'NMAX', 'یاماها NMAX 155', 385000000, 1403, 4500, '', 'بنزین', 'مشهد', 'آریا موتور', 21, 'dealer-stock-3.jpg', 'آبی', 'instant', true, 420000000],
            ['bike', 'هوندا', 'CBR 250', 'موتور هوندا CBR 250', 390000000, 1399, 18000, '', 'بنزین', 'تبریز', 'موتور سیتی', 27, 'yamaha.jpg', 'قرمز', null, false, null],
            ['bike', 'سوزوکی', 'جیکسر', 'سوزوکی جیکسر ۱۵۰', 210000000, 1400, 22000, '', 'بنزین', 'تهران', 'آریا موتور', 21, 'dealer-stock-3.jpg', 'مشکی', null, false, null],
            ['bicycle', 'جاینت', 'ATX', 'دوچرخه کوهستان جاینت ATX', 28000000, 1403, 800, '', '', 'تهران', 'آریا موتور', 21, 'dealer-stock-3.jpg', 'مشکی', 'instant', true, 31000000],
            ['bicycle', 'مریدا', 'Big Nine', 'دوچرخه مریدا Big Nine', 46000000, 1402, 2500, '', '', 'مشهد', 'موتور سیتی', 27, 'yamaha.jpg', 'قرمز', null, false, null],
            ['bicycle', 'اسکات', 'Speedster', 'دوچرخه جاده اسکات Speedster', 72000000, 1401, 4200, '', '', 'تهران', 'آریا موتور', 21, 'dealer-stock-3.jpg', 'آبی', 'auction', false, null],
        ];
        $axle = ['۴×۲', '۶×۴', '۶×۲', '۸×۴'];
        $weight = ['۱۸', '۲۶', '۴۴'];
        $body = ['سالم و بی‌رنگ', 'رنگ‌شده', 'ضربه‌خورده'];
        $cc = ['۱۲۵', '۱۵۰', '۲۵۰', '۴۰۰'];
        $types = ['اسکوتر', 'اسپرت', 'کلاسیک', 'کویر'];
        $btype = ['کوهستان', 'جاده', 'شهری', 'برقی'];
        $wheel = ['۲۶', '۲۷٫۵', '۲۹'];
        $posted = ['۲ ساعت پیش', '۵ ساعت پیش', 'دیروز', '۲ روز پیش', '۳ روز پیش'];
        $cities = ['تهران', 'مشهد', 'کرج', 'اصفهان', 'شیراز'];
        $out = [];
        $id = 31000;
        $n = count($base);
        foreach ([0, 1, 2] as $v) foreach ($base as $k => $r) {
            $id++;
            $i = $v * $n + $k;
            $price = (int)round($r[4] * (1 + 0.035 * $v) / 1000000) * 1000000;
            $out[] = ['id' => $id, 'cat' => $r[0], 'brand' => $r[1], 'model' => $r[2], 'title' => $r[3] . ($v ? ' (' . ['', 'مدل بالاتر', 'کم‌کارکرد'][$v] . ')' : ''), 'price' => $price, 'year' => $r[5] + ($v ? -1 * $v + 1 : 0), 'km' => $r[6] + 12000 * $v, 'gearbox' => $r[7], 'fuel' => $r[8],
                'city' => $v ? $cities[($i) % 5] : $r[9], 'dealer' => $r[10], 'dealer_id' => $r[11], 'image' => $this->img($r[12]), 'color' => $r[13], 'sale' => $r[14], 'featured' => $v ? false : $r[15], 'old' => $v ? null : $r[16],
                'axle' => $axle[$i % 4], 'weight' => $weight[$i % 3], 'body' => $body[$i % 3], 'cc' => $cc[$i % 4], 'type' => $types[$i % 4], 'btype' => $btype[$i % 4], 'wheel' => $wheel[$i % 3], 'posted' => $posted[$i % 5], 'location' => ($v ? $cities[($i) % 5] : $r[9]) . '، ' . ['سعادت‌آباد', 'جاده مخصوص', 'بلوار وکیل‌آباد', 'چهارراه ولیعصر'][$i % 4]];
        }
        return $out;
    }

    protected function listingFilter(array $rows, array $q)
    {
        $c = collect($rows);
        if (!empty($q['q'])) {
            $t = trim($q['q']);
            $c = $c->filter(fn($r) => mb_stripos($r['title'] . ' ' . $r['brand'] . ' ' . $r['model'] . ' ' . $r['dealer'] . ' ' . $r['city'], $t) !== false);
        }
        foreach (['category' => 'cat', 'brand' => 'brand', 'model' => 'model', 'city' => 'city', 'gearbox' => 'gearbox', 'fuel' => 'fuel', 'axle' => 'axle', 'weight' => 'weight', 'color' => 'color', 'body' => 'body', 'cc' => 'cc', 'type' => 'type', 'btype' => 'btype', 'wheel' => 'wheel'] as $p => $f)
            if (!empty($q[$p])) $c = $c->filter(fn($r) => $r[$f] === $q[$p]);
        if (!empty($q['price_min'])) $c = $c->filter(fn($r) => $r['price'] >= $this->digits($q['price_min']));
        if (!empty($q['price_max'])) $c = $c->filter(fn($r) => $r['price'] <= $this->digits($q['price_max']));
        if (!empty($q['year_from'])) $c = $c->filter(fn($r) => $r['year'] >= $this->digits($q['year_from']));
        if (!empty($q['year_to'])) $c = $c->filter(fn($r) => $r['year'] <= $this->digits($q['year_to']));
        if (!empty($q['km_max'])) $c = $c->filter(fn($r) => $r['km'] <= $this->digits($q['km_max']));
        if (!empty($q['with_photo'])) $c = $c->filter(fn($r) => !empty($r['image']));
        if (!empty($q['sale_type'])) $c = $c->filter(fn($r) => $r['sale'] === $q['sale_type']);
        if (!empty($q['dealer'])) $c = $c->filter(fn($r) => (int)$r['dealer_id'] === (int)$q['dealer']);
        if (!empty($q['mine'])) $c = $c->filter(fn($r) => (int)$r['dealer_id'] === 8);
        $sort = $q['sort'] ?? 'new';
        if ($sort === 'cheap') $c = $c->sortBy('price'); elseif ($sort === 'expensive') $c = $c->sortByDesc('price');
        elseif ($sort === 'low_km') $c = $c->sortBy('km');
        elseif ($sort === 'new_model') $c = $c->sortByDesc('year');
        return $c->values();
    }

    protected function listingPage(string $mode)
    {
        $lf = $this->lf();
        $q = request()->query();
        $embed = !empty($q['embed']);                       // داخل لایه‌ی جست‌وجو: کادر جست‌وجوی خودش را ندارد
        unset($q['embed']);
        if (isset($q['type']) && in_array($q['type'], ['instant', 'auction'], true)) {
            $q['sale_type'] = $q['type'];
            unset($q['type']);
        } // لینک «مشاهده همه» فروش فوری
        $q = array_filter($q, fn($v) => $v !== null && $v !== '');
        $name = $mode === 'search' ? 'search' : 'listings.index';
        $all = $this->listingFilter($this->listingStore(), $q);
        $total = $all->count();
        $perPage = 6;
        $page = max(1, (int)($q['page'] ?? 1));
        $slice = $all->slice(0, $page * $perPage);
        $items = $slice->map(fn($r) => [
            'id' => $r['id'], 'title' => $r['title'], 'url' => route('listings.show', 24817), 'image' => $r['image'], 'price' => $r['price'], 'old_price' => $r['old'], 'year' => $r['year'], 'km' => $r['km'],
            'extra' => $r['cat'] === 'heavy' ? $r['fuel'] : $r['color'], 'dealer' => $r['dealer'], 'verified' => true, 'location' => $r['location'], 'posted' => $r['posted'], 'favorited' => false,
            'sale_type' => $r['sale'], 'featured' => $r['featured'], 'dealer_id' => $r['dealer_id'], 'category' => $r['cat'],
        ])->values()->all();
        $without = fn(array $keys) => route($name, array_diff_key(array_diff_key($q, array_flip($keys)), ['page' => 1]));
        // چیپ‌های فیلتر فعال (دسته در تب‌ها دیده می‌شود)
        $chips = [];
        $add = function (string $label, array $keys) use (&$chips, $without) {
            $chips[] = ['label' => $label, 'url' => $without($keys)];
        };
        foreach (['brand', 'model', 'city', 'gearbox', 'fuel', 'axle', 'color', 'body', 'type', 'btype'] as $k) if (!empty($q[$k])) $add($q[$k], [$k]);
        if (!empty($q['weight'])) $add($q['weight'] . ' تن', ['weight']);
        if (!empty($q['cc'])) $add($q['cc'] . ' سی‌سی', ['cc']);
        if (!empty($q['wheel'])) $add('طوقه ' . $q['wheel'], ['wheel']);
        if (!empty($q['price_min']) || !empty($q['price_max'])) $add((!empty($q['price_min']) ? 'از ' . $this->human($this->digits($q['price_min'])) : '') . (!empty($q['price_max']) ? ' تا ' . $this->human($this->digits($q['price_max'])) : ''), ['price_min', 'price_max']);
        if (!empty($q['year_from']) || !empty($q['year_to'])) $add('سال ' . ($q['year_from'] ?? '…') . ' تا ' . ($q['year_to'] ?? '…'), ['year_from', 'year_to']);
        if (!empty($q['km_max'])) $add('تا ' . $this->fa(number_format($this->digits($q['km_max']))) . ' کیلومتر', ['km_max']);
        if (!empty($q['with_photo'])) $add('فقط دارای عکس', ['with_photo']);
        if (!empty($q['sale_type'])) $add($q['sale_type'] === 'instant' ? 'فروش فوری' : 'مزایده', ['sale_type']);
        if (!empty($q['dealer'])) $add('یک نمایشگاه', ['dealer']);
        if (!empty($q['mine'])) $add('آگهی‌های نمایشگاه من', ['mine']);
        $nonFilter = ['category', 'q', 'sort', 'page', 'scope'];
        $tabs = [];
        foreach ($lf['categories'] as $k => $t) {
            $on = ($q['category'] ?? '') === $k;
            $tabs[] = ['title' => $t, 'on' => $on, 'url' => route($name, array_merge(array_diff_key($q, array_flip(['category', 'brand', 'model', 'axle', 'weight', 'cc', 'type', 'btype', 'wheel', 'body', 'color', 'gearbox', 'fuel', 'page'])), $on ? [] : ['category' => $k]))];
        }
        $sorts = ['new' => 'جدیدترین', 'cheap' => 'ارزان‌ترین', 'expensive' => 'گران‌ترین', 'low_km' => 'کم‌کارکردترین', 'new_model' => 'جدیدترین مدل'];
        $nextQ = array_merge($q, ['page' => $page + 1]);
        $data = [
            'embed' => $embed, 'mode' => $mode, 'q' => $q['q'] ?? '', 'query' => $q, 'items' => $items, 'total' => $total, 'chips' => $chips, 'tabs' => $tabs, 'sorts' => $sorts, 'sort' => $q['sort'] ?? 'new',
            'filter_count' => count($chips),
            'filters_url' => route('listings.filters', $q), 'clear_url' => route($name, array_intersect_key($q, array_flip(['q', 'category']))),
            'has_more' => $total > $page * $perPage, 'next_url' => route($name, $nextQ), 'lf' => $lf,
            'title' => $mode === 'search' ? 'جست‌وجوی خودرو' : 'آگهی‌ها', 'subtitle' => $mode === 'search' ? 'آگهی‌های مورد نظر خودتان را پیدا کنید.' : 'همه آگهی‌های فعال',
            'city' => 'سبزوار',
        ];
        // درخواست AJAX (مرورگر آگهی در js/listing-browser.js): فقط HTML مرورگر، بدون صفحه‌ی کامل
        return $this->varied(request()->ajax() ? 'pages.listings._browser' : 'pages.listings.index', $data);
    }
}
