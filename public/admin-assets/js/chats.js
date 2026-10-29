/* ==========================================================================
   chats.js — نمای فقط‌خواندنی گفتگوها: انتخاب گفتگو، پرچم‌گذاری، «بررسی شد»
   بک‌اندکار: پیام‌ها را با درخواست جداگانه (lazy) بگیرد و وضعیت را سمت سرور ذخیره کند.
   ========================================================================== */
(function ($) {
  'use strict';
  var $shell = $('[data-chats]'), $root = $shell, cur = null;
  if (!$shell.length) return;

  function toggle($t) { var st = String($t.attr('data-status')); $t.find('[data-show]').each(function () { $(this).prop('hidden', (' ' + $(this).attr('data-show') + ' ').indexOf(' ' + st + ' ') < 0); }); }
  function open(id) {
    cur = String(id);
    $shell.addClass('show-thread');
    $('[data-placeholder]').prop('hidden', true);
    $('[data-thread]').prop('hidden', true);
    var $t = $('[data-thread="' + cur + '"]').prop('hidden', false); toggle($t);
    $('.chat-item').removeClass('active'); $('[data-open="' + cur + '"]').addClass('active');
    var el = $t.find('.msgs')[0]; if (el) el.scrollTop = el.scrollHeight;
  }
  function setStatus(st, note) {
    var $t = $('[data-thread="' + cur + '"]'), $row = $('[data-chat="' + cur + '"]'), badge = st === 'normal' ? '' : Admin.statusHtml(st);
    $t.attr('data-status', st); $t.find('[data-d="status"]').html(badge);
    $row.attr('data-status', st).data('status', st); $row.find('[data-status-cell]').html(badge);
    if (note) $t.find('[data-note]').text(note);
    toggle($t); Admin.refreshFilterable($root);
  }

  $shell.on('click', '[data-open]', function () { open($(this).data('open')); });
  $shell.on('click', '[data-back]', function () { $shell.removeClass('show-thread'); });
  $(document).on('click', '[data-reason-modal="flagChat"]', function () {
    var $b = $(this); Admin.askReason('flagChat', $b.data('title')).then(function (reason) { setStatus('flagged', reason); Admin.toast('گفتگو پرچم‌گذاری شد', 'info'); });
  });
  $shell.on('admin:confirmed', '[data-set="reviewed"]', function () { setStatus('reviewed'); Admin.toast('گفتگو بررسی‌شده شد', 'success'); });

  /* گفتگوی اول پرچم‌شده به‌صورت پیش‌فرض باز شود (فقط دسکتاپ) */
  if (window.matchMedia('(min-width: 992px)').matches) { var $f = $('.chat-list [data-chat]').first(); if ($f.length) { open($f.data('chat')); $shell.removeClass('show-thread'); } }
})(jQuery);
