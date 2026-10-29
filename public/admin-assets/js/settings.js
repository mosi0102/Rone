/* ==========================================================================
   settings.js — فرم تنظیمات: اعتبارسنجی عددی، جمع وزن‌ها، نوار «ذخیره»
   بک‌اندکار: در رویداد submit درخواست واقعی بفرستد (به‌جای toast نمایشی).
   ========================================================================== */
(function ($) {
  'use strict';
  var $f = $('#settingsForm'); if (!$f.length) return;
  var $bar = $('[data-save-bar]'), initial = {};

  var FA = '۰۱۲۳۴۵۶۷۸۹', AR = '٠١٢٣٤٥٦٧٨٩';
  function latin(v) { return String(v).replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/[٠-٩]/g, function (d) { return AR.indexOf(d); }).replace(/٫/g, '.').replace(/[٬,\s]/g, ''); }
  function show(v, money) { return money ? Admin.money(v) : String(v); }

  $f.find('[data-num]').each(function () { var $i = $(this); var v = latin($i.val()); $i.val($i.is('[data-money]') ? Admin.money(v) : Admin.toFa(v)); });
  function snapshot() { $f.find('input').each(function () { initial[this.name] = this.value; }); }
  snapshot();

  function valid($i) {
    var v = parseFloat(latin($i.val())), min = parseFloat($i.data('min')), max = parseFloat($i.data('max')), ok = !isNaN(v) && (isNaN(min) || v >= min) && (isNaN(max) || v <= max);
    $i.toggleClass('is-invalid', !ok); return ok;
  }
  function weightsOk() {
    var sum = 0; $f.find('[data-weight]').each(function () { sum += +this.value; });
    $('[data-weight-sum]').toggleClass('bad', sum !== 100).find('b').text(Admin.toFa(sum)); return sum === 100;
  }
  function dirty() { var d = false; $f.find('input').each(function () { if (initial[this.name] !== this.value) d = true; }); return d; }
  function refresh() { var ok = weightsOk(); $f.find('[data-num]').each(function () { if (!valid($(this))) ok = false; }); $bar.prop('hidden', !dirty()); $('[data-save]').prop('disabled', !ok); }

  $f.on('input', '[data-num]', function () {
    var $i = $(this), raw = latin($i.val()).replace(/[^0-9.]/g, '');
    $i.val($i.is('[data-money]') && raw ? Admin.money(raw) : Admin.toFa(raw)); refresh();
  });
  $f.on('input', '[data-weight]', function () { $(this).siblings('output').text(Admin.toFa(this.value)); refresh(); });

  $f.on('click', '[data-reset]', function () {
    $f.find('input').each(function () { this.value = initial[this.name]; }); $f.find('[data-weight]').each(function () { $(this).siblings('output').text(Admin.toFa(this.value)); }); refresh();
  });
  $f.on('submit', function (e) { e.preventDefault(); refresh(); if ($('[data-save]').prop('disabled')) return; snapshot(); $bar.prop('hidden', true); Admin.toast('تنظیمات ذخیره شد', 'success'); });

  /* ناوبری بخش‌ها: اسکرول نرم */
  $('.set-nav a').on('click', function (e) { var t = document.querySelector($(this).attr('href')); if (!t) return; e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); });
  refresh();
})(jQuery);
