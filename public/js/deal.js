/* deal.js — بیعانه امانی و معاملات (jQuery)
   رویدادها برای بک‌اند:
     app:deposit-pay({listing, method, total})   ← پرداخت؛ در موفقیت به data-next هدایت کنید
     app:deal-decide({deal, result:'completed'|'dispute', reason, note})
     app:deal-remind(dealId) · app:deal-report(dealId, text) · app:deal-statement(dealId, text) · app:receipt(dealId)
*/
(function ($) {
  'use strict';

  /* ---------- پرداخت بیعانه ---------- */
  var $dep = $('#depositPage');
  if ($dep.length) {
    $('#agreeDeposit').on('change', function () { $('.tr-ck').removeClass('is-invalid'); });
    $('#payDeposit').on('click', function () {
      var $b = $(this);
      if (!$('#agreeDeposit').is(':checked')) {
        $('.tr-ck').addClass('is-invalid'); App.toast('برای ادامه، شرایط بیعانهٔ امانی را بپذیرید', 'error');
        var el = document.querySelector('.terms-row'); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' }); return;
      }
      App.loading($b);
      var info = { listing: $b.data('listing'), method: $('input[name=method]:checked').val(), total: $b.data('total') };
      $(document).trigger('app:deposit-pay', [info]); // بک‌اند: کسر از کیف پول یا انتقال به درگاه
      setTimeout(function () { App.toast('بیعانه با موفقیت در امانت قرار گرفت', 'success', 1800); setTimeout(function () { location.href = $dep.data('next'); }, 900); }, 1100);
    });
  }

  /* ---------- چاپ/ذخیره رسید ---------- */
  $(document).on('click', '[data-print]', function () { $(document).trigger('app:receipt'); window.print(); });

  /* ---------- لیست معاملات: فیلتر ---------- */
  $('#dealTabs').on('click', 'button', function () {
    $(this).addClass('on').siblings().removeClass('on'); var f = $(this).data('status'), n = 0;
    $('#dealList .deal-row').each(function () {
      var s = $(this).data('status'), ok = f === 'all' || (f === 'held' && s === 'held') || (f === 'done' && s !== 'held'); $(this).toggle(ok); if (ok) n++;
    });
    $('#dealEmpty').toggleClass('d-none', n > 0);
  });

  /* ---------- تعیین وضعیت ---------- */
  var $dec = $('#decidePage');
  if ($dec.length) {
    var $btn = $('#submitDecision');
    function result() { return $('input[name=result]:checked').val(); }
    function refresh() {
      var r = result(), ok = false;
      $('#disputeBox').toggleClass('d-none', r !== 'dispute');
      if (r === 'completed') { ok = true; $btn.text('تأیید انجام معامله'); }
      else if (r === 'dispute') { ok = !!$('input[name=reason]:checked').length; $btn.text(ok ? 'ثبت اختلاف' : 'دلیل را انتخاب کنید'); }
      else $btn.text('یک گزینه را انتخاب کنید');
      $btn.prop('disabled', !ok);
    }
    $('input[name=result], input[name=reason]').on('change', refresh); refresh();
    $('#proofFile').on('change', function () { $('#proofName').text(this.files[0] ? this.files[0].name : 'افزودن عکس یا مدرک (اختیاری)'); });
    $btn.on('click', function () {
      var r = result();
      $('#dcText').text(r === 'completed'
        ? 'با تأیید، بیعانه به نمایشگاه منتقل می‌شود و این کار برگشت‌پذیر نیست. آیا معامله واقعاً انجام شده است؟'
        : 'اختلاف شما ثبت می‌شود و پشتیبانی آن را بررسی می‌کند. تا اعلام نتیجه، بیعانه نزد پلتفرم می‌ماند.');
      App.sheet('decideConfirm');
    });
    $('#dcYes').on('click', function () {
      var $y = $(this), r = result(); App.loading($y);
      $(document).trigger('app:deal-decide', [{ deal: $dec.data('deal'), result: r, reason: $('input[name=reason]:checked').val() || null, note: $('#reasonNote').val() || null }]);
      setTimeout(function () { App.toast(r === 'completed' ? 'معامله ثبت شد؛ بیعانه به نمایشگاه منتقل می‌شود' : 'اختلاف شما ثبت شد', 'success', 1800); setTimeout(function () { location.href = r === 'completed' ? $dec.data('ok') : $dec.data('dispute'); }, 900); }, 900);
    });
  }

  /* ---------- نمایشگاه: یادآوری / گزارش / توضیح ---------- */
  $('#remindBuyer').on('click', function () {
    var $b = $(this); App.loading($b); $(document).trigger('app:deal-remind', [$b.data('deal')]);
    setTimeout(function () { App.loading($b, false); $b.prop('disabled', true).text('یادآوری ارسال شد'); App.toast('پیام یادآوری برای خریدار ارسال شد', 'success'); }, 800);
  });
  $('#sendReport').on('click', function () {
    var t = $.trim($('#reportText').val()); if (!t) { App.toast('متن مشکل را بنویسید', 'error'); return; }
    $(document).trigger('app:deal-report', [$(this).data('deal'), t]); App.sheet('reportSheet', 'hide'); App.toast('گزارش برای پشتیبانی ارسال شد', 'success');
  });
  $('#sendStatement').on('click', function () {
    var t = $.trim($('#stmtText').val()); if (!t) { App.toast('توضیح را بنویسید', 'error'); return; }
    $(document).trigger('app:deal-statement', [$(this).data('deal'), t]); App.sheet('statementSheet', 'hide'); App.toast('توضیح شما برای بررسی ثبت شد', 'success');
  });
})(jQuery);
