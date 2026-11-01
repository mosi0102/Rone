/* search-open.js — لمس کادر جست‌وجوی صفحه‌ی اول، لایه‌ی جست‌وجو را همان‌جا باز می‌کند (بدون رفتن به آدرس دیگر).
   کادر فقط‌خواندنی است تا کیبورد دوبار باز/بسته نشود. اگر لایه نبود، به صفحه‌ی جست‌وجو می‌رود (جایگزین). */
(function ($) {
  'use strict';
  var $in = $('.sc-input[data-open-search]'); if (!$in.length) return;
  $in.attr({ readonly: 'readonly', inputmode: 'none' });
  function open(q) {
    if (window.SearchOverlay) return SearchOverlay.open(q);
    var u = new URL($in.data('open-search'), location.origin), c = $('#cityInput').val();
    if (c) u.searchParams.set('city', c);
    location.href = u.toString();
  }
  $in.on('click focus', function (e) { e.preventDefault(); open(); });
  $in.closest('form').on('submit', function (e) { e.preventDefault(); open($.trim($in.val())); });
})(jQuery);
