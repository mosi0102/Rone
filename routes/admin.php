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


/* ---------- داده‌ی نمایشی بیعانه‌ها و اختلاف‌ها (فقط برای قالب) ---------- */
$depositData = function () {
    $img = fn ($f) => asset('img/mock/' . $f);
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    $buyers = [
        ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
        ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
        ['name' => 'رضا نوری', 'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
    ];
    // [id، عنوان، دسته، نمایشگاه، تصویر، قیمت خودرو، درصد بیعانه، منبع(instant|auction)، وضعیت، ثبت(دقیقه‌ی قبل)، مهلت(دقیقه‌ی بعد)، خریدار]
    $seed = [
        [301, 'پژو ۲۰۷ اتوماتیک',     'car',   'اتوگالری هالیوود',    'car-207.jpg',        985000000,  2,   'instant', 'held',     95,    2800, 0],
        [302, 'ولوو FH500 کشنده',     'heavy', 'آریا خودروهای تجاری', 'truck-fh.jpg',       9800000000, 1.5, 'instant', 'held',     600,   1200, 2],
        [303, 'کامیون بنز ۲۶۳۱',      'heavy', 'سنگین‌خودرو البرز',   'truck-fh-a.jpg',     5800000000, 1.5, 'auction', 'held',     180,   4200, 4],
        [304, 'یاماها NMAX 155',      'bike',  'آریا موتور',          'yamaha.jpg',         385000000,  3,   'instant', 'dispute',  3100,  null, 1],
        [305, 'سمند LX EF7',          'car',   'نمایشگاه پارس',       'dealer-stock-1.jpg', 480000000,  2,   'instant', 'dispute',  4200,  null, 3],
        [306, 'تیبا ۲ صفر',           'car',   'اتوگالری هالیوود',    'detail-t1.jpg',      370000000,  2,   'auction', 'released', 6000,  null, 5],
        [307, 'رنو تندر ۹۰',          'car',   'نمایشگاه پارس',       'detail-t3.jpg',      560000000,  2,   'instant', 'released', 9000,  null, 0],
        [308, 'موتور هوندا CBR 250',  'bike',  'موتور سیتی',          'dealer-stock-3.jpg', 360000000,  3,   'instant', 'refunded', 7000,  null, 2],
        [309, 'هیوندای النترا',       'car',   'نمایشگاه نگین',       'detail-main.jpg',    1700000000, 2,   'auction', 'refunded', 11000, null, 4],
        [310, 'پژو ۲۰۷ اتوماتیک',     'car',   'اتوگالری هالیوود',    'car-207-a.jpg',      990000000,  2,   'instant', 'held',     30,    2900, 5],
    ];
    return array_map(function ($r) use ($buyers, $img, $min) {
        [$id, $title, $cat, $dealer, $image, $price, $pct, $src, $st, $agoM, $leftM, $b] = $r;
        $amount = (int) round($price * $pct / 100);
        $events = [['t' => $min(-$agoM), 'label' => 'بیعانه از کیف پول خریدار کسر و در امانت قرار گرفت', 'tone' => 'info']];
        if ($st === 'released') $events[] = ['t' => $min(-($agoM - 2400)), 'label' => 'معامله تأیید شد و بیعانه به کیف پول نمایشگاه‌دار منتقل شد', 'tone' => 'ok'];
        if ($st === 'refunded') $events[] = ['t' => $min(-($agoM - 1500)), 'label' => 'معامله انجام نشد و بیعانه به کیف پول خریدار برگشت', 'tone' => 'warn'];
        if ($st === 'dispute')  $events[] = ['t' => $min(-($agoM - 1300)), 'label' => 'اختلاف ثبت شد؛ بیعانه تا تعیین تکلیف مسدود است', 'tone' => 'bad'];
        return [
            'id' => $id, 'code' => 'DP-' . (5200 + $id), 'title' => $title, 'cat' => $cat, 'dealer' => $dealer, 'image' => $img($image),
            'price' => $price, 'percent' => $pct, 'amount' => $amount, 'source' => $src, 'status' => $st,
            'created_at' => $min(-$agoM), 'hold_until' => $leftM !== null ? $min($leftM) : null,
            'buyer' => $buyers[$b], 'events' => array_reverse($events), 'listing_code' => 31000 + $id,
        ];
    }, $seed);
};

$disputeData = function () use ($depositData) {
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    $deps = [];
    foreach ($depositData() as $d) $deps[$d['id']] = $d;
    // [id، کد بیعانه، بازکننده(buyer|dealer)، موضوع، شرح، وضعیت، ثبت(دقیقه‌ی قبل)، پاسخ طرف مقابل]
    $seed = [
        [901, 304, 'buyer',  'مغایرت خودرو با آگهی', 'موتور هنگام بازدید دچار خرابی قطعه‌ی ترمز بود و نمایشگاه‌دار آن را در آگهی ذکر نکرده بود. خواستار برگشت بیعانه هستم.', 'open',   1300, null],
        [902, 305, 'dealer', 'انصراف خریدار پس از رزرو', 'خریدار پس از دو روز هماهنگی بدون دلیل از معامله منصرف شد و خودرو را از فروش خارج کرده بودیم. درخواست آزادسازی بیعانه.', 'review', 2900, 'من با کارشناس مستقل بررسی کردم و رنگ‌شدگی دو در مخفی شده بود؛ به همین دلیل منصرف شدم.'],
        [903, 306, 'buyer',  'عدم تحویل خودرو در زمان مقرر', 'نمایشگاه‌دار سه بار قرار تحویل را به تعویق انداخت.', 'resolved', 7000, 'تحویل به دلیل مشکل مدارک بود و در نهایت انجام شد.'],
    ];
    return array_map(function ($r) use ($deps, $min) {
        [$id, $depId, $by, $subject, $text, $st, $agoM, $reply] = $r;
        $d = $deps[$depId] ?? array_values($deps)[0];
        return [
            'id' => $id, 'code' => 'DS-' . (700 + $id), 'deposit' => $d, 'by' => $by, 'subject' => $subject, 'text' => $text, 'reply' => $reply,
            'status' => $st, 'opened_at' => $min(-$agoM), 'age_hours' => (int) round($agoM / 60),
            'resolution' => $st === 'resolved' ? ['type' => 'release', 'note' => 'پس از بررسی مدارک، تحویل انجام شده بود؛ بیعانه به نمایشگاه‌دار آزاد شد.', 'at' => $min(-5000)] : null,
            'evidence' => $id === 901 ? ['detail-t1.jpg', 'detail-t2.jpg'] : ($id === 902 ? ['detail-t3.jpg'] : []),
            'messages' => [
                ['from' => 'buyer',  'text' => 'سلام، موتور رو از نزدیک دیدم، ترمز جلو مشکل داره.', 'at' => $min(-$agoM - 120)],
                ['from' => 'dealer', 'text' => 'در زمان بازدید مشکلی نبود، احتمالاً بعدش پیش اومده.', 'at' => $min(-$agoM - 100)],
                ['from' => 'buyer',  'text' => 'تو آگهی چیزی از این موضوع ننوشته بودید.', 'at' => $min(-$agoM - 90)],
            ],
        ];
    }, $seed);
};

/* ---------- داده‌ی نمایشی «درخواست خرید» کاربران (فقط برای قالب) ---------- */
$requestData = function () {
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    $img = fn ($f) => asset('img/mock/' . $f);
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
        [501, 0, 'car',     'پژو ۲۰۷ اتوماتیک، سفید',          'پژو ۲۰۷',        '۱۴۰۱', 1000000000, '۵۰٬۰۰۰', 'تهران',  'active',    2,  [[0, 96], [1, 82], [4, 41]]],
        [502, 1, 'car',     'خودرو شاسی‌بلند اقتصادی',         'هر مدل',         '۱۴۰۲', 1900000000, '۸۰٬۰۰۰', 'مشهد',   'active',    9,  [[2, 68], [3, 44]]],
        [503, 2, 'bike',    'موتور ۱۵۰ تا ۲۵۰ سی‌سی',          'هر مدل',         '۱۴۰۰', 400000000,  '۲۵٬۰۰۰', 'تبریز',  'active',    5,  [[6, 91], [5, 77]]],
        [504, 3, 'heavy',   'کشنده‌ی ولوو یا بنز',             'ولوو / بنز',     '۱۳۹۸', 10000000000, '۳۵۰٬۰۰۰', 'تهران', 'active',    14, []],
        [505, 4, 'bicycle', 'دوچرخه‌ی کوهستان ۲۷٫۵ اینچ',     'هر مدل',         '۱۴۰۲', 45000000,   '—',       'تهران',  'active',    1,  [[7, 88]]],
        [506, 1, 'car',     'سمند یا پژو ۴۰۵ دنده‌ای',         'سمند / ۴۰۵',     '۱۳۹۹', 600000000,  '۹۰٬۰۰۰', 'اصفهان', 'cancelled', 20, [[3, 73]]],
        [507, 2, 'car',     'خودروی صفر کیلومتر زیر ۴۰۰ میلیون', 'تیبا / ساینا',  '۱۴۰۳', 400000000,  '۵٬۰۰۰',  'کرج',    'suspended', 6,  [[4, 94]]],
    ];
    return array_map(function ($r) use ($users, $pool, $img, $min) {
        [$id, $u, $cat, $summary, $model, $year, $budget, $km, $city, $st, $days, $mt] = $r;
        $matches = array_map(function ($m) use ($pool, $img) {
            $l = $pool[$m[0]];
            return ['title' => $l[0], 'image' => $img($l[1]), 'price' => $l[2], 'city' => $l[3], 'dealer' => $l[4], 'code' => $l[5], 'percent' => $m[1]];
        }, $mt);
        usort($matches, fn ($a, $b) => $b['percent'] <=> $a['percent']);
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
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    $img = fn ($f) => asset('img/mock/' . $f);
    // [id، نام نمایشگاه، مدیر، موبایل، شهر، وضعیت، ثبت(روز قبل)، آگهی فعال، فروش، کیف پول، اشتراک(روز مانده|null)، لوگو]
    $seed = [
        [101, 'نمایشگاه ستاره', 'امیر حسینی',    '09131000101', 'اصفهان', 'pending',   1,  0,  0,  0,        null, 'dealer-thumb-1.jpg'],
        [102, 'اتوگالری مهر',   'کامران یزدانی', '09132000202', 'شیراز',  'pending',   2,  0,  0,  0,        null, 'dealer-thumb-2.jpg'],
        [103, 'دوچرخه‌ی نیما',  'نیما قاسمی',    '09133000303', 'تهران',  'pending',   3,  0,  0,  0,        null, 'dealer-thumb-3.jpg'],
        [104, 'موتور پلاس',     'سعید فرهادی',   '09134000404', 'مشهد',   'pending',   4,  0,  0,  0,        null, 'dealer-thumb-1.jpg'],
        [105, 'اتوگالری هالیوود', 'بهرام اسدی',  '09121005005', 'تهران',  'verified',  120, 14, 31, 184000000, 34,   'dealer-thumb-1.jpg'],
        [106, 'نمایشگاه پارس',  'آرش مرادی',     '09122006006', 'اصفهان', 'verified',  200, 9,  22, 96000000,  12,   'dealer-thumb-2.jpg'],
        [107, 'آریا خودروهای تجاری', 'پویا نصیری', '09123007007', 'تهران', 'verified',  90,  6,  8,  540000000, 61,   'dealer-thumb-3.jpg'],
        [108, 'نمایشگاه نگین',  'مهدی کیانی',    '09124008008', 'مشهد',   'suspended', 150, 0,  12, 40000000,  null, 'dealer-thumb-2.jpg'],
        [109, 'موتور سیتی',     'کیوان رحمانی',  '09125009009', 'تبریز',  'rejected',  18,  0,  0,  0,        null, 'dealer-thumb-3.jpg'],
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
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    // [id، نام، موبایل، شهر، وضعیت، عضویت(روز قبل)، کیف پول، بیعانه‌ی در امانت، مزایده‌ی شرکت‌کرده، محرومیت از مزایده(روز|null)، درخواست خرید فعال]
    $seed = [
        [201, 'علی رضایی',   '09121110011', 'تهران',  'active', 300, 2400000,  19700000, 6, null, 1],
        [202, 'مریم احمدی',  '09122220022', 'مشهد',   'active', 220, 850000,   11550000, 3, null, 1],
        [203, 'حسین کریمی',  '09123330033', 'تبریز',  'active', 180, 0,        147000000, 9, null, 1],
        [204, 'سارا موسوی',  '09124440044', 'اصفهان', 'active', 90,  15000000, 9600000,  2, null, 0],
        [205, 'رضا نوری',    '09125550055', 'تهران',  'active', 60,  300000,   87000000, 5, null, 1],
        [206, 'نیلوفر صادقی', '09126660066', 'کرج',   'active', 45,  0,        19800000, 1, 27,   0],
        [207, 'پدرام شریفی', '09127770077', 'شیراز',  'banned', 400, 0,        0,         4, null, 0],
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
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    // [نوع، شرح، طرف حساب، مبلغ(+ ورودی به سایت/کیف پول، - خروجی)، وضعیت، دقیقه‌ی قبل]
    $seed = [
        ['entry',       'ورودی مزایده‌ی پژو ۲۰۷',          'علی رضایی',          200000,     'paid',   12],
        ['deposit',     'بیعانه‌ی پژو ۲۰۷ اتوماتیک',       'علی رضایی',          19700000,   'paid',   95],
        ['fee',         'هزینه‌ی خدمات بیعانه',             'علی رضایی',          200000,     'paid',   95],
        ['subscription', 'اشتراک ۳۰ روزه',                  'اتوگالری هالیوود',   4500000,    'paid',   260],
        ['entry',       'ورودی مزایده‌ی ولوو FH500',        'مریم احمدی',         500000,     'failed', 310],
        ['release',     'آزادسازی بیعانه به نمایشگاه',      'نمایشگاه پارس',      -7400000,   'paid',   1440],
        ['refund',      'برگشت بیعانه به خریدار',           'نیلوفر صادقی',       -10800000,  'paid',   2100],
        ['withdraw',    'برداشت نمایشگاه',                  'آریا موتور',         -60000000,  'paid',   3000],
        ['subscription', 'اشتراک ۹۰ روزه',                  'نمایشگاه نگین',      11000000,   'paid',   4000],
        ['deposit',     'بیعانه‌ی کامیون بنز ۲۶۳۱',         'رضا نوری',           87000000,   'paid',   180],
    ];
    return array_map(fn ($r) => ['type' => $r[0], 'title' => $r[1], 'party' => $r[2], 'amount' => $r[3], 'status' => $r[4], 'at' => $min(-$r[5]), 'ref' => 'TX-' . (9000 + crc32($r[1] . $r[5]) % 900)], $seed);
};

$settleData = function () {
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    // [id، نمایشگاه، مدیر، مبلغ، شبا، بانک، وضعیت، دقیقه‌ی قبل، توضیح]
    $seed = [
        [401, 'اتوگالری هالیوود', 'بهرام اسدی',  120000000, 'IR120170000000123456789012', 'ملت',   'unpaid',  50,   null],
        [402, 'نمایشگاه پارس',    'آرش مرادی',   60000000,  'IR330620000000234567890123', 'ملی',   'unpaid',  200,  null],
        [403, 'آریا خودروهای تجاری', 'پویا نصیری', 400000000, 'IR560180000000345678901234', 'پاسارگاد', 'unpaid', 600, null],
        [404, 'نمایشگاه نگین',    'مهدی کیانی',  25000000,  'IR790190000000456789012345', 'سامان', 'unpaid',  1300, null],
        [405, 'اتوگالری هالیوود', 'بهرام اسدی',  15000000,  'IR120170000000123456789012', 'ملت',   'unpaid',  2000, null],
        [406, 'آریا موتور',       'کیوان رحمانی', 60000000, 'IR220170000000567890123456', 'ملت',   'paid',     3000, 'کد پیگیری: ۷۴۸۲۹۱۰۳'],
        [407, 'موتور سیتی',       'کیوان رحمانی', 30000000, 'IR220170000000567890123456', 'ملت',   'rejected', 5000, 'شماره‌ی شبا با نام صاحب حساب مطابقت ندارد'],
    ];
    return array_map(fn ($r) => ['id' => $r[0], 'dealer' => $r[1], 'owner' => $r[2], 'amount' => $r[3], 'iban' => $r[4], 'bank' => $r[5], 'status' => $r[6], 'at' => $min(-$r[7]), 'note' => $r[8]], $seed);
};

/* ---------- داده‌ی نمایشی گفتگوها (فقط برای قالب) ---------- */
$chatData = function () {
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    $c = fn ($id, $dealer, $user, $title, $st, $note, $msgs) => ['id' => $id, 'dealer' => $dealer, 'user' => $user, 'title' => $title, 'status' => $st, 'note' => $note, 'messages' => $msgs];
    $m = fn ($from, $text, $ago, $hit = null) => ['from' => $from, 'text' => $text, 'at' => $min(-$ago), 'hit' => $hit];
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
$settingsData = fn () => [
    'deposit'  => ['car' => 2, 'heavy' => 1.5, 'bike' => 3, 'bicycle' => 5, 'service_fee' => 200000, 'hold_hours' => 72, 'min_amount' => 500000],
    'auction'  => ['entry_fee' => 200000, 'min_step_percent' => 0.5, 'pay_hours' => 24, 'ban_days' => 30, 'extend_minutes' => 3, 'max_duration_hours' => 72],
    'pricing'  => ['sub_30' => 1500000, 'sub_90' => 4000000, 'sub_365' => 14000000, 'instant_fee' => 300000, 'auction_listing_fee' => 500000],
    'matching' => ['min_percent' => 60, 'max_requests' => 5, 'w' => ['model' => 30, 'price' => 25, 'year' => 15, 'mileage' => 15, 'city' => 10, 'extra' => 5]],
    'payout'   => ['min_withdraw' => 5000000, 'process_hours' => 48],
];

/* ---------- داده‌ی نمایشی داده‌های پایه، محتوا و گزارش فعالیت (فقط برای قالب) ---------- */
$catalogData = fn () => [
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

$pageData = fn () => [
    ['id' => 1, 'title' => 'قوانین و مقررات',    'slug' => 'terms',   'status' => 'active',  'ago' => '۳ روز پیش',   'body' => "۱. نمایشگاه‌داران موظف به ثبت اطلاعات صحیح خودرو هستند.\n۲. بیعانه تا مشخص‌شدن نتیجه‌ی معامله نزد سایت نگه داشته می‌شود.\n۳. تبادل اطلاعات تماس پیش از پرداخت بیعانه ممنوع است."],
    ['id' => 2, 'title' => 'حریم خصوصی',          'slug' => 'privacy', 'status' => 'active',  'ago' => '۲ هفته پیش',  'body' => 'اطلاعات کاربران فقط برای ارائه‌ی خدمات سایت استفاده می‌شود و در اختیار اشخاص ثالث قرار نمی‌گیرد.'],
    ['id' => 3, 'title' => 'راهنمای خرید با بیعانه', 'slug' => 'deposit-guide', 'status' => 'active', 'ago' => '۱ ماه پیش', 'body' => 'پس از انتخاب خودرو، بیعانه را از کیف پول پرداخت کنید تا اطلاعات تماس نمایشگاه نمایش داده شود.'],
    ['id' => 4, 'title' => 'راهنمای شرکت در مزایده', 'slug' => 'auction-guide', 'status' => 'pending', 'ago' => 'پیش‌نویس', 'body' => 'ابتدا هزینه‌ی ورود را بپردازید؛ سپس پیشنهاد خود را ثبت کنید.'],
    ['id' => 5, 'title' => 'سوالات متداول',       'slug' => 'faq',     'status' => 'active',  'ago' => '۵ روز پیش',   'body' => 'پرسش و پاسخ‌های پرتکرار کاربران.'],
    ['id' => 6, 'title' => 'درباره‌ی ما',          'slug' => 'about',   'status' => 'active',  'ago' => '۲ ماه پیش',   'body' => 'معرفی سایت و تیم.'],
];

$logData = function () {
    $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
    // [نوع، اقدام، هدف، دقیقه‌ی قبل]
    $seed = [
        ['listings', 'تأیید آگهی',            'پژو ۲۰۷ اتوماتیک • اتوگالری هالیوود', 8],
        ['listings', 'رد آگهی',               'NMAX 155 • دلیل: تصویر نامرتبط', 40],
        ['auctions', 'لغو مزایده',            'رنو تندر ۹۰ • دلیل: درخواست نمایشگاه‌دار', 95],
        ['auctions', 'بن برنده‌ی مزایده',      'نیلوفر صادقی • ۳۰ روز', 180],
        ['deals',    'آزادسازی بیعانه',        'DP-5506 • ۷٬۴۰۰٬۰۰۰ تومان', 300],
        ['deals',    'تصمیم اختلاف',           'DS-701 • برگشت به خریدار', 420],
        ['people',   'تأیید احراز هویت',       'اتوگالری هالیوود', 600],
        ['people',   'مسدودسازی کاربر',        'پدرام شریفی • دلیل: انتشار اطلاعات تماس', 1000],
        ['finance',  'ثبت پرداخت تسویه',       'آریا موتور • ۶۰٬۰۰۰٬۰۰۰ تومان • کد پیگیری ۷۴۸۲۹۱۰۳', 1500],
        ['settings', 'ویرایش تنظیمات',         'درصد بیعانه‌ی سواری: ۱٫۵ ← ۲', 2400],
        ['chats',    'پرچم‌گذاری گفتگو',       'علی رضایی و اتوگالری هالیوود', 2900],
        ['settings', 'ورود به پنل',            'مرورگر Chrome • ویندوز', 3000],
    ];
    return array_map(fn ($r) => ['type' => $r[0], 'action' => $r[1], 'target' => $r[2], 'at' => $min(-$r[3]), 'ip' => '185.12.' . (30 + $r[3] % 9) . '.' . (10 + $r[3] % 80)], $seed);
};

Route::prefix('admin')->name('admin.')->group(function () use ($catalogData, $pageData, $logData, $settingsData, $chatData, $txData, $settleData, $auctionData, $depositData, $disputeData, $dealerData, $userData, $requestData) {

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
    Route::get('/auctions', fn () => view('admin.pages.auctions.index', ['auctions' => $auctionData()]))->name('auctions.index');
    Route::get('/auctions/{id}', function ($id) use ($auctionData) {
        $all = $auctionData();
        $a = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.auctions.show', ['a' => $a]);
    })->whereNumber('id')->name('auctions.show');

    /* ---------- درخواست‌های خرید کاربران ---------- */
    Route::get('/requests', fn () => view('admin.pages.requests.index', ['requests' => $requestData()]))->name('requests.index');
    Route::get('/requests/{id}', function ($id) use ($requestData) {
        $all = $requestData();
        $r = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.requests.show', ['r' => $r]);
    })->whereNumber('id')->name('requests.show');

    /* ---------- بیعانه‌ها و رزروها ---------- */
    Route::get('/deposits', fn () => view('admin.pages.deposits.index', ['deposits' => $depositData()]))->name('deposits.index');
    Route::get('/deposits/{id}', function ($id) use ($depositData) {
        $all = $depositData();
        $d = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.deposits.show', ['d' => $d]);
    })->whereNumber('id')->name('deposits.show');

    /* ---------- اختلاف‌ها ---------- */
    Route::get('/disputes', fn () => view('admin.pages.disputes.index', ['disputes' => $disputeData()]))->name('disputes.index');
    Route::get('/disputes/{id}', function ($id) use ($disputeData) {
        $all = $disputeData();
        $x = array_values(array_filter($all, fn ($r) => $r['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.disputes.show', ['x' => $x]);
    })->whereNumber('id')->name('disputes.show');

    /* ---------- نمایشگاه‌داران ---------- */
    Route::get('/dealers', fn () => view('admin.pages.dealers.index', ['dealers' => $dealerData()]))->name('dealers.index');
    Route::get('/dealers/{id}', function ($id) use ($dealerData) {
        $all = $dealerData();
        $d = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.dealers.show', ['d' => $d]);
    })->whereNumber('id')->name('dealers.show');

    /* ---------- کاربران ---------- */
    Route::get('/users', fn () => view('admin.pages.users.index', ['users' => $userData()]))->name('users.index');
    Route::get('/users/{id}', function ($id) use ($userData) {
        $all = $userData();
        $u = array_values(array_filter($all, fn ($x) => $x['id'] === (int) $id))[0] ?? $all[0];
        return view('admin.pages.users.show', ['u' => $u]);
    })->whereNumber('id')->name('users.show');

    /* ---------- مالی و تسویه ---------- */
    Route::get('/finance', fn () => view('admin.pages.finance.index', ['txs' => $txData()]))->name('finance.index');
    Route::get('/settlements', fn () => view('admin.pages.settlements.index', ['items' => $settleData()]))->name('settlements.index');

    /* ---------- گفتگوها (فقط‌خواندنی برای ادمین) ---------- */
    Route::get('/chats', fn () => view('admin.pages.chats.index', ['chats' => $chatData()]))->name('chats.index');

    /* ---------- تنظیمات تجاری ---------- */
    Route::get('/settings', fn () => view('admin.pages.settings.index', ['s' => $settingsData()]))->name('settings.index');

    /* ---------- داده‌های پایه، محتوا، گزارش فعالیت ---------- */
    Route::get('/catalog', fn () => view('admin.pages.catalog.index', ['sets' => $catalogData()]))->name('catalog.index');
    Route::get('/content', fn () => view('admin.pages.content.index', ['pages' => $pageData()]))->name('content.index');
    Route::get('/logs', fn () => view('admin.pages.logs.index', ['logs' => $logData()]))->name('logs.index');
});
