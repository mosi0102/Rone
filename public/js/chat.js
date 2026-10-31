/* ==========================================================================
   chat.js — گفتگوی خریدار ↔ نمایشگاه‌دار
   • بررسی سریع سمت کلاینت (شماره/لینک/آی‌دی/پرداخت بیرونی) — مرجع اصلی **سرور** است (422 + {error, hit})
   • ارسال، پیام‌های آماده، گزارش پیام، اسکرول خودکار
   بک‌اند: POST data-send-url {text} → 201 {message} | 422 {error,hit} ؛ POST data-report-url {message_id, reason}
   ========================================================================== */
(function ($) {
  'use strict';
  var $c = $('#chat'); if (!$c.length) return;
  var unlocked = +$c.data('unlocked') === 1, role = $c.data('role'), strikes = 0;
  var $msgs = $('#chMsgs'), $text = $('#chText'), $send = $('#chSend'), $form = $('#chForm'), $warn = $('#chWarn');

  /* ---------- بررسی محتوای ممنوع (هم‌ارز routes/chat.php) ---------- */
  var DIG = { '۰': 0, '۱': 1, '۲': 2, '۳': 3, '۴': 4, '۵': 5, '۶': 6, '۷': 7, '۸': 8, '۹': 9, '٠': 0, '١': 1, '٢': 2, '٣': 3, '٤': 4, '٥': 5, '٦': 6, '٧': 7, '٨': 8, '٩': 9 };
  var WORDS = { 'صفر': 0, 'یک': 1, 'دو': 2, 'سه': 3, 'چهار': 4, 'پنج': 5, 'شش': 6, 'هفت': 7, 'هشت': 8, 'نه': 9 };
  var REASON = {
    phone: 'ارسال شماره‌ی تماس پیش از پرداخت بیعانه ممنوع است.',
    link: 'ارسال لینک، ایمیل یا آی‌دی پیام‌رسان و شبکه‌های اجتماعی مجاز نیست.',
    offsite: 'پیشنهاد پرداخت یا معامله‌ی خارج از سامانه ممنوع است؛ بیعانه فقط از طریق سامانه امن می‌ماند.'
  };
  function norm(s) { return String(s).replace(/[۰-۹٠-٩]/g, function (d) { return DIG[d]; }).replace(/‌/g, ' ').replace(/[‎‏]/g, '').toLowerCase(); }
  function check(raw) {
    var t = norm(raw);
    if (/(https?:\/\/|www\.|t\.me\/|\b[a-z0-9-]{2,}\.(?:com|ir|net|org|me|io|co|app|ly)\b|[\w.+-]+@[\w-]+\.\w{2,})/.test(t) || /(^|[\s:؛،,])@[a-z0-9_.]{3,}/.test(t) ||
        /(telegram|whatsapp|instagram|eitaa|rubika|تلگرام|واتس\s?اپ|واتساپ|اینستا|ایتا|روبیکا|دایرکت)/.test(t)) return 'link';
    if (/(کارت\s*به\s*کارت|شماره\s*کارت|شماره\s*حساب|شبا|واریز\s*(مستقیم|به\s*حساب)|خارج\s*از\s*(سایت|سامانه)|بدون\s*(سایت|سامانه)|بیرون\s*از\s*(سایت|سامانه))/.test(t)) return 'offsite';
    if (!unlocked) {
      var t2 = t.split(/\s+/).map(function (w) { return WORDS.hasOwnProperty(w) ? ' ' + WORDS[w] + ' ' : w; }).join(' ');
      var runs = t2.match(/(?:\+|00)?\d(?:[\s\-._()\/،,]*\d){6,}/g) || [];
      for (var i = 0; i < runs.length; i++) {
        var d = runs[i].replace(/\D/g, '');
        if (/^(?:00989|989|09)\d{9}$/.test(d) || /^0[1-8]\d{9}$/.test(d) || /^(?:0098|98)[1-8]\d{9}$/.test(d)) return 'phone';
      }
    }
    return null;
  }

  /* ---------- نمایش ---------- */
  function toBottom() { var el = document.scrollingElement || document.documentElement; el.scrollTo ? el.scrollTo({ top: el.scrollHeight }) : (el.scrollTop = el.scrollHeight); }
  function addMsg(m) {
    var $m = $('<div class="ch-msg mine">').attr('data-mid', m.id).append($('<div class="ch-bubble">').text(m.text))
      .append($('<div class="ch-meta">').append($('<time data-jfmt="time">').attr('data-jdate', m.at)));
    $msgs.append($m); if (window.App && App.hydrate) App.hydrate($m); toBottom();
  }
  function warn(hit, srv) {
    strikes++;
    $('#chWarnT').text('پیام ارسال نشد'); $('#chWarnS').text(srv || REASON[hit] || 'این پیام با قوانین سامانه سازگار نیست.');
    if (strikes >= 3) $('#chWarnS').append(document.createTextNode(' در صورت تکرار، امکان گفتگوی شما محدود می‌شود.'));
    $warn.prop('hidden', false); $form.removeClass('shake'); void $form[0].offsetWidth; $form.addClass('shake');
    if (navigator.vibrate) navigator.vibrate(60);
  }
  function csrf() { return $('meta[name="csrf-token"]').attr('content') || ''; }

  function send(text) {
    text = $.trim(text); if (!text) return;
    var hit = check(text); if (hit) { warn(hit); return; }        // مسدود محلی: متن در کادر می‌ماند تا ویرایش شود
    $warn.prop('hidden', true); $send.prop('disabled', true);
    $.ajax({ url: $c.data('send-url'), method: 'POST', data: { text: text }, dataType: 'json', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .done(function (r) { addMsg(r.message); $text.val('').trigger('input'); })
      .fail(function (x) { var j = x.responseJSON || {}; if (x.status === 422) warn(j.hit, j.error); else App.toast('ارسال ناموفق بود؛ دوباره تلاش کنید', 'error'); })
      .always(function () { $send.prop('disabled', false); });
  }
  $form.on('submit', function (e) { e.preventDefault(); send($text.val()); });
  $text.on('input', function () { this.style.height = 'auto'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'; if (!$.trim(this.value)) $warn.prop('hidden', true); })
    .on('keydown', function (e) { if (e.key === 'Enter' && !e.shiftKey && window.matchMedia('(min-width: 992px)').matches) { e.preventDefault(); $form.trigger('submit'); } });
  $('#chQuick').on('click', '[data-quick]', function () { send($(this).text()); });

  /* ---------- گزارش ---------- */
  var repId = null, $go = $('#repGo');
  $msgs.on('click', '[data-report]', function () { repId = $(this).data('report'); $('#repSheet [name=reason]').prop('checked', false); $go.prop('disabled', true); App.sheet('repSheet'); });
  $('#repSheet').on('change', '[name=reason]', function () { $go.prop('disabled', false); });
  $go.on('click', function () {
    var reason = $('#repSheet [name=reason]:checked').val(); if (!reason || !repId) return; App.loading($go);
    $.ajax({ url: $c.data('report-url'), method: 'POST', data: { message_id: repId, reason: reason }, dataType: 'json', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } })
      .done(function () { $msgs.find('[data-report="' + repId + '"]').text('گزارش شد').prop('disabled', true); App.sheet('repSheet', 'hide'); App.toast('گزارش ثبت شد؛ مدیریت بررسی می‌کند', 'success'); })
      .fail(function () { App.toast('ثبت گزارش ناموفق بود', 'error'); }).always(function () { App.loading($go, false); });
  });

  toBottom();
})(jQuery);
