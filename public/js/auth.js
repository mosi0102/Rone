/* ==========================================================================
   auth.js — هسته‌ی مشترک ورود/ثبت‌نام: ابزارها، سنجش رمز، OTP، مراحل، API نمایشی
   ----------------------------------------------------------------------
   بک‌اندکار: فقط شیء «Auth.api» (پایین همین فایل) را با درخواست‌های واقعی جایگزین کند.
   هر تابع باید یک Promise/Deferred برگرداند: موفق ← resolve(داده)، ناموفق ← reject({ message }).
   ========================================================================== */
(function ($) {
  'use strict';
  var Auth = window.Auth = window.Auth || {};
  var FA = '۰۱۲۳۴۵۶۷۸۹', AR = '٠١٢٣٤٥٦٧٨٩';

  /* ---------- ابزارهای کوچک ---------- */
  Auth.en = function (v) { return String(v == null ? '' : v).replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/[٠-٩]/g, function (d) { return AR.indexOf(d); }); };
  Auth.phoneOk = function (p) { return /^09\d{9}$/.test(p); };
  Auth.nameOk = function (v) { return /^[؀-ۿa-zA-Z‌\s]{2,40}$/.test($.trim(v)); };
  Auth.nationalOk = function (c) {   // الگوریتم رسمی کد ملی
    c = Auth.en(c); if (!/^\d{10}$/.test(c) || /^(\d)\1{9}$/.test(c)) return false;
    for (var i = 0, s = 0; i < 9; i++) s += +c[i] * (10 - i);
    var r = s % 11, k = +c[9]; return r < 2 ? k === r : k === 11 - r;
  };
  Auth.store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) { /* نادیده */ } },
    sget: function (k) { try { return sessionStorage.getItem(k); } catch (e) { return null; } },
    sset: function (k, v) { try { v == null ? sessionStorage.removeItem(k) : sessionStorage.setItem(k, v); } catch (e) { /* نادیده */ } }
  };
  Auth.markSeen = function () { Auth.store.set('rone_welcome', '1'); };

  /* خطای زیر فیلد: Auth.err($input, 'متن') — با متن خالی پاک می‌شود */
  Auth.err = function ($el, msg) {
    var $f = $el.closest('.fld'); $el.toggleClass('is-invalid', !!msg).attr('aria-invalid', msg ? 'true' : 'false');
    $f.find('.fld-err').first().text(msg || '');
  };
  Auth.shake = function ($el) { if (!$el || !$el.length) return; $el.removeClass('shake'); void $el[0].offsetWidth; $el.addClass('shake'); };

  /* ---------- سنجش قدرت رمز عبور ---------- */
  var COMMON = /^(12345678|123456789|1234567890|11111111|00000000|password|password1|qwerty123|qwertyui|abc12345|iloveyou|1q2w3e4r|a1234567|12341234|09123456789)$/i;
  function seq(v) { var s = v.toLowerCase(), up = 0, dn = 0; for (var i = 1; i < s.length; i++) { var d = s.charCodeAt(i) - s.charCodeAt(i - 1); if (d === 1) up++; if (d === -1) dn++; } return (up >= s.length - 2 || dn >= s.length - 2) && s.length >= 6; }
  Auth.pwEval = function (raw, phone) {
    var v = Auth.en(raw), r = { len: v.length >= 8, mix: /[A-Za-z؀-ۿ]/.test(v) && /\d/.test(v) };
    r.common = !!v && !(COMMON.test(v) || /^(.)\1+$/.test(v) || seq(v) || (phone && v.length >= 6 && Auth.en(phone).indexOf(v) > -1));
    var ok = r.len && r.mix && r.common, bonus = (/[a-z]/.test(v) && /[A-Z]/.test(v) ? 1 : 0) + (/[^A-Za-z0-9؀-ۿ\s]/.test(v) ? 1 : 0) + (v.length >= 12 ? 1 : 0);
    var level = !v ? 0 : !ok ? 1 : bonus >= 2 ? 3 : 2;   // ۰ خالی • ۱ ضعیف • ۲ متوسط (قابل‌قبول) • ۳ قوی
    return { rules: r, ok: ok, level: level };
  };
  var LV = ['—', 'ضعیف', 'متوسط', 'قوی'];

  /* راه‌اندازی یک فیلد رمز: Auth.pw($('[data-pw]')) → { ok(), val() } */
  Auth.pw = function ($w, getPhone) {
    var $i = $w.find('input').first(), api = {
      eval: function () { return Auth.pwEval($i.val(), getPhone && getPhone()); },
      ok: function () { return api.eval().ok; }, val: function () { return $i.val(); }, $input: $i
    };
    $w.on('click', '[data-pw-toggle]', function () {
      var show = $i.attr('type') === 'password'; $i.attr('type', show ? 'text' : 'password');
      $(this).attr({ 'aria-pressed': show, 'aria-label': show ? 'پنهان‌کردن رمز عبور' : 'نمایش رمز عبور' }).find('use').attr('href', show ? '#i-eye-off' : '#i-eye');
    });
    if ($w.is('[data-meter]')) {
      $i.on('input', function () {
        var e = api.eval(); $w.find('[data-pw-meter]').attr('data-level', e.level).find('[data-pw-label]').text(LV[e.level]);
        $w.find('[data-rule]').each(function () { $(this).toggleClass('ok', !!$i.val() && !!e.rules[$(this).data('rule')]); });
        Auth.err($i, '');
      });
    } else { $i.on('input', function () { Auth.err($i, ''); }); }
    return api;
  };

  /* ---------- کادرهای کد یکبارمصرف ---------- */
  Auth.otp = function ($w, cb) {
    var $b = $w.find('.otp-box');
    function val() { return $b.map(function () { return this.value; }).get().join(''); }
    function fill(s) { s = Auth.en(s).replace(/\D/g, '').slice(0, $b.length); $b.each(function (i) { this.value = s[i] || ''; }); $b.eq(Math.min(s.length, $b.length - 1)).trigger('focus'); if (s.length === $b.length) cb(s); }
    $b.on('input', function () {
      var i = $b.index(this), v = Auth.en(this.value).replace(/\D/g, '');
      if (v.length > 1) { fill(v); return; }                         // چسباندن یا پر شدن خودکار پیامک
      this.value = v; $w.removeClass('is-invalid');
      if (v && i < $b.length - 1) $b.eq(i + 1).trigger('focus');
      if (val().length === $b.length) cb(val());
    }).on('keydown', function (e) {
      var i = $b.index(this);
      if (e.key === 'Backspace' && !this.value && i > 0) { $b.eq(i - 1).val('').trigger('focus'); e.preventDefault(); }
      if (e.key === 'ArrowLeft' && i < $b.length - 1) { $b.eq(i + 1).trigger('focus'); e.preventDefault(); }   // چیدمان LTR
      if (e.key === 'ArrowRight' && i > 0) { $b.eq(i - 1).trigger('focus'); e.preventDefault(); }
    }).on('paste', function (e) { var t = (e.originalEvent.clipboardData || window.clipboardData).getData('text'); e.preventDefault(); fill(t); })
      .on('focus', function () { this.select(); });
    return {
      val: val, focus: function () { $b.first().trigger('focus'); },
      clear: function () { $b.val(''); $w.removeClass('is-invalid'); },
      error: function () { $w.addClass('is-invalid'); Auth.shake($w); $b.val('').first().trigger('focus'); },
      webotp: function () {   // پر کردن خودکار از پیامک در مرورگرهای پشتیبان
        if (!('OTPCredential' in window)) return;
        try { navigator.credentials.get({ otp: { transport: ['sms'] } }).then(function (o) { if (o) fill(o.code); }).catch(function () {}); } catch (e) { /* نادیده */ }
      }
    };
  };

  /* ---------- مراحل: Auth.steps($card) → { go(name), back(), cur } ---------- */
  Auth.steps = function ($card, onChange) {
    var stack = [], $title = $card.find('[data-auth-title]'), $sub = $card.find('[data-auth-sub]'), $back = $card.find('[data-back]');
    function go(name, push) {
      var $s = $card.find('[data-step="' + name + '"]'); if (!$s.length) return;
      $card.find('[data-step]').prop('hidden', true); $s.prop('hidden', false);
      if (push !== false) stack.push(name);
      $title.text($s.data('title') || ''); $sub.html($s.data('sub') || '');
      $card.find('[data-phone-show]').text(Auth.phone ? Auth.toFa(Auth.phone) : '');
      $back.prop('hidden', stack.length < 2 && !$back.data('always'));
      var $f = $s.find('input:visible, select:visible').first(); if ($f.length && !$f.is('.otp-box')) setTimeout(function () { $f.trigger('focus'); }, 60);
      if (onChange) onChange(name);
    }
    $back.on('click', function () { if (stack.length > 1) { stack.pop(); go(stack[stack.length - 1], false); } else if ($back.data('href')) { location.href = $back.data('href'); } });
    return { go: go, cur: function () { return stack[stack.length - 1]; }, reset: function (n) { stack = []; go(n); } };
  };
  Auth.toFa = function (v) { return window.App ? App.toFa(v) : String(v); };

  /* ---------- تایمر ارسال مجدد ---------- */
  Auth.timer = function ($btn, secs) {
    var t = secs, id;
    function draw() { $btn.html(t > 0 ? 'ارسال مجدد (<span>' + Auth.toFa(t) + '</span>)' : 'ارسال مجدد کد').prop('disabled', t > 0); }
    function start() { t = secs; clearInterval(id); draw(); id = setInterval(function () { t--; draw(); if (t <= 0) clearInterval(id); }, 1000); }
    return { start: start, stop: function () { clearInterval(id); } };
  };

  /* ---------- جریان مشترک «شماره ← کد» ----------
     cfg: { $root, steps, onVerified(res), onSent(), resetMode() }  */
  Auth.flow = function (cfg) {
    var $r = cfg.$root, $phone = $r.find('#phone'), $codeErr = $r.find('#codeErr'), tries = 0, resends = 0;
    var tm = Auth.timer($r.find('[data-resend]'), 60), otp = Auth.otp($r.find('.otp-boxes'), verify);
    Auth.phone = '';

    $phone.on('input', function () { this.value = Auth.en(this.value).replace(/\D/g, '').slice(0, 11); Auth.err($phone, ''); });

    function send(isResend) {
      var d = $.Deferred();
      Auth.api.sendOtp(Auth.phone).done(function () {
        if (isResend) { resends++; App.toast('کد جدید ارسال شد', 'success', 1800); } else { cfg.steps.go('code'); }
        otp.clear(); tm.start(); otp.webotp(); if (isResend) otp.focus(); else setTimeout(otp.focus, 80);
        if (resends >= 2) $r.find('[data-step="code"] [data-goto-password]').addClass('hl');
        d.resolve();
      }).fail(function (e) { App.toast((e && e.message) || 'ارسال کد ناموفق بود. دوباره تلاش کنید.', 'error'); d.reject(); });
      return d.promise();
    }
    Auth.sendCode = function () { return send(false); };

    $r.on('submit', '[data-form="phone"]', function (e) {
      e.preventDefault(); var p = Auth.en($phone.val()).replace(/\s/g, '');
      if (!Auth.phoneOk(p)) { Auth.err($phone, 'شماره موبایل معتبر نیست (مثال: ۰۹۱۲۳۴۵۶۷۸۹)'); Auth.shake($phone); return; }
      Auth.phone = p; var $b = $(this).find('[type=submit]'); App.loading($b);
      send(false).always(function () { App.loading($b, false); });
    });
    $r.on('click', '[data-resend]', function () { send(true); });
    $r.on('click', '[data-edit-phone]', function () { tm.stop(); cfg.steps.go('phone'); });
    $r.on('submit', '[data-form="code"]', function (e) { e.preventDefault(); var c = otp.val(); if (c.length < 5) { $codeErr.text('کد ۵ رقمی را کامل وارد کنید'); Auth.shake($r.find('.otp-boxes')); return; } verify(c); });

    function verify(code) {
      if ($r.data('busy')) return; $r.data('busy', true); $codeErr.text('');
      var $b = $r.find('[data-verify]'); App.loading($b);
      Auth.api.verifyOtp(Auth.phone, code).done(function (res) { tm.stop(); cfg.onVerified(res || {}); })
        .fail(function (e) {
          tries++; otp.error(); $codeErr.text(tries >= 3 ? 'چند بار کد اشتباه وارد شد؛ کد جدید دریافت کنید.' : ((e && e.message) || 'کد وارد‌شده درست نیست.'));
          if (tries >= 3) { $r.find('[data-resend]').trigger('focus'); tries = 0; }
        }).always(function () { $r.data('busy', false); App.loading($b, false); });
    }
    return { otp: otp, send: send, phone: function () { return Auth.phone; } };
  };

  /* ---------- پایان ورود: رفتن به مقصد ---------- */
  Auth.done = function (next) {
    Auth.markSeen(); Auth.store.sset('rone_dealer_draft', null);
    var u = new URL(next || '/', location.origin); u.searchParams.set('as', 'user'); location.href = u.toString();
  };

  /* ==========================================================================
     API (نمایشی) — بک‌اندکار جایگزین کند. قرارداد:
       sendOtp(phone)                 → resolve()                          | reject({message})
       verifyOtp(phone, code)         → resolve({ isNew: bool })           | reject({message})
       loginPassword(phone, password) → resolve()                          | reject({message})
       register({phone, first_name, last_name, password})        → resolve()
       resetPassword({phone, password})                          → resolve()
       registerDealer(FormData)                                  → resolve({ status:'pending' })
     نمایش دمو: کد «00000» اشتباه است؛ شماره‌ای که رقم آخرش فرد باشد «کاربر جدید» حساب می‌شود؛
     رمز «Demo1234» برای همه‌ی حساب‌های موجود درست است.
     ========================================================================== */
  function later(fn, ms) { var d = $.Deferred(); setTimeout(function () { fn(d); }, ms || 700); return d.promise(); }
  Auth.api = Auth.api || {
    sendOtp: function () { return later(function (d) { d.resolve(); }); },
    verifyOtp: function (phone, code) { return later(function (d) { code === '00000' ? d.reject({ message: 'کد وارد‌شده درست نیست.' }) : d.resolve({ isNew: +phone.slice(-1) % 2 === 1 }); }); },
    loginPassword: function (phone, pw) { return later(function (d) { +phone.slice(-1) % 2 === 1 ? d.reject({ message: 'حسابی با این شماره پیدا نشد. با کد تأیید ثبت‌نام کنید.' }) : (pw === 'Demo1234' ? d.resolve() : d.reject({ message: 'رمز عبور درست نیست.' })); }); },
    register: function () { return later(function (d) { d.resolve(); }, 900); },
    resetPassword: function () { return later(function (d) { d.resolve(); }, 900); },
    registerDealer: function () { return later(function (d) { d.resolve({ status: 'pending' }); }, 1400); }
  };
})(jQuery);
