/* ==========================================================================
   dealer-register.js — ثبت‌نام نمایشگاه‌دار: شماره/کد ← مدیر ← نمایشگاه ← مدارک ← در انتظار تأیید
   پیش‌نویس فیلدها در sessionStorage نگه داشته می‌شود (با رفرش از دست نمی‌رود).
   ========================================================================== */
(function ($) {
  'use strict';
  var $c = $('[data-dealer]'); if (!$c.length) return;
  var DRAFT = 'rone_dealer_draft', MAX = 5 * 1024 * 1024, TYPES = ['image/jpeg', 'image/png'];
  var PROG = { phone: 1, code: 1, manager: 2, showroom: 3, docs: 4, done: 4 }, isNew = false, files = {};

  var steps = Auth.steps($c, function (name) {
    var n = PROG[name] || 1, $w = $c.find('[data-wiz]');
    $w.attr('aria-valuenow', n).prop('hidden', name === 'done');
    $c.find('[data-wiz-fill]').css('width', (n * 25) + '%'); $c.find('[data-wiz-text]').text('مرحله‌ی ' + Auth.toFa(n) + ' از ' + Auth.toFa(4));
    if (name === 'done') $c.find('[data-back]').prop('hidden', true);
    if (PROG[name] > 1 && name !== 'done') $c.find('[data-back]').prop('hidden', name === 'manager');  // بعد از تأیید کد، برگشت به مرحله‌ی کد معنی ندارد
  });
  var pw = Auth.pw($c.find('#dPw').closest('[data-pw]'), function () { return Auth.phone; });

  /* ---------- پیش‌نویس ---------- */
  function readDraft() { try { return JSON.parse(Auth.store.sget(DRAFT) || '{}'); } catch (e) { return {}; } }
  function saveDraft() {
    var d = {}; $c.find('[data-draft]').each(function () { d[this.id] = this.value; });
    d.types = $c.find('[data-draft-chk]:checked').map(function () { return this.value; }).get(); Auth.store.sset(DRAFT, JSON.stringify(d));
  }
  (function restore() {
    var d = readDraft();
    $c.find('[data-draft]').each(function () { if (d[this.id]) this.value = d[this.id]; });
    $.each(d.types || [], function (_, v) { $c.find('[data-draft-chk][value="' + v + '"]').prop('checked', true); });
  })();
  $c.on('input change', '[data-draft],[data-draft-chk]', saveDraft);
  $c.find('#dnc').on('input', function () { this.value = Auth.en(this.value).replace(/\D/g, '').slice(0, 10); });

  steps.go('phone');
  Auth.flow({
    $root: $c, steps: steps,
    onVerified: function (res) {
      isNew = !!res.isNew; $c.find('[data-newuser]').prop('hidden', !isNew);
      steps.reset('manager');
    }
  });

  /* ---------- ۲: مدیر ---------- */
  $c.on('submit', '[data-form="manager"]', function (e) {
    e.preventDefault(); var ok = true, $fn = $c.find('#dfn'), $ln = $c.find('#dln'), $nc = $c.find('#dnc');
    function chk($f, good, msg) { Auth.err($f, good ? '' : msg); if (!good) ok = false; }
    chk($fn, Auth.nameOk($fn.val()), 'نام را درست وارد کنید');
    chk($ln, Auth.nameOk($ln.val()), 'نام خانوادگی را درست وارد کنید');
    chk($nc, Auth.nationalOk(Auth.en($nc.val())), 'کد ملی معتبر نیست');
    if (isNew && !pw.ok()) { Auth.err(pw.$input, 'رمز عبور باید حداقل ۸ کاراکتر و ترکیبی از حروف و عدد و غیرقابل‌حدس باشد'); ok = false; }
    if (!ok) { Auth.shake($c.find('[data-form="manager"] .is-invalid').first()); return; }
    steps.go('showroom');
  });

  /* ---------- ۳: نمایشگاه ---------- */
  $c.on('submit', '[data-form="showroom"]', function (e) {
    e.preventDefault(); var ok = true;
    function chk($f, good, msg) { Auth.err($f, good ? '' : msg); if (!good) ok = false; }
    chk($c.find('#dsn'), $.trim($c.find('#dsn').val()).length >= 3, 'نام نمایشگاه را وارد کنید');
    chk($c.find('#dct'), !!$c.find('#dct').val(), 'شهر را انتخاب کنید');
    chk($c.find('#dad'), $.trim($c.find('#dad').val()).length >= 10, 'نشانی کامل را وارد کنید');
    var t = $c.find('[data-draft-chk]:checked').length; $c.find('#dtyErr').text(t ? '' : 'حداقل یک نوع را انتخاب کنید'); if (!t) ok = false;
    if (!ok) { Auth.shake($c.find('[data-form="showroom"] .is-invalid').first().length ? $c.find('[data-form="showroom"] .is-invalid').first() : $c.find('.chip-group')); return; }
    steps.go('docs');
  });
  $c.on('change', '[data-draft-chk]', function () { $c.find('#dtyErr').text(''); });

  /* ---------- ۴: مدارک ---------- */
  $c.on('change', '[data-upload] input[type=file]', function () {
    var $u = $(this).closest('[data-upload]'), k = $u.data('upload'), f = this.files && this.files[0], $e = $c.find('#' + k + 'Err');
    if (!f) return;
    if (TYPES.indexOf(f.type) < 0) { $e.text('فقط تصویر JPG یا PNG مجاز است'); this.value = ''; return; }
    if (f.size > MAX) { $e.text('حجم تصویر باید کمتر از ۵ مگابایت باشد'); this.value = ''; return; }
    $e.text(''); files[k] = f;
    var url = (window.URL || window.webkitURL).createObjectURL(f);
    $u.addClass('has').find('.up-empty').prop('hidden', true).end().find('.up-done').prop('hidden', false).end()
      .find('[data-prev]').attr('src', url).end().find('[data-name]').text(f.name);
  });
  $c.on('click', '[data-remove]', function (e) {
    e.preventDefault(); var $u = $(this).closest('[data-upload]'); delete files[$u.data('upload')];
    $u.removeClass('has').find('input').val('').end().find('.up-empty').prop('hidden', false).end().find('.up-done').prop('hidden', true);
  });
  $c.on('change', '#dterms', function () { $c.find('#dtermsErr').text(''); });

  $c.on('submit', '[data-form="docs"]', function (e) {
    e.preventDefault(); var ok = true;
    $.each(['license', 'idcard'], function (_, k) { if (!files[k]) { $c.find('#' + k + 'Err').text('بارگذاری این مدرک الزامی است'); ok = false; } });
    if (!$c.find('#dterms').prop('checked')) { $c.find('#dtermsErr').text('پذیرش قوانین الزامی است'); ok = false; }
    if (!ok) return;
    var fd = new FormData(), d = readDraft();
    fd.append('phone', Auth.phone); fd.append('first_name', $.trim($c.find('#dfn').val())); fd.append('last_name', $.trim($c.find('#dln').val()));
    fd.append('national_code', Auth.en($c.find('#dnc').val())); if (isNew) fd.append('password', pw.val());
    fd.append('showroom_name', $.trim($c.find('#dsn').val())); fd.append('city', $c.find('#dct').val()); fd.append('address', $.trim($c.find('#dad').val()));
    $.each(d.types || [], function (_, t) { fd.append('types[]', t); });
    fd.append('license', files.license); fd.append('id_card', files.idcard);
    var $b = $(this).find('[type=submit]'); App.loading($b);
    Auth.api.registerDealer(fd).done(function () { Auth.store.sset(DRAFT, null); Auth.markSeen(); steps.go('done'); })
      .fail(function (er) { App.toast((er && er.message) || 'ثبت درخواست ناموفق بود', 'error'); }).always(function () { App.loading($b, false); });
  });
})(jQuery);
