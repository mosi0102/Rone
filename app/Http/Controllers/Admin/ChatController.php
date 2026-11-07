<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.pages.chats.index', ['chats' => $this->chatData()]);
    }

    protected function chatData()
    {
        $min = fn ($m) => now()->addMinutes($m)->toIso8601String();
        $c = fn ($id, $agency, $user, $title, $st, $note, $msgs) => ['id' => $id, 'agency' => $agency, 'user' => $user, 'title' => $title, 'status' => $st, 'note' => $note, 'messages' => $msgs];
        $m = fn ($from, $text, $ago, $hit = null) => ['from' => $from, 'text' => $text, 'at' => $min(-$ago), 'hit' => $hit];

        return [
            $c(1, 'املاک پارسیان', 'علی رضایی', 'آپارتمان ۱۲۰ متری نوساز', 'flagged', 'تلاش برای خروج از سایت', [
                $m('user', 'سلام، ملک هنوز موجوده؟', 190), $m('agency', 'سلام، بله موجوده.', 185),
                $m('user', 'قیمت قابل مذاکره هست؟', 180), $m('agency', 'برای رزرو، پیش‌پرداخت رو کارت به کارت کنید به ۶۰۳۷۹۹۱۲۳۴۵۶۷۸۹۰', 170, 'offsite'),
                $m('user', 'سایت که امنیت بیشتری داره…', 160)]),
            $c(2, 'املاک پارسیان', 'حسین کریمی', 'مغازه ۳۵ متری بر خیابان اصلی', 'normal', null, [
                $m('user', 'متراژ دقیقش چقدره؟', 700), $m('agency', 'سی‌وپنج متر با بالکن، سند تک‌برگ.', 690), $m('user', 'ممنون، فردا برای بازدید میام.', 650)]),
            $c(3, 'املاک گیلان', 'مریم احمدی', 'ویلای ساحلی اجاره روزانه', 'flagged', 'اختلاف باز', [
                $m('user', 'استخر ویلا قابل استفاده نیست، تو آگهی ننوشته بودید.', 3000), $m('agency', 'موقع بازدید مشکلی نبود، به تلگرام من پیام بدید @gilan_villa', 2990, 'link'), $m('user', 'من اختلاف ثبت می‌کنم.', 2950)]),
            $c(4, 'املاک آریا', 'سارا موسوی', 'آپارتمان ۸۵ متری فول امکانات', 'reviewed', 'بررسی شد؛ مشکلی نبود', [
                $m('user', 'سلام، امروز می‌تونم بیام بازدید؟', 400), $m('agency', 'بله، تا ساعت ۸ شب در آژانس هستیم.', 390)]),
            $c(5, 'املاک ارم', 'رضا نوری', 'دفتر اداری ۶۰ متری موقعیت اداری', 'normal', null, [
                $m('user', 'سلام، پارکینگ اختصاصی داره؟', 55), $m('agency', 'سلام، بله یک واحد پارکینگ سندی.', 50)]),
            $c(6, 'املاک البرز', 'نیلوفر صادقی', 'رهن کامل آپارتمان ۷۰ متری', 'normal', null, [
                $m('user', 'رهن قابل تبدیل به اجاره هست؟', 20), $m('agency', 'بله، تا نصف مبلغ قابل تبدیله.', 15)]),
        ];
    }
}
