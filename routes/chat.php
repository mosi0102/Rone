<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| گفتگوی خریدار ↔ نمایشگاه‌دار — Mock
|--------------------------------------------------------------------------
| هر گفتگو به یک آگهی وصل است. قوانین سامانه:
|   • تا «پرداخت بیعانه‌ی همان آگهی» (state = pre_deposit): شماره‌ی تلفن، آی‌دی شبکه‌های اجتماعی، لینک و ایمیل،
|     و هر پیشنهاد پرداخت/معامله‌ی خارج از سامانه ممنوع است؛ پیام ارسال نمی‌شود (422) و به کاربر هشدار می‌رسد.
|   • بعد از بیعانه (state = deposit_paid): تبادل شماره برای هماهنگی بازدید مجاز می‌شود؛ لینک/آیدی/پرداخت بیرونی همچنان ممنوع.
|   • هر طرف می‌تواند پیام طرف مقابل را «گزارش» کند؛ گزارش و موارد تشخیص خودکار در پنل ادمین (/admin/chats) می‌آید.
| اتصال: در routes/web.php (بعد از تعریف $listingStore):   require __DIR__ . '/chat.php';
| مهم: تشخیص باید **سمت سرور** (مرجع) انجام شود؛ نسخه‌ی جاوااسکریپت (js/chat.js) فقط برای بازخورد سریع است.
| hit: phone | link | offsite   (هم‌راستا با برچسب‌های پنل ادمین)
*/

