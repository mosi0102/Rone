@extends('layouts.auth')
@section('title', 'ثبت‌نام نمایشگاه')

@push('scripts')<script src="{{ asset('js/dealer-register.js') }}"></script>@endpush

@php $types = ['car' => 'سواری', 'truck' => 'سنگین', 'moto' => 'موتورسیکلت', 'bike' => 'دوچرخه']; @endphp

@section('content')
<div class="auth-wrap">
  <div class="card-s auth-card" data-dealer data-login="{{ route('login') }}" data-pending="{{ route('home') }}">
    <header class="auth-head">
      <button type="button" class="auth-back" data-back data-always="1" data-href="{{ route('welcome') }}" aria-label="بازگشت"><x-icon name="arrow-left" flip /></button>
      <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
    </header>

    <div class="wiz" data-wiz role="progressbar" aria-valuemin="1" aria-valuemax="4" aria-valuenow="1" aria-label="مراحل ثبت‌نام">
      <span class="wiz-bar"><i data-wiz-fill></i></span>
      <small data-wiz-text>مرحله‌ی ۱ از ۴</small>
    </div>

    <h1 data-auth-title>ثبت‌نام نمایشگاه</h1>
    <p class="auth-sub" data-auth-sub>ابتدا شماره‌ی موبایل مدیر نمایشگاه را وارد کنید.</p>

    <x-auth.otp-flow title="ثبت‌نام نمایشگاه" sub="ابتدا شماره‌ی موبایل مدیر نمایشگاه را وارد کنید." :password="false">
      <p class="auth-terms">قبلاً ثبت‌نام کرده‌اید؟ <a href="{{ route('login') }}">ورود</a> · فقط خریدار هستید؟ <a href="{{ route('login') }}">ثبت‌نام کاربر عادی</a></p>
    </x-auth.otp-flow>

    {{-- ۲: اطلاعات مدیر --}}
    <section class="auth-step" data-step="manager" data-title="اطلاعات مدیر نمایشگاه" data-sub="این اطلاعات برای احراز هویت و تأیید نمایشگاه لازم است." hidden>
      <form data-form="manager" novalidate>
        <div class="row g-2">
          <div class="col-6 fld"><label class="form-label" for="dfn">نام</label><input class="form-control form-control-lg" id="dfn" data-draft autocomplete="given-name" maxlength="40" aria-describedby="dfnErr"><div class="fld-err" id="dfnErr" role="alert"></div></div>
          <div class="col-6 fld"><label class="form-label" for="dln">نام خانوادگی</label><input class="form-control form-control-lg" id="dln" data-draft autocomplete="family-name" maxlength="40" aria-describedby="dlnErr"><div class="fld-err" id="dlnErr" role="alert"></div></div>
        </div>
        <div class="fld"><label class="form-label" for="dnc">کد ملی</label><input class="form-control form-control-lg ltr" id="dnc" data-draft inputmode="numeric" maxlength="10" aria-describedby="dncErr" placeholder="۱۰ رقم"><div class="fld-err" id="dncErr" role="alert"></div></div>
        <div data-newuser><x-auth.password id="dPw" label="رمز عبور" :meter="true" autocomplete="new-password" /></div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">ادامه</button>
      </form>
    </section>

    {{-- ۳: اطلاعات نمایشگاه --}}
    <section class="auth-step" data-step="showroom" data-title="اطلاعات نمایشگاه" data-sub="نمایشگاه شما با این مشخصات به خریداران نشان داده می‌شود." hidden>
      <form data-form="showroom" novalidate>
        <div class="fld"><label class="form-label" for="dsn">نام نمایشگاه</label><input class="form-control form-control-lg" id="dsn" data-draft maxlength="80" aria-describedby="dsnErr"><div class="fld-err" id="dsnErr" role="alert"></div></div>
        <div class="fld"><label class="form-label" for="dct">شهر</label>
          <select class="form-select form-select-lg" id="dct" data-draft aria-describedby="dctErr">
            <option value="">انتخاب کنید</option>
            @foreach(['تهران','کرج','مشهد','اصفهان','شیراز','تبریز','قم','اهواز','رشت','کرمانشاه','یزد','سایر'] as $c)<option>{{ $c }}</option>@endforeach
          </select><div class="fld-err" id="dctErr" role="alert"></div></div>
        <div class="fld"><label class="form-label" for="dad">نشانی</label><textarea class="form-control" id="dad" data-draft rows="2" maxlength="250" aria-describedby="dadErr"></textarea><div class="fld-err" id="dadErr" role="alert"></div></div>
        <fieldset class="fld">
          <legend class="form-label">نوع وسایل نقلیه‌ی مورد فروش</legend>
          <div class="chip-group">
            @foreach($types as $k => $t)<label class="chip-opt"><input type="checkbox" name="types" value="{{ $k }}" data-draft-chk><span>{{ $t }}</span></label>@endforeach
          </div>
          <div class="fld-err" id="dtyErr" role="alert"></div>
        </fieldset>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">ادامه</button>
      </form>
    </section>

    {{-- ۴: مدارک و قوانین --}}
    <section class="auth-step" data-step="docs" data-title="بارگذاری مدارک" data-sub="تصویر واضح مدارک را بارگذاری کنید (JPG یا PNG، حداکثر ۵ مگابایت)." hidden>
      <form data-form="docs" novalidate>
        @foreach(['license' => 'جواز کسب نمایشگاه', 'idcard' => 'کارت ملی مدیر'] as $k => $t)
        <div class="fld">
          <span class="form-label" id="{{ $k }}L">{{ $t }}</span>
          <label class="upload" data-upload="{{ $k }}">
            <input type="file" accept="image/jpeg,image/png" hidden aria-labelledby="{{ $k }}L">
            <span class="up-empty"><x-icon name="upload-cloud" /> انتخاب تصویر</span>
            <span class="up-done" hidden><img alt="" data-prev><b data-name></b><button type="button" class="link-btn" data-remove>حذف</button></span>
          </label>
          <div class="fld-err" id="{{ $k }}Err" role="alert"></div>
        </div>
        @endforeach
        <label class="form-check auth-check fld"><input class="form-check-input" type="checkbox" id="dterms"><span class="form-check-label">با <a href="{{ route('help') }}" target="_blank" rel="noopener">قوانین نمایشگاه‌داران و حریم خصوصی</a> موافقم.</span></label>
        <div class="fld-err" id="dtermsErr" role="alert"></div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">ثبت درخواست</button>
      </form>
    </section>

    {{-- ۵: موفقیت --}}
    <section class="auth-step auth-success" data-step="done" data-title="درخواست شما ثبت شد" data-sub="" hidden>
      <div class="ok-ico"><x-icon name="check-circle" /></div>
      <p>مدارک شما در انتظار تأیید مدیریت است. نتیجه با پیامک اطلاع‌رسانی می‌شود (معمولاً کمتر از ۲۴ ساعت). تا آن زمان می‌توانید مثل کاربر عادی از برنامه استفاده کنید.</p>
      <a class="btn btn-primary btn-lg w-100" href="{{ route('home') }}?as=user">ورود به برنامه</a>
    </section>
  </div>
</div>
@endsection
