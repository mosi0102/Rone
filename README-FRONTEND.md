# اسکلت فرانت (مرحله ۱)

## نصب در پروژه لاراول
1. پوشه‌های `public/` و `resources/` این بسته را روی همان پوشه‌های پروژه کپی کنید (merge).
2. محتوای `routes/web.php` این بسته **موقت و نمایشی** است. اگر پروژه فایل مسیر دارد، فقط نام مسیرهای زیر را در آن تعریف کنید (یا مسیرهای موقت را در فایل خودتان کپی کنید):
   `home, listings.index, auctions.index, dealers.index, account, search, notifications, offline, styleguide`
3. در `.env` مقدار `APP_NAME` را نام سایت بگذارید (برای عنوان و هدر استفاده می‌شود).
4. `php artisan serve` و بازدید از:
   - `/` صفحه خانه نمایشی
   - `/styleguide` همه کامپوننت‌ها یک‌جا

> PWA فقط روی **HTTPS** یا `localhost` فعال می‌شود. `sw.js` و `manifest.webmanifest` باید از ریشه دامنه سرو شوند (پوشه `public` همین‌طور است).

## ساختار
| مسیر | کاربرد |
|---|---|
| `public/css/app.css` | سیستم طراحی؛ توکن‌ها (رنگ، شعاع، سایه) در بخش ۱ فایل |
| `public/js/app.js` | توست، شیت، ریپل، شمارنده معکوس، قالب‌بندی تاریخ شمسی/پول، نصب PWA |
| `public/sw.js` | سرویس‌ورکر (کش استاتیک + صفحه آفلاین) |
| `resources/views/layouts/app.blade.php` | لایوت اصلی (موبایل + دسکتاپ) |
| `resources/views/partials/*` | هدر، نوار بالا، ناوبری پایین، فوتر، آیکن‌ها، بنر نصب |
| `resources/views/components/*` | `x-icon` `x-price` `x-countdown` `x-listing-card` `x-section-head` `x-empty-state` |

## قراردادهای استفاده در صفحه جدید
```blade
@extends('layouts.app')
@section('title', 'عنوان صفحه')          {{-- عنوان تب مرورگر --}}
@section('page_title', 'عنوان نوار بالا') {{-- فقط وقتی back هست --}}
@section('back', route('home'))           {{-- دکمه بازگشت موبایل --}}
@section('body_class', 'no-bottom-nav')   {{-- مخفی کردن ناوبری پایین (مثلاً صفحه پرداخت/مزایده) --}}
@section('content') ... @endsection
@push('scripts') <script>…</script> @endpush
```

## قرارداد داده برای بک‌اندکار
**کارت آگهی** (`<x-listing-card :item="…">`): `id, title, url, image(nullable), price(تومان، عدد), year(شمسی), km, city, dealer, sale_type('instant'|'auction'|null), ends_at(فقط مزایده), favorited(bool)`

**شمارنده**: `<x-countdown :end="$date" />` — تاریخ به‌صورت Carbon/ISO 8601 با منطقه زمانی. هنگام اتمام، رویداد `app:countdown-end` روی المنت ارسال می‌شود.

**پیام‌ها**: `session('success')` و `session('error')` خودکار به توست تبدیل می‌شوند.

**اعداد و تاریخ**: `data-money="123"` و `data-jdate="ISO"` (با `data-time` برای ساعت) توسط JS با ارقام فارسی/تقویم شمسی نمایش داده می‌شوند. مقدارها را همیشه خام (عدد/ISO) بدهید.

**رویدادهای JS برای اتصال بک‌اند**: `app:favorite (id, on)` و `app:chip (value)`.

## نکته
مسیرهای `/api`، `/payment`، `/broadcasting`، `/livewire` عمداً از کش سرویس‌ورکر مستثنی هستند. اگر بک‌اندکار مسیرهای دیگری برای پرداخت یا وب‌سوکت دارد، regex را در `sw.js` اصلاح کند. با هر تغییر فایل‌های استاتیک، `VERSION` در `sw.js` را بالا ببرید.
