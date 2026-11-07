@extends('layouts.app')
@section('title', 'اطلاعات کاربری و احراز هویت')
@section('page_title', 'احراز هویت')
@section('page_subtitle', 'برای شرکت در مزایده و برداشت')
@section('back', route('account'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/account.js') }}"></script>@endpush

@php
  $st = $auth->profile_status;
  $editable = in_array($st, ['none', 'rejected'], true);
  $meta = [
    'none' => ['warn', 'alert', 'احراز هویت تکمیل نشده', 'برای شرکت در مزایده و برداشت از کیف پول باید اطلاعات زیر را تکمیل کنید.'],
    'pending' => ['info', 'clock', 'مدارک در حال بررسی است', 'معمولاً کمتر از ۲۴ ساعت کاری طول می‌کشد. نتیجه را پیامکی و در همین صفحه اعلام می‌کنیم.'],
    'approved' => ['ok', 'badge-check', 'هویت شما تأیید شده است', 'همه‌ی امکانات پرداختی برای حساب شما فعال است.'],
    'rejected' => ['bad', 'x-circle', 'مدارک تأیید نشد', $reject_reason],
  ][$st];
@endphp

@section('content')
<div class="pf">
  <section class="pf-status is-{{ $meta[0] }}" role="status">
    <span class="pf-st-ic"><x-icon :name="$meta[1]" /></span>
    <div><h2>{{ $meta[2] }}</h2><p>{{ $meta[3] }}</p></div>
  </section>

  @if($need && $editable)
    <p class="pf-need"><x-icon name="lock" class="icon-sm" /> برای ادامه‌ی پرداخت، ابتدا احراز هویت را کامل کنید؛ پس از تأیید به همان مرحله برمی‌گردید.</p>
  @endif
  @if($sent)
    <p class="pf-need is-ok"><x-icon name="check-circle" class="icon-sm" /> مدارک شما ثبت شد و در صف بررسی قرار گرفت.</p>
  @endif

  @if($editable)
  <form class="pf-form" id="pfForm" method="post" action="{{ route('account.profile.save') }}" enctype="multipart/form-data" novalidate data-next="{{ $next }}">
    @csrf

    <section class="pf-card">
      <h3><span class="pf-n">۱</span> اطلاعات شخصی</h3>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label" for="first_name">نام <b class="req">*</b></label>
          <input class="form-control" id="first_name" name="first_name" autocomplete="given-name" required value="{{ $st === 'rejected' ? $p->first_name : '' }}">
          <div class="invalid-feedback">نام را وارد کنید.</div>
        </div>
        <div class="col-6">
          <label class="form-label" for="last_name">نام خانوادگی <b class="req">*</b></label>
          <input class="form-control" id="last_name" name="last_name" autocomplete="family-name" required value="{{ $st === 'rejected' ? $p->last_name : '' }}">
          <div class="invalid-feedback">نام خانوادگی را وارد کنید.</div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="national_code">کد ملی <b class="req">*</b></label>
          <input class="form-control ltr" id="national_code" name="national_code" inputmode="numeric" maxlength="10" autocomplete="off" required data-digits placeholder="۰۰۱۲۳۴۵۶۷۸">
          <div class="invalid-feedback">کد ملی معتبر نیست.</div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="birth_date">تاریخ تولد <b class="req">*</b></label>
          <input class="form-control ltr" id="birth_date" name="birth_date" inputmode="numeric" maxlength="10" autocomplete="bday" required data-date placeholder="۱۳۷۰/۰۱/۰۱">
          <div class="invalid-feedback">تاریخ را به‌صورت ۱۳۷۰/۰۱/۰۱ وارد کنید.</div>
        </div>
        <div class="col-12">
          <label class="form-label" for="phone_ro">شماره موبایل</label>
          <input class="form-control ltr" id="phone_ro" value="{{ $auth->phone }}" readonly>
        </div>
      </div>
    </section>

    <section class="pf-card">
      <h3><span class="pf-n">۲</span> مدارک هویتی</h3>
      <p class="pf-hint">تصویر باید واضح، بدون بازتاب نور و با همه‌ی لبه‌های کارت باشد (jpg، png یا webp تا ۵ مگابایت).</p>
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="pf-file" data-file>
            <input type="file" name="national_card" id="national_card" accept="image/jpeg,image/png,image/webp" class="d-none" required>
            <label for="national_card" class="pf-drop" tabindex="0" role="button" aria-label="انتخاب تصویر کارت ملی">
              <span class="pf-drop-ic"><x-icon name="camera" /></span>
              <b>تصویر کارت ملی</b><small>روی کارت (سمت عکس‌دار)</small>
            </label>
            <div class="pf-prev" hidden><img alt="پیش‌نمایش کارت ملی"><button type="button" class="pf-rm" aria-label="حذف تصویر"><x-icon name="x" class="icon-sm" /></button></div>
            <div class="invalid-feedback d-block pf-err" hidden></div>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="pf-file" data-file>
            <input type="file" name="selfie_with_card" id="selfie_with_card" accept="image/jpeg,image/png,image/webp" class="d-none" required>
            <label for="selfie_with_card" class="pf-drop" tabindex="0" role="button" aria-label="انتخاب تصویر سلفی همراه کارت ملی">
              <span class="pf-drop-ic"><x-icon name="user" /></span>
              <b>سلفی همراه کارت ملی</b><small>چهره و کارت در یک قاب</small>
            </label>
            <div class="pf-prev" hidden><img alt="پیش‌نمایش سلفی"><button type="button" class="pf-rm" aria-label="حذف تصویر"><x-icon name="x" class="icon-sm" /></button></div>
            <div class="invalid-feedback d-block pf-err" hidden></div>
          </div>
        </div>
      </div>
    </section>

    <section class="pf-card">
      <h3><span class="pf-n">۳</span> اطلاعات بانکی (برای برداشت از کیف پول)</h3>
      <p class="pf-hint">حساب باید به نام خودِ شما باشد؛ واریز به حساب دیگران انجام نمی‌شود.</p>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label" for="iban">شماره شبا <b class="req">*</b></label>
          <div class="pf-iban"><span>IR</span><input class="form-control ltr" id="iban" name="iban" inputmode="numeric" maxlength="24" autocomplete="off" required data-digits placeholder="۰۶۲۹۶۰۰۰۰۰۰۰۱۰۰۳۲۴۲۰۰۰۰۱"></div>
          <div class="invalid-feedback">شماره شبا معتبر نیست.</div>
        </div>
        <div class="col-12">
          <label class="form-label" for="card_number">شماره کارت <b class="req">*</b></label>
          <input class="form-control ltr" id="card_number" name="card_number" inputmode="numeric" maxlength="19" autocomplete="cc-number" required data-card placeholder="۶۰۳۷ ۹۹۱۲ ۳۴۵۶ ۷۸۹۰">
          <div class="invalid-feedback">شماره کارت معتبر نیست.</div>
        </div>
      </div>
    </section>

    <label class="pf-agree"><input type="checkbox" id="agree" required> <span>اطلاعات واردشده متعلق به خودم است و <a href="{{ route('help') }}">قوانین سامانه</a> را می‌پذیرم.</span></label>
    <div class="invalid-feedback pf-agree-err" hidden>پذیرش قوانین الزامی است.</div>

    <button type="submit" class="btn btn-primary btn-lg w-100" id="pfSubmit">ارسال برای بررسی</button>
  </form>
  @else
  <section class="pf-card pf-sum">
    <h3>اطلاعات ثبت‌شده</h3>
    <dl>
      <div><dt>نام و نام خانوادگی</dt><dd>{{ $p->first_name }} {{ $p->last_name }}</dd></div>
      <div><dt>کد ملی</dt><dd class="ltr">{{ $p->national_code }}</dd></div>
      <div><dt>تاریخ تولد</dt><dd>{{ $p->birth_date }}</dd></div>
      <div><dt>صاحب حساب</dt><dd>{{ $p->holder }}</dd></div>
      <div><dt>شماره شبا</dt><dd class="ltr">{{ $p->iban }}</dd></div>
      <div><dt>شماره کارت</dt><dd class="ltr">{{ $p->card }}</dd></div>
      <div><dt>مدارک</dt><dd>کارت ملی و سلفی ارسال شده <x-icon name="check" class="icon-sm" /></dd></div>
    </dl>
    @if($st === 'approved')<p class="pf-hint mb-0">برای تغییر اطلاعات بانکی یا هویتی با پشتیبانی تماس بگیرید.</p>@endif
  </section>
  @endif
</div>
@endsection
