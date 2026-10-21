/* auction.js — مزایده‌ها (jQuery)
   لیست: فیلتر دسته/وضعیت | پرداخت ورودی | پیشنهاد زنده.
   اتصال بک‌اند / Real-time:
     • ارسال پیشنهاد:  $(document).on('app:bid-submit', function (e, auctionId, amount) { ... })  ← POST بزنید و در پاسخ موفق Auction.addBid({...mine:true}) را صدا بزنید
     • پیشنهاد جدید دیگران (WebSocket/Echo):  Auction.addBid({ amount, user:'کاربر ۴۲۰۰۰', time:'۱۹:۱۰' })
     • شبیه‌ساز نمایشی: attribute data-demo را از #bidBox / #bidFeed حذف کنید.
*/
(function ($) {
  'use strict';
  var fa = '۰۱۲۳۴۵۶۷۸۹';
  function digits(v) { return String(v).replace(/[۰-۹]/g, function (d) { return fa.indexOf(d); }).replace(/\D/g, ''); }
  function nowTime() { return new Intl.DateTimeFormat('fa-IR', { hour: '2-digit', minute: '2-digit', hour12: false }).format(new Date()); }

  /* ---------- لیست: فیلترها ---------- */
  var $list = $('#aucList');
  if ($list.length) {
    var cat = 'all', st = 'all';
    function apply() {
      var n = 0;
      $list.children('.auc-card').each(function () {
        var ok = (cat === 'all' || $(this).data('cat') === cat) && (st === 'all' || $(this).data('status') === st);
        $(this).toggle(ok); if (ok) n++;
      });
      $('#aucEmpty').toggleClass('d-none', n > 0);
    }
    $('#aucCats').on('click', 'button', function () { $(this).addClass('on').siblings().removeClass('on'); cat = $(this).data('cat'); apply(); });
    $('#aucStatus').on('click', 'button', function () { $(this).addClass('on').siblings().removeClass('on'); st = $(this).data('status'); apply(); });
  }

  /* ---------- پرداخت ورودی ---------- */
  $('#payEntry').on('click', function () {
    var $b = $(this); App.loading($b);
    $(document).trigger('app:auction-pay', [$b.data('auction'), $b.data('fee')]); // بک‌اند: کسر از کیف پول
    setTimeout(function () { App.loading($b, false); $('#entryWrap').addClass('d-none'); $('#entryDone').removeClass('d-none'); window.scrollTo({ top: 0, behavior: 'smooth' }); }, 1100);
  });

  /* ---------- شمارنده‌ها: شروع/پایان مزایده ---------- */
  var reloaded = false;
  $(document).on('app:countdown-end', function () {
    if (reloaded || !$('.cdcard, .auc-card').length) return; reloaded = true;
    // محافظ: اگر سرور هنوز وضعیت قبلی را می‌دهد، حداکثر هر ۳۰ ثانیه یک‌بار رفرش (جلوگیری از حلقه بی‌پایان)
    try { var last = +sessionStorage.getItem('aucReloadAt') || 0; if (Date.now() - last < 30000) return; sessionStorage.setItem('aucReloadAt', Date.now()); } catch (e) {}
    App.toast('وضعیت مزایده تغییر کرد؛ صفحه به‌روز می‌شود…', 'info', 1600);
    setTimeout(function () { location.reload(); }, 1700);
  });

  /* ---------- لغو مزایده (صاحب) ---------- */
  $('#cancelAuction').on('click', function () {
    var $b = $(this); App.loading($b); $(document).trigger('app:auction-cancel', [$b.data('id')]);
    setTimeout(function () { App.loading($b, false); App.sheet('cancelSheet', 'hide'); App.toast('مزایده لغو شد (نمایشی)', 'warn'); }, 900);
  });

  /* ---------- پیشنهاد زنده ---------- */
  var $root = $('#bidBox').length ? $('#bidBox') : $('#bidFeed');
  if (!$root.length) return;
  var S = { top: +$root.data('top') || 0, step: +$root.data('step') || 1, min: +$root.data('min') || 0, mine: +$root.data('mine') || 0 };
  var $input = $('#bidInput'), canBid = $('#bidBox').length > 0, pending = 0;
  function moneyHtml(n) { return '<span><span class="num" data-money="' + n + '">' + App.money(n) + '</span> <small>تومان</small></span>'; }
  function val() { return parseInt(digits($input.val()) || '0', 10); }
  function render() { if (canBid) $('#bidBtnAmt').text(App.money(val())); }
  function setMin() { $('#minBid').html(moneyHtml(S.min)); if (canBid && val() < S.min) { $input.val(App.money(S.min)); } render(); }

  var Auction = window.Auction = {
    /* b: {amount, user, time, mine} */
    addBid: function (b) {
      var wasMine = S.top && S.top === S.mine;
      S.top = b.amount; S.min = b.amount + S.step;
      var $l = $('#bidList'); $l.find('.bid-empty').remove(); $l.find('.bid-row.top').removeClass('top');
      var $r = $('<div class="bid-row top new' + (b.mine ? ' mine' : '') + '"><div class="br-main"><b>' + moneyHtml(b.amount) + '</b><span></span></div><time class="br-time"></time></div>');
      $r.find('.br-main > span').text(b.mine ? 'شما' : b.user); $r.find('time').text(b.time || nowTime());
      $l.prepend($r); $l.children('.bid-row').slice(8).remove();
      if (b.mine) { S.mine = b.amount; $('#mineLine').prop('hidden', false); $('#myBid').html(moneyHtml(b.amount)); }
      else if (wasMine) { App.toast('پیشنهاد بالاتری از شما ثبت شد', 'warn', 3000); }
      setMin(); $(document).trigger('auction:bid', [b]);
    }
  };

  if (canBid) {
    $input.val(App.money(digits($input.val()) || S.min)); // ارقام فارسی با جداکننده
    render();
    $input.on('input', function () { var d = digits(this.value); this.value = d ? App.money(d) : ''; $('#bidErr').text(''); render(); });
    $('.bid-chips').on('click', 'button', function () { var base = S.top || (S.min - S.step); $input.val(App.money(base + $(this).data('add') * S.step)); $('#bidErr').text(''); render(); });
    $('#bidBtn').on('click', function () {
      var v = val();
      if (v < S.min) { $('#bidErr').text('حداقل پیشنهاد ' + App.money(S.min) + ' تومان است.'); $input.trigger('focus'); return; }
      if (S.top && v > S.top * 1.5) { $('#bidErr').text('این مبلغ خیلی بیشتر از پیشنهاد فعلی است؛ لطفاً دوباره بررسی کنید.'); return; }
      pending = v; $('#confirmAmt').text(App.money(v)); App.sheet('bidConfirm');
    });
    $('#bidConfirmBtn').on('click', function () {
      var $b = $(this); App.loading($b);
      $(document).trigger('app:bid-submit', [$root.data('auction'), pending]);
      setTimeout(function () { App.loading($b, false); App.sheet('bidConfirm', 'hide'); Auction.addBid({ amount: pending, mine: true, time: nowTime() }); App.toast('پیشنهاد شما ثبت شد', 'success', 2200); }, 800);
    });
  }

  /* شبیه‌ساز نمایشی: هر ۹ تا ۱۴ ثانیه یک پیشنهاد از کاربر دیگر */
  if ($root.data('demo')) {
    (function tick() {
      setTimeout(function () {
        Auction.addBid({ amount: S.top + S.step * (1 + Math.floor(Math.random() * 2)), user: 'کاربر ' + App.toFa(String(10000 + Math.floor(Math.random() * 89999))), time: nowTime() });
        tick();
      }, 9000 + Math.random() * 5000);
    })();
  }
})(jQuery);
