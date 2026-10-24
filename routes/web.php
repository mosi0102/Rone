<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| مسیرهای موقت فرانت (Mock)
|--------------------------------------------------------------------------
| این فایل فقط برای نمایش قالب است. بک‌اندکار هر مسیر را با کنترلر واقعی
| جایگزین می‌کند؛ نام مسیرها (route name) را تغییر ندهید چون ناوبری به آن‌ها وابسته است.
*/

$mock = fn () => [
    ['id' => 1, 'title' => 'پژو ۲۰۷ پانوراما دنده‌ای', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 685000000, 'year' => 1402, 'km' => 24000, 'city' => 'تهران', 'dealer' => 'نمایشگاه آریا', 'sale_type' => 'instant', 'favorited' => false],
    ['id' => 2, 'title' => 'سمند LX EF7', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 520000000, 'year' => 1401, 'km' => 41000, 'city' => 'اصفهان', 'dealer' => 'نمایشگاه پارس', 'sale_type' => 'auction', 'ends_at' => now()->addHours(5), 'favorited' => true],
    ['id' => 3, 'title' => 'هیوندای النترا', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 1850000000, 'year' => 1400, 'km' => 62000, 'city' => 'مشهد', 'dealer' => 'نمایشگاه نگین', 'sale_type' => null, 'favorited' => false],
    ['id' => 4, 'title' => 'کامیون بنز ۲۶۳۱', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 6200000000, 'year' => 1395, 'km' => 380000, 'city' => 'شیراز', 'dealer' => 'سنگین‌خودرو البرز', 'sale_type' => 'auction', 'ends_at' => now()->addMinutes(4), 'favorited' => false],
    ['id' => 5, 'title' => 'موتور هوندا CBR 250', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 390000000, 'year' => 1399, 'km' => 18000, 'city' => 'تبریز', 'dealer' => 'موتور سیتی', 'sale_type' => 'instant', 'favorited' => false],
    ['id' => 6, 'title' => 'تیبا ۲ صفر', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 390000000, 'year' => 1403, 'km' => 0, 'city' => 'کرج', 'dealer' => 'نمایشگاه آریا', 'sale_type' => null, 'favorited' => false],
];

Route::get('/', function () {
    $img = fn ($f) => asset('img/mock/' . $f);
    $car = route('listings.show', 24817);

    // مزایده‌های در آستانه شروع (status: scheduled|live)
    $auctions = [
        ['id' => 11, 'title' => 'مزایده پژو ۲۰۷ اتوماتیک', 'url' => route('auctions.show', 11), 'image' => $img('car-207-a.jpg'), 'price' => 1985000000, 'year' => 1402, 'km' => 24000, 'extra' => 'مشکی', 'status' => 'scheduled', 'starts_at' => now()->addHours(42)->addMinutes(23)->addSeconds(15)],
        ['id' => 12, 'title' => 'مزایده ولوو FH500 کشنده', 'url' => route('auctions.show', 12), 'image' => $img('truck-fh-a.jpg'), 'price' => 9800000000, 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'status' => 'scheduled', 'starts_at' => now()->addHours(42)->addMinutes(23)->addSeconds(15)],
    ];

    $instant = [
        ['id' => 1, 'title' => 'پژو ۲۰۷ اتوماتیک', 'url' => $car, 'image' => $img('car-207.jpg'), 'price' => 920000000, 'old_price' => 985000000, 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'dealer' => 'نمایشگاه آریا', 'verified' => true, 'location' => 'تهران، سعادت‌آباد', 'posted' => '۲ ساعت پیش', 'favorited' => false],
        ['id' => 2, 'title' => 'ولوو FH500 کشنده', 'url' => $car, 'image' => $img('truck-fh.jpg'), 'price' => 9800000000, 'old_price' => null, 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'dealer' => 'آریا خودروهای تجاری', 'verified' => true, 'location' => 'تهران، شهریار', 'posted' => '۵ ساعت پیش', 'favorited' => false],
        ['id' => 3, 'title' => 'یاماها NMAX 155', 'url' => $car, 'image' => $img('yamaha.jpg'), 'price' => 385000000, 'old_price' => 420000000, 'year' => 1403, 'km' => 4500, 'extra' => 'آبی', 'dealer' => 'آریا موتور', 'verified' => true, 'location' => 'مشهد', 'posted' => 'دیروز', 'favorited' => true],
    ];

    $latest = [
        ['id' => 21, 'title' => 'ولوو FH500 کشنده', 'url' => $car, 'image' => $img('truck-fh.jpg'), 'price' => 9800000000, 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'dealer' => 'آریا خودروهای تجاری', 'verified' => true, 'sale_type' => null],
        ['id' => 22, 'title' => 'یاماها NMAX 155', 'url' => $car, 'image' => $img('yamaha.jpg'), 'price' => 385000000, 'year' => 1403, 'km' => 4500, 'extra' => 'آبی', 'dealer' => 'آریا موتور', 'verified' => true, 'sale_type' => null],
    ];

    return view('pages.home', [
        'city'          => 'سبزوار',
        'auctions'      => $auctions,
        'auctionsCount' => 3,
        'categories'    => [
            ['slug' => 'car',   'title' => 'سواری',      'icon' => 'car',   'count' => 1240],
            ['slug' => 'heavy', 'title' => 'سنگین',      'icon' => 'truck', 'count' => 182],
            ['slug' => 'bike',  'title' => 'موتورسیکلت', 'icon' => 'bike',  'count' => 246],
        ],
        'ad'            => ['image' => $img('ad-1.jpg'), 'label' => 'تبلیغات', 'title' => 'همراه مسیر زندگی شما', 'subtitle' => 'لیزینگ • فروش انواع ماشین سنگین', 'cta' => 'بازدید از نمایشگاه', 'url' => '#'],
        'instant'       => $instant,
        'latest'        => collect($latest),
    ]);
})->name('home');

