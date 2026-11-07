<?php

namespace App\Http\Controllers;

use App\Support\DemoAuth;

class ChatController extends Controller
{
    public function index()
    {
        $rows = array_map(function ($t) {
            $role = $this->chatRole($t);
            $last = end($t['messages']);

            return $t + ['last' => $last['hidden'] ? 'پیام حاوی اطلاعات ممنوع (نمایش داده نمی‌شود)' : $last['text'], 'last_at' => $last['at'], 'with' => $role === 'agency' ? $t['buyer'] : $t['agency']['name']];
        }, array_values($this->chatStore()));

        return view('pages.chat.index', ['threads' => $rows]);
    }

    public function start()
    {
        return redirect()->route('chat.show', 1);
    }

    public function show($id)
    {
        $chatReason = $this->chatReason();
        $t = $this->chatStore()[(int) $id] ?? abort(404);
        $role = $this->chatRole($t);

        return view('pages.chat.show', ['t' => $t, 'role' => $role, 'reasons' => $chatReason,
            'quick' => $role === 'agency'
                ? ['سلام، بله موجود است.', 'برای بازدید با شماره‌ی آژانس تماس بگیرید.', 'قرارداد با کد رهگیری در آژانس تنظیم می‌شود.']
                : ['سلام، هنوز موجود است؟', 'امکان بازدید حضوری هست؟', 'قیمت قابل مذاکره است؟', 'سند تک‌برگ است؟']]);
    }

    public function send($id)
    {
        $chatReason = $this->chatReason();
        $t = $this->chatStore()[(int) $id] ?? abort(404);
        $text = trim((string) request()->input('text', ''));
        if ($text === '' || mb_strlen($text) > 1000) {
            return response()->json(['error' => 'متن پیام معتبر نیست.'], 422);
        }
        $c = $this->chatCheck($text, true);   // شماره‌ی آژانس عمومی است؛ فقط لینک/آی‌دی و درخواست واریز بررسی می‌شود
        if (! $c['ok']) {
            return response()->json(['error' => $chatReason[$c['hit']], 'hit' => $c['hit']], 422);
        }   // بک‌اند: ثبت رویداد برای ادمین
        $m = ['id' => 9000 + rand(1, 999), 'from' => $this->chatRole($t), 'text' => $text, 'at' => now()->toIso8601String()];
        session(['demo_chat_msgs' => array_replace((array) session('demo_chat_msgs', []), [(int) $id => array_merge((array) (session('demo_chat_msgs', [])[(int) $id] ?? []), [$m])])]);

        return response()->json(['message' => $m], 201);
    }

    public function report($id)
    {
        session(['demo_chat_reported' => array_merge((array) session('demo_chat_reported', []), [(int) request()->input('message_id')])]);

        return response()->json(['ok' => true]);   // بک‌اند: ساخت گزارش در /admin/chats با دلیل
    }

