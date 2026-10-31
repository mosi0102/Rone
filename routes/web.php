<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| مسیرهای موقت فرانت (Mock)
|--------------------------------------------------------------------------
| این فایل فقط برای نمایش قالب است. بک‌اندکار هر مسیر را با کنترلر واقعی
| جایگزین می‌کند؛ نام مسیرها (route name) را تغییر ندهید چون ناوبری به آن‌ها وابسته است.
*/

$mock = fn() => [
    ['id' => 1, 'title' => 'پژو ۲۰۷ پانوراما دنده‌ای', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 685000000, 'year' => 1402, 'km' => 24000, 'city' => 'تهران', 'dealer' => 'نمایشگاه آریا', 'sale_type' => 'instant', 'favorited' => false],
    ['id' => 2, 'title' => 'سمند LX EF7', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 520000000, 'year' => 1401, 'km' => 41000, 'city' => 'اصفهان', 'dealer' => 'نمایشگاه پارس', 'sale_type' => 'auction', 'ends_at' => now()->addHours(5), 'favorited' => true],
    ['id' => 3, 'title' => 'هیوندای النترا', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 1850000000, 'year' => 1400, 'km' => 62000, 'city' => 'مشهد', 'dealer' => 'نمایشگاه نگین', 'sale_type' => null, 'favorited' => false],
    ['id' => 4, 'title' => 'کامیون بنز ۲۶۳۱', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 6200000000, 'year' => 1395, 'km' => 380000, 'city' => 'شیراز', 'dealer' => 'سنگین‌خودرو البرز', 'sale_type' => 'auction', 'ends_at' => now()->addMinutes(4), 'favorited' => false],
    ['id' => 5, 'title' => 'موتور هوندا CBR 250', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 390000000, 'year' => 1399, 'km' => 18000, 'city' => 'تبریز', 'dealer' => 'موتور سیتی', 'sale_type' => 'instant', 'favorited' => false],
    ['id' => 6, 'title' => 'تیبا ۲ صفر', 'url' => route('listings.show', 24817), 'image' => null, 'price' => 390000000, 'year' => 1403, 'km' => 0, 'city' => 'کرج', 'dealer' => 'نمایشگاه آریا', 'sale_type' => null, 'favorited' => false],
];

Route::get('/', function () {
    $img = fn($f) => asset('img/mock/' . $f);
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
        'city' => 'سبزوار',
        'auctions' => $auctions,
        'auctionsCount' => 3,
        'categories' => [
            ['slug' => 'car', 'title' => 'سواری', 'icon' => 'car', 'count' => 1240],
            ['slug' => 'heavy', 'title' => 'سنگین', 'icon' => 'truck', 'count' => 182],
            ['slug' => 'bike', 'title' => 'موتورسیکلت', 'icon' => 'bike', 'count' => 246],
            ['slug' => 'bicycle', 'title' => 'دوچرخه', 'icon' => 'bicycle', 'count' => 98],
        ],
        'ad' => ['image' => $img('ad-1.jpg'), 'label' => 'تبلیغات', 'title' => 'همراه مسیر زندگی شما', 'subtitle' => 'لیزینگ • فروش انواع ماشین سنگین', 'cta' => 'بازدید از نمایشگاه', 'url' => '#'],
        'instant' => $instant,
        'latest' => collect($latest),
    ]);
})->name('home');

Route::get('/styleguide', fn() => view('pages.styleguide', ['items' => collect($mock())]))->name('styleguide');
Route::view('/offline', 'pages.offline')->name('offline');

/* ---------- نمایشگاه‌ها ---------- */
$cities = ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت'];
$img = fn($f) => asset('img/mock/' . $f);

