@extends('admin.layouts.auth')
@section('title', 'ورود مدیر')

@section('content')
<section class="auth-card" aria-labelledby="loginTitle">
  <div class="sb-brand">
    <span class="sb-logo">{{ mb_substr(config('app.name'), 0, 1) }}</span>
    <div><b>{{ config('app.name') }}</b><small>پنل مدیریت</small></div>
  </div>
  <h1 id="loginTitle">ورود به پنل مدیریت</h1>
  <p class="sub">برای ادامه، اطلاعات حساب مدیر را وارد کنید.</p>

  {{-- بک‌اندکار: action به مسیر واقعی ورود متصل شود. data-demo-submit فقط برای نمایش قالب است و باید حذف شود --}}
  <form method="POST" action="{{ route('admin.login.submit') }}" data-demo-submit="{{ route('admin.dashboard') }}" novalidate>
    @csrf
    <div class="mb-3">
      <label class="form-label" for="mobile">شماره موبایل</label>
      <div class="field-icon">
        <x-admin.icon name="user" />
        <input type="tel" id="mobile" name="mobile" class="form-control" inputmode="numeric" autocomplete="username" maxlength="11" placeholder="09123456789" data-latin-digits required pattern="09[0-9]{9}">
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label" for="password">رمز عبور</label>
      <div class="field-icon">
        <x-admin.icon name="lock" />
        <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" placeholder="رمز عبور" required minlength="6">
        <button type="button" class="eye" data-eye="#password" aria-label="نمایش رمز"><x-admin.icon name="eye" /></button>
      </div>
    </div>
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="form-check m-0"><input class="form-check-input" type="checkbox" id="remember" name="remember"><label class="form-check-label" for="remember">مرا به خاطر بسپار</label></div>
    </div>
    <button type="submit" class="btn btn-ink w-100">ورود <x-admin.icon name="arrow-left" class="icon-sm" /></button>
  </form>
  <div class="auth-foot"><x-admin.icon name="lock" class="icon-sm" /> اتصال امن • فقط برای مدیران سایت</div>
</section>
@endsection
