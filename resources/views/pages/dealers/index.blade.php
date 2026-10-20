@extends('layouts.app')
@section('title', 'نمایشگاه‌ها')
@section('page_title', 'نمایشگاه ها')
@section('page_subtitle', 'نمایشگاه‌های خودرو را مشاهده نمایید.')

@section('content')
  {{-- بنر بالای صفحه --}}
  <x-banner-slider :banners="$banners" class="bs-top" />

  {{-- جستجو و شهر --}}
  <x-search-bar placeholder="جست و جو در نمایشگاه ها" scope="dealers" :city="$city" />

  {{-- دسته‌بندی نمایشگاه‌ها --}}
  <nav class="cat-grid" aria-label="دسته‌بندی نمایشگاه‌ها">
    @foreach($categories as $cat)
      <a href="{{ route('dealers.index', ['category' => $cat['slug']]) }}" class="cat-card">
        <span class="cat-ic"><x-icon :name="$cat['icon']" /></span>
        {{ $cat['title'] }}
        <small><span class="num" data-money="{{ $cat['count'] }}">{{ $cat['count'] }}</span> نمایشگاه</small>
      </a>
    @endforeach
  </nav>

  {{-- نمایشگاه‌های ویژه --}}
  <x-section-head title="نمایشگاه های ویژه" :href="route('dealers.index', ['featured' => 1])" more="مشاهده همه" />
  <div class="h-scroll wide">
    @foreach($featured as $dealer)<x-dealer-card :dealer="$dealer" />@endforeach
  </div>

  @if(!empty($banners2))<x-banner-slider :banners="$banners2" :full="false" class="rounded-banner mt-3" style="aspect-ratio: 370 / 186" />@endif

  {{-- جدیدترین نمایشگاه‌ها --}}
  <x-section-head title="جدیدترین نمایشگاه ها" :href="route('dealers.index', ['sort' => 'new'])" more="مشاهده همه" />
  <div class="lr-list">
    @forelse($latest as $dealer)
      <x-dealer-row :dealer="$dealer" />
    @empty
      <div class="w-100"><x-empty-state icon="store" title="نمایشگاهی پیدا نشد" text="شهر یا عبارت جستجو را تغییر دهید." /></div>
    @endforelse
  </div>

  {{-- دعوت به همکاری --}}
  {{-- فقط برای مهمان/کاربر عادی؛ نمایشگاه‌دار این دعوت را نمی‌بیند --}}
  @unless($auth->is_dealer)
  <a href="{{ route('dealer.join') }}" class="dealer-cta text-reset">
    <div>
      <h3>نمایشگاه‌دار هستید؟</h3>
      <p>به‌صورت رایگان به خانواده بزرگ {{ config('app.name') }} بپیوندید.</p>
    </div>
    <span class="cta-link"><x-icon name="arrow-left" class="icon-sm" /> شروع همکاری</span>
  </a>
  @endunless
@endsection
