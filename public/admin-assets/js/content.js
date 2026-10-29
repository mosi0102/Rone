/* ==========================================================================
   content.js — ویرایش صفحه‌های ثابت (نمایشی؛ بک‌اندکار به‌جای تغییر DOM، ذخیره‌ی واقعی انجام دهد)
   ========================================================================== */
(function ($) {
  'use strict';
  var $w = $('[data-content]'); if (!$w.length) return;
  var $row = null, oc = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('pageEditor'));
  var $t = $('#pgTitle'), $s = $('#pgSlug'), $b = $('#pgBody'), $p = $('#pgPublish'), $save = $('#pgSave');
  function check() { $save.prop('disabled', $.trim($t.val()).length < 2 || !/^[a-z0-9-]+$/.test($.trim($s.val()))); $s.toggleClass('is-invalid', $s.val() !== '' && !/^[a-z0-9-]+$/.test($s.val())); }

  $w.on('click', '[data-edit]', function () {
    $row = $(this).closest('[data-row]');
    $t.val($row.attr('data-title')); $s.val($row.attr('data-slug')); $b.val($row.attr('data-body')); $p.prop('checked', $row.attr('data-status') === 'active'); check(); oc.show();
  });
  $('#pageEditor').on('input', 'input, textarea', check);
  $save.on('click', function () {
    var st = $p.prop('checked') ? 'active' : 'pending';
    $row.attr({ 'data-title': $.trim($t.val()), 'data-slug': $.trim($s.val()), 'data-body': $b.val(), 'data-status': st });
    $row.find('[data-t]').text($.trim($t.val())); $row.find('.tnum').text('/' + $.trim($s.val()));
    $row.find('[data-status-cell]').html(st === 'active' ? '<span class="badge-s ok">منتشرشده</span>' : '<span class="badge-s warn">پیش‌نویس</span>');
    $row.find('td:nth-child(3)').text('هم‌اکنون'); $row.addClass('row-flash'); setTimeout(function () { $row.removeClass('row-flash'); }, 1200);
    oc.hide(); Admin.toast('صفحه ذخیره شد', 'success');
  });
})(jQuery);
