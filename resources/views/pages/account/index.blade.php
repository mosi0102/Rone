@extends('layouts.app')
@section('title', 'حساب من')
@section('page_title', 'حساب من')
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush

@php
  $st = $auth->profile_status;
  $stLabel = ['none' => 'تکمیل نشده', 'pending' => 'در حال بررسی', 'approved' => 'تأیید شده', 'rejected' => 'ردشده'][$st];
  $tone = ['none' => 'warn', 'pending' => 'info', 'approved' => 'ok', 'rejected' => 'bad'][$st];
@endphp

@section('content')
@if(!$auth->check)
  <x-empty-state icon="user" title="وارد حساب خود شوید" text="برای دیدن حساب و عملیات پرداختی ابتدا وارد شوید." action="ورود / ثبت‌نام" :href="route('login')" />
@else
<div class="pf">
  <section class="ac-head">
    <span class="ac-av"><x-icon name="user" /></span>
    <div>
      <h2>{{ $auth->name }}</h2>
      <p class="ltr">{{ $auth->phone }}</p>
      <span class="ac-role">{{ $auth->is_dealer ? 'نمایشگاه‌دار • ' . $auth->dealer->name : 'کاربر' }}</span>
    </div>
  </section>

  <a href="{{ route('account.profile') }}" class="ac-verify is-{{ $tone }}">
    <span class="pf-st-ic"><x-icon :name="$st === 'approved' ? 'badge-check' : ($st === 'pending' ? 'clock' : 'shield')" /></span>
    <div><b>احراز هویت: {{ $stLabel }}</b><small>{{ $st === 'approved' ? 'همه‌ی امکانات پرداختی فعال است' : ($st === 'pending' ? 'نتیجه‌ی بررسی را اعلام می‌کنیم' : 'لازم برای بیعانه، مزایده و برداشت') }}</small></div>
    <x-icon name="chevron-left" class="icon-sm ac-go" />
  </a>

  <div class="ac-wallet">
    <span><x-icon name="wallet" class="icon-sm" /> موجودی کیف پول</span>
    <b><x-price :value="$auth->wallet" /></b>
    <a href="{{ route('account.wallet') }}" class="btn btn-accent btn-sm">شارژ / برداشت</a>
  </div>

  <nav class="ac-list" aria-label="امکانات حساب">
    <a href="{{ route('account.reservations') }}"><span class="mg-ic"><x-icon name="shield" /></span>رزروها و بیعانه‌ها @if($pending_deals)<em>{{ $pending_deals }}</em>@endif<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
    <a href="{{ route('radar.index') }}"><span class="mg-ic"><x-icon name="sparkles" /></span>رادار خودرو<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
    <a href="{{ route('chat.index') }}"><span class="mg-ic"><x-icon name="message" /></span>پیام‌ها @if($unread_chats)<em>{{ $unread_chats }}</em>@endif<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
    <a href="{{ route('favorites') }}"><span class="mg-ic"><x-icon name="bookmark" /></span>نشان‌شده‌ها<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
    <a href="{{ route('notifications') }}"><span class="mg-ic"><x-icon name="bell" /></span>اعلان‌ها<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
  </nav>

  @if($auth->is_dealer)
    <h3 class="ac-sec">نمایشگاه</h3>
    <nav class="ac-list" aria-label="امکانات نمایشگاه">
      <a href="{{ route('panel') }}"><span class="mg-ic"><x-icon name="layout" /></span>پنل نمایشگاه<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
      <a href="{{ route('panel', ['tab' => 'listings']) }}"><span class="mg-ic"><x-icon name="car" /></span>آگهی‌های من<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
      <a href="{{ route('panel.radar') }}"><span class="mg-ic"><x-icon name="sparkles" /></span>درخواست خریداران<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
      <a href="{{ route('panel', ['tab' => 'finance']) }}"><span class="mg-ic"><x-icon name="receipt" /></span>مالی و تسویه<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
      <a href="{{ route('dealers.show', $auth->dealer->id) }}"><span class="mg-ic"><x-icon name="store" /></span>پروفایل عمومی<x-icon name="chevron-left" class="icon-sm ac-go" /></a>
    </nav>
  @else
    <a href="{{ route('dealer.join') }}" class="ac-join"><x-icon name="store" /><div><b>نمایشگاه دارید؟</b><small>با همین حساب نمایشگاه ثبت کنید و آگهی بگذارید</small></div></a>
  @endif

  <button type="submit" form="logoutForm" class="btn btn-outline-danger w-100 mt-3">خروج از حساب</button>
</div>
@endif
@endsection
