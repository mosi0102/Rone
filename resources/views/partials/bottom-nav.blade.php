{{--
  ناوبری پایین (طبق طرح نهایی): خانه · آژانس · مزایده · بیشتر
  برای مشاور املاک یک آیتم پنجم «ثبت آگهی» اضافه می‌شود. حساب و کیف پول داخل «بیشتر» هستند.
--}}
<nav class="bottom-nav" style="--n: {{ $auth->is_agency ? 5 : 4 }}" aria-label="ناوبری اصلی">
  <a href="{{ route('home') }}" class="bn-item {{ request()->routeIs('home') ? 'active' : '' }}"><x-icon name="home" /> خانه</a>
  <a href="{{ route('agencies.index') }}" class="bn-item {{ request()->routeIs('agencies.index') ? 'active' : '' }}"><x-icon name="store" /> آژانس</a>
  @if($auth->is_agency)
    <a href="{{ route('listings.create') }}" class="bn-item {{ request()->routeIs('listings.create') ? 'active' : '' }}"><x-icon name="plus-circle" /> ثبت آگهی</a>
  @endif
  <a href="{{ route('auctions.index') }}" class="bn-item {{ request()->routeIs('auctions.*') ? 'active' : '' }}"><x-icon name="gavel" /> مزایده</a>
  <button type="button" class="bn-item {{ request()->routeIs('panel') ? 'active' : '' }}" data-bs-toggle="offcanvas" data-bs-target="#moreSheet" aria-controls="moreSheet">
    <x-icon name="grid" /> بیشتر
    @hasSection('bn_more_badge')<span class="bn-badge">@yield('bn_more_badge')</span>@endif
  </button>
</nav>
