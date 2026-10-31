/* panel.js — ابزارهای پنل نمایشگاه‌دار و فرم‌ها (jQuery)
   تب‌ها، پیش‌نمایش فایل، ورودی قیمت با حروف، آپلودر تصاویر، پیش‌نمایش تاریخ شمسی، ویزارد. */
(function ($) {
  'use strict';

  /* ---------- تب‌ها (با ?tab= در URL) ---------- */
  $('[data-tabs]').each(function () {
    var $tabs = $(this), $panes = $($tabs.data('tabs'));
    function show(name, push) {
      $tabs.find('[data-tab]').removeClass('active').attr('aria-selected', false).filter('[data-tab="' + name + '"]').addClass('active').attr('aria-selected', true);
      $panes.children('[data-pane]').each(function () {
        var on = $(this).data('pane') === name; $(this).toggleClass('d-none', !on);
        if (on) { $(this).css('animation', 'none'); void this.offsetWidth; $(this).css('animation', ''); }
      });
      $(window).trigger('panel:tab', [name]);
      if (push) { var u = new URL(location.href); u.searchParams.set('tab', name); history.replaceState(null, '', u); }
    }
    $tabs.on('click', '[data-tab]', function () { show($(this).data('tab'), true); this.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' }); });
    show($tabs.data('initial') || $tabs.find('[data-tab]').first().data('tab'));
  });

  /* ---------- پیش‌نمایش فایل: <input type=file data-preview="#img"> ---------- */
  $(document).on('change', 'input[type=file][data-preview]', function () {
    var f = this.files && this.files[0], $t = $($(this).data('preview'));
    if (!f) return;
    if (f.size > 5 * 1024 * 1024) { App.toast('حجم فایل بیشتر از ۵ مگابایت است', 'error'); this.value = ''; return; }
    var r = new FileReader(); r.onload = function (e) { $t.attr('src', e.target.result); }; r.readAsDataURL(f);
  });

  /* ---------- مبلغ به حروف ---------- */
  App.moneyWords = function (n) {
    n = Math.floor(Number(n) || 0); if (!n) return '';
    var parts = [], u = [[1e9, 'میلیارد'], [1e6, 'میلیون'], [1e3, 'هزار']];
    u.forEach(function (x) { var q = Math.floor(n / x[0]); if (q) { parts.push(App.toFa(q) + ' ' + x[1]); n -= q * x[0]; } });
    if (n) parts.push(App.toFa(n));
    return parts.join(' و ') + ' تومان';
  };
  $(document).on('input', 'input[data-money-input]', function () {
    var raw = App.toEn ? App.toEn(this.value) : this.value;
    var digits = String(raw).replace(/[۰-۹]/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d); }).replace(/\D/g, '');
    this.value = digits ? App.money(digits) : '';
    $($(this).data('words')).text(App.moneyWords(digits));
    $(this).data('raw', digits);
  });

  /* ---------- شمارنده کاراکتر ---------- */
  $(document).on('input', 'textarea[data-count]', function () {
    var max = Number($(this).attr('maxlength')) || 0;
    $($(this).data('count')).text(App.toFa(this.value.length) + (max ? ' / ' + App.toFa(max) : ''));
  });

  /* ---------- پیش‌نمایش تاریخ شمسی برای datetime-local ---------- */
  $(document).on('input change', 'input[data-jpreview]', function () {
    var v = this.value, $o = $($(this).data('jpreview'));
    $o.text(v ? App.jdate(v, true) : '');
  });

  /* ---------- نوع فروش: نمایش پنل متناظر ---------- */
  $(document).on('change', 'input[name=sale_type]', function () {
    var v = $('input[name=sale_type]:checked').val();
    $('[data-sale-panel]').each(function () { $(this).toggleClass('d-none', $(this).data('sale-panel') !== v); });
    $(window).trigger('listing:sale-type', [v]);
  });
  $('input[name=sale_type]:checked').trigger('change');

  /* ---------- آپلودر تصاویر آگهی ---------- */
  $('[data-uploader]').each(function () {
    var $root = $(this), $grid = $root.find('.up-grid'), $input = $root.find('input[type=file]'), max = Number($root.data('max')) || 12, files = [];
    function render() {
      $grid.children('.up-item').remove();
      files.forEach(function (it, i) {
        var $el = $('<div class="up-item' + (i === 0 ? ' is-cover' : '') + '"><img alt=""><button type="button" class="up-x" aria-label="حذف"><svg class="icon icon-sm"><use href="#i-x"/></svg></button>' +
          (i === 0 ? '<span class="up-cover">تصویر اصلی</span>' : '<button type="button" class="up-make" aria-label="تصویر اصلی">اصلی کن</button>') + '</div>');
        $el.find('img').attr('src', it.src);
        $el.find('.up-x').on('click', function () { files.splice(i, 1); render(); });
        $el.find('.up-make').on('click', function () { var x = files.splice(i, 1)[0]; files.unshift(x); render(); });
        $grid.children('.up-add').before($el);
      });
      $root.find('.up-count').text(App.toFa(files.length) + ' از ' + App.toFa(max));
      $root.find('.up-add').toggle(files.length < max);
      $(document).trigger('listing:images', [files.map(function (f) { return f.file || f.src; })]);
    }
    // تصاویر موجود (ویرایش)
    ($root.data('existing') || []).forEach(function (u) { files.push({ src: u, file: null }); });
    $input.on('change', function () {
      [].slice.call(this.files).forEach(function (f) {
        if (files.length >= max) return;
        if (!/^image\//.test(f.type)) return;
        if (f.size > 8 * 1024 * 1024) { App.toast('هر تصویر حداکثر ۸ مگابایت', 'error'); return; }
        var r = new FileReader(); r.onload = function (e) { files.push({ src: e.target.result, file: f }); render(); }; r.readAsDataURL(f);
      });
      this.value = '';
    });
    $root.on('dragover', function (e) { e.preventDefault(); $root.addClass('is-drag'); }).on('dragleave drop', function () { $root.removeClass('is-drag'); })
      .on('drop', function (e) { e.preventDefault(); $input[0].files = e.originalEvent.dataTransfer.files; $input.trigger('change'); });
    render();
  });

  /* ---------- ویزارد چندمرحله‌ای ---------- */
  $('[data-wizard]').each(function () {
    var $w = $(this), $steps = $w.find('[data-step]'), $dots = $w.find('[data-dot]'), cur = 0;
    function go(i, quiet) {
      cur = Math.max(0, Math.min($steps.length - 1, i));
      $steps.addClass('d-none').eq(cur).removeClass('d-none');
      $dots.each(function (k) { $(this).toggleClass('done', k < cur).toggleClass('active', k === cur); });
      $w.find('.wz-bar i').each(function (k) { $(this).css('width', k < cur ? '100%' : '0'); });
      if (!quiet) $w[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function valid() {
      var ok = true; $steps.eq(cur).find('[required]').each(function () {
        var bad = this.type === 'file' ? !this.files.length : (this.type === 'checkbox' ? !this.checked : !$.trim(this.value));
        $(this).toggleClass('is-invalid', bad); if (bad && ok) { ok = false; this.focus && this.focus(); }
      });
      if (!ok) App.toast('لطفاً فیلدهای الزامی را کامل کنید', 'error', 2200);
      return ok;
    }
    $w.on('click', '[data-next]', function () { if (valid()) go(cur + 1); });
    $w.on('click', '[data-prev]', function () { go(cur - 1); });
    $w.on('input change', '.is-invalid', function () { $(this).removeClass('is-invalid'); });
    go(0, true);
    $w.data('go', go);
  });

  /* ---------- نمودار میله‌ای / قیف: انیمیشن هنگام دیده شدن ---------- */
  function animateIn(el) { el.classList.add('in'); }
  $(function () {
    var els = document.querySelectorAll('[data-animate-in]');
    if (!('IntersectionObserver' in window)) { [].forEach.call(els, animateIn); return; }
    var io = new IntersectionObserver(function (en) { en.forEach(function (x) { if (x.isIntersecting) { animateIn(x.target); io.unobserve(x.target); } }); }, { threshold: .3 });
    [].forEach.call(els, function (e) { io.observe(e); });
    $(window).on('panel:tab', function () { setTimeout(function () { [].forEach.call(document.querySelectorAll('[data-animate-in]:not(.in)'), function (e) { var r = e.getBoundingClientRect(); if (r.width) animateIn(e); }); }, 60); });
  });
})(jQuery);


/* فرم آگهی: نمایش فیلدهای مخصوص هر دسته (سوخت/گیربکس برای دوچرخه پنهان؛ نوع و سایز طوقه فقط دوچرخه) */
(function ($) {
  'use strict';
  var $f = $('#listingForm'); if (!$f.length) return;
  function sync() {
    var cat = $f.find('[name=category]:checked').val() || 'car';
    $f.find('[data-cats]').each(function () { var on = ($(this).data('cats') + '').split(',').indexOf(cat) > -1; $(this).prop('hidden', !on); });
  }
  $f.on('change', '[name=category]', sync); sync();
})(jQuery);
