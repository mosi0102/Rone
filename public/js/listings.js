/* listings.js — پیشنهاد جست‌وجو (اخیر + پرطرفدار) برای کادر جست‌وجوی صفحه‌ی آگهی‌ها.
   ارسال و فیلتر بدون رفرش در js/listing-browser.js است. رویداد: app:search-submit(q) */
(function ($) {
  'use strict';
  var $form = $('#lsSearch'), $in = $('#lsInput'), $box = $('#lsSuggest'); if (!$box.length) return;
  var KEY = 'recentSearches', popular = $box.data('popular') || [];
  function recents() { try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { return []; } }
  function save(q) { q = $.trim(q); if (!q) return; var r = recents().filter(function (x) { return x !== q; }); r.unshift(q); try { localStorage.setItem(KEY, JSON.stringify(r.slice(0, 6))); } catch (e) {} }
  function item(t, icon) { return $('<button type="button" class="sg-item">').append('<svg class="icon icon-sm"><use href="#i-' + icon + '"/></svg>').append($('<span>').text(t)).data('q', t); }
  function hide() { $box.prop('hidden', true); }
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
  $in.on('focus input', render);
  $box.on('click', '.sg-item, .sg-chips button', function () { $in.val($(this).data('q')); $form.trigger('submit'); hide(); });
  $box.on('click', '#sgClear', function () { try { localStorage.removeItem(KEY); } catch (e) {} render(); });
  $(document).on('click', function (e) { if (!$(e.target).closest('#lsSearch').length) hide(); }).on('keydown', function (e) { if (e.key === 'Escape') hide(); });
  $form.on('submit', function () { var q = $.trim($in.val()); save(q); hide(); $(document).trigger('app:search-submit', [q]); });
  /* آمده از آدرس با ?focus=1 (سازگاری با لینک‌های قدیمی) */
  var a = new URL(location.href);
  if (a.searchParams.get('focus')) { a.searchParams.delete('focus'); history.replaceState(history.state, '', a.pathname + a.search); setTimeout(function () { $in.trigger('focus'); }, 50); }
})(jQuery);
