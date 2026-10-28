/* ==========================================================================
   auction-show.js — جزئیات مزایده: شبیه‌ساز پیشنهاد زنده، پایان مزایده، لغو، بن
   ----------------------------------------------------------------------
   بک‌اندکار: بخش «شبیه‌ساز» را حذف کند و به‌جای آن از WebSocket/SSE استفاده کند؛
   با هر پیام جدید فقط تابع addBid(user, amount) را صدا بزند.
   ========================================================================== */
(function ($) {
  'use strict';

  var $hero = $('[data-auction]'), $feed = $('#bidFeed');
  var users = $hero.data('users') || [], step = Number($hero.data('step')) || 1, entry = Number($hero.data('entry')) || 0;
  var endsAt = $hero.data('ends') ? new Date($hero.data('ends')).getTime() : 0;
  var top = Number($('[data-top]').data('money')) || Number($hero.data('base')), count = $feed.children().length;
  var people = Number($('[data-kpi="people"] [data-count]').data('count')) || 0;
  var simTimer = null, tickTimer = null, state = $hero.data('status');

  function kpi(name, text) { $('[data-kpi="' + name + '"] [data-count]').text(text); }
  function setStatus(st) { state = st; $hero.attr('data-status', st); $('[data-d="status"]').html(Admin.statusHtml(st)); }
  function setTimeline(states) { $('[data-tline] .tl-step').each(function (i) { this.className = 'tl-step is-' + states[i]; }).each(function (i) { $(this).find('.tl-dot').html(states[i] === 'done' ? '<svg class="icon"><use href="#a-check"/></svg>' : states[i] === 'fail' ? '<svg class="icon"><use href="#a-x"/></svg>' : ''); }); }
  function stopLive() { clearTimeout(simTimer); clearInterval(tickTimer); $('.live-pill').remove(); }

  /* ---------- افزودن پیشنهاد جدید (برای اتصال زنده) ---------- */
  function addBid(user, amount) {
    var delta = amount - top; top = amount; count++;
    var html = '<li class="bid-row is-top is-new"><span class="rank">' + Admin.toFa(count) + '</span><span class="avatar sm"></span><div class="who"><b></b><small class="tnum"></small></div>' +
      '<div class="amt"><b><span>' + Admin.money(amount) + '</span> <small>تومان</small></b><small><span class="up">+' + Admin.money(delta) + '</span> • ' + Admin.jfmt(new Date(), 'time') + '</small></div></li>';
    var $r = $(html); $r.find('.avatar').text(user.name.charAt(0)); $r.find('.who b').text(user.name); $r.find('.who small').text(Admin.toFa(user.phone));
    $feed.find('.is-top').removeClass('is-top'); $feed.prepend($r); $('[data-no-bids]').prop('hidden', true);
    if ($feed.children().length > 80) $feed.children().last().remove();
    $('[data-top]').text(Admin.money(amount)).data('money', amount).closest('.ah-v').removeClass('flash').each(function () { void this.offsetWidth; }).addClass('flash');
    $('[data-leader]').text(user.name); $('[data-ah-label]').text('بالاترین پیشنهاد');
    kpi('bids', Admin.toFa(count));
  }

  /* ---------- شبیه‌ساز (فقط نمایشی) ---------- */
  function simulate() {
    simTimer = setTimeout(function () {
      if (state === 'live' && !document.hidden) {
        addBid(users[Math.floor(Math.random() * users.length)], top + step * (1 + Math.floor(Math.random() * 2)));
        if (Math.random() < .25) { people++; kpi('people', Admin.toFa(people)); kpi('entry', Admin.money(people * entry)); }
      }
      simulate();
    }, 2500 + Math.random() * 4500);
  }
  if ($hero.is('[data-simulate]')) simulate();

  /* ---------- پایان خودکار مزایده‌ی زنده ---------- */
  tickTimer = setInterval(function () {
    if (state !== 'live' || !endsAt || Date.now() < endsAt) return;
    stopLive(); setStatus('awaiting_pay'); setTimeline(['done', 'done', 'done', 'current', 'wait']);
    $('[data-time-label]').text('پایان‌یافته'); $('[data-main-timer]').replaceWith('<div class="ah-t ah-t-sm">پایان یافت</div>');
    Admin.toast('مزایده به پایان رسید؛ برای دیدن برنده و پرداخت، صفحه را تازه کنید', 'info', 5000);
  }, 1000);

  /* ---------- لغو مزایده ---------- */
  $('#cancelBtn').on('click', function () {
    var $b = $(this);
    Admin.askReason('cancelModal', $('.page-head h1').text()).then(function (reason) {
      stopLive(); setStatus('cancelled'); setTimeline(['done', 'wait', 'wait', 'wait', 'wait']);
      $('[data-time-label]').text('لغو شد'); $('[data-main-timer]').replaceWith('<div class="ah-t ah-t-sm">لغوشده</div>');
      $('.dash-aside').prepend('<section class="card-a"><div class="note-box bad"><svg class="icon"><use href="#a-alert"/></svg><div><b>این مزایده لغو شده است</b><p></p></div></div></section>').find('.note-box p').first().text('دلیل: ' + reason);
      $b.remove(); Admin.toast('مزایده لغو شد و ورودی‌ها برگردانده می‌شود', 'info');
    });
  });

  /* ---------- پرداخت برنده: بن و رفع بن ---------- */
  function showPay(name) { $('[data-pay-state]').prop('hidden', true).filter('[data-pay-state="' + name + '"]').prop('hidden', false); }
  $('#banBtn').on('click', function () {
    Admin.askReason('banModal', $('[data-pay-card] .lr-body b').text()).then(function (reason, $m) {
      var days = Number($m.find('#banDays').val()) || 30;
      $('[data-pay-state="banned"] [data-jdate]').text(Admin.jfmt(Date.now() + days * 864e5, 'date'));
      showPay('banned'); setStatus('defaulted'); setTimeline(['done', 'done', 'done', 'fail', 'wait']);
      Admin.toast('برنده برای ' + Admin.toFa(days) + ' روز از مزایده‌ها محروم شد', 'success');
    });
  });
  $('#unbanBtn').on('click', function () {
    Admin.confirm({ title: 'محرومیت برداشته شود؟', text: 'کاربر دوباره می‌تواند در مزایده‌ها شرکت کند.', ok: 'رفع محرومیت' }).then(function (yes) {
      if (!yes) return;
      $('[data-pay-state="banned"]').html('<div class="note-box ok"><svg class="icon"><use href="#a-check-circle"/></svg><div><b>محرومیت برداشته شد</b><p>کاربر می‌تواند در مزایده‌ها شرکت کند.</p></div></div>');
      Admin.toast('محرومیت کاربر برداشته شد', 'success');
    });
  });
})(jQuery);
