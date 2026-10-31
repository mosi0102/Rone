/* ==========================================================================
   welcome.js — اسلایدر خوش‌آمدگویی (scroll-snap) + نقطه‌ها، رد کردن، کیبورد
   ========================================================================== */
(function ($) {
  'use strict';
  var $w = $('[data-welcome]'); if (!$w.length) return;
  var el = $w.find('[data-slides]')[0], $sl = $w.find('[data-slide]'), $dots = $w.find('[data-dot]'),
      n = $sl.length, cur = 0, rtl = getComputedStyle(document.documentElement).direction === 'rtl',
      reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  function show(i) {
    cur = Math.max(0, Math.min(n - 1, i));
    $dots.removeClass('on').attr('aria-selected', 'false').eq(cur).addClass('on').attr('aria-selected', 'true');
    var last = cur === n - 1;
    $w.find('[data-next-slide]').prop('hidden', last); $w.find('[data-final]').prop('hidden', !last);
    $w.find('[data-skip]').css('visibility', last ? 'hidden' : 'visible');
    $sl.each(function (k) { $(this).attr('aria-hidden', k === cur ? 'false' : 'true'); });
    if (last) Auth.markSeen();
  }
  function goTo(i) {
    i = Math.max(0, Math.min(n - 1, i));
    var t = $sl[i].offsetLeft - el.offsetLeft;  // مستقل از جهت (RTL/LTR)
    if (el.scrollTo) el.scrollTo({ left: t, behavior: reduce ? 'auto' : 'smooth' }); else el.scrollLeft = t;
    show(i);
  }
  /* تشخیص اسلاید فعلی از روی موقعیت اسکرول (swipe) */
  var raf;
  el.addEventListener('scroll', function () {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(function () {
      var w = el.clientWidth || 1, i = Math.round(Math.abs(el.scrollLeft) / w);
      if (i !== cur) show(i);
    });
  }, { passive: true });

  $w.on('click', '[data-next-slide]', function () { goTo(cur + 1); });
  $w.on('click', '[data-dot]', function () { goTo(+$(this).data('dot')); });
  $w.on('click', '[data-skip]', function () { goTo(n - 1); });
  $w.on('click', '[data-go]', function () { Auth.markSeen(); });
  $(el).on('keydown', function (e) {
    var k = e.key; if (k !== 'ArrowLeft' && k !== 'ArrowRight') return; e.preventDefault();
    var fwd = (k === 'ArrowLeft') === rtl; goTo(cur + (fwd ? 1 : -1));
  });
  $(window).on('resize', function () { el.scrollLeft = $sl[cur].offsetLeft - el.offsetLeft; });
  show(0);
})(jQuery);
