@extends('layouts.app')
@section('title', 'پیام‌ها')
@section('page_title', 'پیام‌ها')
@section('page_subtitle', $role === 'dealer' ? 'گفتگو با خریداران' : 'گفتگو با نمایشگاه‌ها')
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/chat.css') }}">@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="message" title="برای دیدن پیام‌ها وارد شوید" action="ورود / ثبت‌نام" :href="route('login', ['next' => url()->current()])" />
@else
  <p class="ch-rule"><x-icon name="shield" class="icon-sm" /> برای امنیت شما، تبادل شماره تلفن، آی‌دی و لینک پیش از پرداخت بیعانه ممکن نیست.</p>
  <div class="ch-list">
    @foreach($threads as $t)
      <a href="{{ route('chat.show', $t['id']) }}" class="ch-row {{ $t['unread'] ? 'is-unread' : '' }}">
        <img src="{{ $t['listing']['image'] }}" alt="" loading="lazy">
        <div class="ch-main">
          <div class="ch-top"><b>{{ $t['with'] }}</b><time data-jfmt="time" data-jdate="{{ $t['last_at'] }}"></time></div>
          <span class="ch-car">{{ $t['listing']['title'] }}</span>
          <p class="ch-last">{{ \Illuminate\Support\Str::limit($t['last'], 60) }}</p>
        </div>
        @if($t['unread'])<span class="ch-badge" data-fa>{{ $t['unread'] }}</span>@endif
      </a>
    @endforeach
  </div>
  @if(!count($threads))<x-empty-state icon="message" title="هنوز گفتگویی ندارید" text="از صفحه‌ی هر آگهی می‌توانید با نمایشگاه گفتگو کنید." action="مشاهده آگهی‌ها" :href="route('listings.index')" />@endif
@endif
@endsection
