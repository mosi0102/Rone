/* ==========================================================================
   people.js — نمایشگاه‌داران و کاربران: تغییر وضعیت (تأیید، رد، تعلیق، مسدودسازی…)
   ----------------------------------------------------------------------
   - دکمه‌ی دارای data-set="وضعیت جدید" + data-confirm: پس از تأیید، وضعیت عوض می‌شود.
   - دکمه‌ی دارای data-set + data-reason-modal="id": ابتدا دلیل گرفته می‌شود.
   - هر عنصر دارای data-show="وضعیت۱ وضعیت۲" فقط در آن وضعیت‌ها دیده می‌شود.
   - بک‌اندکار: به‌جای تغییر DOM، درخواست واقعی بفرستد و پاسخ را اعمال کند.
   ========================================================================== */
(function ($) {
  'use strict';
  var $root = $('[data-filterable]');

  function scopeOf(el) { var $r = $(el).closest('[data-row]'); return $r.length ? $r : $('[data-entity]').first(); }
  function toggle($scope) {
    var st = String($scope.attr('data-status'));
    $scope.find('[data-show]').each(function () { $(this).prop('hidden', (' ' + $(this).attr('data-show') + ' ').indexOf(' ' + st + ' ') < 0); });
  }
  function apply($scope, st, reason, toast) {
    $scope.attr('data-status', st).data('status', st);
    $scope.find('[data-status-cell]').html(Admin.statusHtml(st));
    if ($scope.is('[data-entity]')) $('[data-d="status"]').html(Admin.statusHtml(st));
    $scope.find('[data-reason-note]').text(reason ? (st === 'paid' ? 'کد پیگیری: ' + reason : reason) : '').prop('hidden', !reason);
    toggle($scope);
    if ($scope.is('tr')) { $scope.addClass('row-flash'); setTimeout(function () { $scope.removeClass('row-flash'); }, 1200); Admin.refreshFilterable($root); }
    Admin.toast(toast || 'انجام شد', st === 'rejected' || st === 'banned' || st === 'suspended' ? 'info' : 'success');
  }

  $(document).on('admin:confirmed', '[data-set]', function () { var $b = $(this); apply(scopeOf(this), String($b.data('set')), '', $b.data('done')); });
  $(document).on('click', '[data-reason-modal]', function () {
    var $b = $(this), $s = scopeOf(this);
    Admin.askReason(String($b.data('reason-modal')), $b.data('title')).then(function (reason) { apply($s, String($b.data('set')), reason, $b.data('done')); });
  });

  $('[data-row], [data-entity]').each(function () { toggle($(this)); });
})(jQuery);

/* رفع محرومیت از مزایده (صفحه‌ی جزئیات کاربر) */
jQuery(document).on('admin:confirmed', '#unbanAuc', function () { jQuery(this).closest('section').find('.note-box.warn').remove(); jQuery(this).remove(); });
