{{-- <x-admin.status status="live" /> — برچسب وضعیت. وضعیت جدید: یک خط به $map اضافه کنید --}}
@props(['status'])
@php
  $map = [
    // آگهی
    'active' => ['ok', 'فعال'], 'pending' => ['warn', 'در انتظار تأیید'], 'rejected' => ['bad', 'ردشده'], 'suspended' => ['bad', 'تعلیق‌شده'], 'expired' => ['', 'منقضی'], 'sold' => ['info', 'فروخته‌شده'],
    // نوع فروش
    'normal' => ['', 'فروش عادی'], 'instant' => ['warn', 'فروش فوری'], 'auction' => ['info', 'مزایده'],
    // مزایده
    'live' => ['bad live', 'زنده'], 'scheduled' => ['info', 'زمان‌بندی‌شده'], 'ended' => ['', 'پایان‌یافته'], 'awaiting_pay' => ['warn', 'منتظر پرداخت برنده'], 'defaulted' => ['bad', 'عدم پرداخت'], 'cancelled' => ['', 'لغوشده'],
    // بیعانه / اختلاف
    'held' => ['info', 'در امانت'], 'released' => ['ok', 'آزادشده'], 'refunded' => ['', 'برگشت‌خورده'], 'dispute' => ['bad', 'اختلاف'],
    // پرونده‌ی اختلاف
    'open' => ['bad', 'باز'], 'review' => ['warn', 'در حال بررسی'], 'resolved' => ['ok', 'حل‌شده'],
    // کاربران / مالی
    'verified' => ['ok', 'تأییدشده'], 'banned' => ['bad', 'مسدود'], 'paid' => ['ok', 'پرداخت‌شده'], 'flagged' => ['bad', 'پرچم‌شده'], 'reviewed' => ['ok', 'بررسی‌شده'], 'unpaid' => ['warn', 'در انتظار پرداخت'], 'failed' => ['bad', 'ناموفق'],
  ];
  [$cls, $label] = $map[$status] ?? ['', $status];
@endphp
<span {{ $attributes->class(['badge-s', $cls]) }}>{{ $label }}</span>