/* ---------- تشخیص محتوای ممنوع (مرجع نمونه) ---------- */
$chatCheck = function (string $text, bool $unlocked) {
    $t = mb_strtolower(strtr($text, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', "\u{200c}" => ' ', "\u{200f}" => '', "\u{200e}" => '']));
    // ۱) پیوند و آی‌دی
    if (preg_match('~(https?://|www\.|t\.me/|\b[a-z0-9-]{2,}\.(?:com|ir|net|org|me|io|co|app|ly)\b|[\w.+-]+@[\w-]+\.\w{2,})~u', $t)
        || preg_match('~(^|[\s:؛،,])@[a-z0-9_.]{3,}~u', $t)
        || preg_match('~(telegram|whatsapp|instagram|eitaa|rubika|تلگرام|واتس\s?اپ|واتساپ|اینستا|ایتا|روبیکا|دایرکت)~u', $t)) return ['ok' => false, 'hit' => 'link'];
    // ۲) پرداخت/معامله‌ی بیرونی
    if (preg_match('~(کارت\s*به\s*کارت|شماره\s*کارت|شماره\s*حساب|شبا|واریز\s*(مستقیم|به\s*حساب)|خارج\s*از\s*(سایت|سامانه)|بدون\s*(سایت|سامانه)|بیرون\s*از\s*(سایت|سامانه))~u', $t)) return ['ok' => false, 'hit' => 'offsite'];
    // ۳) شماره‌ی تلفن (فقط پیش از بیعانه): ارقام، حتی با فاصله/خط‌تیره، یا ارقام به حروف (صفر نه یک …)
    if (!$unlocked) {
        $w = ['صفر' => 0, 'یک' => 1, 'دو' => 2, 'سه' => 3, 'چهار' => 4, 'پنج' => 5, 'شش' => 6, 'هفت' => 7, 'هشت' => 8, 'نه' => 9];
        $t2 = preg_replace_callback('~(?<![\p{L}])(صفر|یک|دو|سه|چهار|پنج|شش|هفت|هشت|نه)(?![\p{L}])~u', fn($m) => ' ' . $w[$m[1]] . ' ', $t);
        if (preg_match_all('~(?:\+|00)?\d(?:[\s\-._()/،,]*\d){6,}~u', $t2, $mm)) {
            foreach ($mm[0] as $run) {
                $d = preg_replace('~\D~', '', $run);
                if (preg_match('~^(?:00989|989|09)\d{9}$~', $d) || preg_match('~^0[1-8]\d{9}$~', $d) || preg_match('~^(?:0098|98)[1-8]\d{9}$~', $d)) return ['ok' => false, 'hit' => 'phone'];
            }
        }
    }
    return ['ok' => true, 'hit' => null];
};
$chatReason = [
    'phone' => 'ارسال شماره‌ی تماس پیش از پرداخت بیعانه ممنوع است.',
    'link' => 'ارسال لینک، ایمیل یا آی‌دی پیام‌رسان و شبکه‌های اجتماعی مجاز نیست.',
    'offsite' => 'پیشنهاد پرداخت یا معامله‌ی خارج از سامانه ممنوع است؛ بیعانه فقط از طریق سامانه امن می‌ماند.',
];

/* ---------- گفتگوهای نمونه (پیام‌های جدید در session) ---------- */
$chatStore = function () use ($chatCheck) {
    $img = fn($f) => asset('img/mock/' . $f);
    $at = fn($m) => now()->addMinutes(-$m)->toIso8601String();
    $threads = [
        1 => ['id' => 1, 'state' => 'pre_deposit', 'unread' => 1, 'dealer' => ['id' => 8, 'name' => 'اتوگالری هالیوود', 'verified' => true], 'buyer' => 'خریدار ۴۲۰۰۰',
            'listing' => ['id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'image' => $img('car-207.jpg'), 'price' => 985000000, 'url' => route('listings.show', 24817), 'deposit_url' => route('listings.deposit', 24817)],
            'messages' => [
                ['id' => 101, 'from' => 'buyer', 'text' => 'سلام، خودرو هنوز موجوده؟', 'at' => $at(190)],
                ['id' => 102, 'from' => 'dealer', 'text' => 'سلام، بله موجوده و آماده بازدیده.', 'at' => $at(185)],
                ['id' => 103, 'from' => 'buyer', 'text' => 'قیمت قابل مذاکره هست؟', 'at' => $at(180)],
                ['id' => 104, 'from' => 'dealer', 'text' => 'اگه بیعانه رو از سایت ندید ۱۵ میلیون تخفیف می‌دم. شمارمو بنویسید: ۰۹۱۲۱۰۰۵۰۰۵', 'at' => $at(170)],
                ['id' => 105, 'from' => 'dealer', 'text' => 'برای هماهنگی بازدید، بیعانه را از طریق سامانه پرداخت کنید.', 'at' => $at(12)],
            ]],
        2 => ['id' => 2, 'state' => 'deposit_paid', 'unread' => 0, 'dealer' => ['id' => 26, 'name' => 'نمایشگاه نگین', 'verified' => true], 'buyer' => 'خریدار ۴۲۰۰۰',
            'listing' => ['id' => 24821, 'title' => 'هیوندای النترا', 'image' => $img('detail-main.jpg'), 'price' => 1850000000, 'url' => route('listings.show', 24821), 'deposit_url' => route('listings.deposit', 24821)],
            'messages' => [
                ['id' => 201, 'from' => 'buyer', 'text' => 'بیعانه را پرداخت کردم، چه ساعتی بیام؟', 'at' => $at(1500)],
                ['id' => 202, 'from' => 'dealer', 'text' => 'خوش آمدید. فردا ساعت ۱۱ در نمایشگاه هستیم. شماره‌ی من برای هماهنگی: ۰۹۱۲۱۰۰۵۰۰۵', 'at' => $at(1480)],
            ]],
        3 => ['id' => 3, 'state' => 'pre_deposit', 'unread' => 0, 'dealer' => ['id' => 22, 'name' => 'آریا خودروهای تجاری', 'verified' => true], 'buyer' => 'خریدار ۴۲۰۰۱',
            'listing' => ['id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $img('truck-fh.jpg'), 'price' => 9800000000, 'url' => route('listings.show', 24818), 'deposit_url' => route('listings.deposit', 24818)],
            'messages' => [
                ['id' => 301, 'from' => 'buyer', 'text' => 'کارکرد واقعی چقدره؟', 'at' => $at(900)],
                ['id' => 302, 'from' => 'dealer', 'text' => 'سیصد و بیست هزار کیلومتر، سند و بیمه کامل.', 'at' => $at(890)],
            ]],
    ];
    $extra = (array)session('demo_chat_msgs', []);
    $reported = (array)session('demo_chat_reported', []);
    foreach ($threads as $id => &$t) {
        foreach ($extra[$id] ?? [] as $m) $t['messages'][] = $m;
        $unlocked = $t['state'] === 'deposit_paid';
        foreach ($t['messages'] as &$m) {   // پیام‌های قدیمیِ حاوی اطلاعات ممنوع، تا پیش از بیعانه برای طرف مقابل پنهان می‌شوند
            $m['reported'] = in_array($m['id'], $reported, true);
            $m['hidden'] = false;
            if (!$unlocked) { $c = $chatCheck($m['text'], false); if (!$c['ok']) { $m['hidden'] = true; $m['hit'] = $c['hit']; } }
        }
        unset($m);
    }
    return $threads;
};

$chatRole = fn() => request()->query('as') === 'dealer' || session('demo_mode') === 'dealer' ? 'dealer' : 'buyer';

Route::get('/chats', function () use ($chatStore, $chatRole) {
    $role = $chatRole();
    $rows = array_map(function ($t) use ($role) {
        $last = end($t['messages']);
        return $t + ['last' => $last['hidden'] ? 'پیام حاوی اطلاعات ممنوع (نمایش داده نمی‌شود)' : $last['text'], 'last_at' => $last['at'], 'with' => $role === 'dealer' ? $t['buyer'] : $t['dealer']['name']];
    }, array_values($chatStore()));
    return view('pages.chat.index', ['threads' => $rows, 'role' => $role]);
})->name('chat.index');

// شروع گفتگو از صفحه‌ی آگهی/نمایشگاه (نمونه: همیشه به گفتگوی ۱ می‌رود)
Route::get('/chats/start', fn() => redirect()->route('chat.show', 1))->name('chat.start');

Route::get('/chats/{id}', function ($id) use ($chatStore, $chatRole, $chatReason) {
    $t = $chatStore()[(int)$id] ?? abort(404);
    return view('pages.chat.show', ['t' => $t, 'role' => $chatRole(), 'reasons' => $chatReason,
        'quick' => $chatRole() === 'dealer'
            ? ['سلام، بله موجود است.', 'برای بازدید بیعانه را از سامانه پرداخت کنید.', 'قیمت در مزایده/فروش فوری مشخص است.']
            : ['سلام، هنوز موجود است؟', 'امکان بازدید حضوری هست؟', 'قیمت قابل مذاکره است؟', 'سابقه‌ی تصادف یا رنگ دارد؟']]);
})->whereNumber('id')->name('chat.show');

Route::post('/chats/{id}/messages', function ($id) use ($chatStore, $chatCheck, $chatReason, $chatRole) {
    $t = $chatStore()[(int)$id] ?? abort(404);
    $text = trim((string)request()->input('text', ''));
    if ($text === '' || mb_strlen($text) > 1000) return response()->json(['error' => 'متن پیام معتبر نیست.'], 422);
    $c = $chatCheck($text, $t['state'] === 'deposit_paid');
    if (!$c['ok']) return response()->json(['error' => $chatReason[$c['hit']], 'hit' => $c['hit']], 422);   // بک‌اند: ثبت رویداد برای ادمین
    $m = ['id' => 9000 + rand(1, 999), 'from' => $chatRole(), 'text' => $text, 'at' => now()->toIso8601String()];
    session(['demo_chat_msgs' => array_replace((array)session('demo_chat_msgs', []), [(int)$id => array_merge((array)(session('demo_chat_msgs', [])[(int)$id] ?? []), [$m])])]);
    return response()->json(['message' => $m], 201);
})->whereNumber('id')->name('chat.send');

Route::post('/chats/{id}/report', function ($id) {
    session(['demo_chat_reported' => array_merge((array)session('demo_chat_reported', []), [(int)request()->input('message_id')])]);
    return response()->json(['ok' => true]);   // بک‌اند: ساخت گزارش در /admin/chats با دلیل
})->whereNumber('id')->name('chat.report');
