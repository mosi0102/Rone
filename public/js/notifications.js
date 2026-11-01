/* notifications.js — فیلتر «خوانده‌نشده» و «خواندن همه» (نمایشی؛ بک‌اند به API وصل کند) */
(function ($) {
  'use strict';
  var $l = $('#ntList'); if (!$l.length) return;
  var filter = 'all';
  function fa(n) { return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }); }
  function sync() {
    var u = $l.find('.is-unread').length; $('#ntCount').text(fa(u)); $('#ntReadAll').prop('disabled', !u);
    var $rows = $l.find('.nt').each(function () { $(this).toggleClass('d-none', filter === 'unread' && !$(this).hasClass('is-unread')); });
    $('#ntEmpty').toggleClass('d-none', $rows.not('.d-none').length > 0);
  }
  $(document).on('click', '.nt-bar [data-f]', function () { filter = $(this).data('f'); $('.nt-bar .chip').removeClass('active'); $(this).addClass('active'); sync(); });
  $('#ntReadAll').on('click', function () { $l.find('.is-unread').removeClass('is-unread'); sync(); });
  $l.on('click', '.nt', function () { $(this).removeClass('is-unread'); });   /* خواندن هنگام باز کردن */
})(jQuery);
