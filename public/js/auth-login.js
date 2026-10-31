/* ==========================================================================
   auth-login.js — صفحه‌ی ورود/ثبت‌نام: شماره ← کد ← (ورود | تکمیل ثبت‌نام)، ورود با رمز، فراموشی رمز
   ========================================================================== */
(function ($) {
  'use strict';
  var $c = $('[data-login]'); if (!$c.length) return;
  var next = $c.data('next'), mode = 'login', steps = Auth.steps($c), pwFails = 0;
  var regPw = Auth.pw($c.find('#regPw').closest('[data-pw]'), function () { return Auth.phone; });
  var resetPw = Auth.pw($c.find('#resetPw').closest('[data-pw]'), function () { return Auth.phone; });
  var loginPw = Auth.pw($c.find('#loginPw').closest('[data-pw]'));
  steps.go('phone');

  var flow = Auth.flow({
    $root: $c, steps: steps,
    onVerified: function (res) {
      if (mode === 'reset' && !res.isNew) { steps.go('reset'); return; }
      mode = 'login';
      if (res.isNew) steps.go('register'); else Auth.done(next);
    }
  });

  /* رفتن به «ورود با رمز عبور» (از مرحله‌ی شماره یا کد) */
  $c.on('click', '[data-goto-password]', function () {
    var p = Auth.en($c.find('#phone').val()).replace(/\D/g, ''); Auth.phone = Auth.phone || p;
    $c.find('#pwPhone').val(Auth.phone || p); steps.go('password');
  });
  $c.find('#pwPhone').on('input', function () { this.value = Auth.en(this.value).replace(/\D/g, '').slice(0, 11); Auth.err($(this), ''); });

  $c.on('submit', '[data-form="password"]', function (e) {
    e.preventDefault(); var $p = $c.find('#pwPhone'), p = Auth.en($p.val()), pw = loginPw.val();
    if (!Auth.phoneOk(p)) { Auth.err($p, 'شماره موبایل معتبر نیست'); return; }
    if (!pw) { Auth.err(loginPw.$input, 'رمز عبور را وارد کنید'); return; }
    Auth.phone = p; var $b = $(this).find('[type=submit]'); App.loading($b);
    Auth.api.loginPassword(p, pw).done(function () { Auth.done(next); })
      .fail(function (er) {
        pwFails++; Auth.err(loginPw.$input, (er && er.message) || 'ورود ناموفق بود'); Auth.shake(loginPw.$input);
        if (pwFails >= 3) App.toast('اگر رمز را فراموش کرده‌اید، با کد تأیید وارد شوید', 'info', 3500);
      }).always(function () { App.loading($b, false); });
  });

  /* ورود با کد از داخل مرحله‌ی رمز */
  $c.on('click', '[data-use-code]', function () {
    var p = Auth.en($c.find('#pwPhone').val());
    if (!Auth.phoneOk(p)) { Auth.err($c.find('#pwPhone'), 'ابتدا شماره‌ی موبایل را وارد کنید'); return; }
    Auth.phone = p; $c.find('#phone').val(p); mode = 'login'; Auth.sendCode();
  });
  /* فراموشی رمز: کد می‌فرستیم و بعد از تأیید، رمز جدید گرفته می‌شود */
  $c.on('click', '[data-forgot]', function () {
    var p = Auth.en($c.find('#pwPhone').val());
    if (!Auth.phoneOk(p)) { Auth.err($c.find('#pwPhone'), 'ابتدا شماره‌ی موبایل را وارد کنید'); return; }
    Auth.phone = p; $c.find('#phone').val(p); mode = 'reset'; Auth.sendCode();
  });

  /* تکمیل ثبت‌نام */
  $c.on('submit', '[data-form="register"]', function (e) {
    e.preventDefault(); var $fn = $c.find('#fn'), $ln = $c.find('#ln'), ok = true;
    if (!Auth.nameOk($fn.val())) { Auth.err($fn, 'نام را درست وارد کنید'); ok = false; } else Auth.err($fn, '');
    if (!Auth.nameOk($ln.val())) { Auth.err($ln, 'نام خانوادگی را درست وارد کنید'); ok = false; } else Auth.err($ln, '');
    if (!regPw.ok()) { Auth.err(regPw.$input, 'رمز عبور باید حداقل ۸ کاراکتر و ترکیبی از حروف و عدد و غیرقابل‌حدس باشد'); ok = false; }
    if (!$c.find('#terms').prop('checked')) { $c.find('#termsErr').text('پذیرش قوانین الزامی است'); ok = false; } else $c.find('#termsErr').text('');
    if (!ok) { Auth.shake($c.find('.is-invalid').first()); return; }
    var $b = $(this).find('[type=submit]'); App.loading($b);
    Auth.api.register({ phone: Auth.phone, first_name: $.trim($fn.val()), last_name: $.trim($ln.val()), password: regPw.val() })
      .done(function () { App.toast('ثبت‌نام انجام شد؛ خوش آمدید', 'success', 1500); setTimeout(function () { Auth.done(next); }, 700); })
      .fail(function (er) { App.toast((er && er.message) || 'ثبت‌نام ناموفق بود', 'error'); }).always(function () { App.loading($b, false); });
  });
  $c.on('change', '#terms', function () { $c.find('#termsErr').text(''); });

  /* رمز جدید */
  $c.on('submit', '[data-form="reset"]', function (e) {
    e.preventDefault(); if (!resetPw.ok()) { Auth.err(resetPw.$input, 'رمز عبور به‌اندازه‌ی کافی قوی نیست'); Auth.shake(resetPw.$input); return; }
    var $b = $(this).find('[type=submit]'); App.loading($b);
    Auth.api.resetPassword({ phone: Auth.phone, password: resetPw.val() }).done(function () { App.toast('رمز عبور تغییر کرد', 'success', 1500); setTimeout(function () { Auth.done(next); }, 700); })
      .fail(function (er) { App.toast((er && er.message) || 'تغییر رمز ناموفق بود', 'error'); }).always(function () { App.loading($b, false); });
  });

  $c.on('click', '[data-guest]', function () { Auth.markSeen(); });
})(jQuery);
