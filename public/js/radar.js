/* ==========================================================================
   radar.js — رادار خودرو
   ۱) فرم ثبت (create): دسته ← برند ← مدل، فیلدهای هر دسته، پیش‌نمایش زنده با درصد تطابق، ثبت
   ۲) لیست/نمایش (index/show): حذف با تأیید، فیلتر «تطابق بالا»
   ۳) پنل نمایشگاه‌دار (panel/radar): فیلتر/مرتب‌سازی سمت کلاینت + پیشنهاد آگهی
   بک‌اند: GET data-preview-url (پارامترهای فرم) → {count,best,top[]} ؛ POST data-store-url → {id,url} ؛ DELETE data-delete-url ؛ POST data-url (پیشنهاد)
   ========================================================================== */
(function ($) {
  'use strict';
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function digits(v) { return String(v || '').replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/\D/g, ''); }
  function csrf() { return $('meta[name="csrf-token"]').attr('content') || ''; }
  function ajax(method, url, data) {
    return $.ajax({ url: url, method: method, data: data, dataType: 'json', headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
  }

  /* ---------------- ۱) فرم ثبت ---------------- */
  var $f = $('#radarForm');
  if ($f.length) (function () {
    var cfg = $f.data('config') || {}, $brand = $('#rBrand'), $model = $('#rModel'), timer, xhr;
    function cat() { return $f.find('[name=category]:checked').val() || 'car'; }
    function fill($s, items, first) { $s.empty().append($('<option value="">').text(first)); items.forEach(function (x) { $s.append($('<option>').text(x)); }); }
    function brands() { return (cfg.brands || {})[cat()] || {}; }
    function refill() {
      fill($brand, Object.keys(brands()), 'هر برند'); fill($model, [], 'هر مدل'); $model.prop('disabled', true);
      $f.find('[data-cats]').each(function () {
        var cats = String($(this).data('cats') || ''), on = !cats || cats.split(',').indexOf(cat()) > -1;
        $(this).prop('hidden', !on); if (!on) $(this).find('select,input').val('');
      });
    }
    $f.on('change', '[name=category]', function () { refill(); preview(); });
    $brand.on('change', function () { var m = brands()[$brand.val()] || []; fill($model, m, 'هر مدل'); $model.prop('disabled', !$brand.val()); });
    $f.on('input', '[data-money-field]', function () { var d = digits(this.value); this.value = d ? App.money(d) : ''; });

    function params() {
      var p = {}; $f.serializeArray().forEach(function (x) { var v = $.trim(x.value); if (!v) return; p[x.name] = /price_max|km_max/.test(x.name) ? digits(v) : v; });
      return p;
    }
    /* پیش‌نمایش زنده */
    function preview() {
      clearTimeout(timer); timer = setTimeout(function () {
        if (xhr) xhr.abort();
        xhr = $.getJSON($f.data('preview-url'), params()).done(function (r) {
          var $l = $('#pvList').empty();
          if (!r.count) { $('#pvText').text('هنوز مورد مشابهی نداریم؛ رادار را فعال کنید تا به‌محض ثبت آگهی خبرتان کنیم.'); return; }
          $('#pvText').html('هم‌اکنون <b>' + App.toFa(r.count) + '</b> آگهی مشابه داریم؛ بهترین تطابق <b>' + App.toFa(r.best) + '٪</b>');
          r.top.forEach(function (t) { $l.append($('<span class="rd-pv-i">').append($('<b>').text(App.toFa(t.percent) + '٪')).append(document.createTextNode(' ' + t.title))); });
        });
      }, 300);
    }
    $f.on('input change', 'select,input,textarea', preview);

    $f.on('submit', function (e) {
      e.preventDefault(); var $b = $('#rdSubmit'); App.loading($b);
      ajax('POST', $f.data('store-url'), params()).done(function (r) {
        App.toast('رادار فعال شد؛ تا وقتی حذفش نکنید برایتان جست‌وجو می‌کند', 'success', 1800);
        setTimeout(function () { location.href = r.url || $f.data('after'); }, 700);
      }).fail(function () { App.toast('ثبت رادار ناموفق بود؛ دوباره تلاش کنید', 'error'); App.loading($b, false); });
    });
    refill(); preview();
  })();

  /* ---------------- ۲) لیست و نمایش: حذف ---------------- */
  var $dlg = $('#rdConfirm'), target = null;
  if ($dlg.length) {
    var modal = window.bootstrap ? bootstrap.Modal.getOrCreateInstance($dlg[0]) : null;
    $(document).on('click', '[data-del]', function () { target = $(this).closest('[data-radar]'); modal ? modal.show() : $('#rdConfirmYes').trigger('click'); });
    $('#rdConfirmYes').on('click', function () {
      if (!target || !target.length) return; var $t = target, $b = $(this); App.loading($b);
      ajax('DELETE', $t.data('delete-url')).done(function () {
        if (modal) modal.hide(); App.toast('رادار حذف شد', 'success', 1600);
        var after = $t.data('after'); if (after) { setTimeout(function () { location.href = after; }, 600); return; }
        $t.fadeOut(180, function () { $t.remove(); if (!$('#rdList [data-radar]').length) $('#rdEmpty').removeClass('d-none'); });
      }).fail(function () { App.toast('حذف ناموفق بود', 'error'); }).always(function () { App.loading($b, false); });
    });
  }
  /* تب «تطابق بالا» در صفحه‌ی نمایش */
  $('#rdTabs').on('click', 'button', function () {
    var min = +$(this).data('min'), n = 0; $(this).addClass('on').siblings().removeClass('on');
    $('#rdResults .rd-match').each(function () { var ok = +$(this).data('percent') >= min; $(this).toggle(ok); if (ok) n++; });
    $('#rdNone').toggleClass('d-none', n > 0);
  });

  /* ---------------- ۳) پنل نمایشگاه‌دار ---------------- */
  var $list = $('#rdBuyer');
  if ($list.length) (function () {
    var state = { cat: 'all', city: '', sort: 'new', mine: false };
    function apply() {
      var $c = $list.children('.rd-card'), n = 0;
      $c.each(function () {
        var $x = $(this), ok = (state.cat === 'all' || $x.data('cat') === state.cat) && (!state.city || $x.data('city') === state.city) && (!state.mine || +$x.data('mine') === 1);
        $x.toggle(ok); if (ok) n++;
      });
      var key = { new: function (a) { return +$(a).data('hours'); }, match: function (a) { return -$(a).data('match'); }, budget: function (a) { return -$(a).data('budget'); } }[state.sort];
      $c.sort(function (a, b) { return key(a) - key(b); }).appendTo($list);
      $('#rdCount').text(App.toFa(n)); $('#rdBuyerEmpty').toggleClass('d-none', n > 0);
    }
    $('#rdFilter').on('click', '[data-cat]', function () { state.cat = $(this).data('cat'); $(this).addClass('on').siblings().removeClass('on'); apply(); });
    $('#fCity').on('change', function () { state.city = this.value; apply(); });
    $('#fSort').on('change', function () { state.sort = this.value; apply(); });
    $('#fMine').on('change', function () { state.mine = this.checked; apply(); });
    $list.on('click', '[data-offer]', function () {
      var $b = $(this); if (!window.confirm('«' + $b.data('title') + '» برای این خریدار پیشنهاد شود؟')) return; App.loading($b);
      ajax('POST', $b.data('url')).done(function () {
        $b.replaceWith('<button type="button" class="btn btn-soft w-100" disabled>پیشنهاد ارسال شد ✓</button>'); App.toast('پیشنهاد برای خریدار ارسال شد', 'success');
      }).fail(function () { App.toast('ارسال ناموفق بود', 'error'); App.loading($b, false); });
    });
    apply();
  })();
})(jQuery);
