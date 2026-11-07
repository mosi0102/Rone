/* filters.js — فرم فیلتر: دسته ← نوع ملک، شهر ← محله، فیلدهای فنی بر اساس دسته، اسلایدر قیمت، شمارش زنده
   رویداد: $(document).trigger('filters:change', [paramsObject])  — بک‌اند می‌تواند به‌جای endpoint شمارش، این را بگیرد.
   Endpoint شمارش: data-count-url (GET با همان پارامترها ← {"count": N}) */
(function ($) {
  'use strict';
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function digits(v) { return String(v || '').replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/\D/g, ''); }

  function init(root) { $(root || document).find('.ff').addBack('.ff').each(function () {
    var $f = $(this); if ($f.data('ffInit')) return; $f.data('ffInit', 1);
    var cfg = $f.data('config') || {}, countUrl = $f.data('count-url');
    var $cat = $f.find('[name=category]'), $type = $f.find('[name=ptype]'), $city = $f.find('[name=city]'), $district = $f.find('[name=district]');
    var $pmin = $f.find('[name=price_min]'), $pmax = $f.find('[name=price_max]'), $rng = $f.find('.rng'), $a = $rng.find('.rng-a'), $b = $rng.find('.rng-b');
    var $submit = $f.find('[type=submit]').first(), t;

    function maxPrice() { return (cfg.maxPrice || {})[$cat.val() || ''] || 150000000000; }
    function priceLabel() { return (cfg.priceLabels || {})[$cat.val() || ''] || 'قیمت'; }
    function fill($sel, items, first, keep) {
      $sel.empty().append($('<option value="">').text(first));
      items.forEach(function (x) { $sel.append($('<option>').text(x)); });
      if (keep && items.indexOf(keep) > -1) $sel.val(keep); else $sel.val('');
    }
    function refillTypes(keep) { fill($type, (cfg.types || {})[$cat.val()] || [], 'همه انواع', keep); $type.prop('disabled', !$cat.val()); }
    function refillDistricts(keep) { fill($district, (cfg.districts || {})[$city.val()] || [], 'همه محله‌ها', keep); $district.prop('disabled', !$city.val()); }
    function toggleSpecs() {
      var c = $cat.val();
      $f.find('.ff-field').each(function () {
        var cats = String($(this).data('cats') || ''), show = !cats || (c ? cats.split(',').indexOf(c) > -1 : cats.split(',').length >= 4);
        $(this).toggleClass('d-none', !show); if (!show) { $(this).find('select,input:not([type=checkbox])').val(''); $(this).find('input[type=checkbox]').prop('checked', false); }
      });
      $f.find('[data-cat-label]').text((cfg.categories || {})[c] || '');
      $f.find('[data-price-label]').text(priceLabel());
    }

    /* ----- قیمت: اسلایدر ↔ ورودی‌ها ----- */
    function setVars(a, b2) { $rng[0].style.setProperty('--a', a); $rng[0].style.setProperty('--b', b2); }
    function fromSlider(src) {
      var a = +$a.val(), b2 = +$b.val(), max = maxPrice();
      if (a > b2) { if (src === 'a') { a = b2; $a.val(a); } else { b2 = a; $b.val(b2); } }
      setVars(a, b2);
      $pmin.val(a > 0 ? App.money(Math.round(max * a / 100)) : ''); $pmax.val(b2 < 100 ? App.money(Math.round(max * b2 / 100)) : '');
    }
    function fromInputs() {
      var max = maxPrice(), mn = +digits($pmin.val()), mx = +digits($pmax.val()) || max;
      var a = Math.min(100, Math.round(mn / max * 100)), b2 = Math.min(100, Math.round(mx / max * 100));
      $a.val(a); $b.val(Math.max(a, b2)); setVars(+$a.val(), +$b.val());
    }
    $a.on('input', function () { fromSlider('a'); schedule(); }); $b.on('input', function () { fromSlider('b'); schedule(); });
    $f.find('[data-money-field]').on('input', function () { var d = digits(this.value); this.value = d ? App.money(d) : ''; if ($(this).is($pmin.add($pmax))) fromInputs(); });
    $pmax.attr('placeholder', App.money(maxPrice()));

    /* ----- رویدادها ----- */
    $cat.on('change', function () { refillTypes(); toggleSpecs(); $pmax.attr('placeholder', App.money(maxPrice())); fromInputs(); schedule(); });
    $city.on('change', function () { refillDistricts(); schedule(); });
    $f.on('change input', 'select, input[type=checkbox], input[type=radio], [data-money-field]', schedule);

    /* ----- شمارش زنده ----- */
    function params() {
      var o = {}; $f.serializeArray().forEach(function (p) { if (p.value !== '') o[p.name] = $f.find('[name="' + p.name + '"][data-money-field]').length ? digits(p.value) : p.value; }); return o;
    }
    $f.data('ffParams', params);                       /* برای مرورگر آگهی (AJAX) */
    function schedule() { clearTimeout(t); t = setTimeout(recount, 350); }
    function recount() {
      var p = params(); $(document).trigger('filters:change', [p]);
      if (!countUrl || !window.fetch) return;
      $submit.addClass('is-counting');
      fetch(countUrl + '?' + $.param(p), { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (d) { $f.find('.ff-n').text(App.toFa(d.count)); $submit.prop('disabled', d.count === 0); })
        .catch(function () {}).then(function () { $submit.removeClass('is-counting'); });
    }

    /* ----- پاک کردن ----- */
    $f.find('.ff-clear').on('click', function () {
      $f.find('select').val(''); $f.find('[data-money-field]').val(''); $f.find('input[type=checkbox]').prop('checked', false);
      $f.find('input[name=sale_type][value=""]').prop('checked', true);
      refillTypes(); refillDistricts(); toggleSpecs(); $a.val(0); $b.val(100); setVars(0, 100); $pmax.attr('placeholder', App.money(maxPrice())); recount();
    });

    /* ----- ارسال: فیلدهای خالی و ارقام فارسی ----- */
    $f.on('submit', function () {
      if ($f.closest('[data-lb]').length) return;      /* داخل مرورگر آگهی: ارسال AJAX است (js/listing-browser.js) */
      $f.find('[data-money-field]').each(function () { this.value = digits(this.value); });
      $f.find('input[name],select[name]').each(function () { if (this.type !== 'hidden' && this.type !== 'checkbox' && !this.value) this.disabled = true; });
    });

    /* ----- شروع ----- */
    $f.find('[data-money-field]').each(function () { var d = digits(this.value); if (d) this.value = App.money(d); });
    refillTypes($type.data('sel')); refillDistricts($district.data('sel')); toggleSpecs(); fromInputs();
  }); }

  window.Filters = { init: init };
  init(document);
})(jQuery);
