/* ==========================================================================
   catalog.js — داده‌های پایه: تب‌ها، جستجو، فعال/غیرفعال، افزودن، ویرایش، حذف
   (نمایشی؛ بک‌اندکار درخواست واقعی بفرستد. حذف فقط برای مواردی که آگهی ندارند مجاز است.)
   ========================================================================== */
(function ($) {
  'use strict';
  var $c = $('[data-catalog]'); if (!$c.length) return;
  var tab = $c.find('[data-tab]').first().data('tab'), $editing = null;
  var modalEl = document.getElementById('itemModal'), modal = bootstrap.Modal.getOrCreateInstance(modalEl), $m = $(modalEl);
  var $name = $('#itemName'), $parent = $('#itemParent'), $save = $m.find('[data-m="save"]');

  function $pane() { return $c.find('[data-pane="' + tab + '"]'); }
  function filter() {
    var q = $.trim($c.find('[data-q]').val()).toLowerCase(), n = 0;
    $pane().find('[data-item]').each(function () { var ok = !q || String($(this).data('search')).toLowerCase().indexOf(q) > -1; $(this).prop('hidden', !ok); if (ok) n++; });
    $pane().find('[data-none]').prop('hidden', n > 0);
  }
  function count() { $c.find('[data-tab]').each(function () { $(this).find('.n').text(Admin.toFa($c.find('[data-pane="' + $(this).data('tab') + '"] [data-item]').length)); }); }

  $c.on('click', '[data-tab]', function () {
    tab = $(this).data('tab'); $c.find('[data-tab]').removeClass('active').attr('aria-selected', 'false'); $(this).addClass('active').attr('aria-selected', 'true');
    $c.find('[data-pane]').prop('hidden', true); $pane().prop('hidden', false); filter();
  });
  $c.on('input', '[data-q]', filter);

  $c.on('change', '[data-toggle]', function () { Admin.toast(this.checked ? 'مورد فعال شد' : 'مورد غیرفعال شد؛ در فرم‌ها نمایش داده نمی‌شود', this.checked ? 'success' : 'info'); });

  function openModal($row) {
    $editing = $row; var label = $pane().data('parent-label');
    $m.find('[data-m="title"]').text($row ? 'ویرایش مورد' : 'افزودن مورد'); $m.find('[data-m="sub"]').text($c.find('[data-tab="' + tab + '"]').text().replace(/[۰-۹0-9]+/g, '').trim());
    $m.find('[data-m="parentLabel"]').text(label);
    $name.val($row ? $row.find('[data-name]').text() : ''); $parent.val($row ? $row.find('[data-parent]').text() : ''); check(); modal.show();
  }
  function check() { $save.prop('disabled', $.trim($name.val()).length < 1); }
  $m.on('input', 'input', check); $m.on('shown.bs.modal', function () { $name.trigger('focus'); });
  $(document).on('click', '[data-add]', function () { openModal(null); });
  $c.on('click', '[data-edit]', function () { openModal($(this).closest('[data-item]')); });

  $save.on('click', function () {
    var n = $.trim($name.val()), p = $.trim($parent.val());
    if ($editing) { $editing.find('[data-name]').text(n); $editing.find('[data-parent]').text(p); $editing.data('search', n + ' ' + p).attr('data-search', n + ' ' + p); Admin.toast('تغییرات ذخیره شد', 'success'); }
    else {
      var $r = $pane().find('[data-item]').first().clone(); $r.attr({ 'data-ads': 0, 'data-search': n + ' ' + p }).data('ads', 0);
      $r.find('[data-name]').text(n); $r.find('[data-parent]').text(p); $r.find('[data-label="آگهی‌ها"], td:nth-child(3)').text('۰'); $r.find('[data-toggle]').prop('checked', true).attr('aria-label', 'فعال بودن ' + n);
      $pane().find('tbody').prepend($r.addClass('row-flash')); count(); Admin.toast('مورد جدید افزوده شد', 'success');
    }
    modal.hide(); filter();
  });

  $c.on('click', '[data-del]', function () {
    var $r = $(this).closest('[data-item]'), n = $r.find('[data-name]').text();
    if (Number($r.attr('data-ads')) > 0) { Admin.toast('«' + n + '» در آگهی‌ها استفاده شده؛ به‌جای حذف، غیرفعالش کنید', 'info'); return; }
    Admin.confirm({ title: 'حذف «' + n + '»؟', text: 'این کار قابل بازگشت نیست.', ok: 'حذف', danger: true }).then(function (yes) { if (!yes) return; $r.remove(); count(); filter(); Admin.toast('مورد حذف شد', 'info'); });
  });
})(jQuery);
