<?php

namespace App\Http\Controllers;

use App\Support\DemoAuth;

class NotificationController extends Controller
{
    /** اعلان‌ها. kind: auction | radar | chat | wallet | system */
    public function index()
    {
        $rows = [
            ['id' => 1, 'kind' => 'auction', 'title' => 'مزایده‌ی «خانه کلنگی ۲۴۰ متر» شروع شد', 'text' => 'مزایده‌ای که ورودی آن را پرداخت کرده‌اید هم‌اکنون در حال برگزاری است.', 'url' => route('auctions.show', 13), 'read' => false, 'at' => now()->subMinutes(25)],
            ['id' => 2, 'kind' => 'radar', 'title' => 'آگهی مشابه رادار شما پیدا شد', 'text' => 'یک «آپارتمان ۱۲۰ متری در سعادت‌آباد» با ۸۶٪ تطابق منتشر شد.', 'url' => route('radar.show', 701), 'read' => false, 'at' => now()->subHours(2)],
            ['id' => 4, 'kind' => 'chat', 'title' => 'پیام جدید از املاک پارسیان', 'text' => 'فردا ساعت ۱۰ برای بازدید تشریف بیاورید.', 'url' => route('chat.show', 1), 'read' => true, 'at' => now()->subDays(1)],
            ['id' => 5, 'kind' => 'wallet', 'title' => 'کیف پول شارژ شد', 'text' => 'مبلغ ۵۰٬۰۰۰٬۰۰۰ تومان به موجودی شما اضافه شد.', 'url' => route('account.wallet'), 'read' => true, 'at' => now()->subDays(2)],
            ['id' => 6, 'kind' => 'system', 'title' => 'احراز هویت خود را تکمیل کنید', 'text' => 'برای شرکت در مزایده و برداشت از کیف پول، مدارک هویتی لازم است.', 'url' => route('account.profile'), 'read' => true, 'at' => now()->subDays(3)],
        ];
        if (DemoAuth::get()->is_agency) {
            array_unshift($rows, ['id' => 7, 'kind' => 'radar', 'title' => 'درخواست مشتری مشابه آگهی شما', 'text' => 'یک مشتری دنبال «آپارتمان ۹۰ متری در پونک» با بودجه‌ی نزدیک آگهی شماست.', 'url' => route('panel.radar'), 'read' => false, 'at' => now()->subMinutes(8)]);
        }

        return view('pages.account.notifications', ['rows' => $rows]);
    }
}
