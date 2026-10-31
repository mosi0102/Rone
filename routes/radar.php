<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| رادار خودرو (درخواست خرید) — Mock
|--------------------------------------------------------------------------
| کاربر مشخصات خودروی دلخواه را ثبت می‌کند؛ سیستم بین همه‌ی آگهی‌ها مواردِ مشابه/نزدیک را با «درصد تطابق»
| نشان می‌دهد. درخواست تا وقتی کاربر حذفش نکند فعال می‌ماند و هر آگهی‌ی تازه با آن سنجیده می‌شود.
| نمایشگاه‌دار هم فهرست درخواست‌های فعال (ناشناس) را می‌بیند و آگهی مطابق خودش را پیشنهاد می‌دهد.
|
| اتصال: در routes/web.php، بعد از تعریف $lf / $listingStore / $listingFilter / $fa / $digits / $human:
|     require __DIR__ . '/radar.php';
| بک‌اندکار: الگوریتم $radarScore (وزن‌ها در بالای تابع) و ذخیره‌ی درخواست‌ها (فعلاً session) را با دیتابیس واقعی
| جایگزین کند و هنگام ثبت هر آگهی‌ی جدید/ویرایش قیمت، درخواست‌های فعال را دوباره بسنجد و اعلان بفرستد.
| وضعیت‌ها (هم‌راستا با پنل ادمین): active | cancelled | suspended
*/

/* ---------- امتیازدهی تطابق ---------- */
// فقط فیلدهایی که کاربر پر کرده‌ است در درصد حساب می‌شوند. دسته‌ی نامطابق = ۰.
$radarScore = function (array $q, array $row) use ($fa, $human) {
    if (!empty($q['category']) && $row['cat'] !== $q['category']) return [0, []];
    $earn = 0; $max = 0; $notes = [];
    $add = function (int $w, int $got, bool $ok, string $label) use (&$earn, &$max, &$notes) { $max += $w; $earn += $got; $notes[] = ['ok' => $ok, 't' => $label]; };
    if (!empty($q['brand'])) { $ok = $row['brand'] === $q['brand']; $add(25, $ok ? 25 : 0, $ok, $ok ? 'برند: ' . $q['brand'] : 'برند متفاوت (' . $row['brand'] . ')'); }
    if (!empty($q['model'])) { $ok = $row['model'] === $q['model']; $add(15, $ok ? 15 : 0, $ok, $ok ? 'مدل: ' . $q['model'] : 'مدل متفاوت (' . $row['model'] . ')'); }
    if (!empty($q['price_max'])) {
        $r = $row['price'] / max(1, (int)$q['price_max']);
        if ($r <= 1) $add(20, 20, true, 'در بودجه‌ی شما');
        elseif ($r <= 1.1) $add(20, 14, false, $fa(round(($r - 1) * 100)) . '٪ بالاتر از بودجه');
        elseif ($r <= 1.25) $add(20, 7, false, $fa(round(($r - 1) * 100)) . '٪ بالاتر از بودجه');
        else $add(20, 0, false, 'خارج از بودجه');
    }
    if (!empty($q['year_from']) || !empty($q['year_to'])) {
        $from = (int)($q['year_from'] ?: 0); $to = (int)($q['year_to'] ?: 9999); $y = $row['year'];
        $d = $y < $from ? $from - $y : ($y > $to ? $y - $to : 0);
        $add(15, $d === 0 ? 15 : ($d === 1 ? 10 : ($d === 2 ? 5 : 0)), $d === 0, $d === 0 ? 'سال ' . $fa($y) . ' در بازه' : 'سال ' . $fa($y) . ' (' . $fa($d) . ' سال خارج از بازه)');
    }
    if (!empty($q['km_max'])) {
        $r = $row['km'] / max(1, (int)$q['km_max']);
        $add(10, $r <= 1 ? 10 : ($r <= 1.2 ? 5 : 0), $r <= 1, $r <= 1 ? 'کارکرد مناسب' : 'کارکرد بیشتر از حد انتظار');
    }
    if (!empty($q['city'])) { $ok = $row['city'] === $q['city']; $add(10, $ok ? 10 : 0, $ok, $ok ? 'شهر: ' . $q['city'] : 'در شهر ' . $row['city']); }
    foreach (['color' => 'رنگ', 'gearbox' => 'گیربکس', 'fuel' => 'سوخت', 'cc' => 'حجم', 'btype' => 'نوع', 'wheel' => 'طوقه', 'axle' => 'آرایش محور'] as $k => $label) {
        if (!empty($q[$k])) { $ok = ($row[$k] ?? '') === $q[$k]; $add(5, $ok ? 5 : 0, $ok, $label . ($ok ? ': ' : ' متفاوت: ') . $q[$k]); }
    }
    return [$max ? (int)round(100 * $earn / $max) : 60, $notes];
};
const RADAR_MIN = 50;   // کمتر از این درصد «مشابه» حساب نمی‌شود

