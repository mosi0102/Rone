<header class="desk-header">
  <div class="page-container desk-header-inner">
    <a href="{{ route('home') }}" class="brand"><img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" height="34"></a>
    <nav class="desk-nav" aria-label="منوی اصلی">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">خانه</a>
      <a href="{{ route('listings.index') }}" class="{{ request()->routeIs('listings.*') ? 'active' : '' }}">آگهی‌ها</a>
      <a href="{{ route('auctions.index') }}" class="{{ request()->routeIs('auctions.*') ? 'active' : '' }}">مزایده‌ها</a>
      <a href="{{ route('dealers.index') }}" class="{{ request()->routeIs('dealers.*') ? 'active' : '' }}">نمایشگاه‌ها</a>
    </nav>
    <form class="desk-search" action="{{ route('search') }}" role="search">
      <x-icon name="search" />
      <input type="search" name="q" class="form-control form-control-sm" placeholder="جستجوی خودرو، برند، نمایشگاه…">
    </form>
    <button type="button" class="btn btn-soft btn-sm d-none" data-install><x-icon name="download" class="icon-sm" /> نصب اپ</button>
    @if($auth->dealer_mode)
      <a href="{{ route('listings.create') }}" class="btn btn-accent btn-sm"><x-icon name="plus" class="icon-sm" /> ثبت آگهی</a>
    @elseif(!$auth->check)
      <a href="{{ route('dealer.join') }}" class="btn btn-outline-primary btn-sm">ثبت‌نام نمایشگاه</a>
    @endif
    @if($auth->check)
      <a href="{{ route('notifications') }}" class="icon-btn" aria-label="اعلان‌ها"><x-icon name="bell" /><span class="dot"></span></a>
      <div class="dropdown">
        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><x-icon name="user" class="icon-sm" /> {{ $auth->name }}</button>
        <ul class="dropdown-menu dropdown-menu-end user-menu">
          <li class="um-wallet"><x-icon name="wallet" class="icon-sm" /> <x-price :value="$auth->wallet" /></li>
          @if($auth->is_dealer)
            <li class="um-switch">
              <a href="{{ route('mode.switch', 'buyer') }}" class="{{ !$auth->dealer_mode ? 'active' : '' }}">خریدار</a>
              <a href="{{ route('mode.switch', 'dealer') }}" class="{{ $auth->dealer_mode ? 'active' : '' }}">نمایشگاه</a>
            </li>
          @endif
          <li><hr class="dropdown-divider"></li>
          @if($auth->dealer_mode)
            <li><a class="dropdown-item" href="{{ route('panel') }}">پنل نمایشگاه</a></li>
            <li><a class="dropdown-item" href="{{ route('panel', ['tab' => 'listings']) }}">آگهی‌های من</a></li>
            <li><a class="dropdown-item" href="{{ route('panel', ['tab' => 'finance']) }}">مالی و تسویه</a></li>
            <li><a class="dropdown-item" href="{{ route('dealers.show', $auth->dealer->id) }}">پروفایل عمومی</a></li>
          @else
            <li><a class="dropdown-item" href="{{ route('account') }}">حساب من</a></li>
            <li><a class="dropdown-item" href="{{ route('account.reservations') }}">رزروهای من</a></li>
            <li><a class="dropdown-item" href="{{ route('favorites') }}">نشان‌شده‌ها</a></li>
          @endif
          <li><a class="dropdown-item" href="{{ route('account.wallet') }}">کیف پول</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="{{ route('logout') }}">خروج</a></li>
        </ul>
      </div>
    @else
      <a href="{{ route('login') }}" class="btn btn-primary btn-sm"><x-icon name="user" class="icon-sm" /> ورود / ثبت‌نام</a>
    @endif
  </div>
</header>