Route::get('/styleguide', fn () => view('pages.styleguide', ['items' => collect($mock())]))->name('styleguide');
Route::view('/offline', 'pages.offline')->name('offline');

/* ---------- نمایشگاه‌ها ---------- */
$cities = ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'];
$img = fn ($f) => asset('img/mock/' . $f);

Route::get('/dealers', function () use ($cities, $img) {
    $hasht = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'manager' => 'ابولفضل پناهی', 'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'cover' => $img('dealer-cover.jpg'), 'url' => route('dealers.show', 8), 'verified' => true, 'featured' => true,
        'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']]];
    return view('pages.dealers.index', [
        'city' => 'سبزوار', 'cities' => $cities,
        'banners'  => [['image' => $img('banner-dealers-1.jpg'), 'alt' => 'جزو بهترین‌ها باش؛ به‌صورت رایگان به خانواده بزرگ آروان بپیوندید', 'url' => route('dealer.join')]],
        'categories' => [
            ['slug' => 'car', 'title' => 'نمایشگاه سواری', 'icon' => 'car', 'count' => 50],
            ['slug' => 'heavy', 'title' => 'نمایشگاه سنگین', 'icon' => 'truck', 'count' => 16],
            ['slug' => 'bike', 'title' => 'نمایشگاه موتور', 'icon' => 'bike', 'count' => 23],
        ],
        'featured' => [$hasht, array_merge($hasht, ['id' => 9, 'name' => 'اتوگالری آرش', 'manager' => 'عباس آرمانجو', 'featured' => false, 'url' => route('dealers.show', 9)])],
        'latest' => collect([
            ['id' => 31, 'name' => 'اتوگالری آرش', 'manager' => 'عباس آرمانجو', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'image' => $img('dealer-thumb-1.jpg'), 'url' => route('dealers.show', 9)],
            ['id' => 32, 'name' => 'اتوگالری کرمی', 'manager' => 'یار رمضانی', 'area' => 'سبزوار، چهل متری', 'active_count' => 21, 'image' => $img('dealer-thumb-2.jpg'), 'url' => route('dealers.show', 8)],
            ['id' => 33, 'name' => 'اتوگالری علیپور', 'manager' => 'علی علیپور', 'area' => 'سبزوار، چهار راه فرودگاه', 'active_count' => 21, 'image' => $img('dealer-thumb-3.jpg'), 'url' => route('dealers.show', 8)],
        ]),
    ]);
})->name('dealers.index');

