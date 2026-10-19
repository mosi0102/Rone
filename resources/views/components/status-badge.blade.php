{{-- <x-status-badge status="active" /> — وضعیت‌های آگهی و مزایده --}}
@props(['status'])
@php
  $map = [
    'active' => ['badge-success', 'فعال'], 'draft' => ['', 'پیش‌نویس'], 'expired' => ['badge-danger', 'منقضی'],
    'auction' => ['badge-primary', 'در مزایده'], 'sold' => ['badge-info', 'فروخته شد'],
    'live' => ['badge-danger', 'در حال برگزاری'], 'scheduled' => ['badge-info', 'زمان‌بندی‌شده'], 'ended' => ['', 'پایان‌یافته'],
    'pending' => ['badge-warn', 'در انتظار بررسی'],
  ];
  [$cls, $label] = $map[$status] ?? ['', $status];
@endphp
<span {{ $attributes->class(['badge-s', $cls]) }}>@if($status === 'live')<i class="live-dot"></i>@endif{{ $label }}</span>