$radarMatches = function (array $q) use ($listingStore, $radarScore) {
    $out = [];
    foreach ($listingStore() as $row) {
        [$p, $notes] = $radarScore($q, $row);
        if ($p >= RADAR_MIN) $out[] = ['row' => $row, 'percent' => $p, 'notes' => $notes];
    }
    usort($out, fn($a, $b) => $b['percent'] <=> $a['percent'] ?: $a['row']['price'] <=> $b['row']['price']);
    return $out;
};

$radarItem = fn(array $m) => [
    'id' => $m['row']['id'], 'title' => $m['row']['title'], 'url' => route('listings.show', 24817), 'image' => $m['row']['image'], 'price' => $m['row']['price'],
    'year' => $m['row']['year'], 'km' => $m['row']['km'], 'extra' => $m['row']['color'], 'dealer' => $m['row']['dealer'], 'verified' => true, 'sale_type' => $m['row']['sale'],
    'category' => $m['row']['cat'], 'percent' => $m['percent'], 'notes' => $m['notes'], 'city' => $m['row']['city'],
];

/* ---------- عنوان و چیپ‌های یک درخواست ---------- */
$radarTitle = function (array $r) use ($lf) {
    $name = trim(($r['brand'] ?? '') . ' ' . ($r['model'] ?? ''));
    return $name !== '' ? $name : ($lf['categories'][$r['category']] ?? 'خودرو') . ' — هر مدل';
};
$radarChips = function (array $r) use ($fa, $human, $lf) {
    $c = [];
    $c[] = $lf['categories'][$r['category']] ?? '';
    if (!empty($r['price_max'])) $c[] = 'تا ' . $human((int)$r['price_max']) . ' تومان';
    if (!empty($r['year_from']) || !empty($r['year_to'])) $c[] = 'سال ' . ($r['year_from'] ? $fa($r['year_from']) : '…') . ' تا ' . ($r['year_to'] ? $fa($r['year_to']) : '…');
    if (!empty($r['km_max'])) $c[] = 'تا ' . $fa(number_format((int)$r['km_max'])) . ' کیلومتر';
    if (!empty($r['city'])) $c[] = $r['city'];
    foreach (['color', 'gearbox', 'fuel', 'btype', 'axle'] as $k) if (!empty($r[$k])) $c[] = $r[$k];
    if (!empty($r['wheel'])) $c[] = 'طوقه ' . $r['wheel'];
    if (!empty($r['cc'])) $c[] = $r['cc'] . ' سی‌سی';
    return array_values(array_filter($c));
};

