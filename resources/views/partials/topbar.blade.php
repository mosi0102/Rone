{{--
  سرتیتر صفحات داخلی (طبق طرح): فلش بازگشت (راست) + عنوان/زیرعنوان + لوگو (چپ).
  @section('page_title', '...')  @section('page_subtitle', '...')  @section('back', url)
  در دسکتاپ فقط عنوان داخل محتوا نمایش داده می‌شود (هدر اصلی بالاست).
  صفحه‌ای که خودش h1 دارد (جزئیات آگهی/مزایده): @section('own_h1', 1) ← این عنوان h1 نمی‌شود (در هر صفحه فقط یک h1).
--}}
<header class="page-head">
  @hasSection('back')
    <a href="@yield('back')" class="icon-btn back" aria-label="بازگشت"><x-icon name="arrow-left" flip /></a>
  @endif
  <div class="ph-text">
    @hasSection('own_h1')<div class="ph-h1">@yield('page_title', config('app.name'))</div>@else<h1>@yield('page_title', config('app.name'))</h1>@endif
    @hasSection('page_subtitle')<p>@yield('page_subtitle')</p>@endif
  </div>
  <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="ph-logo" width="84" height="44" decoding="async">
</header>
<div class="page-head-desk">
  <div class="page-container">
    @hasSection('back')<a href="@yield('back')" class="ph-back"><x-icon name="chevron-right" class="icon-sm" /> بازگشت</a>@endif
    @hasSection('own_h1')<div class="ph-h1">@yield('page_title', config('app.name'))</div>@else<h1>@yield('page_title', config('app.name'))</h1>@endif
    @hasSection('page_subtitle')<p>@yield('page_subtitle')</p>@endif
  </div>
</div>
