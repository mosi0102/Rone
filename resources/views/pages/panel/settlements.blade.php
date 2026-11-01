@extends('layouts.app')
@section('title', 'سابقه تسویه')
@section('page_title', 'سابقه تسویه')
@section('page_subtitle', 'درخواست‌های برداشت از موجودی نمایشگاه')
@section('back', route('panel', ['tab' => 'finance']))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush

@section('content')
@if(!$auth->is_dealer)
  <x-empty-state icon="store" title="این بخش مخصوص نمایشگاه‌داران است" text="با حساب نمایشگاه‌دار وارد شوید." action="ثبت‌نام نمایشگاه" :href="route('dealer.join')" />
@else
@php
  $lbl = ['pending' => ['در انتظار', 'is-warn', 'clock'], 'paid' => ['واریز شد', 'is-ok', 'check-circle'], 'rejected' => ['ردشده', 'is-bad', 'x-circle']];
@endphp
<div class="pf">
  <div class="wl-bank"><x-icon name="receipt" class="icon-sm" /><div><b>حساب مقصد</b><small class="ltr d-block text-end">{{ $iban }}</small></div></div>
  <div class="list-s">
    @foreach($items as $it)
      @php $l = $lbl[$it['status']]; @endphp
      <div class="li stl">
        <div class="li-icon {{ $l[1] }}"><x-icon :name="$l[2]" /></div>
        <div class="li-main">
          <div class="li-title"><x-price :value="$it['amount']" /> <span class="stl-badge {{ $l[1] }}">{{ $l[0] }}</span></div>
          <div class="li-sub" data-jdate="{{ $it['at']->toIso8601String() }}" data-time></div>
          @if($it['note'])<div class="nt-text">{{ $it['note'] }}</div>@endif
        </div>
      </div>
    @endforeach
  </div>
  <a href="{{ route('panel', ['tab' => 'finance']) }}" class="btn btn-primary">درخواست تسویه‌ی جدید</a>
</div>
@endif
@endsection