Route::get('/dealers/{id}', function ($id) use ($img) {
    $row = fn ($i, $t, $p, $y, $k, $x, $d, $im, $cat, $st = null) => ['id' => $i, 'title' => $t, 'url' => route('listings.show', 24817), 'image' => $img($im), 'price' => $p, 'year' => $y, 'km' => $k, 'extra' => $x, 'dealer' => $d, 'verified' => true, 'sale_type' => $st, 'category' => $cat];
    return view('pages.dealers.show', [
        'dealer' => (object) [
            'id' => $id, 'name' => 'اتوگالری هالیوود', 'short_name' => 'گالری هالیوود', 'manager' => 'ابولفضل پناهی', 'verified' => true, 'cover' => $img('dealer-cover.jpg'),
            'video' => asset('video/intro.mp4'), // آدرس ویدیو معرفی (mp4)؛ خالی = بخش ویدیو نمایش داده نمی‌شود
            'about' => 'فروش تخصصی سواری، سنگین و موتورسیکلت در شعب آریا؛ بازدید حضوری و کارشناسی مستقل با هماهنگی.',
            'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰',
            'map_url' => 'https://maps.google.com/?q=35.78,51.37', 'active_count' => 12,
            'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'موجودی فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']],
        ],
        'tabs' => [['slug' => 'all', 'title' => 'همه', 'count' => 12], ['slug' => 'car', 'title' => 'سواری', 'count' => 7], ['slug' => 'heavy', 'title' => 'سنگین', 'count' => 2], ['slug' => 'bike', 'title' => 'موتور', 'count' => 3]],
        'stock' => [
            $row(1, 'پژو ۲۰۷ اتوماتیک', 985000000, 1402, 24000, 'سفید', 'نمایشگاه آریا', 'dealer-stock-1.jpg', 'car', 'instant'),
            $row(2, 'ولوو FH500 کشنده', 9800000000, 1399, 320000, 'دیزل', 'آریا خودروهای تجاری', 'dealer-stock-2.jpg', 'heavy'),
            $row(3, 'یاماها NMAX 155', 385000000, 1403, 4500, 'آبی', 'آریا موتور', 'dealer-stock-3.jpg', 'bike', 'auction'),
        ],
    ]);
})->whereNumber('id')->name('dealers.show');

