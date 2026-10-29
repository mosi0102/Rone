/* ==========================================================================
   requests.js — تعلیق درخواست خرید کاربر (نمایشی؛ بک‌اندکار درخواست واقعی بفرستد)
   ========================================================================== */
(function ($) {
  'use strict';
  var $root = $('[data-filterable]');
  $(document).on('click', '.js-suspend', function () {
    var $b = $(this), $row = $b.closest('tr');
    Admin.askReason('suspendModal', $b.data('title')).then(function () {
      if ($row.length) {
        $row.attr('data-status', 'suspended').data('status', 'suspended');
        $row.find('[data-status-cell]').html(Admin.statusHtml('suspended'));
        $row.addClass('row-flash'); setTimeout(function () { $row.removeClass('row-flash'); }, 1200);
        Admin.refreshFilterable($root);
      } else {
        $('[data-d="status"]').html(Admin.statusHtml('suspended')); $b.remove();
      }
      Admin.toast('درخواست تعلیق شد', 'info');
    });
  });
})(jQuery);