/* ---------- درخواست‌های کاربر (نمایشی؛ جدیدها در session) ---------- */
$radarStore = function () {
    $base = [
        701 => ['id' => 701, 'category' => 'car', 'brand' => 'پژو', 'model' => '۲۰۷', 'year_from' => '1401', 'year_to' => '', 'price_max' => 1000000000, 'km_max' => 50000, 'city' => 'تهران', 'color' => 'سفید', 'gearbox' => '', 'fuel' => '', 'note' => '', 'status' => 'active', 'created' => '۲ روز پیش', 'new' => 2],
        702 => ['id' => 702, 'category' => 'bike', 'brand' => '', 'model' => '', 'year_from' => '1400', 'year_to' => '', 'price_max' => 420000000, 'km_max' => 25000, 'city' => 'مشهد', 'color' => '', 'cc' => '۲۵۰', 'note' => '', 'status' => 'active', 'created' => '۵ روز پیش', 'new' => 0],
        703 => ['id' => 703, 'category' => 'bicycle', 'brand' => '', 'model' => '', 'year_from' => '1402', 'year_to' => '', 'price_max' => 50000000, 'km_max' => '', 'city' => '', 'wheel' => '۲۷٫۵', 'btype' => 'کوهستان', 'note' => '', 'status' => 'active', 'created' => 'هفته‌ی پیش', 'new' => 1],
        704 => ['id' => 704, 'category' => 'car', 'brand' => 'کیا', 'model' => 'سراتو', 'year_from' => '1402', 'year_to' => '', 'price_max' => 1500000000, 'km_max' => 30000, 'city' => 'تهران', 'color' => '', 'note' => '', 'status' => 'suspended', 'created' => 'ماه پیش', 'new' => 0],
    ];
    $deleted = (array)session('demo_radar_deleted', []);
    $mine = (array)session('demo_radars', []);
    return collect(array_diff_key($base + $mine, array_flip($deleted)))->all();
};

$radarCard = function (array $r) use ($radarMatches, $radarItem, $radarTitle, $radarChips) {
    $m = $radarMatches($r);
    return ['r' => $r, 'title' => $radarTitle($r), 'chips' => $radarChips($r), 'count' => count($m), 'best' => $m ? $m[0]['percent'] : 0,
        'top' => array_map($radarItem, array_slice($m, 0, 3)), 'new' => (int)($r['new'] ?? 0)];
};

/* ---------- صفحات کاربر ---------- */
Route::get('/radar', function () use ($radarStore, $radarCard) {
    $cards = array_map($radarCard, array_values($radarStore()));
    return view('pages.radar.index', ['cards' => $cards, 'newTotal' => array_sum(array_column($cards, 'new'))]);
})->name('radar.index');

Route::get('/radar/create', fn() => view('pages.radar.create', ['lf' => $lf]))->name('radar.create');

// پیش‌نمایش زنده‌ی فرم: GET با همان پارامترهای فرم ← {count, best, top:[{title,percent}]}
Route::get('/radar/preview', function () use ($radarMatches) {
    $q = array_filter(request()->query(), fn($v) => $v !== null && $v !== '');
    $m = $radarMatches($q);
    return response()->json(['count' => count($m), 'best' => $m ? $m[0]['percent'] : 0,
        'top' => array_map(fn($x) => ['title' => $x['row']['title'], 'percent' => $x['percent']], array_slice($m, 0, 3))]);
})->name('radar.preview');

Route::post('/radar', function () {
    $in = array_filter(request()->all(), fn($v) => $v !== null && $v !== '');
    $id = 800 + count((array)session('demo_radars', [])) + 1;
    $r = array_merge(['category' => 'car', 'status' => 'active', 'created' => 'همین الان', 'new' => 0], $in, ['id' => $id]);
    session(['demo_radars' => ((array)session('demo_radars', [])) + [$id => $r]]);
    return response()->json(['id' => $id, 'url' => route('radar.show', $id)], 201);
})->name('radar.store');

Route::get('/radar/{id}', function ($id) use ($radarStore, $radarMatches, $radarItem, $radarTitle, $radarChips) {
    $r = $radarStore()[(int)$id] ?? abort(404);
    $items = array_map($radarItem, $radarMatches($r));
    return view('pages.radar.show', ['r' => $r, 'title' => $radarTitle($r), 'chips' => $radarChips($r), 'items' => $items,
        'high' => count(array_filter($items, fn($i) => $i['percent'] >= 80))]);
})->whereNumber('id')->name('radar.show');

