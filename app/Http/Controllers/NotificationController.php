<?php

namespace App\Http\Controllers;

use App\Support\DemoAuth;

class NotificationController extends Controller
{
    /** اعلان‌ها. kind: deal | auction | radar | chat | wallet | system */
    public function index()
    {
        $rows = [
            ['id' => 1, 'kind' => 'deal', 'title' => 'بیعانه پرداخت شد', 'text' => 'بیعانه‌ی «پژو ۲۰۷ اتوماتیک» در امانت سامانه نگه‌داری می‌شود. اطلاعات تماس نمایشگاه فعال شد.', 'url' => route('deals.show', 101), 'read' => false, 'at' => now()->subMinutes(25)],
            ['id' => 2, 'kind' => 'radar', 'title' => 'آگهی مشابه رادار شما پیدا شد', 'text' => 'یک «پژو ۲۰۷ اتوماتیک» با ۸۶٪ تطابق منتشر شد.', 'url' => route('radar.show', 701), 'read' => false, 'at' => now()->subHours(2)],
            ['id' => 3, 'kind' => 'auction', 'title' => 'مزایده‌ی «یاماها NMAX» شروع شد', 'text' => 'مزایده‌ای که در آن ثبت‌نام کرده‌اید هم‌اکنون در حال برگزاری است.', 'url' => route('auctions.show', 11), 'read' => false, 'at' => now()->subHours(5)],
            ['id' => 4, 'kind' => 'chat', 'title' => 'پیام جدید از اتوگالری هالیوود', 'text' => 'برای بازدید بیعانه را از سامانه پرداخت کنید.', 'url' => route('chat.show', 1), 'read' => true, 'at' => now()->subDays(1)],
            ['id' => 5, 'kind' => 'wallet', 'title' => 'کیف پول شارژ شد', 'text' => 'مبلغ ۵۰٬۰۰۰٬۰۰۰ تومان به موجودی شما اضافه شد.', 'url' => route('account.wallet'), 'read' => true, 'at' => now()->subDays(2)],
            ['id' => 6, 'kind' => 'system', 'title' => 'احراز هویت خود را تکمیل کنید', 'text' => 'برای بیعانه، مزایده و برداشت از کیف پول، مدارک هویتی لازم است.', 'url' => route('account.profile'), 'read' => true, 'at' => now()->subDays(3)],
        ];
        if (DemoAuth::get()->is_dealer) {
            array_unshift($rows, ['id' => 7, 'kind' => 'radar', 'title' => 'درخواست خریدار مشابه آگهی شما', 'text' => 'یک خریدار دنبال «پژو ۲۰۶ تیپ ۲» با بودجه‌ی نزدیک آگهی شماست.', 'url' => route('panel.radar'), 'read' => false, 'at' => now()->subMinutes(8)]);
        }
        return view('pages.account.notifications', ['rows' => $rows]);
    }
}
