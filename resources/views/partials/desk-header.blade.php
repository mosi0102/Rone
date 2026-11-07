<header class="desk-header">
  <div class="page-container desk-header-inner">
    <a href="{{ route('home') }}" class="brand"><img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" width="65" height="34"></a>
    <nav class="desk-nav" aria-label="منوی اصلی">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">خانه</a>
      <a href="{{ route('listings.index') }}" class="{{ request()->routeIs('listings.*') && !request()->routeIs('listings.map') ? 'active' : '' }}">آگهی‌ها</a>
      <a href="{{ route('listings.map') }}" class="{{ request()->routeIs('listings.map') ? 'active' : '' }}">روی نقشه</a>
      <a href="{{ route('auctions.index') }}" class="{{ request()->routeIs('auctions.*') ? 'active' : '' }}">مزایده‌ها</a>
      <a href="{{ route('agencies.index') }}" class="{{ request()->routeIs('agencies.*') ? 'active' : '' }}">مشاوران املاک</a>
      <a href="{{ route('radar.index') }}" class="{{ request()->routeIs('radar.*') ? 'active' : '' }}">رادار ملک</a>
    </nav>
    <form class="desk-search" action="{{ route('search') }}" role="search">
      <x-icon name="search" />
      <input type="search" name="q" class="form-control form-control-sm" aria-label="جستجوی ملک، محله، آژانس" placeholder="جستجوی ملک، محله، آژانس…">
    </form>
    <button type="button" class="btn btn-soft btn-sm d-none" data-install><x-icon name="download" class="icon-sm" /> نصب اپ</button>
    @if($auth->is_agency)
      <a href="{{ route('listings.create') }}" class="btn btn-accent btn-sm"><x-icon name="plus" class="icon-sm" /> ثبت آگهی</a>
    @elseif(!$auth->check)
      <a href="{{ route('agency.join') }}" class="btn btn-outline-primary btn-sm">ثبت‌نام آژانس</a>
    @endif
    @if($auth->check)
      <a href="{{ route('notifications') }}" class="icon-btn" aria-label="اعلان‌ها"><x-icon name="bell" /><span class="dot"></span></a>
      <div class="dropdown">
        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><x-icon name="user" class="icon-sm" /> {{ $auth->name }}</button>
        <ul class="dropdown-menu dropdown-menu-end user-menu">
          <li class="um-wallet"><x-icon name="wallet" class="icon-sm" /> <x-price :value="$auth->wallet" /></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="{{ route('account') }}">حساب من@if(!$auth->verified) <span class="um-dot" aria-label="نیازمند تکمیل"></span>@endif</a></li>
          <li><a class="dropdown-item" href="{{ route('chat.index') }}">پیام‌ها</a></li>
          <li><a class="dropdown-item" href="{{ route('favorites') }}">نشان‌شده‌ها</a></li>
          @if($auth->is_agency)
            <li><hr class="dropdown-divider"></li>
            <li class="um-sec">آژانس</li>
            <li><a class="dropdown-item" href="{{ route('panel') }}">پنل آژانس</a></li>
            <li><a class="dropdown-item" href="{{ route('panel', ['tab' => 'listings']) }}">آگهی‌های من</a></li>
            <li><a class="dropdown-item" href="{{ route('panel.radar') }}">درخواست مشتریان</a></li>
            <li><a class="dropdown-item" href="{{ route('panel', ['tab' => 'finance']) }}">مالی و تسویه</a></li>
            <li><a class="dropdown-item" href="{{ route('agencies.show', $auth->agency->id) }}">پروفایل عمومی</a></li>
            <li><hr class="dropdown-divider"></li>
          @endif
          <li><a class="dropdown-item" href="{{ route('account.wallet') }}">کیف پول</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><button type="submit" form="logoutForm" class="dropdown-item text-danger">خروج</button></li>
        </ul>
      </div>
    @else
      <a href="{{ route('login') }}" class="btn btn-primary btn-sm"><x-icon name="user" class="icon-sm" /> ورود / ثبت‌نام</a>
    @endif
  </div>
</header>