/* ---------- جزئیات آگهی ---------- */
Route::get('/listings/{id}', function ($id) use ($img) {
    return view('pages.listings.show', [
        'wallet' => 120000000, // موجودی کیف پول کاربر (برای تست حالت کمبود موجودی عدد را کم کنید)
        'car' => (object) [
            'id' => $id, 'code' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'subtitle' => 'مدل ۱۴۰۲ • سقف شیشه‌ای • بدون رنگ', 'sale_type' => 'instant',
            'price' => 985000000, 'price_note' => 'قیمت نقدی • قابل مذاکره', 'location' => 'تهران، سعادت‌آباد', 'views' => 326, 'favorited' => false,
            'deposit_percent' => 2, 'deposit_amount' => 20000000, 'service_fee' => 200000,
            'images' => [
                ['src' => $img('detail-main.jpg'), 'thumb' => $img('detail-t1.jpg')],
                ['src' => $img('detail-t1.jpg'), 'thumb' => $img('detail-t2.jpg')],
                ['src' => $img('detail-t2.jpg'), 'thumb' => $img('detail-t3.jpg')],
                ['src' => $img('detail-t3.jpg'), 'thumb' => $img('detail-t3.jpg')],
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
            'dealer' => ['id' => 8, 'name' => 'اتوگالری هالیوود', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'verified' => true, 'cover' => $img('detail-banner.jpg'), 'url' => route('dealers.show', 8)],
        ],
        'similar' => [
            ['id' => 41, 'title' => 'پژو ۲۰۷ پانوراما', 'url' => route('listings.show', 24817), 'image' => $img('car-207.jpg'), 'price' => 940000000, 'year' => 1401, 'km' => 52000, 'extra' => 'مشکی', 'dealer' => 'نمایشگاه پارس', 'verified' => true, 'sale_type' => null],
        ],
    ]);
})->whereNumber('id')->name('listings.show');


/*
|--------------------------------------------------------------------------
| نقش کاربر (فقط نمایشی)
|--------------------------------------------------------------------------
| متغیر سراسری $auth در همه Viewها در دسترس است. بک‌اندکار همین ساختار را با کاربر واقعی از
| یک View Composer واقعی پر کند و این بلوک را حذف کند.
| پیش‌نمایش:  ?as=guest | user | dealer      و برای نمایشگاه‌دار:  ?mode=buyer | dealer
| یک حساب نمایشگاه‌دار هم خریدار است (بیعانه/مزایده) و هم فروشنده؛ «mode» فقط ناوبری را تعیین می‌کند.
*/
View::composer('*', function ($view) {
    static $auth = null;
    if (!$auth) {
        $as = request('as') ?: session('demo_as', 'user');
        session(['demo_as' => $as]);
        $mode = request('mode') ?: session('demo_mode', 'buyer');
        if ($as !== 'dealer') $mode = 'buyer';
        session(['demo_mode' => $mode]);
        $dealer = $as === 'dealer' ? (object) ['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true, 'plan_days_left' => 41, 'plan_total_days' => 90, 'plan_name' => 'اشتراک ۳ ماهه'] : null;
        $auth = (object) [
            'check'       => $as !== 'guest',
            'name'        => $as === 'dealer' ? 'ابولفضل پناهی' : 'علی محمدی',
            'phone'       => '۰۹۱۲۳۴۵۶۷۸۹',
            'wallet'      => 120000000,
            'is_dealer'   => (bool) $dealer,
            'mode'        => $mode,
            'dealer_mode' => $dealer && $mode === 'dealer',
            'dealer'      => $dealer,
        ];
    }
    $view->with('auth', $auth);
});

Route::get('/switch-mode/{mode}', function ($mode) {
    $mode = $mode === 'dealer' ? 'dealer' : 'buyer';
    session(['demo_mode' => $mode]);
    return redirect($mode === 'dealer' ? route('panel') : route('home'));
})->name('mode.switch');

Route::get('/login', fn () => view('pages.auth.login', ['next' => request('next', route('home'))]))->name('login');
Route::get('/logout', fn () => redirect('/?as=guest'))->name('logout');
Route::get('/dealers/join', fn () => view('pages.dealers.join'))->name('dealer.join');

/* ---------- پنل نمایشگاه‌دار (Mock) ---------- */
Route::get('/panel', function () {
    $img = fn ($f) => asset('img/mock/' . $f);
    $listings = [
        ['id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $img('dealer-stock-1.jpg'), 'price' => 985000000, 'status' => 'active', 'sale_type' => 'instant', 'views' => 326, 'saves' => 18, 'reserves' => 2, 'expires' => 'تا ۴۱ روز دیگر'],
        ['id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $img('dealer-stock-2.jpg'), 'price' => 9800000000, 'status' => 'active', 'sale_type' => null, 'views' => 148, 'saves' => 9, 'reserves' => 0, 'expires' => 'تا ۴۱ روز دیگر'],
        ['id' => 24819, 'title' => 'یاماها NMAX 155', 'image' => $img('dealer-stock-3.jpg'), 'price' => 385000000, 'status' => 'auction', 'sale_type' => 'auction', 'views' => 412, 'saves' => 31, 'reserves' => 0, 'expires' => 'مزایده جاری'],
        ['id' => 24820, 'title' => 'پژو ۲۰۶ تیپ ۲', 'image' => null, 'price' => 540000000, 'status' => 'draft', 'sale_type' => null, 'views' => 0, 'saves' => 0, 'reserves' => 0, 'expires' => 'پیش‌نویس'],
        ['id' => 24821, 'title' => 'سمند سورن', 'image' => null, 'price' => 610000000, 'status' => 'expired', 'sale_type' => null, 'views' => 97, 'saves' => 4, 'reserves' => 1, 'expires' => 'منقضی شده'],
    ];
    return view('pages.panel.dashboard', [
        'deals' => collect($GLOBALS['__dealStore']())->only([101, 104, 105])->values()->all(),
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
            ['id' => 13, 'title' => 'یاماها NMAX 155', 'image' => $img('dealer-stock-3.jpg'), 'status' => 'live', 'base' => 380000000, 'top' => 395000000, 'bidders' => 7, 'ends_at' => now()->addHours(3)],
            ['id' => 12, 'title' => 'ولوو FH500 کشنده', 'image' => $img('dealer-stock-2.jpg'), 'status' => 'scheduled', 'base' => 9500000000, 'top' => null, 'bidders' => 2, 'ends_at' => now()->addDays(2)],
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
        'dealer' => (object) ['name' => 'اتوگالری هالیوود', 'short' => 'گالری هالیوود', 'manager' => 'ابولفضل پناهی', 'phone' => '۰۲۱۲۲۳۴۵۶۷۸', 'about' => 'فروش تخصصی سواری، سنگین و موتورسیکلت؛ بازدید حضوری و کارشناسی مستقل با هماهنگی.', 'address' => 'تهران، سعادت‌آباد، بین سعادتی ۲۰ و ۲۲، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰', 'cover' => $img('dealer-cover.jpg')],
    ]);
})->name('panel');

$formOptions = fn () => [
    'brands' => ['پژو', 'سمند', 'تیبا', 'هیوندای', 'کیا', 'ولوو', 'بنز', 'یاماها', 'هوندا'],
    'colors' => ['سفید', 'مشکی', 'نقره‌ای', 'خاکستری', 'آبی', 'قرمز', 'بدون رنگ'],
    'conditions' => ['سالم و پلمپ', 'سالم', 'رنگ‌شده', 'ضربه خورده', 'تعویض شده'],
];
Route::get('/panel/listings/create', fn () => view('pages.panel.listing-form', ['listing' => null, 'opt' => $formOptions()]))->name('listings.create');
Route::get('/panel/listings/{id}/edit', fn ($id) => view('pages.panel.listing-form', ['listing' => (object) ['id' => $id, 'category' => 'car', 'brand' => 'پژو', 'model' => '۲۰۷ اتوماتیک', 'year' => 1402, 'km' => 24000, 'color' => 'سفید', 'gearbox' => 'auto', 'fuel' => 'بنزین', 'price' => 985000000, 'negotiable' => true, 'description' => 'خودرو تک‌مالک، سرویس‌ها به‌موقع.', 'sale_type' => 'instant', 'images' => [asset('img/mock/detail-main.jpg'), asset('img/mock/detail-t1.jpg')]], 'opt' => $formOptions()]))->whereNumber('id')->name('listings.edit');


/*
|--------------------------------------------------------------------------
| مزایده‌ها (Mock)
|--------------------------------------------------------------------------
| وضعیت‌ها: scheduled (قبل از شروع) | live (در حال برگزاری) | ended (پایان‌یافته)
| پیش‌نمایش حالت‌ها:  /auctions/11?state=live&paid=1   |  ?state=ended&won=1  |  ?paid=1
*/
$auctionStore = function () {
    $img = fn ($f) => asset('img/mock/' . $f);
    $hollywood = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'area' => 'تهران، سعادت‌آباد', 'active_count' => 12, 'verified' => true, 'cover' => $img('detail-banner.jpg'), 'url' => route('dealers.show', 8)];
    $gallery = [['src' => $img('detail-main.jpg'), 'thumb' => $img('detail-t1.jpg')], ['src' => $img('detail-t1.jpg'), 'thumb' => $img('detail-t2.jpg')], ['src' => $img('detail-t2.jpg'), 'thumb' => $img('detail-t3.jpg')]];
    $bids = [
        ['amount' => 975000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false],
        ['amount' => 970000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true],
        ['amount' => 965000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false],
    ];
    return [
        11 => ['id' => 11, 'status' => 'scheduled', 'category' => 'car', 'title' => 'پژو ۲۰۷ اتوماتیک', 'short_title' => 'پژو ۲۰۷', 'subtitle' => 'مدل ۱۴۰۲ • سقف شیشه‌ای • بدون رنگ', 'year' => 1402, 'km' => 24000, 'extra' => 'سفید',
            'image' => $img('dealer-stock-1.jpg'), 'images' => $gallery, 'listing_id' => 24817, 'listing_url' => route('listings.show', 24817), 'dealer' => $hollywood, 'dealer_name' => 'اتوگالری هالیوود', 'verified' => true,
            'start_price' => 950000000, 'original_price' => 985000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => null, 'bids_count' => 0, 'participants' => 14,
            'starts_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36), 'ends_at' => now()->addHours(4)->addMinutes(11), 'paid' => false, 'bids' => [], 'my_last_bid' => null],
        12 => ['id' => 12, 'status' => 'scheduled', 'category' => 'heavy', 'title' => 'ولوو FH500 کشنده', 'short_title' => 'ولوو FH500', 'subtitle' => 'مدل ۱۳۹۹ • دیزل • تک‌مالک', 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل',
            'image' => $img('dealer-stock-2.jpg'), 'images' => $gallery, 'listing_id' => 24818, 'listing_url' => route('listings.show', 24818), 'dealer' => array_merge($hollywood, ['id' => 22, 'name' => 'آریا خودروهای تجاری', 'url' => route('dealers.show', 22)]), 'dealer_name' => 'آریا خودروهای تجاری', 'verified' => true,
            'start_price' => 9500000000, 'original_price' => 9800000000, 'entry_fee' => 50000, 'min_step' => 50000000, 'top_bid' => null, 'bids_count' => 0, 'participants' => 3,
            'starts_at' => now()->addDays(3), 'ends_at' => now()->addDays(3)->addHours(2), 'paid' => false, 'bids' => [], 'my_last_bid' => null],
        13 => ['id' => 13, 'status' => 'live', 'category' => 'bike', 'title' => 'یاماها NMAX 155', 'short_title' => 'یاماها NMAX', 'subtitle' => 'مدل ۱۴۰۳ • آبی • بدون رنگ', 'year' => 1403, 'km' => 4500, 'extra' => 'آبی',
            'image' => $img('dealer-stock-3.jpg'), 'images' => $gallery, 'listing_id' => 24819, 'listing_url' => route('listings.show', 24819), 'dealer' => array_merge($hollywood, ['id' => 21, 'name' => 'آریا موتور', 'url' => route('dealers.show', 21)]), 'dealer_name' => 'آریا موتور', 'verified' => true,
            'start_price' => 350000000, 'original_price' => 385000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => 370000000, 'bids_count' => 9, 'participants' => 7,
            'starts_at' => now()->subHours(1), 'ends_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36), 'paid' => false,
            'bids' => [['amount' => 370000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 365000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 360000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]], 'my_last_bid' => 365000000],
        14 => ['id' => 14, 'status' => 'ended', 'category' => 'car', 'title' => 'پژو ۲۰۶ تیپ ۲', 'short_title' => 'پژو ۲۰۶', 'subtitle' => 'مدل ۱۴۰۰ • سفید', 'year' => 1400, 'km' => 61000, 'extra' => 'سفید',
            'image' => $img('dealer-stock-1.jpg'), 'images' => $gallery, 'listing_id' => 24820, 'listing_url' => route('listings.show', 24820), 'dealer' => $hollywood, 'dealer_name' => 'اتوگالری هالیوود', 'verified' => true,
            'start_price' => 500000000, 'original_price' => 540000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => 562000000, 'bids_count' => 11, 'participants' => 11,
            'starts_at' => now()->subDays(1)->subHours(2), 'ends_at' => now()->subDays(1), 'paid' => false, 'won' => false, 'deposit_deadline' => now()->addHours(23), 'bids' => $bids, 'my_last_bid' => null],
    ];
};

Route::get('/auctions', function () use ($auctionStore) {
    $list = collect($auctionStore())->filter(fn ($a) => $a['status'] !== 'ended')->map(function ($a) {
        $a['dealer_id'] = $a['dealer']['id']; $a['dealer'] = $a['dealer_name']; return $a;
    })->sortBy(fn ($a) => $a['status'] === 'live' ? 0 : 1)->values()->all();
    return view('pages.auctions.index', ['auctions' => $list]);
})->name('auctions.index');

Route::get('/auctions/{id}', function ($id) use ($auctionStore) {
    $all = $auctionStore(); $a = $all[(int) $id] ?? $all[11];
    if ($st = request('state')) {                     // نمایشی: تغییر وضعیت
        $a['status'] = $st;
        if ($st === 'live') { $a['starts_at'] = now()->subHours(1); $a['ends_at'] = now()->addHours(1)->addMinutes(11)->addSeconds(36); $a['top_bid'] = 970000000; $a['bids_count'] = 9; $a['bids'] = [['amount' => 975000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 970000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 965000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]]; $a['top_bid'] = 975000000; $a['my_last_bid'] = 970000000; }
        if ($st === 'ended') { $a['ends_at'] = now()->subHours(3); $a['top_bid'] = $a['top_bid'] ?: 990000000; $a['bids_count'] = max(9, $a['bids_count']); $a['deposit_deadline'] = now()->addHours(23); $a['bids'] = $a['bids'] ?: [['amount' => 990000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false]]; }
        if ($st === 'scheduled') { $a['starts_at'] = now()->addHours(2)->addMinutes(11)->addSeconds(36); $a['top_bid'] = null; $a['bids'] = []; }
    }
    if (request('paid') !== null) $a['paid'] = request('paid') === '1';
    if (request('won') === '1') { $a['won'] = true; $a['paid'] = true; }
    return view('pages.auctions.show', ['auction' => (object) $a]);
})->whereNumber('id')->name('auctions.show');

Route::get('/auctions/{id}/entry', function ($id) use ($auctionStore) {
    $all = $auctionStore(); $a = $all[(int) $id] ?? $all[11];
    if ($st = request('state')) $a['status'] = $st;
    return view('pages.auctions.entry', ['auction' => (object) $a]);
})->whereNumber('id')->name('auctions.entry');

/* ساخت مزایده (نمایشگاه‌دار) */
Route::get('/panel/auctions/create', function () {
    $img = fn ($f) => asset('img/mock/' . $f);
    return view('pages.panel.auction-form', [
        'edit'     => request('edit'),
        'selected' => request('listing'),
        'create_fee' => 600000,
        'listings' => [
            ['id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $img('dealer-stock-1.jpg'), 'price' => 985000000, 'year' => 1402, 'km' => 24000],
            ['id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $img('dealer-stock-2.jpg'), 'price' => 9800000000, 'year' => 1399, 'km' => 320000],
            ['id' => 24819, 'title' => 'یاماها NMAX 155', 'image' => $img('dealer-stock-3.jpg'), 'price' => 385000000, 'year' => 1403, 'km' => 4500],
        ],
    ]);
})->name('auctions.create');


/*
|--------------------------------------------------------------------------
| بیعانه امانی و معاملات (Mock)
|--------------------------------------------------------------------------
| وضعیت معامله: held (پول نزد پلتفرم امانت است) | completed (به نمایشگاه منتقل شد) | refunded (به خریدار برگشت) | review (اختلاف در حال بررسی)
| پیش‌نمایش:  /deals/101?state=completed   |  /deals/101?state=review   |  as=dealer برای دید فروشنده
*/
$GLOBALS['__dealStore'] = $dealStore = function () {
    $img = fn ($f) => asset('img/mock/' . $f);
    $hw = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'area' => 'تهران، سعادت‌آباد', 'verified' => true, 'url' => route('dealers.show', 8),
        'address' => 'تهران، سعادت‌آباد، بین سعادتی ۲۰ و ۲۲، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰', 'phone' => '۰۹۱۲۴۵۶۸۹۲۳', 'map_url' => 'https://maps.google.com/?q=35.78,51.37'];
    $base = ['listing_id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'listing_url' => route('listings.show', 24817), 'image' => $img('dealer-stock-1.jpg'), 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'price' => 985000000,
        'amount' => 20000000, 'fee' => 200000, 'tx' => '۸۶۴۲۱۹۷۳', 'dealer' => $hw, 'buyer' => ['name' => 'علی محمدی', 'phone' => '۰۹۱۲۳۴۵۶۷۸۹'],
        'paid_at' => now()->subHours(26), 'decide_by' => now()->addHours(46), 'decided_at' => null, 'reason' => null, 'reason_note' => null];
    return [
        101 => array_merge($base, ['id' => 101, 'status' => 'held']),
        102 => array_merge($base, ['id' => 102, 'status' => 'completed', 'listing_id' => 24819, 'title' => 'یاماها NMAX 155', 'image' => $img('dealer-stock-3.jpg'), 'year' => 1403, 'km' => 4500, 'extra' => 'آبی', 'price' => 385000000, 'amount' => 10000000, 'tx' => '۷۳۱۵۴۰۶۲', 'dealer' => array_merge($hw, ['id' => 21, 'name' => 'آریا موتور']), 'paid_at' => now()->subDays(6), 'decided_at' => now()->subDays(3)]),
        103 => array_merge($base, ['id' => 103, 'status' => 'refunded', 'listing_id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $img('dealer-stock-2.jpg'), 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'price' => 9800000000, 'amount' => 100000000, 'tx' => '۵۵۲۰۹۱۱۸', 'dealer' => array_merge($hw, ['id' => 22, 'name' => 'آریا خودروهای تجاری']), 'paid_at' => now()->subDays(9), 'decided_at' => now()->subDays(6), 'reason' => 'خودرو با توضیحات آگهی تفاوت داشت']),
        104 => array_merge($base, ['id' => 104, 'status' => 'review', 'listing_id' => 24820, 'title' => 'پژو ۲۰۶ تیپ ۲', 'image' => $img('dealer-stock-1.jpg'), 'year' => 1400, 'km' => 61000, 'price' => 540000000, 'amount' => 12000000, 'tx' => '۹۹۱۲۰۳۴۵', 'paid_at' => now()->subDays(3), 'decided_at' => now()->subHours(5), 'reason' => 'نمایشگاه پاسخ نداد / در دسترس نبود', 'reason_note' => 'دو روز تماس گرفتم و کسی پاسخ نداد.']),
        105 => array_merge($base, ['id' => 105, 'status' => 'completed', 'listing_id' => 24821, 'title' => 'سمند سورن', 'image' => $img('dealer-stock-1.jpg'), 'year' => 1401, 'km' => 38000, 'extra' => 'نقره‌ای', 'price' => 610000000, 'amount' => 15000000, 'tx' => '۴۴۱۸۷۲۰۶', 'paid_at' => now()->subDays(8), 'decided_at' => now()->subDays(5)]),
    ];
};

/* پرداخت بیعانه (کاربر) */
Route::get('/listings/{id}/deposit', function ($id) {
    $img = fn ($f) => asset('img/mock/' . $f);
    return view('pages.deals.deposit', ['car' => (object) [
        'id' => $id, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $img('dealer-stock-1.jpg'), 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'price' => 985000000,
        'amount' => 20000000, 'fee' => 200000, 'url' => route('listings.show', $id),
        'dealer' => ['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true],
    ]]);
})->whereNumber('id')->name('listings.deposit');

Route::get('/deals/{id}', function ($id) use ($dealStore) {
    $all = $dealStore(); $d = $all[(int) $id] ?? $all[101];
    if ($st = request('state')) {
        $d['status'] = $st; if ($st !== 'held') $d['decided_at'] = now()->subHours(2);
        if ($st === 'review') { $d['reason'] = 'خودرو با توضیحات آگهی تفاوت داشت'; $d['reason_note'] = 'رنگ و وضعیت بدنه با آگهی فرق داشت.'; }
        if ($st === 'refunded') $d['reason'] = 'خودرو با توضیحات آگهی تفاوت داشت';
    }
    return view('pages.deals.show', ['deal' => (object) $d]);
})->whereNumber('id')->name('deals.show');

Route::get('/deals/{id}/decide', function ($id) use ($dealStore) {
    $all = $dealStore(); $d = $all[(int) $id] ?? $all[101];
    return view('pages.deals.decide', ['deal' => (object) $d]);
})->whereNumber('id')->name('deals.decide');

/* معاملات من (خریدار) */
Route::get('/account/deals', function () use ($dealStore) {
    $all = collect($dealStore())->only([101, 102, 103, 104])->values()->all();
    return view('pages.deals.index', ['deals' => $all]);
})->name('account.reservations');

// صفحات بعدی — فعلاً صفحه «به‌زودی»
foreach ([
    'listings.index'   => ['/listings', 'آگهی‌ها'],
    'account'          => ['/account', 'حساب من'],
    'account.wallet'   => ['/account/wallet', 'کیف پول'],
    'favorites'        => ['/favorites', 'نشان‌شده‌ها'],
    'help'             => ['/help', 'راهنما'],
    'notifications'    => ['/notifications', 'اعلان‌ها'],
] as $name => [$uri, $title]) {
    Route::get($uri, fn () => view('pages.soon', ['title' => $title]))->name($name);
}

// صفحه کامل جستجو (اسکلت؛ داده و طرح نهایی بعداً)
Route::get('/search', function (\Illuminate\Http\Request $r) {
    return view('pages.search', [
        'scope'      => $r->query('scope', 'listings'),
        'city'       => 'سبزوار',
        'cities'     => ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'],
        'popular'    => ['پژو ۲۰۷', 'سمند', 'تیبا', 'پراید', 'هیوندای', 'کامیون', 'ولوو', 'موتور'],
        'categories' => [['slug' => 'car', 'title' => 'سواری', 'icon' => 'car'], ['slug' => 'heavy', 'title' => 'سنگین', 'icon' => 'truck'], ['slug' => 'bike', 'title' => 'موتورسیکلت', 'icon' => 'bike']],
    ]);
})->name('search');
