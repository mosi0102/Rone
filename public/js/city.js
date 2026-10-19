/* city.js — شهر انتخاب‌شده در localStorage ذخیره می‌شود و روی همه‌ی .js-city-label نمایش داده می‌شود.
   API: AppCity.get(), AppCity.set('تهران')؛ رویداد: app:city */
(function ($) {
  var KEY = 'city';
  var AppCity = window.AppCity = {
    get: function () { try { return localStorage.getItem(KEY); } catch (e) { return null; } },
    set: function (c) { try { localStorage.setItem(KEY, c); } catch (e) {} $('.js-city-label').text(c); $(document).trigger('app:city', [c]); }
  };
  $(function () { var c = AppCity.get(); if (c) $('.js-city-label').text(c); });
})(jQuery);
