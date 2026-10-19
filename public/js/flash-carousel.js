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
