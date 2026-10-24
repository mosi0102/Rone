/* ==========================================================================
   app.js — رفتارهای پایه (jQuery)
   API عمومی: window.App.toast(), App.sheet(), App.money(), App.jdate(), App.loading()
   ========================================================================== */
(function ($) {
  'use strict';

  var faDigits = '۰۱۲۳۴۵۶۷۸۹';
  var App = window.App = window.App || {};

  /* ---------- ابزارهای قالب‌بندی ---------- */
  App.toFa = function (v) { return String(v).replace(/\d/g, function (d) { return faDigits[d]; }); };
  App.money = function (n) { return new Intl.NumberFormat('fa-IR').format(Math.round(Number(n) || 0)); };
  App.jdate = function (d, withTime) {
    var opt = { year: 'numeric', month: 'long', day: 'numeric' };
    if (withTime) { opt.hour = '2-digit'; opt.minute = '2-digit'; }
    return new Intl.DateTimeFormat('fa-IR-u-ca-persian', opt).format(new Date(d));
  };
  // قالب عددی شمسی: 1405/07/20 • 18:00  (data-jfmt="date|time|datetime" + data-jdate="ISO")
  App.jfmt = function (d, kind) {
    var dt = new Date(d), dd = new Intl.DateTimeFormat('fa-IR-u-ca-persian', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(dt),
        tt = new Intl.DateTimeFormat('fa-IR', { hour: '2-digit', minute: '2-digit', hour12: false }).format(dt);
    return kind === 'date' ? dd : (kind === 'time' ? tt : dd + ' • ' + tt);
  };
  App.relative = function (d) {
    var s = Math.round((new Date(d) - Date.now()) / 1000), rtf = new Intl.RelativeTimeFormat('fa', { numeric: 'auto' });
    var u = [['day', 86400], ['hour', 3600], ['minute', 60], ['second', 1]];
    for (var i = 0; i < u.length; i++) { if (Math.abs(s) >= u[i][1] || i === u.length - 1) return rtf.format(Math.round(s / u[i][1]), u[i][0]); }
  };

  /* ---------- توست ---------- */
  var icons = { success: 'check-circle', error: 'alert', info: 'info', warn: 'alert' };
  App.toast = function (msg, type, ms) {
    type = type || 'info';
    var $stack = $('#toastStack');
    var $t = $('<div class="toast-s ' + type + '" role="status"><svg class="icon"><use href="#i-' + icons[type] + '"/></svg><span></span></div>');
    $t.find('span').text(msg);
    $stack.append($t);
    if (navigator.vibrate && type === 'error') navigator.vibrate(60);
    setTimeout(function () { $t.addClass('out'); setTimeout(function () { $t.remove(); }, 250); }, ms || 3200);
  };

  /* ---------- شیت پایین (Bootstrap Offcanvas) ---------- */
  App.sheet = function (id, action) {
    var el = document.getElementById(id); if (!el) return;
    var inst = bootstrap.Offcanvas.getOrCreateInstance(el);
    action === 'hide' ? inst.hide() : inst.show();
  };

  /* ---------- دکمه در حال بارگذاری ---------- */
  App.loading = function ($btn, on) { $btn.toggleClass('is-loading', on !== false).prop('disabled', on !== false); };

  /* ---------- اثر ریپل روی دکمه‌ها ---------- */
  $(document).on('pointerdown', '.btn', function (e) {
    var $b = $(this), r = this.getBoundingClientRect(), s = Math.max(r.width, r.height);
    var $r = $('<span class="ripple"></span>').css({ width: s, height: s, left: e.clientX - r.left - s / 2, top: e.clientY - r.top - s / 2 });
    $b.append($r); setTimeout(function () { $r.remove(); }, 600);
  });

  /* ---------- سایه هدر هنگام اسکرول ---------- */
  var $top = $('.topbar');
  $(window).on('scroll', function () { $top.toggleClass('is-scrolled', window.scrollY > 4); }).trigger('scroll');

  /* ---------- علاقه‌مندی (نمایشی؛ بک‌اند به data-url وصل می‌کند) ---------- */
  $(document).on('click', '.lc-fav, .fc-save, [data-save]', function (e) {
    e.preventDefault(); e.stopPropagation();
    var $b = $(this), on = !$b.hasClass('is-on');
    $b.toggleClass('is-on', on).attr('aria-pressed', on);
    App.toast(on ? 'به نشان‌شده‌ها اضافه شد' : 'از نشان‌شده‌ها حذف شد', on ? 'success' : 'info', 1800);
    $(document).trigger('app:favorite', [$b.data('id'), on]);
  });

  /* ---------- اشتراک‌گذاری: <button data-share data-title="..."> ---------- */
  $(document).on('click', '[data-share]', function () {
    var data = { title: $(this).data('title') || document.title, url: $(this).data('url') || location.href };
    if (navigator.share) { navigator.share(data).catch(function () {}); return; }
    if (navigator.clipboard) navigator.clipboard.writeText(data.url).then(function () { App.toast('لینک کپی شد', 'success', 1800); });
    else App.toast(data.url, 'info');
  });

  /* ---------- چیپ‌های فیلتر ---------- */
  $(document).on('click', '.chips .chip', function () {
    var $c = $(this); $c.addClass('active').siblings().removeClass('active');
    this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    $(document).trigger('app:chip', [$c.data('value')]);
  });

  /* ---------- نمایش اعداد و تاریخ (data-money / data-jdate) ---------- */
  App.hydrate = function (root) {
    $(root || document).find('[data-money]').each(function () { $(this).text(App.money($(this).data('money'))); });
    $(root || document).find('[data-jdate]').each(function () { $(this).text(App.jdate($(this).data('jdate'), $(this).is('[data-time]'))); });
    $(root || document).find('[data-jfmt]').each(function () { $(this).text(App.jfmt($(this).data('jdate'), $(this).data('jfmt'))); });
    $(root || document).find('[data-fa]').each(function () { $(this).text(App.toFa($(this).text())); });
  };

  /* ---------- شمارنده معکوس: <div data-countdown="2026-10-20T18:00:00+03:30"> ----------
     بک‌اند زمان پایان را به‌صورت ISO می‌دهد. رویداد app:countdown-end هنگام اتمام ارسال می‌شود. */
  var cdTimer;
  function tickCountdowns() {
    $('[data-countdown]').each(function () {
      var $el = $(this), end = new Date($el.data('countdown')).getTime(), diff = Math.max(0, Math.floor((end - Date.now()) / 1000));
      var win = Number($el.data('window')) || 0, off = win && diff > win;
      $el.closest('[data-cd-wrap]').toggleClass('is-off', !!off); // بیشتر از «window» مانده: شمارنده خاموش، متن جایگزین نمایش داده شود
      if (off) { $el.empty().addClass('is-off'); return; }
      $el.removeClass('is-off');
      var hms = $el.data('units') === 'hms'; // فقط ساعت:دقیقه:ثانیه (ساعت کل، بدون روز)
      var d = hms ? 0 : Math.floor(diff / 86400), h = hms ? Math.floor(diff / 3600) : Math.floor(diff % 86400 / 3600), m = Math.floor(diff % 3600 / 60), s = diff % 60;
      var inline = $el.hasClass('countdown-inline');
      if (inline) {
        $el.text((d ? App.toFa(d) + ' روز و ' : '') + App.toFa(String(h).padStart(2, '0')) + ':' + App.toFa(String(m).padStart(2, '0')) + ':' + App.toFa(String(s).padStart(2, '0')));
      } else {
        var u = (d > 0 && !hms) ? [[d, 'روز'], [h, 'ساعت'], [m, 'دقیقه']] : [[h, 'ساعت'], [m, 'دقیقه'], [s, 'ثانیه']];
        $el.html(u.map(function (x) { return '<span class="cd-unit">' + App.toFa(String(x[0]).padStart(2, '0')) + '<small>' + x[1] + '</small></span>'; }).join(''));
      }
      $el.toggleClass('is-urgent', diff > 0 && diff < 300);
      if (diff === 0 && !$el.hasClass('is-ended')) { $el.addClass('is-ended'); $el.trigger('app:countdown-end'); }
    });
  }
  App.startCountdowns = function () { clearInterval(cdTimer); tickCountdowns(); cdTimer = setInterval(tickCountdowns, 1000); };

  /* ---------- وضعیت شبکه ---------- */
  function net() { $('#netBanner').toggleClass('show', !navigator.onLine); }
  window.addEventListener('online', function () { net(); App.toast('اتصال برقرار شد', 'success', 1800); });
  window.addEventListener('offline', net); net();

  /* ---------- PWA: ثبت سرویس‌ورکر و نصب ---------- */
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () { navigator.serviceWorker.register('/sw.js').catch(function () {}); });
  }
  var deferredPrompt, DISMISS_KEY = 'pwa_dismissed_at';
  var standalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone;
  var recentlyDismissed = function () { try { return Date.now() - Number(localStorage.getItem(DISMISS_KEY) || 0) < 7 * 864e5; } catch (e) { return false; } };

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault(); deferredPrompt = e;
    if (!standalone && !recentlyDismissed()) setTimeout(function () { $('#installBanner').addClass('show'); }, 4000);
    $('[data-install]').removeClass('d-none');
  });
  $(document).on('click', '[data-install]', function () {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then(function () { deferredPrompt = null; $('#installBanner').removeClass('show'); $('[data-install]').addClass('d-none'); });
    } else if (/iphone|ipad|ipod/i.test(navigator.userAgent)) {
      App.sheet('iosInstallSheet');
    }
  });
  $(document).on('click', '#installDismiss', function () { $('#installBanner').removeClass('show'); try { localStorage.setItem(DISMISS_KEY, Date.now()); } catch (e) {} });
  window.addEventListener('appinstalled', function () { $('#installBanner').removeClass('show'); App.toast('اپلیکیشن نصب شد', 'success'); });
  // آیفون: دکمه نصب دستی
  if (/iphone|ipad|ipod/i.test(navigator.userAgent) && !standalone) $('[data-install]').removeClass('d-none');

  /* ---------- شروع ---------- */
  $(function () { App.hydrate(); App.startCountdowns(); });
})(jQuery);


