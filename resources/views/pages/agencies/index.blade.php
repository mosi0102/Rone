@extends('layouts.app')
@section('title', 'آژانس‌ها')
@section('page_title', 'آژانس ها')
@section('page_subtitle', 'آژانس‌های ملک را مشاهده نمایید.')

@section('content')
  {{-- بنر بالای صفحه --}}
  <x-banner-slider :banners="$banners" class="bs-top" />

  {{-- جستجو و شهر --}}
  <x-search-bar placeholder="جست و جو در آژانس ها" scope="agencies" :city="$city" />

  {{-- دسته‌بندی آژانس‌ها --}}
  <nav class="cat-grid" aria-label="دسته‌بندی آژانس‌ها">
    @foreach($categories as $cat)
      <a href="{{ route('agencies.index', ['category' => $cat['slug']]) }}" class="cat-card">
        <span class="cat-ic"><x-icon :name="$cat['icon']" /></span>
        {{ $cat['title'] }}
        <small><span class="num" data-money="{{ $cat['count'] }}">{{ $cat['count'] }}</span> آژانس</small>
      </a>
    @endforeach
  </nav>

  {{-- آژانس‌های ویژه --}}
  <x-section-head title="آژانس های ویژه" :href="route('agencies.index', ['featured' => 1])" more="مشاهده همه" />
  <div class="h-scroll wide">
    @foreach($featured as $agency)<x-agency-card :agency="$agency" />@endforeach
  </div>

  @if(!empty($banners2))<x-banner-slider :banners="$banners2" :full="false" class="rounded-banner mt-3" style="aspect-ratio: 370 / 186" />@endif

  {{-- جدیدترین آژانس‌ها --}}
  <x-section-head title="جدیدترین آژانس ها" :href="route('agencies.index', ['sort' => 'new'])" more="مشاهده همه" />
  <div class="lr-list">
    @forelse($latest as $agency)
      <x-agency-row :agency="$agency" />
    @empty
      <div class="w-100"><x-empty-state icon="store" title="آژانسی پیدا نشد" text="شهر یا عبارت جستجو را تغییر دهید." /></div>
    @endforelse
  </div>

  {{-- دعوت به همکاری --}}
  {{-- فقط برای مهمان/کاربر عادی؛ مشاور املاک این دعوت را نمی‌بیند --}}
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
