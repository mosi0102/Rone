@extends('layouts.app')
@section('title', 'بازار تخصصی خرید، فروش و اجاره ملک')
@section('no_topbar', '1')

@push('styles')<link rel="stylesheet" href="{{ asset('css/home.css') }}">@endpush

@section('content')
  {{-- سرتیتر + هیرو (سفید، تمام‌عرض) --}}
  <header class="masthead sticky-head">
    <div>
      <h1>{{ config('app.name') }}</h1>
      <p>خرید، فروش و اجاره ملک</p>
    </div>
    <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" width="92" height="48" decoding="async">
  </header>
  <h1 class="visually-hidden d-none d-lg-block">{{ config('app.name') }} | بازار تخصصی خرید، فروش و اجاره ملک</h1>
  <div class="home-top">
    <section class="hero" aria-label="معرفی">
      <img class="hero-img" src="{{ asset('img/hero.jpg') }}" srcset="{{ asset('img/hero-sm.jpg') }} 412w, {{ asset('img/hero.jpg') }} 824w" sizes="(min-width: 992px) 1200px, 100vw" width="824" height="400" alt="ملک" fetchpriority="high">
      <h2 class="hero-text"><span>با خیال راحت</span><span>خانه انتخاب کن</span></h2>
    </section>
  </div>

  {{-- جستجو + انتخاب شهر --}}
  <x-search-bar :city="$city" />

  {{-- مزایده ملک --}}
  @if(count($auctions))
    <section class="card-s auction-card">
      <div class="ac-head">
        <h2>مزایده ملک</h2>
        <span class="count-pill"><span data-fa>{{ $auctionsCount }}</span> مزایده فعال</span>
      </div>
      <div class="ac-list">
        @foreach($auctions as $item)<x-auction-row :item="$item" />@endforeach
      </div>
      <a href="{{ route('auctions.index') }}" class="btn btn-primary btn-block-lg">مشاهده همه مزایده‌ها <x-icon name="arrow-left" class="icon-sm" /></a>
    </section>
  @endif

  {{-- دسته‌بندی --}}
  <nav class="cat-grid" aria-label="دسته‌بندی">
    @foreach($categories as $cat)
      <a href="{{ route('listings.index', ['category' => $cat['slug']]) }}" class="cat-card">
        <span class="cat-ic"><x-icon :name="$cat['icon']" /></span>
        {{ $cat['title'] }}
        <small><span class="num" data-money="{{ $cat['count'] }}">{{ $cat['count'] }}</span> آگهی</small>
      </a>
    @endforeach
  </nav>

  {{-- مشاهده روی نقشه --}}
  <a href="{{ route('listings.map') }}" class="rd-cta map-cta text-reset">
    <span class="rd-cta-ic"><x-icon name="pin" /></span>
    <div><h3>آگهی‌ها را روی نقشه ببینید</h3><p>دسته را انتخاب کنید (مثلاً اجاره مسکونی) و آگهی‌های اطراف خود یا هر محله را پیدا کنید.</p></div>
    <x-icon name="chevron-left" />
  </a>

  {{-- رادار ملک --}}
  <a href="{{ route('radar.index') }}" class="rd-cta text-reset">
    <span class="rd-cta-ic"><x-icon name="sparkles" /></span>
    <div><h3>ملک دلخواهتان را پیدا نکردید؟</h3><p>مشخصاتش را ثبت کنید؛ مشابه‌ها را با درصد تطابق نشان می‌دهیم و اگر بعداً آگهی شد، خبرتان می‌کنیم.</p></div>
    <x-icon name="chevron-left" />
  </a>

  {{-- تبلیغات --}}
  <x-ad-banner :ad="$ad" />

  {{-- آگهی‌های فوری (کروسل) --}}
  @if(count($instant))
    <x-flash-carousel :items="$instant" :href="route('listings.index', ['type' => 'instant'])" />
  @endif

  {{-- جدیدترین آگهی‌ها --}}
  <x-section-head title="جدیدترین آگهی ها" :href="route('listings.index')" more="همه آگهی‌ها" />
  <div class="lr-list">
    @forelse($latest as $item)
      <x-listing-row :item="$item" />
    @empty
      <div class="w-100"><x-empty-state title="هنوز آگهی‌ای ثبت نشده" text="به‌زودی آگهی‌های جدید اینجا نمایش داده می‌شود." /></div>
    @endforelse
  </div>

  {{-- دعوت مشاوران املاک (فقط برای کسی که مشاور املاک نیست) --}}
  @unless($auth->is_agency)
    <a href="{{ route('agency.join') }}" class="agency-cta text-reset">
      <div>
        <h3>مشاور املاک هستید؟</h3>
        <p>به‌صورت رایگان به خانواده بزرگ {{ config('app.name') }} بپیوندید.</p>
      </div>
      <span class="cta-link"><x-icon name="arrow-left" class="icon-sm" /> شروع همکاری</span>
    </a>
  @endunless
@endsection
