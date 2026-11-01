/* account.js — فرم احراز هویت: اعتبارسنجی سمت کاربر (بک‌اند هم باید همین قواعد را بسنجد) */
(function ($) {
  'use strict';
  var $f = $('#pfForm');
  if (!$f.length) return;

  var MAX = 5 * 1024 * 1024, TYPES = ['image/jpeg', 'image/png', 'image/webp'];

  /* ارقام فارسی/عربی → لاتین */
  function en(s) {
    return String(s).replace(/[۰-۹]/g, function (d) { return d.charCodeAt(0) - 1776; })
                    .replace(/[٠-٩]/g, function (d) { return d.charCodeAt(0) - 1632; });
  }
  function digitsOnly(s) { return en(s).replace(/\D/g, ''); }

  function validNational(c) {
    if (!/^\d{10}$/.test(c) || /^(\d)\1{9}$/.test(c)) return false;
    var s = 0, i; for (i = 0; i < 9; i++) s += +c[i] * (10 - i);
    var r = s % 11; return r < 2 ? +c[9] === r : +c[9] === 11 - r;
  }
  function validCard(c) {                                   // Luhn
    if (!/^\d{16}$/.test(c)) return false;
    var s = 0, i, d;
    for (i = 0; i < 16; i++) { d = +c[i]; if (i % 2 === 0) { d *= 2; if (d > 9) d -= 9; } s += d; }
    return s % 10 === 0;
  }
  function validIban(n) {                                   // IR + ۲۴ رقم، mod 97
    if (!/^\d{24}$/.test(n)) return false;
    var str = n.slice(2) + '1827' + n.slice(0, 2), rem = 0, i;
    for (i = 0; i < str.length; i++) rem = (rem * 10 + +str[i]) % 97;
    return rem === 1;
  }
  function validDate(v) {
    var m = /^(\d{4})\/(\d{2})\/(\d{2})$/.exec(v); if (!m) return false;
    var y = +m[1], mo = +m[2], d = +m[3];
    return y >= 1300 && y <= 1390 && mo >= 1 && mo <= 12 && d >= 1 && d <= (mo <= 6 ? 31 : 30);
  }

  /* نرمال‌سازی هنگام تایپ */
  $f.on('input', '[data-digits]', function () { this.value = digitsOnly(this.value); });
  $f.on('input', '[data-card]', function () {
    var v = digitsOnly(this.value).slice(0, 16); this.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
  });
  $f.on('input', '[data-date]', function () {
    var v = digitsOnly(this.value).slice(0, 8);
    if (v.length > 6) v = v.slice(0, 4) + '/' + v.slice(4, 6) + '/' + v.slice(6);
    else if (v.length > 4) v = v.slice(0, 4) + '/' + v.slice(4);
    this.value = v;
  });
  $f.on('input change', '.form-control', function () { $(this).removeClass('is-invalid'); });

  function mark($el, ok) { $el.toggleClass('is-invalid', !ok); return ok; }

  /* فایل‌ها */
  function setFile($box, file) {
    var $err = $box.find('.pf-err'), $prev = $box.find('.pf-prev'), $drop = $box.find('.pf-drop');
    $box.removeClass('is-invalid'); $err.prop('hidden', true).text('');
    if (!file) { $prev.prop('hidden', true); $drop.prop('hidden', false); return; }
    if (TYPES.indexOf(file.type) < 0) return fileErr($box, 'فقط تصویر jpg، png یا webp مجاز است.');
    if (file.size > MAX) return fileErr($box, 'حجم تصویر باید کمتر از ۵ مگابایت باشد.');
    var url = URL.createObjectURL(file), img = $prev.find('img')[0];
    img.onload = function () { URL.revokeObjectURL(url); };
    img.src = url; $prev.prop('hidden', false); $drop.prop('hidden', true);
  }
  function fileErr($box, msg) {
    $box.addClass('is-invalid').find('.pf-err').text(msg).prop('hidden', false);
    $box.find('input[type=file]').val(''); $box.find('.pf-prev').prop('hidden', true); $box.find('.pf-drop').prop('hidden', false);
  }
  $f.on('change', '[data-file] input[type=file]', function () { setFile($(this).closest('[data-file]'), this.files[0]); });
  $f.on('click', '.pf-rm', function () { var $b = $(this).closest('[data-file]'); $b.find('input[type=file]').val(''); setFile($b, null); });
  $f.on('keydown', '.pf-drop', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); $('#' + $(this).attr('for')).trigger('click'); } });

  /* ارسال */
  $f.on('submit', function (e) {
    var bad = [];
    function chk($el, ok) { if (!mark($el, ok)) bad.push($el[0]); }
    chk($('#first_name'), $.trim($('#first_name').val()).length >= 2);
    chk($('#last_name'), $.trim($('#last_name').val()).length >= 2);
    chk($('#national_code'), validNational(digitsOnly($('#national_code').val())));
    chk($('#birth_date'), validDate(en($('#birth_date').val())));
    chk($('#iban'), validIban(digitsOnly($('#iban').val())));
    chk($('#card_number'), validCard(digitsOnly($('#card_number').val())));
    $('[data-file]').each(function () {
      var $b = $(this);
      if (!$b.find('input[type=file]')[0].files.length) { fileErr($b, 'این تصویر الزامی است.'); bad.push($b[0]); }
    });
    var agreed = $('#agree').prop('checked'); $('.pf-agree-err').prop('hidden', agreed);
    if (!agreed) bad.push($('#agree')[0]);

    if (bad.length) {
      e.preventDefault();
      bad[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (window.App && App.toast) App.toast('موارد مشخص‌شده را اصلاح کنید.', 'error');
      return;
    }
    /* ارقام لاتین برای سرور */
    $('#national_code, #iban').each(function () { this.value = digitsOnly(this.value); });
    $('#card_number').val(digitsOnly($('#card_number').val()));
    $('#birth_date').val(en($('#birth_date').val()));
    $('#pfSubmit').prop('disabled', true).text('در حال ارسال…');
  });
})(jQuery);