/* ===== کروسل فروش فوری ===== */
/* flash-carousel.js — کروسل ساده فروش فوری (بدون وابستگی؛ مستقل از RTL)
   نقطه‌ها، پخش خودکار (با لمس/هاور/تب پنهان/خارج از دید متوقف می‌شود) */
(function () {
  'use strict';
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function init(root) {
    var track = root.querySelector('.fl-track'), slides = [].slice.call(root.querySelectorAll('.fl-slide')), dots = [].slice.call(root.querySelectorAll('.fl-dots button'));
    if (!slides.length) return;
    var autoplay = reduce ? 0 : (Number(root.getAttribute('data-autoplay')) || 0);
    var active = -1, lock = 0, ticking = false, hold = false, inView = true, holdTimer;

    function pad() { return parseFloat(getComputedStyle(track).paddingInlineStart) || 0; }
    function edge(el, rtl, r) { var b = el.getBoundingClientRect(); return rtl ? b.right - (r.right - pad()) : b.left - (r.left + pad()); }
    function nearest() {
      var rtl = getComputedStyle(track).direction === 'rtl', r = track.getBoundingClientRect(), best = 0, bd = 1e9;
      slides.forEach(function (s, i) { var d = Math.abs(edge(s, rtl, r)); if (d < bd) { bd = d; best = i; } });
      return best;
    }
    function setActive(i) {
      if (i === active) return; active = i;
      slides.forEach(function (s, k) { s.classList.toggle('is-active', k === i); });
      dots.forEach(function (d, k) { d.classList.toggle('is-active', k === i); d.setAttribute('aria-selected', k === i); });
    }
    function go(i, smooth) {
      i = (i + slides.length) % slides.length;
      var rtl = getComputedStyle(track).direction === 'rtl', r = track.getBoundingClientRect();
      lock = Date.now() + 700;
      track.scrollBy({ left: edge(slides[i], rtl, r), behavior: smooth === false ? 'auto' : 'smooth' });
      setActive(i);
    }
    track.addEventListener('scroll', function () {
      if (ticking || Date.now() < lock) return; ticking = true;
      requestAnimationFrame(function () { ticking = false; setActive(nearest()); });
    }, { passive: true });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); pause(); }); });

    function pause() { hold = true; clearTimeout(holdTimer); holdTimer = setTimeout(function () { hold = false; }, 7000); }
    ['pointerdown', 'touchstart', 'mouseenter', 'focusin'].forEach(function (e) { root.addEventListener(e, pause, { passive: true }); });
    if ('IntersectionObserver' in window) new IntersectionObserver(function (en) { inView = en[0].isIntersecting; }, { threshold: .4 }).observe(root);
    if (autoplay) setInterval(function () { if (!hold && inView && !document.hidden) go(active + 1); }, autoplay);

    setActive(0);
  }
  document.addEventListener('DOMContentLoaded', function () { [].forEach.call(document.querySelectorAll('[data-flash]'), init); });
})();


