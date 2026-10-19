{{--
  سرتیتر صفحات داخلی (طبق طرح): فلش بازگشت (راست) + عنوان/زیرعنوان + لوگو (چپ).
  @section('page_title', '...')  @section('page_subtitle', '...')  @section('back', url)
  در دسکتاپ فقط عنوان داخل محتوا نمایش داده می‌شود (هدر اصلی بالاست).
--}}
<header class="page-head">
  @hasSection('back')
    <a href="@yield('back')" class="icon-btn back" aria-label="بازگشت"><x-icon name="arrow-left" flip /></a>
  @endif
  <div class="ph-text">
    <h1>@yield('page_title', config('app.name'))</h1>
    @hasSection('page_subtitle')<p>@yield('page_subtitle')</p>@endif
  </div>
  <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="ph-logo">
</header>
<div class="page-head-desk">
  <div class="page-container">
    @hasSection('back')<a href="@yield('back')" class="ph-back"><x-icon name="chevron-right" class="icon-sm" /> بازگشت</a>@endif
    <h1>@yield('page_title', config('app.name'))</h1>
    @hasSection('page_subtitle')<p>@yield('page_subtitle')</p>@endif
  </div>
</div>
