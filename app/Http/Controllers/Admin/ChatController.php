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
    }
}
