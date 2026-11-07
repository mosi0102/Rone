@extends('layouts.app')
@section('title', 'اعلان‌ها')
@section('page_title', 'اعلان‌ها')
@section('back', route('account'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/notifications.js') }}"></script>@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="bell" title="وارد حساب خود شوید" text="برای دیدن اعلان‌ها ابتدا وارد شوید." action="ورود / ثبت‌نام" :href="route('login')" />
@else
@php
  $ic = ['auction' => 'gavel', 'radar' => 'sparkles', 'chat' => 'message', 'wallet' => 'wallet', 'system' => 'info'];
  $unread = collect($rows)->where('read', false)->count();
@endphp
<div class="pf">
  <div class="nt-bar">
    <div class="chips" role="tablist" aria-label="فیلتر اعلان‌ها">
      <button type="button" class="chip active" data-f="all">همه</button>
      <button type="button" class="chip" data-f="unread">خوانده‌نشده <span data-fa id="ntCount">{{ $unread }}</span></button>
    </div>
    <button type="button" class="btn btn-link btn-sm" id="ntReadAll" {{ $unread ? '' : 'disabled' }}>خواندن همه</button>
  </div>
  <div class="list-s nt-list" id="ntList">
    @foreach($rows as $n)
      <a href="{{ $n['url'] }}" class="li nt {{ $n['read'] ? '' : 'is-unread' }}" data-id="{{ $n['id'] }}">
        <div class="li-icon nt-ic nt-{{ $n['kind'] }}"><x-icon :name="$ic[$n['kind']]" /></div>
        <div class="li-main"><div class="li-title">{{ $n['title'] }}</div><div class="nt-text">{{ $n['text'] }}</div><div class="li-sub" data-jdate="{{ $n['at']->toIso8601String() }}" data-time></div></div>
        <i class="nt-dot" aria-label="خوانده‌نشده"></i>
      </a>
    @endforeach
  </div>
  <div class="d-none" id="ntEmpty"><x-empty-state icon="bell" title="اعلان خوانده‌نشده‌ای ندارید" /></div>
</div>
@endif
@endsection
