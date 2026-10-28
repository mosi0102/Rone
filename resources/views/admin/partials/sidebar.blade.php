{{-- ناوبری ادمین. برای افزودن صفحه: یک ردیف به $nav اضافه کنید. badge از $badges (نام مسیر ← تعداد) خوانده می‌شود --}}
@php
  $badges = $badges ?? [];
  $nav = [
    ['label' => 'نمای کلی', 'items' => [
      ['admin.dashboard', 'داشبورد', 'grid', 'admin.dashboard'],
    ]],
    ['label' => 'محتوا', 'items' => [
      ['admin.listings.index', 'آگهی‌ها', 'list-checks', 'admin.listings.*'],
      ['admin.auctions.index', 'مزایده‌ها', 'gavel', 'admin.auctions.*'],
    ]],
    ['label' => 'معاملات', 'items' => [
      ['admin.deposits.index', 'بیعانه‌ها و رزروها', 'shield-check', 'admin.deposits.*'],
      ['admin.disputes.index', 'اختلاف‌ها', 'alert-triangle', 'admin.disputes.*', 'danger'],
    ]],
    ['label' => 'افراد', 'items' => [
      ['admin.users.index', 'کاربران', 'users', 'admin.users.*'],
      ['admin.dealers.index', 'نمایشگاه‌داران', 'store', 'admin.dealers.*'],
    ]],
    ['label' => 'مالی', 'items' => [
      ['admin.finance.index', 'مالی و تراکنش‌ها', 'wallet', 'admin.finance.*'],
      ['admin.settlements.index', 'تسویه‌ها', 'banknote', 'admin.settlements.*'],
    ]],
    ['label' => 'ارتباط', 'items' => [
      ['admin.chats.index', 'گفتگوها', 'message', 'admin.chats.*'],
    ]],
    ['label' => 'سیستم', 'items' => [
      ['admin.settings.index', 'تنظیمات تجاری', 'settings', 'admin.settings.*'],
      ['admin.catalog.index', 'داده‌های پایه', 'database', 'admin.catalog.*'],
      ['admin.content.index', 'صفحات و محتوا', 'file', 'admin.content.*'],
      ['admin.logs.index', 'گزارش فعالیت', 'history', 'admin.logs.*'],
    ]],
  ];
@endphp
<aside class="adm-sidebar" id="admSidebar" aria-label="منوی اصلی">
  <a href="{{ route('admin.dashboard') }}" class="sb-brand">
    <span class="sb-logo">{{ mb_substr(config('app.name'), 0, 1) }}</span>
    <div><b>{{ config('app.name') }}</b><small>پنل مدیریت</small></div>
  </a>

  <nav class="sb-nav adm-scroll">
    @foreach($nav as $group)
      <div class="sb-group"><span>{{ $group['label'] }}</span></div>
      @foreach($group['items'] as $it)
        @php [$route, $label, $icon, $pattern] = $it; $tone = $it[4] ?? null; $count = $badges[$route] ?? 0; @endphp
        <a href="{{ route($route) }}" class="sb-link {{ request()->routeIs($pattern) ? 'active' : '' }}" data-tip="{{ $label }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
          <x-admin.icon :name="$icon" />
          <span class="sb-label">{{ $label }}</span>
          @if($count)<span class="sb-badge {{ $tone }}" data-fa>{{ $count }}</span>@endif
        </a>
      @endforeach
    @endforeach
  </nav>

  <div class="sb-foot">
    <button type="button" class="sb-link sb-toggle border-0 bg-transparent w-100" data-sb-toggle data-tip="جمع‌کردن منو">
      <x-admin.icon name="panel-left" /><span class="sb-label">جمع‌کردن منو</span>
    </button>
  </div>
</aside>