Route::delete('/radar/{id}', function ($id) {
    session(['demo_radar_deleted' => array_merge((array)session('demo_radar_deleted', []), [(int)$id])]);
    return response()->json(['ok' => true]);
})->whereNumber('id')->name('radar.destroy');

/* ---------- سمت نمایشگاه‌دار: درخواست‌های فعال خریداران ---------- */
// خریداران ناشناس‌اند (شماره/نام نمایش داده نمی‌شود؛ ارتباط فقط از طریق گفتگوی سامانه).
$radarBuyerRequests = function () use ($listingStore, $radarScore, $radarTitle, $radarChips, $radarItem, $fa) {
    $seed = [
        [901, 'car', 'پژو', '۲۰۷', '1401', '', 1000000000, 50000, 'تهران', 'سفید', 2],
        [902, 'car', 'کیا', 'سراتو', '1401', '', 1700000000, 60000, 'تهران', '', 5],
        [903, 'car', '', '', '1400', '', 600000000, 90000, 'کرج', '', 8],
        [904, 'heavy', 'ولوو', 'FH500', '1398', '', 10500000000, 350000, 'تهران', '', 12],
        [905, 'bike', 'یاماها', 'NMAX', '1402', '', 400000000, 15000, 'مشهد', '', 20],
        [906, 'bicycle', '', '', '1402', '', 45000000, '', 'تهران', '', 30],
        [907, 'car', 'هیوندای', 'النترا', '1399', '', 1900000000, 80000, 'مشهد', '', 40],
        [908, 'car', 'سمند', 'LX', '1399', '', 560000000, 100000, 'اصفهان', '', 55],
    ];
    $mine = array_values(array_filter($listingStore(), fn($r) => (int)$r['dealer_id'] === 8));
    $out = [];
    foreach ($seed as [$id, $cat, $brand, $model, $yf, $yt, $pm, $km, $city, $color, $hours]) {
        $r = ['id' => $id, 'category' => $cat, 'brand' => $brand, 'model' => $model, 'year_from' => $yf, 'year_to' => $yt, 'price_max' => $pm, 'km_max' => $km, 'city' => $city, 'color' => $color];
        $best = null;
        foreach ($mine as $row) { [$p, $notes] = $radarScore($r, $row); if ($p >= RADAR_MIN && (!$best || $p > $best['percent'])) $best = ['row' => $row, 'percent' => $p, 'notes' => $notes]; }
        $out[] = ['id' => $id, 'cat' => $cat, 'title' => $radarTitle($r), 'chips' => $radarChips($r), 'city' => $city, 'budget' => $pm, 'hours' => $hours,
            'posted' => $hours < 24 ? $fa($hours) . ' ساعت پیش' : $fa(intdiv($hours, 24)) . ' روز پیش', 'buyer' => 'خریدار ' . $fa(40000 + $id * 7), 'mine' => $best ? $radarItem($best) : null, 'offered' => $id === 905];
    }
    return $out;
};

Route::get('/panel/radar', function () use ($radarBuyerRequests, $lf) {
    $rows = $radarBuyerRequests();
    return view('pages.panel.radar', ['requests' => $rows, 'cities' => $lf['cities'], 'cats' => $lf['categories'], 'related' => count(array_filter($rows, fn($x) => $x['mine']))]);
})->name('panel.radar');

Route::post('/panel/radar/{id}/offer', fn($id) => response()->json(['ok' => true, 'id' => (int)$id]))->whereNumber('id')->name('panel.radar.offer');

// هشدار «درخواست مرتبط با آگهی شما» روی داشبورد نمایشگاه‌دار
View::composer('pages.panel.dashboard', function ($view) use ($radarBuyerRequests) {
    $view->with('radarRelated', count(array_filter($radarBuyerRequests(), fn($x) => $x['mine'] && !$x['offered'])));
});
