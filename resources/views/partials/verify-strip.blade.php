{{-- نوار یادآوری احراز هویت: فقط برای کاربر واردشده‌ی تأییدنشده، در صفحه‌هایی که به پرداخت نزدیک‌اند --}}
@if($auth->check && !$auth->verified && request()->routeIs('home', 'auctions.*', 'account'))
  <a href="{{ route('account.profile') }}" class="verify-strip">
    <x-icon name="shield" class="icon-sm" />
    <span>{{ $auth->profile_status === 'pending' ? 'مدارک شما در حال بررسی است.' : 'برای شرکت در مزایده و برداشت، احراز هویت را تکمیل کنید.' }}</span>
    <b>{{ $auth->profile_status === 'pending' ? 'مشاهده' : 'تکمیل' }}</b>
  </a>
@endif
