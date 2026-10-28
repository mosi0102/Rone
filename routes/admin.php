<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| مسیرهای پنل ادمین (فایل مستقل از routes/web.php)
|--------------------------------------------------------------------------
| اتصال: در انتهای routes/web.php این یک خط اضافه شود:
|     require __DIR__ . '/admin.php';
|
| داده‌های این فایل فقط نمایشی است. بک‌اندکار هر مسیر را با کنترلر واقعی جایگزین کند؛
| نام مسیرها (admin.*) را تغییر ندهید، چون ناوبری به آن‌ها وابسته است.
| بک‌اندکار: میدل‌ور احراز هویت و دسترسی ادمین را روی گروه «بدون ورود» بگذارد.
*/

// داده‌ی مشترک همه‌ی صفحات ادمین (تعداد موارد منتظر در سایدبار و اطلاعات مدیر)
View::composer('admin.*', function ($view) {
    $view->with([
        'admin'  => ['name' => 'مدیر سایت', 'first_name' => 'مدیر'],
        'badges' => [
            'admin.listings.index'    => 12,
            'admin.disputes.index'    => 3,
            'admin.dealers.index'     => 4,
            'admin.settlements.index' => 5,
        ],
    ]);
});

/* ---------- داده‌ی نمایشی مزایده‌ها (فقط برای قالب) ---------- */
$auctionData = function () {
    $img   = fn ($f) => asset('img/mock/' . $f);
    $users = [
        ['name' => 'علی رضایی',   'phone' => '09121110011'], ['name' => 'مریم احمدی',  'phone' => '09122220022'],
        ['name' => 'حسین کریمی',  'phone' => '09123330033'], ['name' => 'سارا موسوی',  'phone' => '09124440044'],
        ['name' => 'رضا نوری',    'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
    ];
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();

    // [id، عنوان، دسته، نمایشگاه، تصویر، قیمت پایه، گام، ورودی، وضعیت، شروع(دقیقه)، پایان(دقیقه)، تعداد پیشنهاد، شرکت‌کننده، برنده، مهلت پرداخت(دقیقه)، وضعیت پرداخت]
    $seed = [
        [11, 'پژو ۲۰۷ اتوماتیک', 'car',   'اتوگالری هالیوود',      'car-207.jpg',      850000000,  5000000,  200000, 'live',         -90,   42,    14, 6, null, null, null],
        [12, 'ولوو FH500 کشنده', 'heavy', 'آریا خودروهای تجاری',   'truck-fh.jpg',     9200000000, 50000000, 500000, 'live',         -30,   312,   8,  4, null, null, null],
        [13, 'یاماها NMAX 155',  'bike',  'آریا موتور',            'yamaha.jpg',       350000000,  2000000,  100000, 'scheduled',    1560,  1680,  0,  0, null, null, null],
        [14, 'سمند LX EF7',      'car',   'نمایشگاه پارس',         'dealer-stock-1.jpg', 480000000, 3000000, 150000, 'scheduled',    4320,  4440,  0,  0, null, null, null],
        [15, 'کامیون بنز ۲۶۳۱',  'heavy', 'سنگین‌خودرو البرز',     'truck-fh-a.jpg',   5800000000, 50000000, 500000, 'awaiting_pay', -240,  -120,  22, 9, 2,    300,  'waiting'],
        [16, 'هیوندای النترا',   'car',   'نمایشگاه نگین',         'detail-main.jpg',  1700000000, 10000000, 300000, 'awaiting_pay', -2000, -1800, 17, 7, 0,    -60,  'overdue'],
        [17, 'موتور هوندا CBR 250', 'bike', 'موتور سیتی',          'dealer-stock-3.jpg', 360000000, 2000000, 100000, 'defaulted',    -5000, -4800, 11, 5, 4,    -2900, 'banned'],
        [18, 'تیبا ۲ صفر',       'car',   'اتوگالری هالیوود',      'detail-t1.jpg',    370000000,  2000000,  100000, 'ended',        -6200, -6000, 19, 8, 1,    -5000, 'paid'],
        [19, 'رنو تندر ۹۰',      'car',   'نمایشگاه پارس',         'detail-t3.jpg',    560000000,  3000000,  150000, 'cancelled',    -300,  -180,  0,  0, null, null, null],
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

Route::prefix('admin')->name('admin.')->group(function () use ($auctionData) {

    /* ---------- ورود و خروج ---------- */
    Route::get('/login', fn () => view('admin.pages.login'))->name('login');
    Route::post('/login', fn () => redirect()->route('admin.dashboard'))->name('login.submit');   // نمایشی
    Route::post('/logout', fn () => redirect()->route('admin.login'))->name('logout');            // نمایشی

    /* ---------- داشبورد ---------- */
    Route::get('/', function () {
        $img = fn ($f) => asset('img/mock/' . $f);

        $queues = [
            ['label' => 'احراز هویت',      'count' => 4,  'icon' => 'user-check',    'tone' => 'sky',   'route' => 'admin.dealers.index'],
            ['label' => 'آگهی در انتظار',  'count' => 12, 'icon' => 'list-checks',   'tone' => 'peach', 'route' => 'admin.listings.index'],
            ['label' => 'اختلاف‌ها',       'count' => 3,  'icon' => 'alert-triangle', 'tone' => 'rose',  'route' => 'admin.disputes.index'],
            ['label' => 'درخواست برداشت',  'count' => 5,  'icon' => 'banknote',      'tone' => 'sage',  'route' => 'admin.settlements.index'],
            ['label' => 'گفتگوی گزارش‌شده', 'count' => 2,  'icon' => 'flag',          'tone' => 'sand',  'route' => 'admin.chats.index'],
        ];

        return view('admin.pages.dashboard', [
            'queues'       => $queues,
            'pendingTotal' => collect($queues)->sum('count'),
            'today'        => ['percent' => 68, 'done' => 17, 'total' => 25],
            'escrow'       => 48200000000,
            'escrowCount'  => 14,
            'liveCount'    => 3,
            'kpi'          => [
                'users' => 12840, 'users_delta' => 8,
                'dealers' => 312, 'dealers_delta' => 3,
                'listings' => 1668, 'listings_delta' => 5,
                'revenue' => 1260, 'revenue_delta' => -2,   // میلیون تومان
            ],
            // درآمد روزانه (میلیون تومان): a = فروش اشتراک، b = مزایده و فروش فوری
            'revenue'    => [
                ['day' => 'ش', 'a' => 82, 'b' => 61], ['day' => 'ی', 'a' => 74, 'b' => 48], ['day' => 'د', 'a' => 91, 'b' => 70],
                ['day' => 'س', 'a' => 66, 'b' => 52], ['day' => 'چ', 'a' => 40, 'b' => 31], ['day' => 'پ', 'a' => 88, 'b' => 94], ['day' => 'ج', 'a' => 55, 'b' => 42],
            ],
            'revTotal'   => 496,
            'revAuction' => 398,
            'pending'    => [
                ['title' => 'پژو ۲۰۷ اتوماتیک', 'meta' => 'مدل ۱۴۰۲ • ۲۴٬۰۰۰ کیلومتر', 'dealer' => 'اتوگالری هالیوود', 'price' => 985000000, 'posted' => '۲ ساعت پیش', 'image' => $img('car-207.jpg')],
                ['title' => 'ولوو FH500 کشنده', 'meta' => 'مدل ۱۳۹۹ • ۳۲۰٬۰۰۰ کیلومتر', 'dealer' => 'آریا خودروهای تجاری', 'price' => 9800000000, 'posted' => '۵ ساعت پیش', 'image' => $img('truck-fh.jpg')],
                ['title' => 'یاماها NMAX 155', 'meta' => 'مدل ۱۴۰۳ • ۴٬۵۰۰ کیلومتر', 'dealer' => 'آریا موتور', 'price' => 385000000, 'posted' => 'دیروز', 'image' => $img('yamaha.jpg')],
            ],
            'weekMarks'  => '1,3,5',
            'upcoming'   => [
                ['title' => 'مزایده‌ی پژو ۲۰۷', 'dealer' => 'اتوگالری هالیوود', 'icon' => 'car',   'tone' => 'sage',  'live' => true,  'at' => now()->addMinutes(42)->toIso8601String()],
                ['title' => 'مزایده‌ی ولوو FH500', 'dealer' => 'آریا خودروهای تجاری', 'icon' => 'truck', 'tone' => 'peach', 'live' => false, 'at' => now()->addHours(5)->addMinutes(12)->toIso8601String()],
                ['title' => 'مزایده‌ی یاماها NMAX', 'dealer' => 'آریا موتور', 'icon' => 'bike',  'tone' => 'sky',   'live' => false, 'at' => now()->addHours(26)->toIso8601String()],
            ],
            'activity'   => [
                ['title' => 'نمایشگاه جدید ثبت‌نام کرد', 'sub' => 'اتوگالری آرش', 'icon' => 'store', 'tone' => 'sky', 'ago' => '۱۰ دقیقه'],
                ['title' => 'بیعانه پرداخت شد', 'sub' => 'سمند LX • ۲۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'shield-check', 'tone' => 'sage', 'ago' => '۳۲ دقیقه'],
                ['title' => 'اختلاف جدید ثبت شد', 'sub' => 'معامله‌ی شماره‌ی ۱۰۴', 'icon' => 'alert-triangle', 'tone' => 'rose', 'ago' => '۱ ساعت'],
                ['title' => 'درخواست برداشت', 'sub' => 'نمایشگاه پارس • ۱۸۰٬۰۰۰٬۰۰۰ تومان', 'icon' => 'banknote', 'tone' => 'peach', 'ago' => '۲ ساعت'],
            ],
        ]);
    })->name('dashboard');

    /* ---------- آگهی‌ها ---------- */
    Route::get('/listings', function () {
        $img = fn ($f) => asset('img/mock/' . $f);
        $desc = 'خودرو تک‌مالک، سرویس‌ها به‌موقع انجام شده و آماده‌ی بازدید حضوری با هماهنگی قبلی در نمایشگاه است.';
        $specsFor = fn ($cat, $year, $km, $color, $extra) => match ($cat) {
            'heavy' => [['سال ساخت', $year], ['کارکرد', number_format($km) . ' کیلومتر'], ['سوخت', 'دیزل'], ['رنگ', $color], ['نوع', $extra], ['وضعیت بدنه', 'سالم']],
            'bike'  => [['سال ساخت', $year], ['کارکرد', number_format($km) . ' کیلومتر'], ['حجم موتور', $extra], ['رنگ', $color], ['گیربکس', 'دنده‌ای'], ['بیمه', 'تا اسفند ۱۴۰۵']],
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
    Route::get('/auctions', fn () => view('admin.pages.auctions.index', ['auctions' => $auctionData()]))->name('auctions.index');
    Route::get('/auctions/{id}', function ($id) use ($auctionData) {
        $all = $auctionData();
        $a = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.auctions.show', ['a' => $a]);
    })->whereNumber('id')->name('auctions.show');

    /* ---------- صفحات بعدی — فعلاً «به‌زودی» ---------- */
    foreach ([
        'deposits.index'    => ['/deposits', 'بیعانه‌ها و رزروها'],
        'disputes.index'    => ['/disputes', 'اختلاف‌ها'],
        'users.index'       => ['/users', 'کاربران'],
        'dealers.index'     => ['/dealers', 'نمایشگاه‌داران'],
        'finance.index'     => ['/finance', 'مالی و تراکنش‌ها'],
        'settlements.index' => ['/settlements', 'تسویه‌ها'],
        'chats.index'       => ['/chats', 'گفتگوها'],
        'settings.index'    => ['/settings', 'تنظیمات تجاری'],
        'catalog.index'     => ['/catalog', 'داده‌های پایه'],
        'content.index'     => ['/content', 'صفحات و محتوا'],
        'logs.index'        => ['/logs', 'گزارش فعالیت'],
    ] as $name => [$uri, $title]) {
        Route::get($uri, fn () => view('admin.pages.soon', ['title' => $title]))->name($name);
    }
});
