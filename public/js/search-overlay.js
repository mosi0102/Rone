/* search-overlay.js — لایه‌ی جست‌وجوی تمام‌صفحه روی صفحه‌ی اول و صفحه‌ی نمایشگاه‌ها (data-scope=dealers: فقط جست‌وجو، بدون فیلتر): بدون رفتن به آدرس دیگر و بدون رفرش.
   پیشنهادها (اخیر/پرطرفدار/دسته) ← با تایپ یا انتخاب، نتایج با ListingBrowser (AJAX) داخل همین لایه می‌آیند.
   دکمه‌ی برگشت مرورگر/گوشی فقط لایه را می‌بندد. API: SearchOverlay.open(q) / close() */
(function ($) {
  'use strict';
  var $o = $('#searchOverlay'); if (!$o.length) return;
  var $in = $('#soInput'), $res = $('#soResults'), $disc = $('#soDiscover'), $clear = $('#soClear'), $scroll = $('#soScroll');
  var dealers = $o.data('scope') === 'dealers', KEY = dealers ? 'recentDealerSearches' : 'recentSearches', cat = '', lb = null, t = null, isOpen = false, loading = 0;

  function recents() { try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { return []; } }
  function saveRecent(q) { q = $.trim(q); if (!q) return; var r = recents().filter(function (x) { return x !== q; }); r.unshift(q); try { localStorage.setItem(KEY, JSON.stringify(r.slice(0, 6))); } catch (e) {} }
  function drawRecent() {
    var r = recents(), $l = $('#soRecent').empty(); $('#soRecentBlock').prop('hidden', !r.length);
    r.forEach(function (x) { $l.append($('<button type="button" class="sp-row">').attr('data-so-q', x).append('<svg class="icon icon-sm"><use href="#i-history"/></svg>').append($('<span>').text(x))); });
  }
  function city() { return (window.AppCity && AppCity.get()) || ''; }   /* فقط اگر کاربر شهر انتخاب کرده باشد */

  /* ---------- نمایش پیشنهادها / نتایج ---------- */
  function showDiscover() { clearTimeout(t); cat = ''; if (lb) lb = null; $res.prop('hidden', true).empty(); $disc.prop('hidden', false); drawRecent(); }
  /* نمایشگاه‌ها: فقط جست‌وجو؛ فهرست HTML از dealers.search می‌آید */
  function dealerQuery(p) {
    $disc.prop('hidden', true); $res.prop('hidden', false);
    var u = new URL($o.data('url'), location.origin);
    $.each(p, function (k, v) { if (v) u.searchParams.set(k, v); });
    var my = ++loading; $res.addClass('is-loading');
    fetch(u.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' }, credentials: 'same-origin' })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(function (html) { if (my !== loading || !isOpen) return; $res.html(html); if (window.App) App.hydrate($res); $scroll.scrollTop(0); })
      .catch(function () { if (window.App) App.toast('بارگذاری ناموفق بود؛ دوباره تلاش کنید', 'error'); })
      .then(function () { $res.removeClass('is-loading'); });
  }
  function query(p, mode) {
    if (dealers) return dealerQuery(p);
    $disc.prop('hidden', true); $res.prop('hidden', false);
    if (lb) { lb.goParams(p, 'none', false); return; }
    var u = new URL($o.data('url'), location.origin); u.searchParams.set('embed', 1);
    $.each($.extend({ city: city() }, p), function (k, v) { if (v) u.searchParams.set(k, v); });
    var my = ++loading; $res.addClass('is-loading');
    fetch(u.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' }, credentials: 'same-origin' })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(function (html) {
        if (my !== loading || !isOpen) return;
        $res.html(html); var $root = $res.children('[data-lb]').first();
        u.searchParams.delete('embed');
        if (window.App) App.hydrate($res); if (window.Filters) Filters.init($res);
        lb = new ListingBrowser($root, { input: $in, clear: '#soClear', embed: true, history: false, bindInput: false, scroller: '#soScroll', keepCity: true });
        lb.cur = u;                                                   /* وضعیت فعلی (بدون embed) */
      })
      .catch(function () { if (window.App) App.toast('بارگذاری ناموفق بود؛ دوباره تلاش کنید', 'error'); })
      .then(function () { $res.removeClass('is-loading'); });
  }
  function run(immediate) {
    clearTimeout(t);
    var q = $.trim($in.val()); $clear.prop('hidden', !q);
    t = setTimeout(function () {
      if (dealers) { if (!q && !cat) return showDiscover(); return dealerQuery({ q: q, category: cat, city: city() }); }
      if (!q && !(lb && hasFilters())) return showDiscover();
      var p = lb ? lb.params() : {}; if (q) p.q = q; else delete p.q;
      if (!lb && !p.city && city()) p.city = city();
      query(p);
    }, immediate ? 0 : 320);
  }
  function hasFilters() { var p = lb.params(), n = 0; $.each(p, function (k, v) { if (v && k !== 'q' && k !== 'city') n++; }); return n > 0; }

  /* ---------- باز / بسته ---------- */
  function open(q) {
    if (isOpen) return; isOpen = true;
    $o.addClass('open').attr('aria-hidden', 'false'); $('body').addClass('overflow-hidden');
    history.pushState({ so: 1 }, '');
    drawRecent();
    if (q) { $in.val(q); run(true); }
    setTimeout(function () { $in.trigger('focus'); }, 120);
  }
  function hide() { if (!isOpen) return; isOpen = false; $o.removeClass('open').attr('aria-hidden', 'true'); $('body').removeClass('overflow-hidden'); $in.val('').trigger('blur'); $clear.prop('hidden', true); loading++; showDiscover(); }   /* هر بار تازه باز می‌شود */
  function close() { if (!isOpen) return; if (history.state && history.state.so) history.back(); else hide(); }
  $(window).on('popstate', function () { if (isOpen && !(history.state && history.state.so)) hide(); });
  $(document).on('keydown', function (e) { if (e.key === 'Escape' && isOpen && !$('#citySheet.show').length && !$o.find('.ls-sheet.open').length) close(); });
  $('#soBack').on('click', close);

  /* ---------- ورودی‌ها ---------- */
  $in.on('input', function () { cat = ''; run(false); });
  $('#soForm').on('submit', function (e) { e.preventDefault(); saveRecent($in.val()); run(true); $in.trigger('blur'); });
  $clear.on('click', function () { $in.val('').trigger('focus'); run(true); });
  $o.on('click', '[data-so-q]', function () { var q = $(this).data('so-q'); $in.val(q); saveRecent(q); run(true); $in.trigger('blur'); });
  $o.on('click', '[data-so-cat]', function () { $in.val(''); $clear.prop('hidden', true); cat = $(this).data('so-cat'); query({ category: cat, city: city() }); });
  $('#soClearRecent').on('click', function () { try { localStorage.removeItem(KEY); } catch (e) {} drawRecent(); });

  /* تغییر شهر: نتایج با شهر جدید دوباره بارگذاری می‌شود */
  $(document).on('app:city', function (e, c) { if (isOpen && dealers && !$res.prop('hidden')) { run(true); return; } if (isOpen && lb) { var p = lb.params(); p.city = c; lb.goParams(p, 'none', false); } });

  window.SearchOverlay = { open: open, close: close };
})(jQuery);
