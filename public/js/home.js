/* home.js — رفتارهای صفحه خانه: پارالکس هیرو، شمارش آمار، فیلتر تب‌ها، «نمایش بیشتر» (نمایشی) */
(function ($) {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // پارالکس عکس هیرو
  var $ph = $('[data-parallax]');
  if ($ph.length && !reduce) {
    var tk = false;
    $(window).on('scroll', function () {
      if (tk) return; tk = true;
      requestAnimationFrame(function () { tk = false; var y = Math.min(window.scrollY, 400); $ph.css('transform', 'translate3d(0,' + (y * 0.18).toFixed(1) + 'px,0) scale(' + (1 + y / 2600).toFixed(3) + ')'); });
    });
  }

  // شمارش آمار هنگام دیده شدن
  function countTo(el) {
    var end = Number(el.getAttribute('data-countto')), t0 = null, dur = 1400;
    if (reduce) { el.textContent = App.money(end); return; }
    function step(t) { if (!t0) t0 = t; var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 4); el.textContent = App.money(end * e); if (p < 1) requestAnimationFrame(step); }
    requestAnimationFrame(step);
  }
  $(function () {
    var els = $('[data-countto]').each(function () { this.textContent = App.money(0); }).get();
    if (!('IntersectionObserver' in window)) { els.forEach(countTo); return; }
    var io = new IntersectionObserver(function (en) { en.forEach(function (x) { if (x.isIntersecting) { countTo(x.target); io.unobserve(x.target); } }); }, { threshold: .6 });
    els.forEach(function (e) { io.observe(e); });
  });

  // فیلتر تب «جدیدترین آگهی‌ها» (نمایشی؛ بک‌اندکار با AJAX جایگزین کند)
  $('#latestTabs').on('click', '.chip', function () {
    var cat = $(this).data('cat'), n = 0;
    $('#latestList .listing-row').each(function () { var ok = cat === 'all' || $(this).data('category') === cat; $(this).toggle(ok); if (ok) n++; });
    $('#latestEmpty').toggleClass('d-none', n > 0);
    $(document).trigger('app:latest-tab', [cat]);
  });
  $('#loadMore').on('click', function () {
    var $b = $(this); App.loading($b);
    setTimeout(function () { App.loading($b, false); App.toast('آگهی دیگری برای نمایش نیست', 'info'); }, 900);
  });
})(jQuery);
