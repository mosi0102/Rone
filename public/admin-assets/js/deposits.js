/* ==========================================================================
   deposits.js — آزادسازی/برگشت بیعانه (لیست و جزئیات). نمایشی؛ بک‌اندکار درخواست واقعی بفرستد.
   ========================================================================== */
(function ($) {
  'use strict';
  var $root = $('[data-filterable]'), $hero = $('[data-deposit]');
  var evText = { released: 'بیعانه به کیف پول نمایشگاه‌دار منتقل شد', refunded: 'بیعانه به کیف پول خریدار برگشت' };

  function apply($scope, st) {
    if ($scope.is('tr')) {
      $scope.attr('data-status', st).data('status', st);
      $scope.find('[data-status-cell]').html(Admin.statusHtml(st));
      $scope.find('.timer').closest('small').remove();
      $scope.addClass('row-flash'); setTimeout(function () { $scope.removeClass('row-flash'); }, 1200);
      Admin.refreshFilterable($root);
    } else {
      $hero.attr('data-status', st); $('[data-d="status"]').html(Admin.statusHtml(st));
      $('.act-held').remove(); $('[data-hold-box]').remove();
      var $li = $('<li class="' + (st === 'released' ? 'ok' : 'warn') + '"><b></b><small>اکنون</small></li>'); $li.find('b').text(evText[st]);
      $('#depEvents').prepend($li);
    }
  }

  $(document).on('admin:confirmed', '.js-release', function () {
    var $row = $(this).closest('tr'); apply($row.length ? $row : $('body'), 'released');
    Admin.toast('بیعانه به نمایشگاه‌دار منتقل شد', 'success');
  });

  $(document).on('click', '.js-refund', function () {
    var $b = $(this), $row = $b.closest('tr');
    Admin.askReason('refundModal', $b.data('title')).then(function () {
      apply($row.length ? $row : $('body'), 'refunded');
      Admin.toast('بیعانه به خریدار برگشت', 'info');
    });
  });
})(jQuery);
