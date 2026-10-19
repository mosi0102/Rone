@extends('layouts.app')
@section('title', 'ورود / ثبت‌نام')
@section('no_topbar', '1')
@section('body_class', 'no-bottom-nav')
@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}">@endpush

@section('content')
<div class="auth-wrap">
  <div class="card-s auth-card">
    <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
    <h1 id="authTitle">ورود یا ثبت‌نام</h1>
    <p id="authSub">شماره موبایل خود را وارد کنید تا کد تأیید ارسال شود.</p>

    {{-- مرحله ۱: شماره --}}
    <form class="auth-step" id="stepPhone" novalidate>
      <label class="form-label" for="phone">شماره موبایل</label>
      <input class="form-control form-control-lg ltr" id="phone" inputmode="numeric" autocomplete="tel" maxlength="11" placeholder="09123456789" autofocus>
      <div class="invalid-feedback d-block text-danger small" id="phoneErr" style="min-height:20px"></div>
      <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">دریافت کد تأیید</button>
      <p class="small mt-3 mb-0 text-center">ورود شما به‌معنی پذیرش <a href="{{ route('help') }}">قوانین و حریم خصوصی</a> است.</p>
    </form>

    {{-- مرحله ۲: کد --}}
    <form class="auth-step d-none" id="stepCode" novalidate>
      <label class="form-label" for="code">کد ۵ رقمی ارسال‌شده به <b class="ltr d-inline-block" id="phoneShow"></b></label>
      <input class="form-control form-control-lg otp-input" id="code" inputmode="numeric" autocomplete="one-time-code" maxlength="5" placeholder="•••••">
      <div class="text-danger small" id="codeErr" style="min-height:20px"></div>
      <button type="submit" class="btn btn-primary btn-lg w-100 mt-2" id="verifyBtn">ورود</button>
      <div class="auth-meta">
        <button type="button" class="link-btn" id="editPhone">ویرایش شماره</button>
        <button type="button" class="link-btn" id="resend" disabled>ارسال مجدد (<span id="timer" data-fa>۶۰</span>)</button>
      </div>
    </form>

    <a href="{{ route('home') }}" class="auth-skip">فعلاً فقط می‌خواهم ببینم</a>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    var next = @json($next), phone = '', tmr;
    function en(v) { return String(v).replace(/[۰-۹]/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d); }).replace(/[٠-٩]/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'.indexOf(d); }); }
    function startTimer() {
      var t = 60, $r = $('#resend').prop('disabled', true); clearInterval(tmr);
      $('#timer').text(App.toFa(t));
      tmr = setInterval(function () { t--; $('#timer').text(App.toFa(t)); if (t <= 0) { clearInterval(tmr); $r.prop('disabled', false).text('ارسال مجدد کد'); } }, 1000);
      $r.html('ارسال مجدد (<span id="timer">' + App.toFa(t) + '</span>)');
    }
    function toCode() {
      $('#stepPhone').addClass('d-none'); $('#stepCode').removeClass('d-none'); $('#phoneShow').text(App.toFa(phone));
      $('#authTitle').text('کد تأیید را وارد کنید'); $('#authSub').text('پیامک ممکن است چند ثانیه طول بکشد.');
      $('#code').val('').focus(); startTimer();
      // پر کردن خودکار کد پیامک در مرورگرهای پشتیبان‌دهنده (WebOTP)
      if ('OTPCredential' in window) { navigator.credentials.get({ otp: { transport: ['sms'] } }).then(function (o) { if (o) { $('#code').val(o.code).trigger('input'); } }).catch(function () {}); }
    }
    $('#stepPhone').on('submit', function (e) {
      e.preventDefault(); phone = en($('#phone').val()).replace(/\s/g, '');
      if (!/^09\d{9}$/.test(phone)) { $('#phoneErr').text('شماره موبایل معتبر نیست (مثال: ۰۹۱۲۳۴۵۶۷۸۹)'); $('#phone').addClass('is-invalid'); return; }
      $('#phoneErr').text(''); $('#phone').removeClass('is-invalid');
      var $b = $(this).find('button[type=submit]'); App.loading($b);
      $(document).trigger('app:otp-send', [phone]); // بک‌اند: ارسال پیامک (فعلاً شبیه‌سازی)
      setTimeout(function () { App.loading($b, false); toCode(); }, 700);
    });
    $('#phone').on('input', function () { $(this).removeClass('is-invalid'); $('#phoneErr').text(''); });
    $('#code').on('input', function () { var v = en(this.value).replace(/\D/g, '').slice(0, 5); this.value = v; $('#codeErr').text(''); if (v.length === 5) $('#stepCode').trigger('submit'); });
    $('#stepCode').on('submit', function (e) {
      e.preventDefault(); var c = en($('#code').val());
      if (c.length !== 5) { $('#codeErr').text('کد ۵ رقمی را کامل وارد کنید'); return; }
      var $b = $('#verifyBtn'); App.loading($b);
      $(document).trigger('app:otp-verify', [phone, c]); // بک‌اند: بررسی کد و ورود
      setTimeout(function () { // نمایشی: هر کدی پذیرفته می‌شود
        var u = new URL(next, location.origin); u.searchParams.set('as', 'user'); location.href = u.toString();
      }, 800);
    });
    $('#editPhone').on('click', function () { clearInterval(tmr); $('#stepCode').addClass('d-none'); $('#stepPhone').removeClass('d-none'); $('#authTitle').text('ورود یا ثبت‌نام'); $('#authSub').text('شماره موبایل خود را وارد کنید تا کد تأیید ارسال شود.'); $('#phone').focus(); });
    $('#resend').on('click', function () { $(document).trigger('app:otp-send', [phone]); App.toast('کد جدید ارسال شد', 'success', 1800); startTimer(); });
  })();
</script>
@endpush
