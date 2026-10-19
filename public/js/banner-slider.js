/* banner-slider.js — محو شدن خودکار بنرها؛ توقف هنگام لمس/هاور و تب پنهان */
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.addEventListener('DOMContentLoaded', function () {
    [].forEach.call(document.querySelectorAll('[data-bslider]'), function (root) {
      var slides = [].slice.call(root.querySelectorAll('.bs-slide')), dots = [].slice.call(root.querySelectorAll('.bs-dots button'));
      if (slides.length < 2) return;
      var i = 0, timer, hold = false;
      function show(n) {
        i = (n + slides.length) % slides.length;
        slides.forEach(function (s, k) { s.classList.toggle('is-active', k === i); });
        dots.forEach(function (d, k) { d.classList.toggle('is-active', k === i); });
      }
      function loop() { clearInterval(timer); if (reduce) return; timer = setInterval(function () { if (!hold && !document.hidden) show(i + 1); }, 5000); }
      dots.forEach(function (d, k) { d.addEventListener('click', function () { show(k); loop(); }); });
      ['mouseenter', 'pointerdown', 'focusin'].forEach(function (e) { root.addEventListener(e, function () { hold = true; }); });
      ['mouseleave', 'pointerup', 'pointercancel', 'focusout'].forEach(function (e) { root.addEventListener(e, function () { hold = false; }); });
      // سوایپ افقی ساده
      var x0 = null;
      root.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
      root.addEventListener('touchend', function (e) { if (x0 === null) return; var dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 40) { show(i + (dx < 0 ? 1 : -1)); loop(); } x0 = null; });
      loop();
    });
  });
})();
