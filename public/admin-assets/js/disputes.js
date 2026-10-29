/* ==========================================================================
   disputes.js — تصمیم نهایی اختلاف (نمایشی؛ بک‌اندکار درخواست واقعی بفرستد)
   ========================================================================== */
(function ($) {
  'use strict';
  var $box = $('[data-dispute]'); if (!$box.length) return;
  var total = Number($box.data('amount')) || 0;
  var $btn = $('#resolveBtn'), $note = $('#resNote'), $split = $('[data-split]'), $range = $('#splitRange');
  var T = { refund: 'بیعانه به خریدار برگشت', release: 'بیعانه به نمایشگاه‌دار آزاد شد', split: 'بیعانه بین طرفین تقسیم شد' };

  function choice() { return $box.find('input[name="res"]:checked').val(); }
  function check() { $btn.prop('disabled', !choice() || $.trim($note.val()).length < 3); }
  function amounts() {
    var p = Number($range.val()), b = Math.round(total * p / 100);
    $('[data-split-v]').text(Admin.toFa(p)); $('[data-buyer-amt]').text(Admin.toFa(Admin.money(b))); $('[data-dealer-amt]').text(Admin.toFa(Admin.money(total - b)));
  }

  $box.on('change', 'input[name="res"]', function () { $split.prop('hidden', choice() !== 'split'); amounts(); check(); });
  $range.on('input', amounts); $note.on('input', check);

  $btn.on('click', function () {
    var c = choice();
    Admin.confirm({ title: T[c] + '؟', text: 'این تصمیم نهایی است و قابل بازگشت نیست.', ok: 'ثبت تصمیم', danger: c !== 'refund' && c !== 'release' }).then(function (yes) {
      if (!yes) return;
      $('[data-decide]').prop('hidden', true); $('[data-res-title]').text(T[c]); $('[data-resolved]').prop('hidden', false);
      $('[data-d="status"]').html(Admin.statusHtml('resolved'));
      Admin.toast('تصمیم ثبت شد', 'success');
    });
  });
})(jQuery);
