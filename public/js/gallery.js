/* gallery.js — گالری با سوایپ + تامبنیل + نمایش تمام‌صفحه. مستقل از جهت RTL (بر پایه مختصات صفحه). */
(function () {
  'use strict';
  var fa = '۰۱۲۳۴۵۶۷۸۹', toFa = function (n) { return String(n).replace(/\d/g, function (d) { return fa[d]; }); };

  function Slider(track, slideSel, onChange) {
    var slides = function () { return [].slice.call(track.querySelectorAll(slideSel)); };
    var active = 0, lock = 0, tick = false;
    function nearest() {
      var r = track.getBoundingClientRect(), c = r.left + r.width / 2, best = 0, bd = 1e9;
      slides().forEach(function (s, i) { var b = s.getBoundingClientRect(), d = Math.abs(b.left + b.width / 2 - c); if (d < bd) { bd = d; best = i; } });
      return best;
    }
    function set(i) { if (i !== active) { active = i; onChange(i); } }
    function go(i, smooth) {
      var all = slides(); i = Math.max(0, Math.min(all.length - 1, i));
      var r = track.getBoundingClientRect(), b = all[i].getBoundingClientRect();
      lock = Date.now() + 600;
      track.scrollBy({ left: (b.left + b.width / 2) - (r.left + r.width / 2), behavior: smooth === false ? 'auto' : 'smooth' });
      set(i);
    }
    track.addEventListener('scroll', function () {
      if (tick || Date.now() < lock) return; tick = true;
      requestAnimationFrame(function () { tick = false; set(nearest()); });
    }, { passive: true });
    return { go: go, get index() { return active; }, count: function () { return slides().length; }, reset: function () { active = 0; } };
  }

  function initGallery(root) {
    var track = root.querySelector('.gal-track'), thumbs = [].slice.call(root.querySelectorAll('.gal-thumb')), cur = root.querySelector('[data-cur]');
    var sl = Slider(track, '.gal-slide', function (i) {
      cur.textContent = toFa(i + 1);
      thumbs.forEach(function (t, k) { t.classList.toggle('active', k === i); });
      if (thumbs[i]) { var tr = thumbs[i].parentNode, tb = thumbs[i].getBoundingClientRect(), pr = tr.getBoundingClientRect(); tr.scrollBy({ left: (tb.left + tb.width / 2) - (pr.left + pr.width / 2), behavior: 'smooth' }); }
    });
    thumbs.forEach(function (t, k) { t.addEventListener('click', function () { sl.go(k); }); });
    [].forEach.call(root.querySelectorAll('.gal-nav'), function (b) { b.addEventListener('click', function (e) { e.stopPropagation(); sl.go(sl.index + (b.dataset.dir === 'next' ? 1 : -1)); }); });
    root.querySelector('.gal-main').addEventListener('keydown', function (e) { if (e.key === 'ArrowLeft') sl.go(sl.index + 1); if (e.key === 'ArrowRight') sl.go(sl.index - 1); });

    // نمایش تمام‌صفحه
    function open() {
      var lb = document.getElementById('galLightbox'), lt = lb.querySelector('.gl-track');
      var fresh = lt.cloneNode(false); lt.parentNode.replaceChild(fresh, lt); lt = fresh; // شنونده‌های قبلی پاک شوند
      lt.innerHTML = [].map.call(track.querySelectorAll('img'), function (im) { return '<div class="gl-slide"><img src="' + im.getAttribute('data-full') + '" alt=""></div>'; }).join('');
      lb.querySelector('[data-total]').textContent = toFa(sl.count());
      var modal = bootstrap.Modal.getOrCreateInstance(lb);
      var start = sl.index;
      lb.addEventListener('shown.bs.modal', function once() {
        lb.removeEventListener('shown.bs.modal', once);
        var ls = Slider(lt, '.gl-slide', function (i) { lb.querySelector('[data-cur]').textContent = toFa(i + 1); sl.go(i, false); });
        lb._ls = ls; ls.go(start, false);
        lb.querySelector('[data-cur]').textContent = toFa(start + 1);
      });
      modal.show();
    }
    root.querySelector('[data-open]').addEventListener('click', open);
    track.addEventListener('click', open);
  }

  document.addEventListener('DOMContentLoaded', function () {
    [].forEach.call(document.querySelectorAll('[data-gallery]'), initGallery);
    var lb = document.getElementById('galLightbox');
    if (lb) {
      [].forEach.call(lb.querySelectorAll('.gal-nav'), function (b) { b.addEventListener('click', function () { if (lb._ls) lb._ls.go(lb._ls.index + (b.dataset.dir === 'next' ? 1 : -1)); }); });
      lb.addEventListener('keydown', function (e) { if (!lb._ls) return; if (e.key === 'ArrowLeft') lb._ls.go(lb._ls.index + 1); if (e.key === 'ArrowRight') lb._ls.go(lb._ls.index - 1); });
    }
  });
})();
