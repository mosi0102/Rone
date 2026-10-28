<header class="adm-topbar">
  <button type="button" class="icon-btn tb-menu" data-sb-toggle aria-label="باز کردن منو"><x-admin.icon name="menu" /></button>

  <form class="tb-search" role="search" action="#" onsubmit="return false">
    <x-admin.icon name="search" />
    <input type="search" id="globalSearch" placeholder="جستجو در کاربران، آگهی‌ها، مزایده‌ها…" aria-label="جستجو" autocomplete="off">
    <kbd>Ctrl K</kbd>
  </form>

  <div class="tb-actions">
    <span class="tb-date" data-today></span>
    <a href="{{ route('admin.disputes.index') }}" class="icon-btn" aria-label="اعلان‌ها"><x-admin.icon name="bell" /><i class="dot"></i></a>
    <div class="dropdown">
      <button class="tb-user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="avatar">{{ mb_substr($admin['name'] ?? 'م', 0, 1) }}</span>
        <div><b>{{ $admin['name'] ?? 'مدیر سایت' }}</b><small>دسترسی کامل</small></div>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><x-admin.icon name="settings" class="icon-sm" /> تنظیمات</a></li>
        <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank" rel="noopener"><x-admin.icon name="eye" class="icon-sm" /> مشاهده‌ی سایت</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <button type="button" class="dropdown-item text-danger w-100" data-confirm="از حساب مدیریت خارج می‌شوید؟" data-confirm-ok="خروج" data-confirm-danger id="logoutBtn"><x-admin.icon name="logout" class="icon-sm" /> خروج</button>
        </li>
      </ul>
    </div>
  </div>
</header>
<form id="logoutForm" method="POST" action="{{ route('admin.logout') }}" class="d-none">@csrf</form>
