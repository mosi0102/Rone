/* ==========================================================================
   auctions.js — لیست مزایده‌ها: لغو با دلیل (نمایشی؛ بک‌اندکار درخواست واقعی بفرستد)
   ========================================================================== */
(function ($) {
  'use strict';
  var $root = $('[data-filterable]');

  $root.on('click', '.js-cancel', function () {
    var $row = $(this).closest('[data-row]');
    Admin.askReason('cancelModal', $(this).data('title')).then(function () {
      $row.attr('data-status', 'cancelled').data('status', 'cancelled');
      $row.find('[data-status-cell]').html(Admin.statusHtml('cancelled'));
      $row.find('.timer').closest('td').html('<small class="text-muted d-block">لغوشده</small>—');
      $row.addClass('row-flash'); setTimeout(function () { $row.removeClass('row-flash'); }, 1200);
      Admin.refreshFilterable($root);
      Admin.toast('مزایده لغو شد و ورودی‌ها برگردانده می‌شود', 'info');
    });
  });
})(jQuery);
