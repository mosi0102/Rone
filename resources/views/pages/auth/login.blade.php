@extends('layouts.auth')
@section('title', 'ورود / ثبت‌نام')

@push('scripts')<script src="{{ asset('js/auth-login.js') }}"></script>@endpush

@section('content')
<div class="auth-wrap">
  <div class="card-s auth-card" data-login data-next="{{ $next }}">
    <header class="auth-head">
      <button type="button" class="auth-back" data-back data-href="{{ route('welcome') }}" data-always="1" aria-label="بازگشت"><x-icon name="arrow-left" flip /></button>
      <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
    </header>
    <h1 data-auth-title>ورود یا ثبت‌نام</h1>
    <p class="auth-sub" data-auth-sub>شماره موبایل خود را وارد کنید تا کد تأیید ارسال شود.</p>

    {{-- ۱ و ۲: شماره و کد --}}
    <x-auth.otp-flow>
      <div class="auth-alt">
        <a class="btn btn-outline-secondary w-100" href="{{ route('home') }}?as=guest" data-guest><x-icon name="eye" /> ورود به‌عنوان مهمان</a>
        <a class="auth-dealer" href="{{ route('dealer.register') }}"><x-icon name="store" /><span><b>نمایشگاه‌دار هستید؟</b> ثبت‌نام نمایشگاه</span><x-icon name="chevron-left" /></a>
      </div>
      <p class="auth-terms">ورود شما به‌معنی پذیرش <a href="{{ route('help') }}">قوانین و حریم خصوصی</a> است.</p>
    </x-auth.otp-flow>

    {{-- ۳: ورود با رمز عبور --}}
    <section class="auth-step" data-step="password" data-title="ورود با رمز عبور" data-sub="شماره‌ی موبایل و رمز عبور خود را وارد کنید." hidden>
      <form data-form="password" novalidate>
        <div class="fld">
          <label class="form-label" for="pwPhone">شماره موبایل</label>
          <input class="form-control form-control-lg ltr" id="pwPhone" type="tel" inputmode="numeric" autocomplete="username" maxlength="11" placeholder="09123456789" aria-describedby="pwPhoneErr">
          <div class="fld-err" id="pwPhoneErr" role="alert"></div>
        </div>
        <x-auth.password id="loginPw" label="رمز عبور" autocomplete="current-password" />
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">ورود</button>
        <div class="auth-meta">
          <button type="button" class="link-btn" data-forgot>رمز را فراموش کرده‌ام</button>
          <button type="button" class="link-btn" data-use-code>ورود با کد تأیید</button>
        </div>
      </form>
    </section>

    {{-- ۴: تکمیل ثبت‌نام (کاربر جدید) --}}
    <section class="auth-step" data-step="register" data-title="تکمیل ثبت‌نام" data-sub="فقط یک قدم مانده؛ نام و رمز عبور خود را وارد کنید." hidden>
      <form data-form="register" novalidate>
        <div class="row g-2">
          <div class="col-6 fld"><label class="form-label" for="fn">نام</label><input class="form-control form-control-lg" id="fn" autocomplete="given-name" maxlength="40" aria-describedby="fnErr"><div class="fld-err" id="fnErr" role="alert"></div></div>
          <div class="col-6 fld"><label class="form-label" for="ln">نام خانوادگی</label><input class="form-control form-control-lg" id="ln" autocomplete="family-name" maxlength="40" aria-describedby="lnErr"><div class="fld-err" id="lnErr" role="alert"></div></div>
        </div>
        <x-auth.password id="regPw" label="رمز عبور" :meter="true" autocomplete="new-password" />
        <label class="form-check auth-check fld"><input class="form-check-input" type="checkbox" id="terms"><span class="form-check-label">با <a href="{{ route('help') }}" target="_blank" rel="noopener">قوانین و حریم خصوصی</a> موافقم.</span></label>
        <div class="fld-err" id="termsErr" role="alert"></div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">ثبت‌نام و ورود</button>
      </form>
    </section>

    {{-- ۵: رمز جدید (فراموشی رمز) --}}
    <section class="auth-step" data-step="reset" data-title="رمز عبور جدید" data-sub="برای حساب <b class='ltr d-inline-block' data-phone-show></b> رمز جدید انتخاب کنید." hidden>
      <form data-form="reset" novalidate>
        <x-auth.password id="resetPw" label="رمز عبور جدید" :meter="true" autocomplete="new-password" />
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">ذخیره و ورود</button>
      </form>
    </section>
  </div>
</div>
@endsection
