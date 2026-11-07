/* ==========================================================================
   map.js — نقشه‌ی آگهی‌ها (Leaflet، فایل‌ها در public/vendor/leaflet)
   ۱) [data-map-pick]   انتخاب لوکیشن در فرم ثبت آگهی: کلیک/کشیدن نشانگر، «موقعیت من»،
                         پرش به محله‌ی انتخاب‌شده. خروجی: input#lat و input#lng
   ۲) [data-map-show]   نمایش یک لوکیشن (صفحه‌ی آگهی)
   ۳) [data-map-browse] مشاهده‌ی آگهی‌ها روی نقشه + فیلتر دسته/شهر/نوع (GET data-url → {items, total})
   تنظیم سرویس نقشه: window.MAP_CFG = {tiles, attribution, center, zoom} (از config/services.php)
   رویدادها برای بک‌اند: map:picked({lat, lng}) روی document
   ========================================================================== */
(function ($) {
  'use strict';
  if (!window.L) return;
  var CFG = $.extend({ tiles: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', attribution: '© OpenStreetMap', center: [35.7219, 51.3347], zoom: 12 }, window.MAP_CFG || {});
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function toFa(s) { return String(s).replace(/\d/g, function (d) { return FA[d]; }); }

  function base(el, opts) {
    var map = L.map(el, $.extend({ zoomControl: true, attributionControl: true, scrollWheelZoom: false }, opts || {}));
    L.tileLayer(CFG.tiles, { maxZoom: 19, attribution: CFG.attribution }).addTo(map);
    // داخل تب/شیت پنهان: پس از نمایش اندازه را دوباره بخوان
    if (window.ResizeObserver) new ResizeObserver(function () { map.invalidateSize(); }).observe(el);
    return map;
  }
  function pin(cls) { return L.divIcon({ className: 'mp-pin ' + (cls || ''), html: '<span></span>', iconSize: [34, 44], iconAnchor: [17, 42] }); }

  /* ---------- ۱) انتخاب لوکیشن ---------- */
  $('[data-map-pick]').each(function () {
    var $el = $(this), $lat = $($el.data('lat-input')), $lng = $($el.data('lng-input'));
    var coords = $el.data('districts') || {}, centers = $el.data('centers') || {};
    var lat = parseFloat($lat.val()), lng = parseFloat($lng.val()), has = !isNaN(lat) && !isNaN(lng);
    var map = base($el.find('.mp-canvas')[0], { center: has ? [lat, lng] : CFG.center, zoom: has ? 16 : CFG.zoom });
    var marker = null;
    function set(ll, pan) {
      if (!marker) {
        marker = L.marker(ll, { draggable: true, icon: pin('is-pick') }).addTo(map);
        marker.on('dragend', function () { set(marker.getLatLng()); });
      } else marker.setLatLng(ll);
      if (pan) map.setView(ll, Math.max(map.getZoom(), 16));
      $lat.val(ll.lat.toFixed(6)); $lng.val(ll.lng.toFixed(6)).trigger('change');
      $el.removeClass('is-invalid').addClass('has-pin');
      $el.find('.mp-coords').text(toFa(ll.lat.toFixed(5)) + '، ' + toFa(ll.lng.toFixed(5)));
      $(document).trigger('map:picked', [{ lat: ll.lat, lng: ll.lng }]);
    }
    if (has) set(L.latLng(lat, lng));
    map.on('click', function (e) { set(e.latlng); });

    $el.find('[data-locate]').on('click', function () {
      var $b = $(this);
      if (!navigator.geolocation) { App.toast('مرورگر شما موقعیت‌یابی را پشتیبانی نمی‌کند', 'error'); return; }
      App.loading($b);
      navigator.geolocation.getCurrentPosition(function (p) { App.loading($b, false); set(L.latLng(p.coords.latitude, p.coords.longitude), true); },
        function () { App.loading($b, false); App.toast('دسترسی به موقعیت داده نشد؛ روی نقشه کلیک کنید', 'warn'); }, { enableHighAccuracy: true, timeout: 10000 });
    });
    // پرش به شهر/محله‌ی انتخاب‌شده در فرم (فقط وقتی هنوز نشانگر نگذاشته)
    $($el.data('city-input')).on('change', function () { var c = centers[this.value]; if (c && !marker) map.setView(c, 13); });
    $($el.data('district-input')).on('change', function () {
      var city = $($el.data('city-input')).val(), d = (coords[city] || {})[this.value];
      if (d) map.setView(d, 15);
    });
    $el.data('mapPick', { map: map, set: set, has: function () { return !!marker; } });
  });

  /* ---------- ۲) نمایش یک لوکیشن ---------- */
  $('[data-map-show]').each(function () {
    var ll = [parseFloat($(this).data('lat')), parseFloat($(this).data('lng'))];
    var map = base(this, { center: ll, zoom: 15, dragging: !L.Browser.mobile, tap: false });
    L.marker(ll, { icon: pin() }).addTo(map);
  });

  /* ---------- ۳) مشاهده‌ی آگهی‌ها روی نقشه ---------- */
  var $b = $('[data-map-browse]');
  if ($b.length) (function () {
    var map = base($b.find('.mb-canvas')[0], { center: CFG.center, zoom: CFG.zoom, scrollWheelZoom: true });
    var layer = L.featureGroup().addTo(map), xhr, $card = $b.find('.mb-card'), $count = $b.find('[data-mb-count]');
    var url = $b.data('url'), state = $.extend({}, $b.data('query') || {});

    function label(it) {
      return L.divIcon({ className: 'mb-price' + (it.sale_type === 'auction' ? ' is-auc' : it.sale_type === 'instant' ? ' is-inst' : ''), html: '<span>' + it.price_short + '</span>', iconSize: null, iconAnchor: [0, 0] });
    }
    function showCard(it) {
      $card.find('img').attr('src', it.image || '').attr('alt', it.title);
      $card.find('.mbc-t').text(it.title);
      $card.find('.mbc-m').text(it.meta);
      $card.find('.mbc-p').text(it.price_text);
      $card.find('.mbc-l').text(it.location);
      $card.find('.mbc-badge').text(it.sale_type === 'auction' ? 'مزایده' : it.sale_type === 'instant' ? 'فوری' : '').prop('hidden', !it.sale_type);
      $card.attr('href', App.safeUrl ? App.safeUrl(it.url) : it.url).prop('hidden', false);
    }
    function load(fit) {
      if (xhr) xhr.abort();
      $b.addClass('is-loading');
      xhr = $.getJSON(url, state).done(function (r) {
        layer.clearLayers(); $card.prop('hidden', true);
        (r.items || []).forEach(function (it) {
          var m = L.marker([it.lat, it.lng], { icon: label(it), title: it.title, keyboard: true });
          m.on('click', function () { $b.find('.mb-price.is-on').removeClass('is-on'); $(m.getElement()).addClass('is-on'); showCard(it); });
          layer.addLayer(m);
        });
        $count.text(toFa(r.total || 0));
        $b.find('[data-mb-empty]').prop('hidden', !!(r.items || []).length);
        if (fit !== false && layer.getLayers().length) map.fitBounds(layer.getBounds(), { padding: [48, 48], maxZoom: 15 });
        // نشانی صفحه همگام با فیلترها (قابل اشتراک‌گذاری)
        if (window.history && history.replaceState) history.replaceState(null, '', location.pathname + (($.param(clean(state))) ? '?' + $.param(clean(state)) : ''));
      }).always(function () { $b.removeClass('is-loading'); });
    }
    function clean(o) { var r = {}; $.each(o, function (k, v) { if (v !== '' && v != null) r[k] = v; }); return r; }

    $b.on('click', '[data-mb-cat]', function () {
      var v = String($(this).data('mb-cat'));
      state.category = state.category === v || v === '' ? '' : v;
      $b.find('[data-mb-cat]').each(function () { var on = String($(this).data('mb-cat')) === (state.category || ''); $(this).toggleClass('on', on).attr('aria-pressed', on); });
      load();
    });
    $b.on('change', '[data-mb-field]', function () { state[this.name] = this.value; load(); });
    map.on('click', function () { $card.prop('hidden', true); $b.find('.mb-price.is-on').removeClass('is-on'); });
    $b.find('[data-locate]').on('click', function () {
      if (!navigator.geolocation) return;
      navigator.geolocation.getCurrentPosition(function (p) { map.setView([p.coords.latitude, p.coords.longitude], 14); }, function () { App.toast('دسترسی به موقعیت داده نشد', 'warn'); });
    });
    load();
  })();
})(jQuery);
