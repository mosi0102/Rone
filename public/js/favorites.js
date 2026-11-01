/* favorites.js — با برداشتن نشان، کارت از فهرست حذف می‌شود (با امکان بازگردانی تا خروج از صفحه) */
(function ($) {
  'use strict';
  var $grid = $('#favGrid'); if (!$grid.length) return;
  function sync() {
    var n = $grid.children().not('.is-leaving').length;
    $('#favCount').text(String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }));
    $('#favEmpty').toggleClass('d-none', n > 0);
  }
  $(document).on('app:favorite', function (e, id, on) {
    var $c = $grid.find('.lc-fav[data-id="' + id + '"]').closest('.listing-card');
    $c.toggleClass('is-leaving', !on).css({ transition: 'opacity .25s, transform .25s', opacity: on ? 1 : .35, transform: on ? '' : 'scale(.97)' });
    sync();
  });
})(jQuery);
