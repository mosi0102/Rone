@extends('layouts.auth')
@section('title', 'خوش آمدید')
@section('body_class', 'is-welcome')

@push('scripts')<script src="{{ asset('js/welcome.js') }}"></script>@endpush

@php
  $slides = [
    ['i' => 'shield-check', 'c' => 'blue',  't' => 'خرید امن با بیعانه‌ی امانی', 'd' => 'بیعانه تا زمان تحویل و تأیید خودرو نزد ما می‌ماند؛ بدون نگرانی از کلاهبرداری معامله کنید.'],
    ['i' => 'gavel',        'c' => 'amber', 't' => 'مزایده‌ی زنده', 'd' => 'با ورودیه‌ی کم در مزایده‌های زنده شرکت کنید و خودرو را با بهترین قیمت بخرید.'],
    ['i' => 'sparkles',     'c' => 'green', 't' => 'بگویید چه می‌خواهید، ما پیدا می‌کنیم', 'd' => 'مشخصات خودروی دلخواه را ثبت کنید؛ تا وقتی لغو نکنید، مورد مشابه را با درصد تطابق نشانتان می‌دهیم.'],
    ['i' => 'store',        'c' => 'navy',  't' => 'مخصوص نمایشگاه‌داران', 'd' => 'نمایشگاه خود را ثبت کنید، آگهی بگذارید و خریداران بیشتری پیدا کنید.'],
  ];
@endphp

@section('content')
<div class="welcome" data-welcome data-login="{{ route('login') }}" data-home="{{ route('home') }}" data-next="{{ $next }}">
  <div class="welcome-top">
    <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
    <button type="button" class="link-btn" data-skip>رد کردن</button>
  </div>

  <div class="slides" data-slides tabindex="0" aria-label="معرفی برنامه" aria-roledescription="carousel">
    @foreach($slides as $k => $s)
      <section class="slide" data-slide aria-roledescription="slide" aria-label="{{ $k + 1 }} از {{ count($slides) }}">
        <div class="slide-art tone-{{ $s['c'] }}" aria-hidden="true"><x-icon :name="$s['i']" /></div>
        <h2>{{ $s['t'] }}</h2>
        <p>{{ $s['d'] }}</p>
      </section>
    @endforeach
  </div>

  <div class="dots" role="tablist" aria-label="اسلایدها">
    @foreach($slides as $k => $s)
      <button type="button" class="dot" role="tab" data-dot="{{ $k }}" aria-label="اسلاید {{ $k + 1 }}"></button>
    @endforeach
  </div>

  <div class="welcome-actions">
    <button type="button" class="btn btn-primary btn-lg w-100" data-next-slide>بعدی</button>
    <div class="welcome-final" data-final hidden>
      <a class="btn btn-primary btn-lg w-100" href="{{ route('login') }}?next={{ urlencode($next) }}" data-go>ورود / ثبت‌نام</a>
      <a class="btn btn-outline-secondary btn-lg w-100" href="{{ route('home') }}?as=guest" data-go>ورود به‌عنوان مهمان</a>
      <a class="auth-dealer" href="{{ route('dealer.register') }}" data-go><x-icon name="store" /><span><b>نمایشگاه‌دار هستید؟</b> ثبت‌نام نمایشگاه</span><x-icon name="chevron-left" /></a>
    </div>
  </div>
</div>
@endsection
