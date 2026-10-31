/* listings.js — نتایج جست‌وجو/لیست آگهی‌ها: مرتب‌سازی، نمایش بیشتر، پیشنهاد جست‌وجو (اخیر + پرطرفدار)
   رویداد: app:search-submit(q) · بارگذاری بیشتر از همان URL صفحه بعد (HTML کامل) انجام می‌شود. */
(function ($) {
  'use strict';

  /* مرتب‌سازی */
  $('#sortSel').on('change', function () {
    var u = new URL(location.href); u.searchParams.set('sort', this.value); u.searchParams.delete('page'); location.href = u.toString();
  });

  /* نمایش آگهی‌های بیشتر */
  $('#loadMore').on('click', function () {
    var $b = $(this), url = $b.data('next'); if (!url) return; App.loading($b);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { return r.text(); }).then(function (html) {
      var doc = new DOMParser().parseFromString(html, 'text/html'), $items = $(doc).find('#resultList').children();
      var known = $('#resultList').children().length; $items = $items.slice(known); // موارد جدید (صفحه بعد، تجمعی)
      $('#resultList').append($items); App.hydrate($('#resultList'));
      var $next = $(doc).find('#loadMore'); if ($next.length) $b.data('next', $next.data('next')); else $b.remove();
      App.loading($b, false);
    }).catch(function () { App.loading($b, false); App.toast('بارگذاری ناموفق بود؛ دوباره تلاش کنید', 'error'); });
  });

  /* پیشنهاد جست‌وجو */
  var $form = $('#lsSearch'), $in = $('#lsInput'), $box = $('#lsSuggest'); if (!$box.length) return;
  /* آمده از کادر صفحه‌ی اول (?focus=1): فوراً فوکوس + پیشنهادها، و پارامتر از آدرس برداشته می‌شود */
  var arrive = new URL(location.href);
  if (arrive.searchParams.get('focus')) { arrive.searchParams.delete('focus'); history.replaceState(null, '', arrive.pathname + arrive.search); setTimeout(function () { $in.trigger('focus'); }, 50); }
  var KEY = 'recentSearches', popular = $box.data('popular') || [];
  function recents() { try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { return []; } }
  function save(q) { q = $.trim(q); if (!q) return; var r = recents().filter(function (x) { return x !== q; }); r.unshift(q); try { localStorage.setItem(KEY, JSON.stringify(r.slice(0, 6))); } catch (e) {} }
  function item(t, icon) { return $('<button type="button" class="sg-item">').append('<svg class="icon icon-sm"><use href="#i-' + icon + '"/></svg>').append($('<span>').text(t)).data('q', t); }
  function render() {
    var q = $.trim($in.val()), $c = $('<div>');
    if (!q) {
      var r = recents();
      if (r.length) { $c.append($('<div class="sg-h"><span>جست‌وجوهای اخیر</span><button type="button" id="sgClear">پاک کردن</button></div>')); r.forEach(function (x) { $c.append(item(x, 'history')); }); }
      $c.append('<div class="sg-h"><span>پرطرفدار</span></div>'); var $ch = $('<div class="sg-chips">'); popular.forEach(function (x) { $ch.append($('<button type="button">').text(x).data('q', x)); }); $c.append($ch);
    } else {
      var m = recents().concat(popular).filter(function (x, i, a) { return x.indexOf(q) > -1 && a.indexOf(x) === i; }).slice(0, 6);
      if (!m.length) return hide();
      m.forEach(function (x) { $c.append(item(x, 'search')); });
    }
    $box.empty().append($c.children()).prop('hidden', false);
  }
  function hide() { $box.prop('hidden', true); }
  $in.on('focus input', render);
  $box.on('click', '.sg-item, .sg-chips button', function () { $in.val($(this).data('q')); $form.trigger('submit'); });
  $box.on('click', '#sgClear', function () { try { localStorage.removeItem(KEY); } catch (e) {} render(); });
  $(document).on('click', function (e) { if (!$(e.target).closest('#lsSearch').length) hide(); }).on('keydown', function (e) { if (e.key === 'Escape') hide(); });
  $form.on('submit', function () { var q = $.trim($in.val()); save(q); $(document).trigger('app:search-submit', [q]); $form.find('input[name]').each(function () { if (!this.value) this.disabled = true; }); });
})(jQuery);
