/* wallet.js — تب‌ها، قالب‌بندی مبلغ، اعتبارسنجی و فیلتر تراکنش */
(function ($) {
  'use strict';
  var $w = $('.wl'); if (!$w.length) return;
  var minC = +$w.data('min-charge'), minW = +$w.data('min-withdraw'), bal = +$w.data('balance');

  function num(s) { return +String(s).replace(/[۰-۹]/g, function (d) { return d.charCodeAt(0) - 1776; }).replace(/\D/g, '') || 0; }
  function fa(n) { return n ? String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '٬').replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }) : ''; }

  /* تب‌ها */
  $w.on('click', '.wl-tabs [data-tab]', function () {
    var t = $(this).data('tab');
    $('.wl-tabs button').removeClass('on').attr('aria-selected', false); $(this).addClass('on').attr('aria-selected', true);
    $w.find('[data-pane]').prop('hidden', true).filter('[data-pane="' + t + '"]').prop('hidden', false);
    history.replaceState(null, '', '?tab=' + t);
  });

  /* مبلغ */
  $w.on('input', '.wl-input', function () { this.value = fa(num(this.value)); $(this).removeClass('is-invalid').closest('form').find('.wl-err').prop('hidden', true); });
  $w.on('click', '[data-quick]', function () { $('#chAmount').val(fa($(this).data('quick'))).trigger('input'); });
  $w.on('click', '[data-all]', function () { $('#wdAmount').val(fa(bal)).trigger('input'); });

  $w.on('submit', 'form[data-kind]', function (e) {
    var $f = $(this), v = num($f.find('.wl-input').val()), ok = $f.data('kind') === 'charge' ? v >= minC : (v >= minW && v <= bal);
    if (!ok) { e.preventDefault(); $f.find('.wl-input').addClass('is-invalid').trigger('focus'); $f.find('.wl-err').prop('hidden', false); return; }
    $f.find('.wl-input').val(v);                      /* عدد لاتین برای سرور */
    $f.find('[type=submit]').prop('disabled', true).text('در حال ثبت…');
  });

  /* فیلتر تراکنش */
  $w.on('click', '.wl-filter [data-f]', function () {
    var f = $(this).data('f'); $('.wl-filter .chip').removeClass('active'); $(this).addClass('active');
    var $r = $('.list-s .li').each(function () { $(this).toggleClass('d-none', f !== 'all' && $(this).data('dir') !== f); });
    $('#wlEmpty').toggleClass('d-none', $r.not('.d-none').length > 0);
  });
})(jQuery);
