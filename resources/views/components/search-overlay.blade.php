{{--
  لایه‌ی جست‌وجوی تمام‌صفحه (بدون رفتن به آدرس دیگر): لمس کادر جست‌وجو آن را باز می‌کند (js/search-overlay.js).
  تایپ/انتخاب پیشنهاد/دسته ← نتایج همان‌جا با AJAX بارگذاری می‌شود (js/listing-browser.js)، فیلتر و مرتب‌سازی هم همین‌طور.
  شهر با همان شیت #citySheet عوض می‌شود. مقصد AJAX: route('listings.index') یا (scope=agencies) route('agencies.search').
--}}
@props(['city' => 'سبزوار', 'scope' => 'listings'])
@php
  $agencies = $scope === 'agencies';   // آژانس‌ها: فقط جست‌وجو (بدون فیلتر/مرتب‌سازی)، مقصد AJAX: agencies.search
  $ph = $agencies ? 'جست و جو در آژانس ها' : 'جست و جو در املاک';
  $popular = $agencies ? ['پارسیان', 'آرش', 'کرمی', 'آریا', 'پدیده'] : ['آپارتمان نوساز', 'رهن کامل', 'ویلا', 'مغازه', 'سوئیت مبله', 'پیش‌فروش', 'سعادت‌آباد'];   // پرطرفدار (از بک‌اند)
  $cats = $agencies
    ? [['res', 'مسکونی', 'home'], ['com', 'اداری و تجاری', 'briefcase'], ['project', 'پروژه‌ها', 'hammer'], ['short', 'کوتاه‌مدت', 'calendar']]
    : [['sale_res', 'فروش مسکونی', 'home'], ['rent_res', 'اجاره مسکونی', 'key'], ['sale_com', 'فروش تجاری', 'briefcase'], ['rent_com', 'اجاره تجاری', 'store'], ['project', 'پروژه‌ها', 'hammer'], ['short', 'کوتاه‌مدت', 'calendar']];
@endphp
@push('styles')<link rel="stylesheet" href="{{ asset('css/search.css') }}">@unless($agencies)<link rel="stylesheet" href="{{ asset('css/listings.css') }}">@endunless @endpush
@push('scripts')@unless($agencies)<script src="{{ asset('js/filters.js') }}"></script><script src="{{ asset('js/listing-browser.js') }}"></script>@endunless<script src="{{ asset('js/search-overlay.js') }}"></script>@endpush
<div class="so" id="searchOverlay" role="dialog" aria-modal="true" aria-label="جست‌وجو" aria-hidden="true" data-scope="{{ $scope }}" data-url="{{ $agencies ? route('agencies.search') : route('listings.index') }}">
  <header class="sp-head">
    <button type="button" class="icon-btn" id="soBack" aria-label="بستن جست‌وجو"><x-icon name="arrow-left" flip /></button>
    <form class="sp-form" id="soForm" role="search">
      <x-icon name="search" class="sp-ic" />
      <input type="search" id="soInput" name="q" autocomplete="off" placeholder="{{ $ph }}" enterkeyhint="search" aria-label="{{ $ph }}">
      <button type="button" class="sp-clear" id="soClear" aria-label="پاک کردن" hidden><x-icon name="x" class="icon-sm" /></button>
    </form>
  </header>

  <div class="so-scroll" id="soScroll">
    <button type="button" class="sp-city" data-bs-toggle="offcanvas" data-bs-target="#citySheet">
      <span class="sp-city-ic"><x-icon name="pin" /></span>
      <span class="sp-city-txt"><small>شهر</small><b class="js-city-label">{{ $city }}</b></span>
      <span class="sp-city-act">تغییر <x-icon name="chevron-left" class="icon-sm" /></span>
    </button>

    <div id="soDiscover">
      <section class="sp-block" id="soRecentBlock" hidden>
        <div class="sp-title"><h2><x-icon name="history" class="icon-sm" /> جستجوهای اخیر</h2><button type="button" id="soClearRecent">پاک کردن</button></div>
        <div class="sp-list" id="soRecent"></div>
      </section>
      <section class="sp-block">
        <div class="sp-title"><h2><x-icon name="trending-up" class="icon-sm" /> پرطرفدارها</h2></div>
        <div class="sp-chips">@foreach($popular as $t)<button type="button" class="sp-chip" data-so-q="{{ $t }}">{{ $t }}</button>@endforeach</div>
      </section>
      <section class="sp-block">
        <div class="sp-title"><h2>دسته‌بندی</h2></div>
        <div class="sp-cats">@foreach($cats as [$slug, $title, $icon])<button type="button" class="sp-cat" data-so-cat="{{ $slug }}"><span><x-icon :name="$icon" /></span>{{ $title }}</button>@endforeach</div>
      </section>
    </div>

    <div class="so-results" id="soResults" hidden></div>
  </div>
</div>
