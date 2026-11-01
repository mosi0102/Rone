@extends('layouts.app')
@section('title', 'کیف پول')
@section('page_title', 'کیف پول')
@section('back', route('account'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/wallet.js') }}"></script>@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="wallet" title="وارد حساب خود شوید" text="برای دیدن کیف پول ابتدا وارد شوید." action="ورود / ثبت‌نام" :href="route('login')" />
@else
@php
  $kinds = ['charge' => 'wallet', 'refund' => 'undo', 'deposit' => 'shield', 'auction' => 'gavel', 'withdraw' => 'arrow-up'];
  $lock = !$auth->verified;
@endphp
<div class="pf wl" data-min-charge="{{ $min_charge }}" data-min-withdraw="{{ $min_withdraw }}" data-balance="{{ $balance }}">
  <section class="wl-hero">
    <small>موجودی قابل استفاده</small>
    <b class="wl-amount"><x-price :value="$balance" /></b>
    <div class="wl-held"><x-icon name="shield" class="icon-sm" /> بیعانه‌های در امانت: <x-price :value="$held" /></div>
  </section>

  @if($done === 'charged')
    <p class="pf-need is-ok"><x-icon name="check-circle" class="icon-sm" /> شارژ <x-price :value="$amount" /> با موفقیت انجام شد.</p>
  @elseif($done === 'withdrawn')
    <p class="pf-need is-ok"><x-icon name="check-circle" class="icon-sm" /> درخواست برداشت <x-price :value="$amount" /> ثبت شد و پس از تأیید به حساب شما واریز می‌شود.</p>
  @endif

  @if($lock)
    <a href="{{ route('account.profile') }}" class="ac-verify is-warn">
      <span class="pf-st-ic"><x-icon name="lock" /></span>
      <div><b>شارژ و برداشت پس از احراز هویت فعال می‌شود</b><small>{{ $auth->profile_status === 'pending' ? 'مدارک شما در حال بررسی است' : 'تکمیل اطلاعات و مدارک' }}</small></div>
      <x-icon name="chevron-left" class="icon-sm ac-go" />
    </a>
  @endif

  <div class="wl-tabs" role="tablist" aria-label="بخش‌های کیف پول">
    <button type="button" role="tab" data-tab="tx" class="{{ $tab === 'tx' ? 'on' : '' }}">تراکنش‌ها</button>
    <button type="button" role="tab" data-tab="charge" class="{{ $tab === 'charge' ? 'on' : '' }}">شارژ</button>
    <button type="button" role="tab" data-tab="withdraw" class="{{ $tab === 'withdraw' ? 'on' : '' }}">برداشت</button>
  </div>

  {{-- شارژ --}}
  <section data-pane="charge" class="pf-card" @if($tab !== 'charge') hidden @endif>
    <form method="post" action="{{ route('account.wallet.charge') }}" class="wl-form" novalidate data-kind="charge">
      @csrf
      <label class="form-label" for="chAmount">مبلغ شارژ (تومان)</label>
      <input class="form-control ltr wl-input" id="chAmount" name="amount" inputmode="numeric" autocomplete="off" placeholder="۱۰٬۰۰۰٬۰۰۰" {{ $lock ? 'disabled' : '' }}>
      <div class="wl-quick">@foreach($quick as $q)<button type="button" class="chip" data-quick="{{ $q }}" {{ $lock ? 'disabled' : '' }}><span data-fa>{{ number_format($q) }}</span></button>@endforeach</div>
      <div class="invalid-feedback wl-err" hidden>حداقل مبلغ شارژ <x-price :value="$min_charge" /> است.</div>
      <p class="pf-hint">پرداخت از طریق درگاه امن بانکی انجام می‌شود.</p>
      <button type="submit" class="btn btn-primary w-100" {{ $lock ? 'disabled' : '' }}>پرداخت و شارژ</button>
    </form>
  </section>

  {{-- برداشت --}}
  <section data-pane="withdraw" class="pf-card" @if($tab !== 'withdraw') hidden @endif>
    <form method="post" action="{{ route('account.wallet.withdraw') }}" class="wl-form" novalidate data-kind="withdraw">
      @csrf
      <div class="wl-bank"><x-icon name="receipt" class="icon-sm" /><div><b>{{ $bank->holder }}</b><small class="ltr d-block text-end">{{ $bank->iban }}</small></div></div>
      <label class="form-label" for="wdAmount">مبلغ برداشت (تومان)</label>
      <input class="form-control ltr wl-input" id="wdAmount" name="amount" inputmode="numeric" autocomplete="off" placeholder="۱۰٬۰۰۰٬۰۰۰" {{ $lock ? 'disabled' : '' }}>
      <button type="button" class="btn btn-link btn-sm px-0" data-all {{ $lock ? 'disabled' : '' }}>برداشت همه‌ی موجودی</button>
      <div class="invalid-feedback wl-err" hidden>مبلغ باید بین <x-price :value="$min_withdraw" /> و موجودی شما باشد.</div>
      <p class="pf-hint">واریز فقط به حساب ثبت‌شده به نام خودتان انجام می‌شود. حداقل برداشت <x-price :value="$min_withdraw" /> است.</p>
      <button type="submit" class="btn btn-primary w-100" {{ $lock ? 'disabled' : '' }}>ثبت درخواست برداشت</button>
    </form>
  </section>

  {{-- تراکنش‌ها --}}
  <section data-pane="tx" @if($tab !== 'tx') hidden @endif>
    <div class="chips wl-filter" role="tablist" aria-label="فیلتر تراکنش">
      <button type="button" class="chip active" data-f="all">همه</button>
      <button type="button" class="chip" data-f="in">واریز</button>
      <button type="button" class="chip" data-f="out">برداشت و پرداخت</button>
    </div>
    <div class="list-s">
      @foreach($tx as $t)
        <div class="li" data-dir="{{ $t['in'] ? 'in' : 'out' }}">
          <div class="li-icon {{ $t['in'] ? 'in' : 'out' }}"><x-icon :name="$kinds[$t['kind']] ?? ($t['in'] ? 'arrow-down' : 'arrow-up')" /></div>
          <div class="li-main"><div class="li-title">{{ $t['title'] }}</div><div class="li-sub" data-jdate="{{ $t['date']->toIso8601String() }}" data-time></div></div>
          <b class="{{ $t['in'] ? 'text-success' : '' }}">{{ $t['in'] ? '+' : '−' }}<x-price :value="$t['amount']" :unit="null" /></b>
        </div>
      @endforeach
    </div>
    <div class="d-none" id="wlEmpty"><x-empty-state icon="receipt" title="تراکنشی نیست" /></div>
  </section>
</div>
@endif
@endsection
