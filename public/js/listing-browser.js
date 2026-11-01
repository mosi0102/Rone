/* listing-browser.js — مرورگر آگهی‌ها بدون رفرش (AJAX): جست‌وجو، فیلتر، مرتب‌سازی، دسته، چیپ‌ها، نمایش بیشتر.
   سرور برای درخواست‌های AJAX (هدر X-Requested-With) فقط HTML پارشیال pages/listings/_browser را می‌دهد؛
   ناحیه‌های [data-lb-region] جایگزین می‌شوند و آدرس صفحه با History API به‌روز می‌ماند.
   استفاده: new ListingBrowser($root, { input: $('#lsInput'), embed: false, history: true })  */
(function ($) {
  'use strict';
  var REGIONS = ['filters-side', 'tabs', 'bar', 'chips', 'head', 'list', 'more', 'filters-sheet'];

  function LB($root, o) {
    var self = this;
    this.$r = $root; this.o = $.extend({ input: $(), clear: '#lsClear', bindInput: true, embed: false, history: true, scroller: window, onUpdate: null }, o || {});
    this.base = $root.data('lb-action') || location.pathname;
    this.cur = new URL(this.o.history ? location.href : this.base, location.origin);
    this.cur.searchParams.delete('page'); this.cur.searchParams.delete('focus');
    this.ctrl = null; this.sheetOpen = false; this.t = null;
    if (this.o.history) history.replaceState($.extend({}, history.state, { lb: 1 }), '', location.href);
    this.bind();
  }
  var P = LB.prototype;

  /* ---------- آدرس‌ها ---------- */
  P.params = function () { var o = {}; this.cur.searchParams.forEach(function (v, k) { o[k] = v; }); return o; };
  P.urlFor = function (p) {
    var u = new URL(this.cur.pathname, location.origin);
    $.each(p, function (k, v) { if (v !== '' && v != null && k !== 'page' && k !== 'embed') u.searchParams.set(k, v); });
    return u;
  };
  P.q = function () { return $.trim(this.o.input.val() || ''); };

  /* ---------- دریافت و جایگزینی ---------- */
  P.fetchHtml = function (u) {
    var x = new URL(u.toString()); if (this.o.embed) x.searchParams.set('embed', 1);
    if (this.ctrl) this.ctrl.abort();
    this.ctrl = window.AbortController ? new AbortController() : null;
    return fetch(x.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' }, credentials: 'same-origin', signal: this.ctrl && this.ctrl.signal })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); });
  };
  P.swap = function (html) {
    var $new = $('<div>').append($.parseHTML(html)), $r = this.$r, $aside = $r.find('[data-lb-region="filters-side"]'), top = $aside.scrollTop();
    REGIONS.forEach(function (n) {
      var $n = $new.find('[data-lb-region="' + n + '"]').first(), $o = $r.find('[data-lb-region="' + n + '"]').first();
      if ($n.length && $o.length) $o.replaceWith($n);
    });
    var $root2 = $new.find('[data-lb]').first(); if ($root2.length) $r.attr('data-lb-sort', $root2.attr('data-lb-sort'));
    $r.find('[data-lb-region="filters-side"]').scrollTop(top);
    if (window.App && App.hydrate) App.hydrate($r);
    if (window.Filters) Filters.init($r);
    if (this.o.onUpdate) this.o.onUpdate(this);
    $(document).trigger('lb:updated', [this]);
  };
  /* mode: push | replace | none */
  P.go = function (target, mode, scroll) {
    var self = this, u = typeof target === 'string' ? new URL(target, location.origin) : target;
    u.searchParams.delete('page'); u.searchParams.delete('focus'); u.searchParams.delete('embed');
    this.$r.addClass('is-loading').attr('aria-busy', 'true');
    return this.fetchHtml(u).then(function (html) {
      self.cur = u; self.swap(html);
      if (self.o.history && mode !== 'none') history[mode === 'replace' ? 'replaceState' : 'pushState']({ lb: 1 }, '', u.pathname + u.search);
      self.syncInput(); if (scroll !== false) self.scrollTop();
    }).catch(function (e) { if (e && e.name === 'AbortError') return; if (window.App) App.toast('بارگذاری ناموفق بود؛ دوباره تلاش کنید', 'error'); })
      .then(function () { self.$r.removeClass('is-loading').removeAttr('aria-busy'); });
  };
  P.goParams = function (p, mode, scroll) { return this.go(this.urlFor(p), mode, scroll); };
  P.syncInput = function () {
    var i = this.o.input; if (!i.length) return;
    if (!i.is(':focus')) i.val(this.cur.searchParams.get('q') || '');
    $(this.o.clear).prop('hidden', !i.val());
  };
  P.scrollTop = function () {
    var $l = this.$r.find('.ls-bar').first(); if (!$l.length) return;
    if (this.o.scroller === window) { if ($l[0].getBoundingClientRect().top < 0) window.scrollTo({ top: $l.offset().top - 90, behavior: 'smooth' }); }
    else { var $s = $(this.o.scroller); $s.stop().animate({ scrollTop: Math.max(0, $s.scrollTop() + $l[0].getBoundingClientRect().top - $s[0].getBoundingClientRect().top - 8) }, 200); }
  };

  /* ---------- فیلتر موبایل (لایه‌ی داخل صفحه) ---------- */
  P.openSheet = function () {
    var $s = this.$r.find('.ls-sheet'); if (this.sheetOpen) return; this.sheetOpen = true;
    $s.addClass('open').attr('aria-hidden', 'false'); $('body').addClass('overflow-hidden');
    if (this.o.history) history.pushState({ lb: 1, lbSheet: 1 }, '');
    setTimeout(function () { $s.find('[data-lb-close-filters]').trigger('focus'); }, 120);
  };
  P.closeSheet = function (viaApply) {
    if (!this.sheetOpen) return; this.sheetOpen = false;
    this.$r.find('.ls-sheet').removeClass('open').attr('aria-hidden', 'true'); $('body').removeClass('overflow-hidden');
    if (this.o.history && !viaApply && history.state && history.state.lbSheet) history.back();
  };

  /* ---------- رویدادها ---------- */
  P.bind = function () {
    var self = this, $r = this.$r;
    /* لینک‌های دسته/چیپ/حذف فیلتر */
    $r.on('click', 'a[data-lb-link]', function (e) {
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.which === 2) return;
      e.preventDefault(); self.go(this.href, 'push');
    });
    /* مرتب‌سازی */
    $r.on('change', '.lb-sort', function () { var p = self.params(); p.sort = this.value; self.goParams(p, 'push', false); });
    /* فیلتر (فرم) */
    $r.on('submit', 'form.ff', function (e) {
      e.preventDefault();
      var p = $(this).data('ffParams')(); delete p.q; delete p.sort;
      var cur = self.params(); if (cur.q) p.q = cur.q; if (cur.sort) p.sort = cur.sort; if (cur.city && !p.city && self.o.keepCity) p.city = cur.city;
      var wasSheet = self.sheetOpen; self.closeSheet(true);
      self.goParams(p, wasSheet && self.o.history ? 'replace' : 'push');
    });
    $r.on('click', '[data-lb-open-filters]', function (e) { e.preventDefault(); self.openSheet(); });
    $r.on('click', '[data-lb-close-filters]', function () { self.closeSheet(); });
    /* نمایش بیشتر (تجمعی) */
    $r.on('click', '[data-lb-more]', function () {
      var $b = $(this), next = $b.data('next'); if (!next) return; if (window.App) App.loading($b);
      self.fetchHtml(new URL(next, location.origin)).then(function (html) {
        var $n = $('<div>').append($.parseHTML(html)), $list = $r.find('.ls-list').first(), known = $list.children().length;
        $list.append($n.find('.ls-list').first().children().slice(known));
        $r.find('[data-lb-region="more"]').first().replaceWith($n.find('[data-lb-region="more"]').first());
        if (window.App) App.hydrate($list);
      }).catch(function (e) { if (e && e.name === 'AbortError') return; if (window.App) { App.loading($b, false); App.toast('بارگذاری ناموفق بود؛ دوباره تلاش کنید', 'error'); } });
    });
    /* جست‌وجو: تایپ زنده + ارسال */
    var $in = this.o.input;
    if (this.o.bindInput) {
    $in.on('input.lb', function () {
      $(self.o.clear).prop('hidden', !this.value);
      clearTimeout(self.t); self.t = setTimeout(function () { self.search('replace', false); }, 320);
    });
    $in.closest('form').on('submit.lb', function (e) { e.preventDefault(); clearTimeout(self.t); self.search('push'); $in.trigger('blur'); });
    $(document).on('click.lb', this.o.clear, function () { $in.val('').trigger('input').trigger('focus'); });
    }
    /* دکمه‌ی برگشت مرورگر */
    if (this.o.history) $(window).on('popstate.lb', function () {
      var st = history.state || {};
      if (self.sheetOpen) { self.closeSheet(true); return; }
      if (st.lb && !st.lbSheet) self.go(location.href, 'none', false);
    });
    $(document).on('keydown.lb', function (e) { if (e.key === 'Escape') self.closeSheet(); });
  };
  P.search = function (mode, scroll) { var p = this.params(), q = this.q(); if (q) p.q = q; else delete p.q; if ((p.q || '') === (this.cur.searchParams.get('q') || '')) return; this.goParams(p, mode, scroll); };

  window.ListingBrowser = LB;

  /* راه‌اندازی خودکار برای صفحه‌ی آگهی‌ها/جست‌وجو */
  $(function () {
    var $root = $('.page-container > [data-lb]').first(); if (!$root.length) return;
    $root.data('lb', new LB($root, { input: $('#lsInput'), history: true, keepCity: true }));
  });
})(jQuery);
