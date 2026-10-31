/* search-open.js — لمس کادر جست‌وجوی صفحه‌ی اول، بلافاصله صفحه‌ی جست‌وجو را باز می‌کند
   (بدون تایپ و اینتر). کادر روی صفحه‌ی اول فقط‌خواندنی است تا کیبورد دوبار باز/بسته نشود. */
(function ($) {
  'use strict';
  var $in = $('.sc-input[data-open-search]'); if (!$in.length) return;
  $in.attr({ readonly: 'readonly', inputmode: 'none' });
  function open() {
    var u = new URL($in.data('open-search'), location.origin), c = $('#cityInput').val();
    if (c) u.searchParams.set('city', c);
    location.href = u.toString();
  }
  $in.on('click focus', function (e) { e.preventDefault(); open(); });
  $in.closest('form').on('submit', function (e) { if (!$.trim($in.val())) { e.preventDefault(); open(); } });
})(jQuery);