    protected function chatCheck(string $text, bool $unlocked)
    {
        $t = mb_strtolower(strtr($text, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', "\u{200c}" => ' ', "\u{200f}" => '', "\u{200e}" => '']));
        // ۱) پیوند و آی‌دی
        if (preg_match('~(https?://|www\.|t\.me/|\b[a-z0-9-]{2,}\.(?:com|ir|net|org|me|io|co|app|ly)\b|[\w.+-]+@[\w-]+\.\w{2,})~u', $t)
            || preg_match('~(^|[\s:؛،,])@[a-z0-9_.]{3,}~u', $t)
            || preg_match('~(telegram|whatsapp|instagram|eitaa|rubika|تلگرام|واتس\s?اپ|واتساپ|اینستا|ایتا|روبیکا|دایرکت)~u', $t)) {
            return ['ok' => false, 'hit' => 'link'];
        }
        // ۲) پرداخت/معامله‌ی بیرونی
        if (preg_match('~(کارت\s*به\s*کارت|شماره\s*کارت|شماره\s*حساب|شبا|واریز\s*(مستقیم|به\s*حساب)|پیش\s*پرداخت\s*(را|رو)?\s*واریز)~u', $t)) {
            return ['ok' => false, 'hit' => 'offsite'];
        }
        // ۳) شماره‌ی تلفن (فقط وقتی $unlocked = false؛ فعلاً همه‌ی گفتگوها باز است): ارقام، حتی با فاصله/خط‌تیره، یا ارقام به حروف (صفر نه یک …)
        if (! $unlocked) {
            $w = ['صفر' => 0, 'یک' => 1, 'دو' => 2, 'سه' => 3, 'چهار' => 4, 'پنج' => 5, 'شش' => 6, 'هفت' => 7, 'هشت' => 8, 'نه' => 9];
            $t2 = preg_replace_callback('~(?<![\p{L}])(صفر|یک|دو|سه|چهار|پنج|شش|هفت|هشت|نه)(?![\p{L}])~u', fn ($m) => ' '.$w[$m[1]].' ', $t);
            if (preg_match_all('~(?:\+|00)?\d(?:[\s\-._()/،,]*\d){6,}~u', $t2, $mm)) {
                foreach ($mm[0] as $run) {
                    $d = preg_replace('~\D~', '', $run);
                    if (preg_match('~^(?:00989|989|09)\d{9}$~', $d) || preg_match('~^0[1-8]\d{9}$~', $d) || preg_match('~^(?:0098|98)[1-8]\d{9}$~', $d)) {
                        return ['ok' => false, 'hit' => 'phone'];
                    }
                }
            }
        }

        return ['ok' => true, 'hit' => null];
    }

    protected function chatReason(): array
    {
        return [
            'phone' => 'ارسال شماره‌ی تماس در این گفتگو مجاز نیست.',
            'link' => 'ارسال لینک، ایمیل یا آی‌دی پیام‌رسان و شبکه‌های اجتماعی مجاز نیست.',
            'offsite' => 'درخواست واریز یا شماره‌ی کارت در گفتگو مجاز نیست؛ پیش از بازدید و قرارداد در آژانس هیچ پولی پرداخت نکنید.',
        ];
    }

    protected function chatStore()
    {
        $at = fn ($m) => now()->addMinutes(-$m)->toIso8601String();
        $threads = [
            1 => ['id' => 1, 'state' => 'open', 'unread' => 1, 'agency' => ['id' => 8, 'name' => 'املاک پارسیان', 'verified' => true], 'buyer' => 'مشتری ۴۲۰۰۰',
                'listing' => ['id' => 31001, 'title' => 'آپارتمان ۱۲۰ متری نوساز', 'image' => $this->img('agency-stock-1.jpg'), 'price' => 18500000000, 'url' => route('listings.show', 31001)],
                'messages' => [
                    ['id' => 101, 'from' => 'buyer', 'text' => 'سلام، ملک هنوز موجوده؟', 'at' => $at(190)],
                    ['id' => 102, 'from' => 'agency', 'text' => 'سلام، بله موجوده و آماده بازدیده.', 'at' => $at(185)],
                    ['id' => 103, 'from' => 'buyer', 'text' => 'قیمت قابل مذاکره هست؟', 'at' => $at(180)],
                    ['id' => 104, 'from' => 'agency', 'text' => 'برای رزرو، پیش‌پرداخت رو واریز کنید به شماره کارت ۶۰۳۷۹۹۱۲۳۴۵۶۷۸۹۰', 'at' => $at(170)],
                    ['id' => 105, 'from' => 'agency', 'text' => 'برای هماهنگی بازدید با شماره‌ی آژانس تماس بگیرید: ۰۲۱-۲۲۳۴۵۶۷۸', 'at' => $at(12)],
                ]],
            2 => ['id' => 2, 'state' => 'open', 'unread' => 0, 'agency' => ['id' => 26, 'name' => 'آژانس نگین', 'verified' => true], 'buyer' => 'مشتری ۴۲۰۰۰',
                'listing' => ['id' => 31005, 'title' => 'اجاره آپارتمان ۹۰ متری', 'image' => $this->img('detail-t2.jpg'), 'price' => 600000000, 'url' => route('listings.show', 31005)],
                'messages' => [
                    ['id' => 201, 'from' => 'buyer', 'text' => 'سلام، برای بازدید چه ساعتی بیام؟', 'at' => $at(1500)],
                    ['id' => 202, 'from' => 'agency', 'text' => 'خوش آمدید. فردا ساعت ۱۱ در آژانس هستیم. شماره‌ی من برای هماهنگی: ۰۹۱۲۱۰۰۵۰۰۵', 'at' => $at(1480)],
                ]],
            3 => ['id' => 3, 'state' => 'open', 'unread' => 0, 'agency' => ['id' => 22, 'name' => 'املاک آریا تجاری', 'verified' => true], 'buyer' => 'مشتری ۴۲۰۰۱',
                'listing' => ['id' => 31008, 'title' => 'مغازه ۳۵ متری بر خیابان اصلی', 'image' => $this->img('agency-thumb-1.jpg'), 'price' => 9500000000, 'url' => route('listings.show', 31008)],
                'messages' => [
                    ['id' => 301, 'from' => 'buyer', 'text' => 'متراژ دقیق و ارتفاع سقف چقدره؟', 'at' => $at(900)],
                    ['id' => 302, 'from' => 'agency', 'text' => 'سی‌وپنج متر با بالکن، ارتفاع چهار متر، سند تک‌برگ.', 'at' => $at(890)],
                ]],
        ];
        $extra = (array) session('demo_chat_msgs', []);
        $reported = (array) session('demo_chat_reported', []);
        foreach ($threads as $id => &$t) {
            foreach ($extra[$id] ?? [] as $m) {
                $t['messages'][] = $m;
            }
            foreach ($t['messages'] as &$m) {   // پیام‌های قدیمیِ حاوی لینک/آی‌دی یا درخواست واریز برای طرف مقابل پنهان می‌شوند
                $m['reported'] = in_array($m['id'], $reported, true);
                $m['hidden'] = false;
                $c = $this->chatCheck($m['text'], true);
                if (! $c['ok']) {
                    $m['hidden'] = true;
                    $m['hit'] = $c['hit'];
                }
            }
            unset($m);
        }

        return $threads;
    }

    /** نقش کاربر در هر گفتگو: اگر آژانسِ همین گفتگو باشد «agency»، وگرنه «buyer» */
    protected function chatRole(array $t): string
    {
        $auth = DemoAuth::get();

        return $auth->is_agency && $auth->agency->id === $t['agency']['id'] ? 'agency' : 'buyer';
    }
}
