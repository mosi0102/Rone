/* filters.js — فرم فیلتر: دسته ← برند ← مدل، فیلدهای فنی بر اساس دسته، اسلایدر قیمت، شمارش زنده
   رویداد: $(document).trigger('filters:change', [paramsObject])  — بک‌اند می‌تواند به‌جای endpoint شمارش، این را بگیرد.
   Endpoint شمارش: data-count-url (GET با همان پارامترها ← {"count": N}) */
(function ($) {
  'use strict';
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function digits(v) { return String(v || '').replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/\D/g, ''); }

  $('.ff').each(function () {
    var $f = $(this), cfg = $f.data('config') || {}, countUrl = $f.data('count-url');
    var $cat = $f.find('[name=category]'), $brand = $f.find('[name=brand]'), $model = $f.find('[name=model]');
    var $pmin = $f.find('[name=price_min]'), $pmax = $f.find('[name=price_max]'), $rng = $f.find('.rng'), $a = $rng.find('.rng-a'), $b = $rng.find('.rng-b');
    var $submit = $f.find('[type=submit]').first(), t;

    function maxPrice() { return (cfg.maxPrice || {})[$cat.val() || ''] || 15000000000; }
    function brandMap(c) {
      if (c) return (cfg.brands || {})[c] || {};
      var all = {}; $.each(cfg.brands || {}, function (_, m) { $.extend(all, m); }); return all;
    }
    function fill($sel, items, first, keep) {
      $sel.empty().append($('<option value="">').text(first));
      items.forEach(function (x) { $sel.append($('<option>').text(x)); });
      if (keep && items.indexOf(keep) > -1) $sel.val(keep); else $sel.val('');
    }
    function refillBrands(keep) { fill($brand, Object.keys(brandMap($cat.val())), 'همه برندها', keep); refillModels($model.data('sel')); }
    function refillModels(keep) { var m = brandMap($cat.val())[$brand.val()] || []; fill($model, m, 'همه مدل‌ها', keep); $model.prop('disabled', !$brand.val()); }
    function toggleSpecs() {
      var c = $cat.val();
      $f.find('.ff-field').each(function () {
        var cats = String($(this).data('cats') || ''), show = !cats || (c ? cats.split(',').indexOf(c) > -1 : cats.split(',').length >= 3);
        $(this).toggleClass('d-none', !show); if (!show) $(this).find('select,input').val('');
      });
      $f.find('[data-cat-label]').text((cfg.categories || {})[c] || '');
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
    $cat.on('change', function () { refillBrands(); toggleSpecs(); $pmax.attr('placeholder', App.money(maxPrice())); fromInputs(); schedule(); });
    $brand.on('change', function () { refillModels(); schedule(); });
    $f.on('change input', 'select, input[type=checkbox], input[type=radio], [data-money-field]', schedule);

    /* ----- شمارش زنده ----- */
    function params() {
      var o = {}; $f.serializeArray().forEach(function (p) { if (p.value !== '') o[p.name] = $f.find('[name="' + p.name + '"][data-money-field]').length ? digits(p.value) : p.value; }); return o;
    }
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
      refillBrands(); toggleSpecs(); $a.val(0); $b.val(100); setVars(0, 100); $pmax.attr('placeholder', App.money(maxPrice())); recount();
    });

    /* ----- ارسال: فیلدهای خالی و ارقام فارسی ----- */
    $f.on('submit', function () {
      $f.find('[data-money-field]').each(function () { this.value = digits(this.value); });
      $f.find('input[name],select[name]').each(function () { if (this.type !== 'hidden' && this.type !== 'checkbox' && !this.value) this.disabled = true; });
    });

    /* ----- شروع ----- */
    $f.find('[data-money-field]').each(function () { var d = digits(this.value); if (d) this.value = App.money(d); });
    refillBrands($brand.data('sel')); $model.prop('disabled', !$brand.val()); toggleSpecs(); fromInputs();
  });
})(jQuery);
