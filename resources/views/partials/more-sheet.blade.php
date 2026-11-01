{{-- شیت «بیشتر» — محتوا بر اساس نوع کاربر: مهمان · کاربر · نمایشگاه‌دار (همه‌ی امکانات کاربر + بخش نمایشگاه) --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="moreSheet" aria-labelledby="moreTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header pb-2">
    <h5 class="offcanvas-title" id="moreTitle">@if($auth->check) {{ $auth->name }} @else بیشتر @endif</h5>
    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button>
  </div>
  <div class="offcanvas-body pt-0">

    @if($auth->check)
      <div class="wallet-mini">
        <span><x-icon name="wallet" class="icon-sm" /> موجودی کیف پول</span>
        <b><x-price :value="$auth->wallet" /></b>
        <a href="{{ route('account.wallet') }}" class="btn btn-accent btn-sm">شارژ</a>
      </div>
    @endif

    @if($auth->check && !$auth->verified)
      <a href="{{ route('account.profile') }}" class="verify-mini"><x-icon name="shield" class="icon-sm" /><span>{{ $auth->profile_status === 'pending' ? 'مدارک در حال بررسی است' : 'تکمیل اطلاعات و احراز هویت' }}</span></a>
    @endif

    <div class="more-grid">
      @if($auth->check)
        <a href="{{ route('account') }}"><span class="mg-ic"><x-icon name="user" /></span>حساب من</a>
        <a href="{{ route('account.reservations') }}"><span class="mg-ic"><x-icon name="shield" /></span>رزروهای من</a>
        <a href="{{ route('radar.index') }}"><span class="mg-ic"><x-icon name="sparkles" /></span>رادار خودرو</a>
        <a href="{{ route('chat.index') }}"><span class="mg-ic"><x-icon name="message" /></span>پیام‌ها</a>
        <a href="{{ route('account.wallet') }}"><span class="mg-ic"><x-icon name="wallet" /></span>کیف پول</a>
        <a href="{{ route('favorites') }}"><span class="mg-ic"><x-icon name="bookmark" /></span>نشان‌شده‌ها</a>
        <a href="{{ route('notifications') }}"><span class="mg-ic"><x-icon name="bell" /></span>اعلان‌ها</a>
        <a href="{{ route('listings.index') }}"><span class="mg-ic"><x-icon name="car" /></span>همه آگهی‌ها</a>
        @if($auth->is_dealer)
          <p class="more-sec">نمایشگاه</p>
          <a href="{{ route('panel') }}"><span class="mg-ic"><x-icon name="layout" /></span>پنل نمایشگاه</a>
          <a href="{{ route('panel', ['tab' => 'listings']) }}"><span class="mg-ic"><x-icon name="car" /></span>آگهی‌های من</a>
          <a href="{{ route('panel', ['tab' => 'auctions']) }}"><span class="mg-ic"><x-icon name="gavel" /></span>مزایده‌های من</a>
          <a href="{{ route('panel.radar') }}"><span class="mg-ic"><x-icon name="sparkles" /></span>درخواست خریداران</a>
          <a href="{{ route('panel', ['tab' => 'finance']) }}"><span class="mg-ic"><x-icon name="receipt" /></span>مالی</a>
          <a href="{{ route('dealers.show', $auth->dealer->id) }}"><span class="mg-ic"><x-icon name="store" /></span>پروفایل عمومی</a>
          <a href="{{ route('panel', ['tab' => 'settings']) }}"><span class="mg-ic"><x-icon name="settings" /></span>تنظیمات</a>
          <p class="more-sec">عمومی</p>
        @endif
      @else
        <a href="{{ route('login') }}"><span class="mg-ic"><x-icon name="user" /></span>ورود / ثبت‌نام</a>
        <a href="{{ route('dealer.join') }}"><span class="mg-ic"><x-icon name="store" /></span>ثبت‌نام نمایشگاه</a>
        <a href="{{ route('radar.index') }}"><span class="mg-ic"><x-icon name="sparkles" /></span>رادار خودرو</a>
        <a href="{{ route('listings.index') }}"><span class="mg-ic"><x-icon name="car" /></span>همه آگهی‌ها</a>
      @endif
      <a href="{{ route('help') }}"><span class="mg-ic"><x-icon name="help" /></span>راهنما</a>
      @if($auth->check && !$auth->is_dealer)
        <a href="{{ route('dealer.join') }}"><span class="mg-ic"><x-icon name="store" /></span>ثبت‌نام نمایشگاه</a>
      @endif
      <button type="button" class="d-none" data-install><span class="mg-ic"><x-icon name="download" /></span>نصب اپ</button>
      @if($auth->check)
        <button type="submit" form="logoutForm" class="mg-danger"><span class="mg-ic"><x-icon name="logout" /></span>خروج</button>
      @endif
    </div>
  </div>
</div>