Route::get('/dealers', function () use ($cities, $img) {
    $hasht = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'manager' => 'ابولفضل پناهی', 'address' => 'تهران، سعادت‌آباد، بین سعادتی 20 و 22، جنب بانک شهر', 'cover' => $img('dealer-cover.jpg'), 'url' => route('dealers.show', 8), 'verified' => true, 'featured' => true,
        'stats' => [['value' => 12, 'unit' => 'آگهی', 'label' => 'فعال'], ['value' => 5, 'unit' => 'سال', 'label' => 'سابقه فعالیت'], ['value' => 3, 'unit' => 'شعبه', 'label' => 'در تهران']]];
    return view('pages.dealers.index', [
        'city' => 'سبزوار', 'cities' => $cities,
        'banners' => [['image' => $img('banner-dealers-1.jpg'), 'alt' => 'جزو بهترین‌ها باش؛ به‌صورت رایگان به خانواده بزرگ آروان بپیوندید', 'url' => route('dealer.join')]],
        'categories' => [
            ['slug' => 'car', 'title' => 'نمایشگاه سواری', 'icon' => 'car', 'count' => 50],
            ['slug' => 'heavy', 'title' => 'نمایشگاه سنگین', 'icon' => 'truck', 'count' => 16],
            ['slug' => 'bike', 'title' => 'نمایشگاه موتور', 'icon' => 'bike', 'count' => 23],
            ['slug' => 'bicycle', 'title' => 'نمایشگاه دوچرخه', 'icon' => 'bicycle', 'count' => 11],
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
    $row = fn($i, $t, $p, $y, $k, $x, $d, $im, $cat, $st = null) => ['id' => $i, 'title' => $t, 'url' => route('listings.show', 24817), 'image' => $img($im), 'price' => $p, 'year' => $y, 'km' => $k, 'extra' => $x, 'dealer' => $d, 'verified' => true, 'sale_type' => $st, 'category' => $cat];
    return view('pages.dealers.show', [
        'dealer' => (object)[
            'id' => $id, 'name' => 'اتوگالری هالیوود', 'short_name' => 'گالری هالیوود', 'manager' => 'ابولفضل پناهی', 'verified' => true, 'cover' => $img('dealer-cover.jpg'),
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
})->whereNumber('id')->name('dealers.show');

/* ---------- جزئیات آگهی ---------- */
Route::get('/listings/{id}', function ($id) use ($img) {
    return view('pages.listings.show', [
        'wallet' => 120000000, // موجودی کیف پول کاربر (برای تست حالت کمبود موجودی عدد را کم کنید)
        'car' => (object)[
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
        $dealer = $as === 'dealer' ? (object)['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true, 'plan_days_left' => 41, 'plan_total_days' => 90, 'plan_name' => 'اشتراک ۳ ماهه'] : null;
        $auth = (object)[
            'check' => $as !== 'guest',
            'name' => $as === 'dealer' ? 'ابولفضل پناهی' : 'علی محمدی',
            'phone' => '۰۹۱۲۳۴۵۶۷۸۹',
            'wallet' => 120000000,
            'is_dealer' => (bool)$dealer,
            'mode' => $mode,
            'dealer_mode' => $dealer && $mode === 'dealer',
            'dealer' => $dealer,
        ];
    }
    $view->with('auth', $auth);
});

Route::get('/switch-mode/{mode}', function ($mode) {
    $mode = $mode === 'dealer' ? 'dealer' : 'buyer';
    session(['demo_mode' => $mode]);
    return redirect($mode === 'dealer' ? route('panel') : route('home'));
})->name('mode.switch');

// ورود، ثبت‌نام، اسپلش و ثبت‌نام نمایشگاه‌دار: routes/auth.php (login، welcome، dealer.register)
require __DIR__ . '/auth.php';
Route::get('/logout', fn() => redirect('/?as=guest'))->name('logout');
Route::get('/dealers/join', fn() => view('pages.dealers.join'))->name('dealer.join');

/* ---------- پنل نمایشگاه‌دار (Mock) ---------- */
Route::get('/panel', function () {
    $img = fn($f) => asset('img/mock/' . $f);
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
        'dealer' => (object)['name' => 'اتوگالری هالیوود', 'short' => 'گالری هالیوود', 'manager' => 'ابولفضل پناهی', 'phone' => '۰۲۱۲۲۳۴۵۶۷۸', 'about' => 'فروش تخصصی سواری، سنگین و موتورسیکلت؛ بازدید حضوری و کارشناسی مستقل با هماهنگی.', 'address' => 'تهران، سعادت‌آباد، بین سعادتی ۲۰ و ۲۲، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰', 'cover' => $img('dealer-cover.jpg')],
    ]);
})->name('panel');

$formOptions = fn() => [
    'brands' => ['پژو', 'سمند', 'تیبا', 'هیوندای', 'کیا', 'ولوو', 'بنز', 'یاماها', 'هوندا'],
    'colors' => ['سفید', 'مشکی', 'نقره‌ای', 'خاکستری', 'آبی', 'قرمز', 'بدون رنگ'],
    'conditions' => ['سالم و پلمپ', 'سالم', 'رنگ‌شده', 'ضربه خورده', 'تعویض شده'],
];
Route::get('/panel/listings/create', fn() => view('pages.panel.listing-form', ['listing' => null, 'opt' => $formOptions()]))->name('listings.create');
Route::get('/panel/listings/{id}/edit', fn($id) => view('pages.panel.listing-form', ['listing' => (object)['id' => $id, 'category' => 'car', 'brand' => 'پژو', 'model' => '۲۰۷ اتوماتیک', 'year' => 1402, 'km' => 24000, 'color' => 'سفید', 'gearbox' => 'auto', 'fuel' => 'بنزین', 'price' => 985000000, 'negotiable' => true, 'description' => 'خودرو تک‌مالک، سرویس‌ها به‌موقع.', 'sale_type' => 'instant', 'images' => [asset('img/mock/detail-main.jpg'), asset('img/mock/detail-t1.jpg')]], 'opt' => $formOptions()]))->whereNumber('id')->name('listings.edit');


/*
|--------------------------------------------------------------------------
| مزایده‌ها (Mock)
|--------------------------------------------------------------------------
| وضعیت‌ها: scheduled (قبل از شروع) | live (در حال برگزاری) | ended (پایان‌یافته)
| پیش‌نمایش حالت‌ها:  /auctions/11?state=live&paid=1   |  ?state=ended&won=1  |  ?paid=1
*/
$auctionStore = function () {
    $img = fn($f) => asset('img/mock/' . $f);
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
        15 => ['id' => 15, 'status' => 'live', 'category' => 'bicycle', 'title' => 'دوچرخه کوهستان جاینت ATX', 'short_title' => 'جاینت ATX', 'subtitle' => 'مدل ۱۴۰۳ • مشکی • فریم آلومینیوم', 'year' => 1403, 'km' => 800, 'extra' => 'مشکی',
            'image' => $img('dealer-stock-3.jpg'), 'images' => $gallery, 'listing_id' => 24830, 'listing_url' => route('listings.show', 24830), 'dealer' => array_merge($hollywood, ['id' => 21, 'name' => 'آریا موتور', 'url' => route('dealers.show', 21)]), 'dealer_name' => 'آریا موتور', 'verified' => true,
            'start_price' => 24000000, 'original_price' => 28000000, 'entry_fee' => 50000, 'min_step' => 500000, 'top_bid' => 26500000, 'bids_count' => 9, 'participants' => 7,
            'starts_at' => now()->subHours(1), 'ends_at' => now()->addHours(2)->addMinutes(11)->addSeconds(36), 'paid' => false,
            'bids' => [['amount' => 26500000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 26000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 25500000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]], 'my_last_bid' => 26000000],
        14 => ['id' => 14, 'status' => 'ended', 'category' => 'car', 'title' => 'پژو ۲۰۶ تیپ ۲', 'short_title' => 'پژو ۲۰۶', 'subtitle' => 'مدل ۱۴۰۰ • سفید', 'year' => 1400, 'km' => 61000, 'extra' => 'سفید',
            'image' => $img('dealer-stock-1.jpg'), 'images' => $gallery, 'listing_id' => 24820, 'listing_url' => route('listings.show', 24820), 'dealer' => $hollywood, 'dealer_name' => 'اتوگالری هالیوود', 'verified' => true,
            'start_price' => 500000000, 'original_price' => 540000000, 'entry_fee' => 50000, 'min_step' => 5000000, 'top_bid' => 562000000, 'bids_count' => 11, 'participants' => 11,
            'starts_at' => now()->subDays(1)->subHours(2), 'ends_at' => now()->subDays(1), 'paid' => false, 'won' => false, 'deposit_deadline' => now()->addHours(23), 'bids' => $bids, 'my_last_bid' => null],
    ];
};

Route::get('/auctions', function () use ($auctionStore) {
    $list = collect($auctionStore())->filter(fn($a) => $a['status'] !== 'ended')->map(function ($a) {
        $a['dealer_id'] = $a['dealer']['id'];
        $a['dealer'] = $a['dealer_name'];
        return $a;
    })->sortBy(fn($a) => $a['status'] === 'live' ? 0 : 1)->values()->all();
    return view('pages.auctions.index', ['auctions' => $list]);
})->name('auctions.index');

Route::get('/auctions/{id}', function ($id) use ($auctionStore) {
    $all = $auctionStore();
    $a = $all[(int)$id] ?? $all[11];
    if ($st = request('state')) {                     // نمایشی: تغییر وضعیت
        $a['status'] = $st;
        if ($st === 'live') {
            $a['starts_at'] = now()->subHours(1);
            $a['ends_at'] = now()->addHours(1)->addMinutes(11)->addSeconds(36);
            $a['top_bid'] = 970000000;
            $a['bids_count'] = 9;
            $a['bids'] = [['amount' => 975000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false], ['amount' => 970000000, 'user' => 'شما', 'time' => '۱۹:۰۸', 'mine' => true], ['amount' => 965000000, 'user' => 'کاربر ۶۷۰۰۰', 'time' => '۱۹:۰۵', 'mine' => false]];
            $a['top_bid'] = 975000000;
            $a['my_last_bid'] = 970000000;
        }
        if ($st === 'ended') {
            $a['ends_at'] = now()->subHours(3);
            $a['top_bid'] = $a['top_bid'] ?: 990000000;
            $a['bids_count'] = max(9, $a['bids_count']);
            $a['deposit_deadline'] = now()->addHours(23);
            $a['bids'] = $a['bids'] ?: [['amount' => 990000000, 'user' => 'کاربر ۴۲۰۰۰', 'time' => '۱۹:۱۰', 'mine' => false]];
        }
        if ($st === 'scheduled') {
            $a['starts_at'] = now()->addHours(2)->addMinutes(11)->addSeconds(36);
            $a['top_bid'] = null;
            $a['bids'] = [];
        }
    }
    if (request('paid') !== null) $a['paid'] = request('paid') === '1';
    if (request('won') === '1') {
        $a['won'] = true;
        $a['paid'] = true;
    }
    return view('pages.auctions.show', ['auction' => (object)$a]);
})->whereNumber('id')->name('auctions.show');

Route::get('/auctions/{id}/entry', function ($id) use ($auctionStore) {
    $all = $auctionStore();
    $a = $all[(int)$id] ?? $all[11];
    if ($st = request('state')) $a['status'] = $st;
    return view('pages.auctions.entry', ['auction' => (object)$a]);
})->whereNumber('id')->name('auctions.entry');

/* ساخت مزایده (نمایشگاه‌دار) */
Route::get('/panel/auctions/create', function () {
    $img = fn($f) => asset('img/mock/' . $f);
    return view('pages.panel.auction-form', [
        'edit' => request('edit'),
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
    $img = fn($f) => asset('img/mock/' . $f);
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
    $img = fn($f) => asset('img/mock/' . $f);
    return view('pages.deals.deposit', ['car' => (object)[
        'id' => $id, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $img('dealer-stock-1.jpg'), 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'price' => 985000000,
        'amount' => 20000000, 'fee' => 200000, 'url' => route('listings.show', $id),
        'dealer' => ['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true],
    ]]);
})->whereNumber('id')->name('listings.deposit');

Route::get('/deals/{id}', function ($id) use ($dealStore) {
    $all = $dealStore();
    $d = $all[(int)$id] ?? $all[101];
    if ($st = request('state')) {
        $d['status'] = $st;
        if ($st !== 'held') $d['decided_at'] = now()->subHours(2);
        if ($st === 'review') {
            $d['reason'] = 'خودرو با توضیحات آگهی تفاوت داشت';
            $d['reason_note'] = 'رنگ و وضعیت بدنه با آگهی فرق داشت.';
        }
        if ($st === 'refunded') $d['reason'] = 'خودرو با توضیحات آگهی تفاوت داشت';
    }
    return view('pages.deals.show', ['deal' => (object)$d]);
})->whereNumber('id')->name('deals.show');

Route::get('/deals/{id}/decide', function ($id) use ($dealStore) {
    $all = $dealStore();
    $d = $all[(int)$id] ?? $all[101];
    return view('pages.deals.decide', ['deal' => (object)$d]);
})->whereNumber('id')->name('deals.decide');

/* معاملات من (خریدار) */
Route::get('/account/deals', function () use ($dealStore) {
    $all = collect($dealStore())->only([101, 102, 103, 104])->values()->all();
    return view('pages.deals.index', ['deals' => $all]);
})->name('account.reservations');


/*
|--------------------------------------------------------------------------
| جست‌وجو، فیلتر و لیست آگهی‌ها (Mock)
|--------------------------------------------------------------------------
| یک صفحه نتایج برای هر دو مسیر: /search (جست‌وجو) و /listings (همه آگهی‌ها)
| پارامترها: category brand model city price_min price_max year_from year_to km_max gearbox fuel axle weight color body cc type
|           with_photo sale_type(instant|auction) mine q sort(new|cheap|expensive|low_km|new_model) page dealer
| بک‌اندکار همین پارامترها را با کوئری واقعی جایگزین کند؛ خروجی View همان ساختار $items/$chips/... است.
*/
$lf = [
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

$fa = fn($s) => strtr((string)$s, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
$digits = fn($v) => (int)preg_replace('/\D/', '', strtr((string)$v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']));
$human = function ($n) use ($fa) {
    if ($n >= 1000000000) return $fa(rtrim(rtrim(number_format($n / 1e9, 1), '0'), '.')) . ' میلیارد';
    if ($n >= 1000000) return $fa(number_format($n / 1e6, 0)) . ' میلیون';
    return $fa(number_format($n));
};

$listingStore = function () {
    $img = fn($f) => asset('img/mock/' . $f);
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
            'city' => $v ? $cities[($i) % 5] : $r[9], 'dealer' => $r[10], 'dealer_id' => $r[11], 'image' => $img($r[12]), 'color' => $r[13], 'sale' => $r[14], 'featured' => $v ? false : $r[15], 'old' => $v ? null : $r[16],
            'axle' => $axle[$i % 4], 'weight' => $weight[$i % 3], 'body' => $body[$i % 3], 'cc' => $cc[$i % 4], 'type' => $types[$i % 4], 'btype' => $btype[$i % 4], 'wheel' => $wheel[$i % 3], 'posted' => $posted[$i % 5], 'location' => ($v ? $cities[($i) % 5] : $r[9]) . '، ' . ['سعادت‌آباد', 'جاده مخصوص', 'بلوار وکیل‌آباد', 'چهارراه ولیعصر'][$i % 4]];
    }
    return $out;
};

$listingFilter = function (array $rows, array $q) use ($digits) {
    $c = collect($rows);
    if (!empty($q['q'])) {
        $t = trim($q['q']);
        $c = $c->filter(fn($r) => mb_stripos($r['title'] . ' ' . $r['brand'] . ' ' . $r['model'] . ' ' . $r['dealer'] . ' ' . $r['city'], $t) !== false);
    }
    foreach (['category' => 'cat', 'brand' => 'brand', 'model' => 'model', 'city' => 'city', 'gearbox' => 'gearbox', 'fuel' => 'fuel', 'axle' => 'axle', 'weight' => 'weight', 'color' => 'color', 'body' => 'body', 'cc' => 'cc', 'type' => 'type', 'btype' => 'btype', 'wheel' => 'wheel'] as $p => $f)
        if (!empty($q[$p])) $c = $c->filter(fn($r) => $r[$f] === $q[$p]);
    if (!empty($q['price_min'])) $c = $c->filter(fn($r) => $r['price'] >= $digits($q['price_min']));
    if (!empty($q['price_max'])) $c = $c->filter(fn($r) => $r['price'] <= $digits($q['price_max']));
    if (!empty($q['year_from'])) $c = $c->filter(fn($r) => $r['year'] >= $digits($q['year_from']));
    if (!empty($q['year_to'])) $c = $c->filter(fn($r) => $r['year'] <= $digits($q['year_to']));
    if (!empty($q['km_max'])) $c = $c->filter(fn($r) => $r['km'] <= $digits($q['km_max']));
    if (!empty($q['with_photo'])) $c = $c->filter(fn($r) => !empty($r['image']));
    if (!empty($q['sale_type'])) $c = $c->filter(fn($r) => $r['sale'] === $q['sale_type']);
    if (!empty($q['dealer'])) $c = $c->filter(fn($r) => (int)$r['dealer_id'] === (int)$q['dealer']);
    if (!empty($q['mine'])) $c = $c->filter(fn($r) => (int)$r['dealer_id'] === 8);
    $sort = $q['sort'] ?? 'new';
    if ($sort === 'cheap') $c = $c->sortBy('price'); elseif ($sort === 'expensive') $c = $c->sortByDesc('price');
    elseif ($sort === 'low_km') $c = $c->sortBy('km');
    elseif ($sort === 'new_model') $c = $c->sortByDesc('year');
    return $c->values();
};

$listingPage = function (string $mode) use ($lf, $listingStore, $listingFilter, $fa, $digits, $human) {
    $q = request()->query();
    if (isset($q['type']) && in_array($q['type'], ['instant', 'auction'], true)) {
        $q['sale_type'] = $q['type'];
        unset($q['type']);
    } // لینک «مشاهده همه» فروش فوری
    $q = array_filter($q, fn($v) => $v !== null && $v !== '');
    $name = $mode === 'search' ? 'search' : 'listings.index';
    $all = $listingFilter($listingStore(), $q);
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
    if (!empty($q['price_min']) || !empty($q['price_max'])) $add((!empty($q['price_min']) ? 'از ' . $human($digits($q['price_min'])) : '') . (!empty($q['price_max']) ? ' تا ' . $human($digits($q['price_max'])) : ''), ['price_min', 'price_max']);
    if (!empty($q['year_from']) || !empty($q['year_to'])) $add('سال ' . ($q['year_from'] ?? '…') . ' تا ' . ($q['year_to'] ?? '…'), ['year_from', 'year_to']);
    if (!empty($q['km_max'])) $add('تا ' . $fa(number_format($digits($q['km_max']))) . ' کیلومتر', ['km_max']);
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
    return view('pages.listings.index', [
        'mode' => $mode, 'q' => $q['q'] ?? '', 'query' => $q, 'items' => $items, 'total' => $total, 'chips' => $chips, 'tabs' => $tabs, 'sorts' => $sorts, 'sort' => $q['sort'] ?? 'new',
        'filter_count' => count($chips),
        'filters_url' => route('listings.filters', $q), 'clear_url' => route($name, array_intersect_key($q, array_flip(['q', 'category']))),
        'has_more' => $total > $page * $perPage, 'next_url' => route($name, $nextQ), 'lf' => $lf,
        'title' => $mode === 'search' ? 'جست‌وجوی خودرو' : 'آگهی‌ها', 'subtitle' => $mode === 'search' ? 'آگهی‌های مورد نظر خودتان را پیدا کنید.' : 'همه آگهی‌های فعال',
        'city' => 'سبزوار',
    ]);
};

Route::get('/search', function () use ($listingPage) {
    if (request('scope') === 'dealers') return redirect(route('dealers.index', array_filter(['q' => request('q'), 'city' => request('city')])));
    return $listingPage('search');
})->name('search');
Route::get('/listings', fn() => $listingPage('list'))->name('listings.index');

Route::get('/listings/filters', function () use ($lf, $listingStore, $listingFilter) {
    $q = array_filter(request()->query(), fn($v) => $v !== null && $v !== '');
    return view('pages.listings.filters', ['f' => $q, 'lf' => $lf, 'count' => $listingFilter($listingStore(), $q)->count(), 'back' => route(isset($q['q']) ? 'search' : 'listings.index', $q)]);
})->name('listings.filters');

Route::get('/listings/count', function () use ($listingStore, $listingFilter) {   // شمارش زنده برای دکمه «نمایش N آگهی»
    $q = array_filter(request()->query(), fn($v) => $v !== null && $v !== '');
    return response()->json(['count' => $listingFilter($listingStore(), $q)->count()]);
})->name('listings.count');

// رادار خودرو (درخواست خرید با درصد تطابق): routes/radar.php — باید بعد از $lf و $listingStore و $listingFilter بیاید
require __DIR__ . '/radar.php';

// صفحات بعدی — فعلاً صفحه «به‌زودی»
foreach ([
             'account' => ['/account', 'حساب من'],
             'account.wallet' => ['/account/wallet', 'کیف پول'],
             'favorites' => ['/favorites', 'نشان‌شده‌ها'],
             'help' => ['/help', 'راهنما'],
             'notifications' => ['/notifications', 'اعلان‌ها'],
         ] as $name => [$uri, $title]) {
    Route::get($uri, fn() => view('pages.soon', ['title' => $title]))->name($name);
}

// داده‌ی مشترک همه‌ی صفحات ادمین (تعداد موارد منتظر در سایدبار و اطلاعات مدیر)
View::composer('admin.*', function ($view) {
    $view->with([
        'admin' => ['name' => 'مدیر سایت', 'first_name' => 'مدیر'],
        'badges' => [
            'admin.listings.index' => 12,
            'admin.disputes.index' => 3,
            'admin.dealers.index' => 4,
            'admin.settlements.index' => 5,
        ],
    ]);
});

/* ---------- داده‌ی نمایشی مزایده‌ها (فقط برای قالب) ---------- */
$auctionData = function () {
    $img = fn($f) => asset('img/mock/' . $f);
    $users = [
        ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
        ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
        ['name' => 'رضا نوری', 'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
    ];
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();

    // [id، عنوان، دسته، نمایشگاه، تصویر، قیمت پایه، گام، ورودی، وضعیت، شروع(دقیقه)، پایان(دقیقه)، تعداد پیشنهاد، شرکت‌کننده، برنده، مهلت پرداخت(دقیقه)، وضعیت پرداخت]
    $seed = [
        [11, 'پژو ۲۰۷ اتوماتیک', 'car', 'اتوگالری هالیوود', 'car-207.jpg', 850000000, 5000000, 200000, 'live', -90, 42, 14, 6, null, null, null],
        [12, 'ولوو FH500 کشنده', 'heavy', 'آریا خودروهای تجاری', 'truck-fh.jpg', 9200000000, 50000000, 500000, 'live', -30, 312, 8, 4, null, null, null],
        [13, 'یاماها NMAX 155', 'bike', 'آریا موتور', 'yamaha.jpg', 350000000, 2000000, 100000, 'scheduled', 1560, 1680, 0, 0, null, null, null],
        [14, 'سمند LX EF7', 'car', 'نمایشگاه پارس', 'dealer-stock-1.jpg', 480000000, 3000000, 150000, 'scheduled', 4320, 4440, 0, 0, null, null, null],
        [15, 'کامیون بنز ۲۶۳۱', 'heavy', 'سنگین‌خودرو البرز', 'truck-fh-a.jpg', 5800000000, 50000000, 500000, 'awaiting_pay', -240, -120, 22, 9, 2, 300, 'waiting'],
        [16, 'هیوندای النترا', 'car', 'نمایشگاه نگین', 'detail-main.jpg', 1700000000, 10000000, 300000, 'awaiting_pay', -2000, -1800, 17, 7, 0, -60, 'overdue'],
        [17, 'موتور هوندا CBR 250', 'bike', 'موتور سیتی', 'dealer-stock-3.jpg', 360000000, 2000000, 100000, 'defaulted', -5000, -4800, 11, 5, 4, -2900, 'banned'],
        [18, 'تیبا ۲ صفر', 'car', 'اتوگالری هالیوود', 'detail-t1.jpg', 370000000, 2000000, 100000, 'ended', -6200, -6000, 19, 8, 1, -5000, 'paid'],
        [20, 'دوچرخه کوهستان جاینت ATX', 'bicycle', 'آریا موتور', 'dealer-stock-3.jpg', 24000000, 500000, 50000, 'live', -20, 200, 5, 3, null, null, null],
        [19, 'رنو تندر ۹۰', 'car', 'نمایشگاه پارس', 'detail-t3.jpg', 560000000, 3000000, 150000, 'cancelled', -300, -180, 0, 0, null, null, null],
    ];

    return array_map(function ($r) use ($users, $img, $min) {
        [$id, $title, $cat, $dealer, $image, $base, $step, $entry, $status, $startM, $endM, $n, $parts, $winner, $payM, $pay] = $r;
        $bids = [];
        for ($i = 1; $i <= $n; $i++) {
            $bids[] = ['n' => $i, 'user' => $users[($i * 7 + $id) % 6], 'amount' => $base + $step * $i, 'at' => $min($endM - ($n - $i) * 6 - 1)];
        }
        $top = $n ? end($bids) : null;
        return [
            'id' => $id, 'title' => $title, 'cat' => $cat, 'dealer' => $dealer, 'image' => $img($image), 'code' => 24800 + $id,
            'base' => $base, 'step' => $step, 'entry' => $entry, 'status' => $status, 'bids_count' => $n, 'participants' => $parts,
            'top' => $top ? $top['amount'] : 0, 'leader' => $top ? $top['user']['name'] : null,
            'starts_at' => $min($startM), 'ends_at' => $min($endM), 'bids' => array_reverse($bids), 'users' => $users,
            'winner' => $winner !== null ? $users[$winner] : null, 'pay' => $pay, 'pay_deadline' => $payM !== null ? $min($payM) : null,
            'banned_until' => $pay === 'banned' ? $min(27 * 1440) : null,
            'cancel_reason' => $status === 'cancelled' ? 'درخواست نمایشگاه‌دار' : null,
            'deposit_percent' => 2, 'service_fee' => 200000,
        ];
    }, $seed);
};


/* ---------- داده‌ی نمایشی بیعانه‌ها و اختلاف‌ها (فقط برای قالب) ---------- */
$depositData = function () {
    $img = fn($f) => asset('img/mock/' . $f);
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    $buyers = [
        ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
        ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
        ['name' => 'رضا نوری', 'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
    ];
    // [id، عنوان، دسته، نمایشگاه، تصویر، قیمت خودرو، درصد بیعانه، منبع(instant|auction)، وضعیت، ثبت(دقیقه‌ی قبل)، مهلت(دقیقه‌ی بعد)، خریدار]
    $seed = [
        [301, 'پژو ۲۰۷ اتوماتیک', 'car', 'اتوگالری هالیوود', 'car-207.jpg', 985000000, 2, 'instant', 'held', 95, 2800, 0],
        [302, 'ولوو FH500 کشنده', 'heavy', 'آریا خودروهای تجاری', 'truck-fh.jpg', 9800000000, 1.5, 'instant', 'held', 600, 1200, 2],
        [303, 'کامیون بنز ۲۶۳۱', 'heavy', 'سنگین‌خودرو البرز', 'truck-fh-a.jpg', 5800000000, 1.5, 'auction', 'held', 180, 4200, 4],
        [304, 'یاماها NMAX 155', 'bike', 'آریا موتور', 'yamaha.jpg', 385000000, 3, 'instant', 'dispute', 3100, null, 1],
        [305, 'سمند LX EF7', 'car', 'نمایشگاه پارس', 'dealer-stock-1.jpg', 480000000, 2, 'instant', 'dispute', 4200, null, 3],
        [306, 'تیبا ۲ صفر', 'car', 'اتوگالری هالیوود', 'detail-t1.jpg', 370000000, 2, 'auction', 'released', 6000, null, 5],
        [307, 'رنو تندر ۹۰', 'car', 'نمایشگاه پارس', 'detail-t3.jpg', 560000000, 2, 'instant', 'released', 9000, null, 0],
        [308, 'موتور هوندا CBR 250', 'bike', 'موتور سیتی', 'dealer-stock-3.jpg', 360000000, 3, 'instant', 'refunded', 7000, null, 2],
        [309, 'هیوندای النترا', 'car', 'نمایشگاه نگین', 'detail-main.jpg', 1700000000, 2, 'auction', 'refunded', 11000, null, 4],
        [310, 'پژو ۲۰۷ اتوماتیک', 'car', 'اتوگالری هالیوود', 'car-207-a.jpg', 990000000, 2, 'instant', 'held', 30, 2900, 5],
    ];
    return array_map(function ($r) use ($buyers, $img, $min) {
        [$id, $title, $cat, $dealer, $image, $price, $pct, $src, $st, $agoM, $leftM, $b] = $r;
        $amount = (int)round($price * $pct / 100);
        $events = [['t' => $min(-$agoM), 'label' => 'بیعانه از کیف پول خریدار کسر و در امانت قرار گرفت', 'tone' => 'info']];
        if ($st === 'released') $events[] = ['t' => $min(-($agoM - 2400)), 'label' => 'معامله تأیید شد و بیعانه به کیف پول نمایشگاه‌دار منتقل شد', 'tone' => 'ok'];
        if ($st === 'refunded') $events[] = ['t' => $min(-($agoM - 1500)), 'label' => 'معامله انجام نشد و بیعانه به کیف پول خریدار برگشت', 'tone' => 'warn'];
        if ($st === 'dispute') $events[] = ['t' => $min(-($agoM - 1300)), 'label' => 'اختلاف ثبت شد؛ بیعانه تا تعیین تکلیف مسدود است', 'tone' => 'bad'];
        return [
            'id' => $id, 'code' => 'DP-' . (5200 + $id), 'title' => $title, 'cat' => $cat, 'dealer' => $dealer, 'image' => $img($image),
            'price' => $price, 'percent' => $pct, 'amount' => $amount, 'source' => $src, 'status' => $st,
            'created_at' => $min(-$agoM), 'hold_until' => $leftM !== null ? $min($leftM) : null,
            'buyer' => $buyers[$b], 'events' => array_reverse($events), 'listing_code' => 31000 + $id,
        ];
    }, $seed);
};

$disputeData = function () use ($depositData) {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    $deps = [];
    foreach ($depositData() as $d) $deps[$d['id']] = $d;
    // [id، کد بیعانه، بازکننده(buyer|dealer)، موضوع، شرح، وضعیت، ثبت(دقیقه‌ی قبل)، پاسخ طرف مقابل]
    $seed = [
        [901, 304, 'buyer', 'مغایرت خودرو با آگهی', 'موتور هنگام بازدید دچار خرابی قطعه‌ی ترمز بود و نمایشگاه‌دار آن را در آگهی ذکر نکرده بود. خواستار برگشت بیعانه هستم.', 'open', 1300, null],
        [902, 305, 'dealer', 'انصراف خریدار پس از رزرو', 'خریدار پس از دو روز هماهنگی بدون دلیل از معامله منصرف شد و خودرو را از فروش خارج کرده بودیم. درخواست آزادسازی بیعانه.', 'review', 2900, 'من با کارشناس مستقل بررسی کردم و رنگ‌شدگی دو در مخفی شده بود؛ به همین دلیل منصرف شدم.'],
        [903, 306, 'buyer', 'عدم تحویل خودرو در زمان مقرر', 'نمایشگاه‌دار سه بار قرار تحویل را به تعویق انداخت.', 'resolved', 7000, 'تحویل به دلیل مشکل مدارک بود و در نهایت انجام شد.'],
    ];
    return array_map(function ($r) use ($deps, $min) {
        [$id, $depId, $by, $subject, $text, $st, $agoM, $reply] = $r;
        $d = $deps[$depId] ?? array_values($deps)[0];
        return [
            'id' => $id, 'code' => 'DS-' . (700 + $id), 'deposit' => $d, 'by' => $by, 'subject' => $subject, 'text' => $text, 'reply' => $reply,
            'status' => $st, 'opened_at' => $min(-$agoM), 'age_hours' => (int)round($agoM / 60),
            'resolution' => $st === 'resolved' ? ['type' => 'release', 'note' => 'پس از بررسی مدارک، تحویل انجام شده بود؛ بیعانه به نمایشگاه‌دار آزاد شد.', 'at' => $min(-5000)] : null,
            'evidence' => $id === 901 ? ['detail-t1.jpg', 'detail-t2.jpg'] : ($id === 902 ? ['detail-t3.jpg'] : []),
            'messages' => [
                ['from' => 'buyer', 'text' => 'سلام، موتور رو از نزدیک دیدم، ترمز جلو مشکل داره.', 'at' => $min(-$agoM - 120)],
                ['from' => 'dealer', 'text' => 'در زمان بازدید مشکلی نبود، احتمالاً بعدش پیش اومده.', 'at' => $min(-$agoM - 100)],
                ['from' => 'buyer', 'text' => 'تو آگهی چیزی از این موضوع ننوشته بودید.', 'at' => $min(-$agoM - 90)],
            ],
        ];
    }, $seed);
};

/* ---------- داده‌ی نمایشی «درخواست خرید» کاربران (فقط برای قالب) ---------- */
$requestData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    $img = fn($f) => asset('img/mock/' . $f);
    $users = [
        ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
        ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
        ['name' => 'رضا نوری', 'phone' => '09125550055'],
    ];
    $pool = [ // آگهی‌های نمایشی برای نمایش تطابق: [عنوان، تصویر، قیمت، شهر، نمایشگاه، کد]
        ['پژو ۲۰۷ اتوماتیک', 'car-207.jpg', 985000000, 'تهران', 'اتوگالری هالیوود', 24817],
        ['پژو ۲۰۷ پانوراما', 'car-207-a.jpg', 940000000, 'اصفهان', 'نمایشگاه پارس', 24825],
        ['هیوندای النترا', 'detail-main.jpg', 1850000000, 'مشهد', 'نمایشگاه نگین', 24821],
        ['سمند LX EF7', 'dealer-stock-1.jpg', 520000000, 'اصفهان', 'نمایشگاه پارس', 24820],
        ['تیبا ۲ صفر', 'detail-t1.jpg', 390000000, 'کرج', 'اتوگالری هالیوود', 24824],
        ['یاماها NMAX 155', 'yamaha.jpg', 385000000, 'مشهد', 'آریا موتور', 24819],
        ['موتور هوندا CBR 250', 'dealer-stock-3.jpg', 390000000, 'تبریز', 'موتور سیتی', 24823],
        ['دوچرخه کوهستان جاینت ATX', 'dealer-stock-3.jpg', 38000000, 'تهران', 'دوچرخه‌ی آرمان', 24829],
    ];
    // [id، کاربر، دسته، خلاصه، برند/مدل، سال از، سقف قیمت، سقف کارکرد، شهر، وضعیت، ثبت(روز قبل)، [[ایندکس آگهی، درصد]…]]
    $seed = [
        [501, 0, 'car', 'پژو ۲۰۷ اتوماتیک، سفید', 'پژو ۲۰۷', '۱۴۰۱', 1000000000, '۵۰٬۰۰۰', 'تهران', 'active', 2, [[0, 96], [1, 82], [4, 41]]],
        [502, 1, 'car', 'خودرو شاسی‌بلند اقتصادی', 'هر مدل', '۱۴۰۲', 1900000000, '۸۰٬۰۰۰', 'مشهد', 'active', 9, [[2, 68], [3, 44]]],
        [503, 2, 'bike', 'موتور ۱۵۰ تا ۲۵۰ سی‌سی', 'هر مدل', '۱۴۰۰', 400000000, '۲۵٬۰۰۰', 'تبریز', 'active', 5, [[6, 91], [5, 77]]],
        [504, 3, 'heavy', 'کشنده‌ی ولوو یا بنز', 'ولوو / بنز', '۱۳۹۸', 10000000000, '۳۵۰٬۰۰۰', 'تهران', 'active', 14, []],
        [505, 4, 'bicycle', 'دوچرخه‌ی کوهستان ۲۷٫۵ اینچ', 'هر مدل', '۱۴۰۲', 45000000, '—', 'تهران', 'active', 1, [[7, 88]]],
        [506, 1, 'car', 'سمند یا پژو ۴۰۵ دنده‌ای', 'سمند / ۴۰۵', '۱۳۹۹', 600000000, '۹۰٬۰۰۰', 'اصفهان', 'cancelled', 20, [[3, 73]]],
        [507, 2, 'car', 'خودروی صفر کیلومتر زیر ۴۰۰ میلیون', 'تیبا / ساینا', '۱۴۰۳', 400000000, '۵٬۰۰۰', 'کرج', 'suspended', 6, [[4, 94]]],
    ];
    return array_map(function ($r) use ($users, $pool, $img, $min) {
        [$id, $u, $cat, $summary, $model, $year, $budget, $km, $city, $st, $days, $mt] = $r;
        $matches = array_map(function ($m) use ($pool, $img) {
            $l = $pool[$m[0]];
            return ['title' => $l[0], 'image' => $img($l[1]), 'price' => $l[2], 'city' => $l[3], 'dealer' => $l[4], 'code' => $l[5], 'percent' => $m[1]];
        }, $mt);
        usort($matches, fn($a, $b) => $b['percent'] <=> $a['percent']);
        return [
            'id' => $id, 'code' => 'RQ-' . (1000 + $id), 'user' => $users[$u], 'cat' => $cat, 'summary' => $summary, 'model' => $model, 'year_from' => $year,
            'budget' => $budget, 'km_max' => $km, 'city' => $city, 'status' => $st, 'created_at' => $min(-$days * 1440),
            'matches' => $matches, 'best' => $matches ? $matches[0]['percent'] : 0,
            'cancelled_at' => $st === 'cancelled' ? $min(-2 * 1440) : null,
        ];
    }, $seed);
};

/* ---------- داده‌ی نمایشی نمایشگاه‌داران و کاربران (فقط برای قالب) ---------- */
$dealerData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    $img = fn($f) => asset('img/mock/' . $f);
    // [id، نام نمایشگاه، مدیر، موبایل، شهر، وضعیت، ثبت(روز قبل)، آگهی فعال، فروش، کیف پول، اشتراک(روز مانده|null)، لوگو]
    $seed = [
        [101, 'نمایشگاه ستاره', 'امیر حسینی', '09131000101', 'اصفهان', 'pending', 1, 0, 0, 0, null, 'dealer-thumb-1.jpg'],
        [102, 'اتوگالری مهر', 'کامران یزدانی', '09132000202', 'شیراز', 'pending', 2, 0, 0, 0, null, 'dealer-thumb-2.jpg'],
        [103, 'دوچرخه‌ی نیما', 'نیما قاسمی', '09133000303', 'تهران', 'pending', 3, 0, 0, 0, null, 'dealer-thumb-3.jpg'],
        [104, 'موتور پلاس', 'سعید فرهادی', '09134000404', 'مشهد', 'pending', 4, 0, 0, 0, null, 'dealer-thumb-1.jpg'],
        [105, 'اتوگالری هالیوود', 'بهرام اسدی', '09121005005', 'تهران', 'verified', 120, 14, 31, 184000000, 34, 'dealer-thumb-1.jpg'],
        [106, 'نمایشگاه پارس', 'آرش مرادی', '09122006006', 'اصفهان', 'verified', 200, 9, 22, 96000000, 12, 'dealer-thumb-2.jpg'],
        [107, 'آریا خودروهای تجاری', 'پویا نصیری', '09123007007', 'تهران', 'verified', 90, 6, 8, 540000000, 61, 'dealer-thumb-3.jpg'],
        [108, 'نمایشگاه نگین', 'مهدی کیانی', '09124008008', 'مشهد', 'suspended', 150, 0, 12, 40000000, null, 'dealer-thumb-2.jpg'],
        [109, 'موتور سیتی', 'کیوان رحمانی', '09125009009', 'تبریز', 'rejected', 18, 0, 0, 0, null, 'dealer-thumb-3.jpg'],
    ];
    return array_map(function ($r) use ($min, $img) {
        [$id, $name, $owner, $phone, $city, $st, $days, $ads, $sales, $wallet, $sub, $logo] = $r;
        return [
            'id' => $id, 'name' => $name, 'owner' => $owner, 'phone' => $phone, 'city' => $city, 'status' => $st,
            'created_at' => $min(-$days * 1440), 'ads' => $ads, 'sales' => $sales, 'wallet' => $wallet, 'sub_days' => $sub, 'logo' => $img($logo),
            'license' => $img('detail-t1.jpg'), 'id_card' => $img('detail-t2.jpg'), 'national_id' => '00' . (12000000 + $id * 137),
            'address' => 'خیابان نمایشگاه‌ها، پلاک ' . (10 + $id % 40), 'reason' => $st === 'rejected' ? 'تصویر مجوز ناخوانا بود' : ($st === 'suspended' ? 'شکایت مکرر خریداران' : null),
        ];
    }, $seed);
};

$userData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    // [id، نام، موبایل، شهر، وضعیت، عضویت(روز قبل)، کیف پول، بیعانه‌ی در امانت، مزایده‌ی شرکت‌کرده، محرومیت از مزایده(روز|null)، درخواست خرید فعال]
    $seed = [
        [201, 'علی رضایی', '09121110011', 'تهران', 'active', 300, 2400000, 19700000, 6, null, 1],
        [202, 'مریم احمدی', '09122220022', 'مشهد', 'active', 220, 850000, 11550000, 3, null, 1],
        [203, 'حسین کریمی', '09123330033', 'تبریز', 'active', 180, 0, 147000000, 9, null, 1],
        [204, 'سارا موسوی', '09124440044', 'اصفهان', 'active', 90, 15000000, 9600000, 2, null, 0],
        [205, 'رضا نوری', '09125550055', 'تهران', 'active', 60, 300000, 87000000, 5, null, 1],
        [206, 'نیلوفر صادقی', '09126660066', 'کرج', 'active', 45, 0, 19800000, 1, 27, 0],
        [207, 'پدرام شریفی', '09127770077', 'شیراز', 'banned', 400, 0, 0, 4, null, 0],
    ];
    return array_map(function ($r) use ($min) {
        [$id, $name, $phone, $city, $st, $days, $wallet, $held, $auc, $ban, $req] = $r;
        return [
            'id' => $id, 'name' => $name, 'phone' => $phone, 'city' => $city, 'status' => $st, 'created_at' => $min(-$days * 1440),
            'wallet' => $wallet, 'held' => $held, 'auctions' => $auc, 'auction_ban_days' => $ban, 'requests' => $req,
            'reason' => $st === 'banned' ? 'انتشار اطلاعات تماس در گفتگو' : null,
        ];
    }, $seed);
};

/* ---------- داده‌ی نمایشی مالی و تسویه (فقط برای قالب) ---------- */
$txData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    // [نوع، شرح، طرف حساب، مبلغ(+ ورودی به سایت/کیف پول، - خروجی)، وضعیت، دقیقه‌ی قبل]
    $seed = [
        ['entry', 'ورودی مزایده‌ی پژو ۲۰۷', 'علی رضایی', 200000, 'paid', 12],
        ['deposit', 'بیعانه‌ی پژو ۲۰۷ اتوماتیک', 'علی رضایی', 19700000, 'paid', 95],
        ['fee', 'هزینه‌ی خدمات بیعانه', 'علی رضایی', 200000, 'paid', 95],
        ['subscription', 'اشتراک ۳۰ روزه', 'اتوگالری هالیوود', 4500000, 'paid', 260],
        ['entry', 'ورودی مزایده‌ی ولوو FH500', 'مریم احمدی', 500000, 'failed', 310],
        ['release', 'آزادسازی بیعانه به نمایشگاه', 'نمایشگاه پارس', -7400000, 'paid', 1440],
        ['refund', 'برگشت بیعانه به خریدار', 'نیلوفر صادقی', -10800000, 'paid', 2100],
        ['withdraw', 'برداشت نمایشگاه', 'آریا موتور', -60000000, 'paid', 3000],
        ['subscription', 'اشتراک ۹۰ روزه', 'نمایشگاه نگین', 11000000, 'paid', 4000],
        ['deposit', 'بیعانه‌ی کامیون بنز ۲۶۳۱', 'رضا نوری', 87000000, 'paid', 180],
    ];
    return array_map(fn($r) => ['type' => $r[0], 'title' => $r[1], 'party' => $r[2], 'amount' => $r[3], 'status' => $r[4], 'at' => $min(-$r[5]), 'ref' => 'TX-' . (9000 + crc32($r[1] . $r[5]) % 900)], $seed);
};

$settleData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    // [id، نمایشگاه، مدیر، مبلغ، شبا، بانک، وضعیت، دقیقه‌ی قبل، توضیح]
    $seed = [
        [401, 'اتوگالری هالیوود', 'بهرام اسدی', 120000000, 'IR120170000000123456789012', 'ملت', 'unpaid', 50, null],
        [402, 'نمایشگاه پارس', 'آرش مرادی', 60000000, 'IR330620000000234567890123', 'ملی', 'unpaid', 200, null],
        [403, 'آریا خودروهای تجاری', 'پویا نصیری', 400000000, 'IR560180000000345678901234', 'پاسارگاد', 'unpaid', 600, null],
        [404, 'نمایشگاه نگین', 'مهدی کیانی', 25000000, 'IR790190000000456789012345', 'سامان', 'unpaid', 1300, null],
        [405, 'اتوگالری هالیوود', 'بهرام اسدی', 15000000, 'IR120170000000123456789012', 'ملت', 'unpaid', 2000, null],
        [406, 'آریا موتور', 'کیوان رحمانی', 60000000, 'IR220170000000567890123456', 'ملت', 'paid', 3000, 'کد پیگیری: ۷۴۸۲۹۱۰۳'],
        [407, 'موتور سیتی', 'کیوان رحمانی', 30000000, 'IR220170000000567890123456', 'ملت', 'rejected', 5000, 'شماره‌ی شبا با نام صاحب حساب مطابقت ندارد'],
    ];
    return array_map(fn($r) => ['id' => $r[0], 'dealer' => $r[1], 'owner' => $r[2], 'amount' => $r[3], 'iban' => $r[4], 'bank' => $r[5], 'status' => $r[6], 'at' => $min(-$r[7]), 'note' => $r[8]], $seed);
};

/* ---------- داده‌ی نمایشی گفتگوها (فقط برای قالب) ---------- */
$chatData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    $c = fn($id, $dealer, $user, $title, $st, $note, $msgs) => ['id' => $id, 'dealer' => $dealer, 'user' => $user, 'title' => $title, 'status' => $st, 'note' => $note, 'messages' => $msgs];
    $m = fn($from, $text, $ago, $hit = null) => ['from' => $from, 'text' => $text, 'at' => $min(-$ago), 'hit' => $hit];
    return [
        $c(1, 'اتوگالری هالیوود', 'علی رضایی', 'پژو ۲۰۷ اتوماتیک', 'flagged', 'تلاش برای خروج از سایت', [
            $m('user', 'سلام، خودرو هنوز موجوده؟', 190), $m('dealer', 'سلام، بله موجوده.', 185),
            $m('user', 'قیمت قابل مذاکره هست؟', 180), $m('dealer', 'اگه بیعانه رو از سایت ندید ۱۵ میلیون تخفیف می‌دم. شمارمو بنویسید: ۰۹۱۲۱۰۰۵۰۰۵', 170, 'phone'),
            $m('user', 'سایت که امنیت بیشتری داره…', 160)]),
        $c(2, 'آریا خودروهای تجاری', 'حسین کریمی', 'ولوو FH500 کشنده', 'normal', null, [
            $m('user', 'کارکرد واقعیش چقدره؟', 700), $m('dealer', 'سیصد و بیست هزار کیلومتر، سند و بیمه کامل.', 690), $m('user', 'ممنون، بیعانه رو پرداخت کردم.', 650)]),
        $c(3, 'آریا موتور', 'مریم احمدی', 'یاماها NMAX 155', 'flagged', 'اختلاف باز', [
            $m('user', 'ترمز جلو مشکل داره، تو آگهی ننوشته بودید.', 3000), $m('dealer', 'موقع بازدید مشکلی نبود، به تلگرام من پیام بدید @aria_moto', 2990, 'link'), $m('user', 'من اختلاف ثبت می‌کنم.', 2950)]),
        $c(4, 'نمایشگاه پارس', 'سارا موسوی', 'سمند LX EF7', 'reviewed', 'بررسی شد؛ مشکلی نبود', [
            $m('user', 'سلام، امروز می‌تونم بیام بازدید؟', 400), $m('dealer', 'بله، تا ساعت ۸ شب در نمایشگاه هستیم.', 390)]),
        $c(5, 'موتور سیتی', 'رضا نوری', 'هوندا CBR 250', 'normal', null, [
            $m('user', 'سلام، تحویل شهرستان دارید؟', 55), $m('dealer', 'سلام، متأسفانه فقط تحویل حضوری.', 50)]),
        $c(6, 'دوچرخه‌ی آرمان', 'نیلوفر صادقی', 'دوچرخه کوهستان جاینت', 'normal', null, [
            $m('user', 'سایز فریم چنده؟', 20), $m('dealer', 'فریم M، برای قد ۱۷۰ تا ۱۸۰.', 15)]),
    ];
};

/* ---------- مقادیر نمایشی تنظیمات تجاری (بک‌اندکار از دیتابیس بخواند) ---------- */
$settingsData = fn() => [
    'deposit' => ['car' => 2, 'heavy' => 1.5, 'bike' => 3, 'bicycle' => 5, 'service_fee' => 200000, 'hold_hours' => 72, 'min_amount' => 500000],
    'auction' => ['entry_fee' => 200000, 'min_step_percent' => 0.5, 'pay_hours' => 24, 'ban_days' => 30, 'extend_minutes' => 3, 'max_duration_hours' => 72],
    'pricing' => ['sub_30' => 1500000, 'sub_90' => 4000000, 'sub_365' => 14000000, 'instant_fee' => 300000, 'auction_listing_fee' => 500000],
    'matching' => ['min_percent' => 60, 'max_requests' => 5, 'w' => ['model' => 30, 'price' => 25, 'year' => 15, 'mileage' => 15, 'city' => 10, 'extra' => 5]],
    'payout' => ['min_withdraw' => 5000000, 'process_hours' => 48],
];

/* ---------- داده‌ی نمایشی داده‌های پایه، محتوا و گزارش فعالیت (فقط برای قالب) ---------- */
$catalogData = fn() => [
    'brands' => ['title' => 'برندها', 'parent' => 'دسته', 'items' => [
        ['پژو', 'سواری', 412, 1], ['سمند', 'سواری', 188, 1], ['هیوندای', 'سواری', 97, 1], ['ولوو', 'سنگین', 23, 1], ['بنز', 'سنگین', 31, 1],
        ['یاماها', 'موتور', 64, 1], ['هوندا', 'موتور', 52, 1], ['جاینت', 'دوچرخه', 14, 1], ['برند قدیمی', 'سواری', 0, 0],
    ]],
    'models' => ['title' => 'مدل‌ها', 'parent' => 'برند', 'items' => [
        ['۲۰۷ اتوماتیک', 'پژو', 210, 1], ['۲۰۷ پانوراما', 'پژو', 96, 1], ['۴۰۵', 'پژو', 106, 1], ['LX EF7', 'سمند', 120, 1], ['النترا', 'هیوندای', 54, 1],
        ['FH500', 'ولوو', 14, 1], ['NMAX 155', 'یاماها', 33, 1], ['CBR 250', 'هوندا', 21, 1],
    ]],
    'cities' => ['title' => 'شهرها', 'parent' => 'استان', 'items' => [
        ['تهران', 'تهران', 820, 1], ['کرج', 'البرز', 140, 1], ['اصفهان', 'اصفهان', 233, 1], ['مشهد', 'خراسان رضوی', 190, 1], ['شیراز', 'فارس', 120, 1], ['تبریز', 'آذربایجان شرقی', 98, 1],
    ]],
    'attrs' => ['title' => 'ویژگی‌ها (رنگ، سوخت، گیربکس)', 'parent' => 'گروه', 'items' => [
        ['سفید', 'رنگ', 0, 1], ['مشکی', 'رنگ', 0, 1], ['نقره‌ای', 'رنگ', 0, 1], ['بنزین', 'سوخت', 0, 1], ['دیزل', 'سوخت', 0, 1], ['اتوماتیک', 'گیربکس', 0, 1], ['دنده‌ای', 'گیربکس', 0, 1],
    ]],
];

$pageData = fn() => [
    ['id' => 1, 'title' => 'قوانین و مقررات', 'slug' => 'terms', 'status' => 'active', 'ago' => '۳ روز پیش', 'body' => "۱. نمایشگاه‌داران موظف به ثبت اطلاعات صحیح خودرو هستند.\n۲. بیعانه تا مشخص‌شدن نتیجه‌ی معامله نزد سایت نگه داشته می‌شود.\n۳. تبادل اطلاعات تماس پیش از پرداخت بیعانه ممنوع است."],
    ['id' => 2, 'title' => 'حریم خصوصی', 'slug' => 'privacy', 'status' => 'active', 'ago' => '۲ هفته پیش', 'body' => 'اطلاعات کاربران فقط برای ارائه‌ی خدمات سایت استفاده می‌شود و در اختیار اشخاص ثالث قرار نمی‌گیرد.'],
    ['id' => 3, 'title' => 'راهنمای خرید با بیعانه', 'slug' => 'deposit-guide', 'status' => 'active', 'ago' => '۱ ماه پیش', 'body' => 'پس از انتخاب خودرو، بیعانه را از کیف پول پرداخت کنید تا اطلاعات تماس نمایشگاه نمایش داده شود.'],
    ['id' => 4, 'title' => 'راهنمای شرکت در مزایده', 'slug' => 'auction-guide', 'status' => 'pending', 'ago' => 'پیش‌نویس', 'body' => 'ابتدا هزینه‌ی ورود را بپردازید؛ سپس پیشنهاد خود را ثبت کنید.'],
    ['id' => 5, 'title' => 'سوالات متداول', 'slug' => 'faq', 'status' => 'active', 'ago' => '۵ روز پیش', 'body' => 'پرسش و پاسخ‌های پرتکرار کاربران.'],
    ['id' => 6, 'title' => 'درباره‌ی ما', 'slug' => 'about', 'status' => 'active', 'ago' => '۲ ماه پیش', 'body' => 'معرفی سایت و تیم.'],
];

$logData = function () {
    $min = fn($m) => now()->addMinutes($m)->toIso8601String();
    // [نوع، اقدام، هدف، دقیقه‌ی قبل]
    $seed = [
        ['listings', 'تأیید آگهی', 'پژو ۲۰۷ اتوماتیک • اتوگالری هالیوود', 8],
        ['listings', 'رد آگهی', 'NMAX 155 • دلیل: تصویر نامرتبط', 40],
        ['auctions', 'لغو مزایده', 'رنو تندر ۹۰ • دلیل: درخواست نمایشگاه‌دار', 95],
        ['auctions', 'بن برنده‌ی مزایده', 'نیلوفر صادقی • ۳۰ روز', 180],
        ['deals', 'آزادسازی بیعانه', 'DP-5506 • ۷٬۴۰۰٬۰۰۰ تومان', 300],
        ['deals', 'تصمیم اختلاف', 'DS-701 • برگشت به خریدار', 420],
        ['people', 'تأیید احراز هویت', 'اتوگالری هالیوود', 600],
        ['people', 'مسدودسازی کاربر', 'پدرام شریفی • دلیل: انتشار اطلاعات تماس', 1000],
        ['finance', 'ثبت پرداخت تسویه', 'آریا موتور • ۶۰٬۰۰۰٬۰۰۰ تومان • کد پیگیری ۷۴۸۲۹۱۰۳', 1500],
        ['settings', 'ویرایش تنظیمات', 'درصد بیعانه‌ی سواری: ۱٫۵ ← ۲', 2400],
        ['chats', 'پرچم‌گذاری گفتگو', 'علی رضایی و اتوگالری هالیوود', 2900],
        ['settings', 'ورود به پنل', 'مرورگر Chrome • ویندوز', 3000],
    ];
    return array_map(fn($r) => ['type' => $r[0], 'action' => $r[1], 'target' => $r[2], 'at' => $min(-$r[3]), 'ip' => '185.12.' . (30 + $r[3] % 9) . '.' . (10 + $r[3] % 80)], $seed);
};

Route::prefix('admin')->name('admin.')->group(function () use ($catalogData, $pageData, $logData, $settingsData, $chatData, $txData, $settleData, $auctionData, $depositData, $disputeData, $dealerData, $userData, $requestData) {

    /* ---------- ورود و خروج ---------- */
    Route::get('/login', fn() => view('admin.pages.login'))->name('login');
    Route::post('/login', fn() => redirect()->route('admin.dashboard'))->name('login.submit');   // نمایشی
    Route::post('/logout', fn() => redirect()->route('admin.login'))->name('logout');            // نمایشی

    /* ---------- داشبورد ---------- */
    Route::get('/', function () {
        $img = fn($f) => asset('img/mock/' . $f);

        $queues = [
            ['label' => 'احراز هویت', 'count' => 4, 'icon' => 'user-check', 'tone' => 'sky', 'route' => 'admin.dealers.index'],
            ['label' => 'آگهی در انتظار', 'count' => 12, 'icon' => 'list-checks', 'tone' => 'peach', 'route' => 'admin.listings.index'],
            ['label' => 'اختلاف‌ها', 'count' => 3, 'icon' => 'alert-triangle', 'tone' => 'rose', 'route' => 'admin.disputes.index'],
            ['label' => 'درخواست برداشت', 'count' => 5, 'icon' => 'banknote', 'tone' => 'sage', 'route' => 'admin.settlements.index'],
            ['label' => 'گفتگوی گزارش‌شده', 'count' => 2, 'icon' => 'flag', 'tone' => 'sand', 'route' => 'admin.chats.index'],
        ];

        return view('admin.pages.dashboard', [
            'queues' => $queues,
            'pendingTotal' => collect($queues)->sum('count'),
            'today' => ['percent' => 68, 'done' => 17, 'total' => 25],
            'escrow' => 48200000000,
            'escrowCount' => 14,
            'liveCount' => 3,
            'kpi' => [
                'users' => 12840, 'users_delta' => 8,
                'dealers' => 312, 'dealers_delta' => 3,
                'listings' => 1668, 'listings_delta' => 5,
                'revenue' => 1260, 'revenue_delta' => -2,   // میلیون تومان
            ],
            // درآمد روزانه (میلیون تومان): a = فروش اشتراک، b = مزایده و فروش فوری
            'revenue' => [
                ['day' => 'ش', 'a' => 82, 'b' => 61], ['day' => 'ی', 'a' => 74, 'b' => 48], ['day' => 'د', 'a' => 91, 'b' => 70],
                ['day' => 'س', 'a' => 66, 'b' => 52], ['day' => 'چ', 'a' => 40, 'b' => 31], ['day' => 'پ', 'a' => 88, 'b' => 94], ['day' => 'ج', 'a' => 55, 'b' => 42],
            ],
            'revTotal' => 496,
            'revAuction' => 398,
            'pending' => [
                ['title' => 'پژو ۲۰۷ اتوماتیک', 'meta' => 'مدل ۱۴۰۲ • ۲۴٬۰۰۰ کیلومتر', 'dealer' => 'اتوگالری هالیوود', 'price' => 985000000, 'posted' => '۲ ساعت پیش', 'image' => $img('car-207.jpg')],
                ['title' => 'ولوو FH500 کشنده', 'meta' => 'مدل ۱۳۹۹ • ۳۲۰٬۰۰۰ کیلومتر', 'dealer' => 'آریا خودروهای تجاری', 'price' => 9800000000, 'posted' => '۵ ساعت پیش', 'image' => $img('truck-fh.jpg')],
                ['title' => 'یاماها NMAX 155', 'meta' => 'مدل ۱۴۰۳ • ۴٬۵۰۰ کیلومتر', 'dealer' => 'آریا موتور', 'price' => 385000000, 'posted' => 'دیروز', 'image' => $img('yamaha.jpg')],
            ],
            'weekMarks' => '1,3,5',
            'upcoming' => [
                ['title' => 'مزایده‌ی پژو ۲۰۷', 'dealer' => 'اتوگالری هالیوود', 'icon' => 'car', 'tone' => 'sage', 'live' => true, 'at' => now()->addMinutes(42)->toIso8601String()],
                ['title' => 'مزایده‌ی ولوو FH500', 'dealer' => 'آریا خودروهای تجاری', 'icon' => 'truck', 'tone' => 'peach', 'live' => false, 'at' => now()->addHours(5)->addMinutes(12)->toIso8601String()],
                ['title' => 'مزایده‌ی یاماها NMAX', 'dealer' => 'آریا موتور', 'icon' => 'bike', 'tone' => 'sky', 'live' => false, 'at' => now()->addHours(26)->toIso8601String()],
            ],
            'activity' => [
                ['title' => 'نمایشگاه جدید ثبت‌نام کرد', 'sub' => 'اتوگالری آرش', 'icon' => 'store', 'tone' => 'sky', 'ago' => '۱۰ دقیقه'],
                ['title' => 'بیعانه پرداخت شد', 'sub' => 'سمند LX • ۲۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'shield-check', 'tone' => 'sage', 'ago' => '۳۲ دقیقه'],
                ['title' => 'اختلاف جدید ثبت شد', 'sub' => 'معامله‌ی شماره‌ی ۱۰۴', 'icon' => 'alert-triangle', 'tone' => 'rose', 'ago' => '۱ ساعت'],
                ['title' => 'درخواست برداشت', 'sub' => 'نمایشگاه پارس • ۱۸۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'banknote', 'tone' => 'peach', 'ago' => '۲ ساعت'],
            ],
        ]);
    })->name('dashboard');

    /* ---------- آگهی‌ها ---------- */
    Route::get('/listings', function () {
        $img = fn($f) => asset('img/mock/' . $f);
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
        $listings = array_map(function ($r) use ($img, $desc, $specsFor) {
            [$code, $title, $cat, $price, $sale, $status, $dealer, $city, $posted, $image, $year, $km, $color, $extra] = $r;
            return [
                'id' => $code, 'code' => $code, 'title' => $title, 'cat' => $cat, 'price' => $price, 'sale' => $sale, 'status' => $status,
                'dealer' => $dealer, 'city' => $city, 'posted' => $posted, 'reason' => $r[14] ?? '', 'desc' => $desc,
                'images' => [$img($image), $img('detail-t1.jpg'), $img('detail-t2.jpg'), $img('detail-t3.jpg')],
                'specs' => $specsFor($cat, $year, $km, $color, $extra),
            ];
        }, $seed);

        return view('admin.pages.listings.index', ['listings' => $listings]);
    })->name('listings.index');

    /* ---------- مزایده‌ها ---------- */
    Route::get('/auctions', fn() => view('admin.pages.auctions.index', ['auctions' => $auctionData()]))->name('auctions.index');
    Route::get('/auctions/{id}', function ($id) use ($auctionData) {
        $all = $auctionData();
        $a = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.auctions.show', ['a' => $a]);
    })->whereNumber('id')->name('auctions.show');

    /* ---------- درخواست‌های خرید کاربران ---------- */
    Route::get('/requests', fn() => view('admin.pages.requests.index', ['requests' => $requestData()]))->name('requests.index');
    Route::get('/requests/{id}', function ($id) use ($requestData) {
        $all = $requestData();
        $r = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.requests.show', ['r' => $r]);
    })->whereNumber('id')->name('requests.show');

    /* ---------- بیعانه‌ها و رزروها ---------- */
    Route::get('/deposits', fn() => view('admin.pages.deposits.index', ['deposits' => $depositData()]))->name('deposits.index');
    Route::get('/deposits/{id}', function ($id) use ($depositData) {
        $all = $depositData();
        $d = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.deposits.show', ['d' => $d]);
    })->whereNumber('id')->name('deposits.show');

    /* ---------- اختلاف‌ها ---------- */
    Route::get('/disputes', fn() => view('admin.pages.disputes.index', ['disputes' => $disputeData()]))->name('disputes.index');
    Route::get('/disputes/{id}', function ($id) use ($disputeData) {
        $all = $disputeData();
        $x = array_values(array_filter($all, fn($r) => $r['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.disputes.show', ['x' => $x]);
    })->whereNumber('id')->name('disputes.show');

    /* ---------- نمایشگاه‌داران ---------- */
    Route::get('/dealers', fn() => view('admin.pages.dealers.index', ['dealers' => $dealerData()]))->name('dealers.index');
    Route::get('/dealers/{id}', function ($id) use ($dealerData) {
        $all = $dealerData();
        $d = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.dealers.show', ['d' => $d]);
    })->whereNumber('id')->name('dealers.show');

    /* ---------- کاربران ---------- */
    Route::get('/users', fn() => view('admin.pages.users.index', ['users' => $userData()]))->name('users.index');
    Route::get('/users/{id}', function ($id) use ($userData) {
        $all = $userData();
        $u = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.users.show', ['u' => $u]);
    })->whereNumber('id')->name('users.show');

    /* ---------- مالی و تسویه ---------- */
    Route::get('/finance', fn() => view('admin.pages.finance.index', ['txs' => $txData()]))->name('finance.index');
    Route::get('/settlements', fn() => view('admin.pages.settlements.index', ['items' => $settleData()]))->name('settlements.index');

    /* ---------- گفتگوها (فقط‌خواندنی برای ادمین) ---------- */
    Route::get('/chats', fn() => view('admin.pages.chats.index', ['chats' => $chatData()]))->name('chats.index');

    /* ---------- تنظیمات تجاری ---------- */
    Route::get('/settings', fn() => view('admin.pages.settings.index', ['s' => $settingsData()]))->name('settings.index');

    /* ---------- داده‌های پایه، محتوا، گزارش فعالیت ---------- */
    Route::get('/catalog', fn() => view('admin.pages.catalog.index', ['sets' => $catalogData()]))->name('catalog.index');
    Route::get('/content', fn() => view('admin.pages.content.index', ['pages' => $pageData()]))->name('content.index');
    Route::get('/logs', fn() => view('admin.pages.logs.index', ['logs' => $logData()]))->name('logs.index');
});