/* ===== پنجره‌ها (modal/offcanvas) به انتهای body منتقل می‌شوند =====
   تا هیچ والدی (transform/opacity/overflow/z-index) نتواند آن‌ها را زیر backdrop ببرد یا برش بدهد. */
(function ($) {
  'use strict';
  window.App = window.App || {};
  App.hoistOverlays = function (root) {
    $(root || document).find('.modal, .offcanvas').each(function () { if (this.parentNode !== document.body) document.body.appendChild(this); });
  };
  $(function () { App.hoistOverlays(); });

  /* ===== هدر هوشمند ===== */
  var $heads = $('.page-head, .masthead.sticky-head, .desk-header');
  if (!$heads.length) return;
  var lastY = window.pageYOffset || 0, ticking = false, THRESH = 10;
  function update() {
    ticking = false;
    var y = Math.max(0, window.pageYOffset || 0), dy = y - lastY;
    $heads.toggleClass('is-stuck', y > 4);
    if (y < 90) $heads.removeClass('is-hidden-up');           // نزدیک بالای صفحه همیشه دیده شود
    else if (dy > THRESH) $heads.addClass('is-hidden-up');    // به پایین ← پنهان
    else if (dy < -THRESH) $heads.removeClass('is-hidden-up'); // به بالا ← ظاهر
    if (Math.abs(dy) > THRESH || y < 90) lastY = y;
  }
  window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
  // هنگام باز بودن پنجره‌ها یا فوکوس روی هدر، پنهان نشود
  $(document).on('focusin', '.page-head, .masthead, .desk-header', function () { $heads.removeClass('is-hidden-up'); });
  update();
})(jQuery);
