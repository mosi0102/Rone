@extends('layouts.app')
@section('title', 'وضعیت ثبت‌نام نمایشگاه')
@section('page_title', 'وضعیت نمایشگاه')
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush

@php
  $meta = [
    'pending' => ['info', 'clock', 'در حال بررسی', 'مدارک شما در صف بررسی است؛ معمولاً کمتر از ۲۴ ساعت کاری طول می‌کشد.'],
    'approved' => ['ok', 'badge-check', 'نمایشگاه شما تأیید شد', 'اکنون می‌توانید اشتراک بخرید و آگهی ثبت کنید.'],
    'rejected' => ['bad', 'x-circle', 'مدارک تأیید نشد', $reason],
  ][$status];
@endphp

@section('content')
<div class="pf">
  <section class="pf-status is-{{ $meta[0] }}" role="status">
    <span class="pf-st-ic"><x-icon :name="$meta[1]" /></span>
    <div><h2>{{ $name }} — {{ $meta[2] }}</h2><p>{{ $meta[3] }}</p><p class="mt-1">کد پیگیری: <b>{{ $ref }}</b></p></div>
  </section>

  <ol class="st-steps" aria-label="مراحل">
    @foreach($steps as $i => $s)
      <li class="{{ $s['done'] ? 'is-done' : '' }} {{ !empty($s['current']) ? 'is-current' : '' }}">
        <span class="st-dot">@if($s['done'])<x-icon name="check" class="icon-sm" />@else {{ $i + 1 }} @endif</span>
        <div><b>{{ $s['title'] }}</b><small>{{ $s['text'] }}</small></div>
      </li>
    @endforeach
  </ol>

  @if($status === 'approved')
    <a href="{{ route('panel') }}" class="btn btn-primary btn-lg">ورود به پنل نمایشگاه</a>
  @elseif($status === 'rejected')
    <a href="{{ route('dealer.register') }}" class="btn btn-primary btn-lg">اصلاح و ارسال مجدد مدارک</a>
  @else
    <p class="pf-hint text-center mb-0">نتیجه را پیامکی هم اعلام می‌کنیم. تا آن زمان می‌توانید مثل یک کاربر عادی از سایت استفاده کنید.</p>
    <a href="{{ route('home') }}" class="btn btn-outline-primary">بازگشت به خانه</a>
  @endif
</div>
@endsection
