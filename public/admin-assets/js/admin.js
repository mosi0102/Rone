/* ==========================================================================
   admin.js — رفتارهای پایه‌ی پنل ادمین (jQuery)
   API عمومی: window.Admin.toast(), Admin.confirm(), Admin.loading(), Admin.money(), Admin.jfmt()
   ========================================================================== */
(function ($) {
  'use strict';

  var Admin = window.Admin = window.Admin || {};
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- ذخیره‌سازی امن (ممکن است در برخی مرورگرها خطا بدهد) ---------- */
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) { /* نادیده */ } }
  };

  /* ---------- قالب‌بندی ---------- */
  Admin.toFa = function (v) { return String(v).replace(/\d/g, function (d) { return FA[d]; }); };
  Admin.toEn = function (v) { return String(v).replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/[٠-٩]/g, function (d) { return d.charCodeAt(0) - 1632; }); };
  Admin.money = function (n) { return new Intl.NumberFormat('fa-IR').format(Math.round(Number(n) || 0)); };
  Admin.jfmt = function (d, kind) {
    var dt = new Date(d);
    var date = new Intl.DateTimeFormat('fa-IR-u-ca-persian', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(dt);
    var time = new Intl.DateTimeFormat('fa-IR', { hour: '2-digit', minute: '2-digit', hour12: false }).format(dt);
    return kind === 'date' ? date : (kind === 'time' ? time : date + ' • ' + time);
  };

  /* ---------- توست ---------- */
  var toastIcon = { success: 'check-circle', error: 'alert', info: 'info' };
  Admin.toast = function (msg, type, ms) {
    type = type || 'info';
    var $t = $('<div class="toast-s ' + type + '" role="status"><svg class="icon"><use href="#a-' + toastIcon[type] + '"/></svg><span></span></div>');
    $t.find('span').text(msg);
    $('#toastStack').append($t);
    setTimeout(function () { $t.addClass('out'); setTimeout(function () { $t.remove(); }, 250); }, ms || 3200);
  };

  /* ---------- دکمه در حال بارگذاری ---------- */
  Admin.loading = function ($btn, on) { $btn.toggleClass('is-loading', on !== false).prop('disabled', on !== false); };

  /* ---------- مودال تأیید (Promise) ----------
     Admin.confirm({ title, text, ok, cancel, danger }).then(function (yes) { ... }) */
  Admin.confirm = function (o) {
    o = $.extend({ title: 'آیا مطمئن هستید؟', text: '', ok: 'تأیید', cancel: 'انصراف', danger: false }, o);
    var el = document.getElementById('confirmModal');
    if (!el) return $.Deferred().resolve(true).promise();
    var $m = $(el), d = $.Deferred(), decided = false;
    $m.find('.confirm').toggleClass('danger', !!o.danger);
    $m.find('[data-c="title"]').text(o.title);
    $m.find('[data-c="text"]').text(o.text).toggle(!!o.text);
    $m.find('[data-c="ok"]').text(o.ok).toggleClass('btn-danger', !!o.danger).toggleClass('btn-ink', !o.danger);
    $m.find('[data-c="cancel"]').text(o.cancel);
    $m.find('[data-c="icon"] use').attr('href', o.danger ? '#a-alert' : '#a-help');
    var modal = bootstrap.Modal.getOrCreateInstance(el);
    $m.off('.cf');
    $m.on('click.cf', '[data-c="ok"]', function () { decided = true; d.resolve(true); modal.hide(); });
    $m.on('hidden.bs.modal.cf', function () { if (!decided) d.resolve(false); $m.off('.cf'); });
    modal.show();
    return d.promise();
  };

  /* دکمه‌های دارای data-confirm: تأیید بگیر و سپس رویداد admin:confirmed را بفرست
     (بک‌اندکار: به‌جای توست نمایشی، فرم/درخواست واقعی را اینجا ارسال کند) */
  $(document).on('click', '[data-confirm]', function (e) {
    var $b = $(this);
    if ($b.data('confirmed')) return;
    e.preventDefault();
    Admin.confirm({ title: $b.data('confirm'), text: $b.data('confirm-text') || '', ok: $b.data('confirm-ok') || 'تأیید', danger: $b.data('confirm-danger') !== undefined })
      .then(function (yes) {
        if (!yes) return;
        $b.trigger('admin:confirmed');
        if ($b.data('done')) Admin.toast($b.data('done'), 'success');
        var $row = $b.closest('[data-removable]');
        if ($row.length) { $row.css({ transition: 'opacity .3s, transform .3s', opacity: 0, transform: 'translateX(24px)' }); setTimeout(function () { $row.remove(); }, 300); }
      });
  });

  /* ---------- پنجره‌ی دلیل (رد آگهی، مسدودسازی…) ----------
     Admin.reason({ title, text, reasons: [..], ok, danger }).then(function (r) { if (r) r.reason, r.note }) */
  Admin.reason = function (o) {
    o = $.extend({ title: 'ثبت دلیل', text: '', reasons: [], ok: 'ثبت', danger: true }, o);
    var el = document.getElementById('reasonModal'); if (!el) return $.Deferred().resolve({ reason: '', note: '' }).promise();
    var $m = $(el), d = $.Deferred(), decided = false, $sel = $m.find('[data-r="reason"]'), $note = $m.find('[data-r="note"]');
    $m.find('[data-r="title"]').text(o.title); $m.find('[data-r="text"]').text(o.text).toggle(!!o.text);
    $sel.empty().append('<option value="">انتخاب دلیل…</option>');
    $.each(o.reasons, function (i, t) { $sel.append($('<option>').val(t).text(t)); });
    $sel.removeClass('is-invalid').val(''); $note.val('');
    $m.find('[data-r="ok"]').text(o.ok).toggleClass('btn-danger', !!o.danger).toggleClass('btn-ink', !o.danger);
    var modal = bootstrap.Modal.getOrCreateInstance(el); $m.off('.rs');
    $m.on('click.rs', '[data-r="ok"]', function () {
      if (!$sel.val()) { $sel.addClass('is-invalid').trigger('focus'); return; }
      decided = true; d.resolve({ reason: $sel.val(), note: $.trim($note.val()) }); modal.hide();
    });
    $sel.on('change.rs', function () { $sel.removeClass('is-invalid'); });
    $m.on('hidden.bs.modal.rs', function () { if (!decided) d.resolve(null); $m.off('.rs'); $sel.off('.rs'); });
    modal.show(); return d.promise();
  };
  // دکمه‌ی دارای data-reason: دلیل بگیر، رویداد admin:reasoned را بفرست (بک‌اندکار: اینجا درخواست واقعی ارسال شود)
  $(document).on('click', '[data-reason]', function () {
    var $b = $(this);
    Admin.reason({ title: $b.data('reason'), text: $b.data('reason-text') || '', reasons: String($b.data('reasons') || '').split('|').filter(Boolean), ok: $b.data('reason-ok') || 'ثبت' })
      .then(function (r) {
        if (!r) return;
        $b.trigger('admin:reasoned', r);
        if ($b.data('done')) Admin.toast($b.data('done'), 'success');
        var $row = $b.closest('[data-removable]');
        if ($row.length) { $row.css({ transition: 'opacity .3s, transform .3s', opacity: 0, transform: 'translateX(24px)' }); setTimeout(function () { $row.remove(); Admin.bulkSync(); }, 300); }
      });
  });

  /* ---------- انتخاب گروهی در جدول ----------
     <div data-bulk> ... <input class="bulk-all"> ... <input class="bulk-one"> ... <div class="bulk-bar"> <b data-bulk-n> */
  Admin.bulkSync = function () {
    $('[data-bulk]').each(function () {
      var $w = $(this), $one = $w.find('.bulk-one'), n = $one.filter(':checked').length;
      $w.find('[data-bulk-n]').text(Admin.toFa(n));
      $w.find('.bulk-bar').toggleClass('show', n > 0);
      $w.find('.bulk-all').prop('checked', $one.length > 0 && n === $one.length).prop('indeterminate', n > 0 && n < $one.length);
    });
  };
  $(document).on('change', '.bulk-all', function () { $(this).closest('[data-bulk]').find('.bulk-one').prop('checked', this.checked); Admin.bulkSync(); });
  $(document).on('change', '.bulk-one', Admin.bulkSync);
  $(document).on('click', '[data-bulk-clear]', function () { $(this).closest('[data-bulk]').find('.bulk-one, .bulk-all').prop('checked', false); Admin.bulkSync(); });
  // اقدام گروهی: پس از تأیید/دلیل، ردیف‌های انتخاب‌شده حذف می‌شوند (نمایشی). بک‌اندکار شناسه‌ها را از .bulk-one:checked بخواند
  $(document).on('admin:confirmed admin:reasoned', '[data-bulk-action]', function () {
    var $w = $(this).closest('[data-bulk]'), $rows = $w.find('.bulk-one:checked').closest('tr'), n = $rows.length;
    $rows.css({ transition: 'opacity .3s', opacity: 0 }); setTimeout(function () { $rows.remove(); Admin.bulkSync(); }, 300);
    Admin.toast(Admin.toFa(n) + ' مورد ' + ($(this).data('bulk-done') || 'انجام شد'), 'success');
  });

  /* ---------- گالری: <div data-gallery> با .gal-main img و دکمه‌های .gal-thumbs ---------- */
  $(document).on('click', '[data-gallery] .gal-thumbs button', function () {
    var $g = $(this).closest('[data-gallery]'), $img = $g.find('.gal-main img'), src = $(this).data('src');
    $g.find('.gal-thumbs button').removeClass('on'); $(this).addClass('on');
    $img.css('opacity', 0); setTimeout(function () { $img.attr('src', src).css('opacity', 1); }, 140);
  });

  /* فرم فیلتر: با تغییر منوی انتخاب، خودکار ارسال شود */
  $(document).on('change', 'form[data-autosubmit] select', function () { this.form.submit(); });

  /* ---------- ریپل ---------- */
  $(document).on('pointerdown', '.btn', function (e) {
    if (reduceMotion) return;
    var r = this.getBoundingClientRect(), s = Math.max(r.width, r.height);
    var $r = $('<span class="ripple"></span>').css({ width: s, height: s, left: e.clientX - r.left - s / 2, top: e.clientY - r.top - s / 2 });
    $(this).append($r); setTimeout(function () { $r.remove(); }, 600);
  });

  /* ---------- سایدبار: جمع‌شدن (دسکتاپ) و کشویی (موبایل) ---------- */
  var $root = $(document.documentElement);
  function isDesktop() { return window.matchMedia('(min-width: 992px)').matches; }
  $(document).on('click', '[data-sb-toggle]', function () {
    if (isDesktop()) {
      var on = !$root.hasClass('sb-collapsed');
      $root.toggleClass('sb-collapsed', on); store.set('adm-sb', on ? '1' : '0');
    } else {
      $root.toggleClass('sb-open');
    }
  });
  $(document).on('click', '.adm-overlay, .sb-link', function () { $root.removeClass('sb-open'); });
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') $root.removeClass('sb-open');
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); $('#globalSearch').trigger('focus'); }
  });
  $(window).on('resize', function () { if (isDesktop()) $root.removeClass('sb-open'); });

  /* سایه‌ی تاپ‌بار هنگام اسکرول */
  var $tb = $('.adm-topbar');
  $(window).on('scroll', function () { $tb.toggleClass('scrolled', window.scrollY > 6); });

  /* ---------- نمایش اعداد، تاریخ، شمارنده ---------- */
  function formatAll(root) {
    $(root || document).find('[data-money]').each(function () { $(this).text(Admin.money($(this).data('money'))); });
    $(root || document).find('[data-fa]').each(function () { $(this).text(Admin.toFa($(this).text())); });
    $(root || document).find('[data-jdate]').each(function () { var $e = $(this); $e.text(Admin.jfmt($e.data('jdate'), $e.data('jfmt') || 'datetime')); });
  }
  Admin.format = formatAll;

  /* شمارش معکوس: <span class="timer" data-countdown="ISO"> */
  function tickCountdowns() {
    $('[data-countdown]').each(function () {
      var $e = $(this), s = Math.max(0, Math.floor((new Date($e.data('countdown')) - Date.now()) / 1000));
      var h = Math.floor(s / 3600), m = Math.floor(s % 3600 / 60), sec = s % 60;
      var p = function (n) { return (n < 10 ? '0' : '') + n; };
      $e.text(Admin.toFa(p(h) + ':' + p(m) + ':' + p(sec)));
    });
  }

  /* شمارنده‌ی صعودی اعداد: <b data-count="1240" [data-count-money]> */
  function countUp() {
    $('[data-count]').each(function () {
      var $e = $(this), to = Number($e.data('count')) || 0, money = $e.is('[data-count-money]');
      if (reduceMotion || to === 0) return;
      var t0 = null, dur = 1000;
      function step(t) {
        t0 = t0 || t; var k = Math.min(1, (t - t0) / dur), v = to * (1 - Math.pow(1 - k, 3));
        $e.text(money ? Admin.money(v) : Admin.toFa(Math.round(v)));
        if (k < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  }

  /* سلام بر اساس ساعت */
  function greeting() {
    var h = new Date().getHours(), g = h < 5 ? 'شب بخیر' : h < 12 ? 'صبح بخیر' : h < 17 ? 'ظهر بخیر' : h < 20 ? 'عصر بخیر' : 'شب بخیر';
    $('[data-greet]').text(g);
    var parts = {}; new Intl.DateTimeFormat('fa-IR-u-ca-persian', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).formatToParts(new Date()).forEach(function (p) { parts[p.type] = p.value; });
    $('[data-today]').text(parts.weekday + ' ' + parts.day + ' ' + parts.month + ' ' + parts.year);
  }

  /* نوار هفته (شنبه تا جمعه) */
  function weekStrip() {
    var $w = $('[data-week]'); if (!$w.length) return;
    var names = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'], now = new Date(), off = (now.getDay() + 1) % 7, marks = ($w.data('week') + '').split(',');
    var fmt = new Intl.DateTimeFormat('fa-IR-u-ca-persian', { day: 'numeric' }), html = '';
    for (var i = 0; i < 7; i++) {
      var d = new Date(now); d.setDate(now.getDate() - off + i);
      var iso = d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
      var cls = 'wd' + (i === off ? ' today' : '') + (marks.indexOf(String(i)) > -1 ? ' has' : '');
      html += '<div class="' + cls + '" title="' + iso + '"><span>' + names[i] + '</span><b>' + fmt.format(d) + '</b></div>';
    }
    $w.html(html);
  }

  /* ---------- فرم‌ها ---------- */
  $(document).on('click', '[data-eye]', function () {
    var $i = $($(this).data('eye')), show = $i.attr('type') === 'password';
    $i.attr('type', show ? 'text' : 'password');
    $(this).attr('aria-label', show ? 'پنهان‌کردن رمز' : 'نمایش رمز').find('use').attr('href', show ? '#a-eye-off' : '#a-eye');
  });
  // ارقام فارسی/عربی در فیلدهای عددی به لاتین تبدیل می‌شوند (بک‌اند هم باید نرمال کند)
  $(document).on('input', '[data-latin-digits]', function () { var v = this.value, n = Admin.toEn(v); if (v !== n) this.value = n; });

  $(document).on('submit', 'form[data-demo-submit]', function (e) {
    var form = this; e.preventDefault();
    if (!form.checkValidity()) { form.reportValidity(); return; }
    var $b = $(form).find('[type=submit]'); Admin.loading($b);
    setTimeout(function () { window.location.href = $(form).data('demo-submit'); }, 700);
  });

  /* ---------- برچسب وضعیت (هم‌خوان با x-admin.status) ---------- */
  var STATUS = {
    active: ['ok', 'فعال'], pending: ['warn', 'در انتظار تأیید'], rejected: ['bad', 'ردشده'], suspended: ['bad', 'تعلیق‌شده'],
    expired: ['', 'منقضی'], sold: ['info', 'فروخته‌شده'], normal: ['', 'فروش عادی'], instant: ['warn', 'فروش فوری'], auction: ['info', 'مزایده'],
    live: ['bad live', 'زنده'], scheduled: ['info', 'زمان‌بندی‌شده'], ended: ['', 'پایان‌یافته'], awaiting_pay: ['warn', 'منتظر پرداخت برنده'], defaulted: ['bad', 'عدم پرداخت'], cancelled: ['', 'لغوشده'],
    held: ['info', 'در امانت'], flagged: ['bad', 'پرچم‌شده'], reviewed: ['ok', 'بررسی‌شده'], unpaid: ['warn', 'در انتظار پرداخت'], paid: ['ok', 'پرداخت‌شده'], released: ['ok', 'آزادشده'], refunded: ['', 'برگشت‌خورده'], dispute: ['bad', 'اختلاف'],
    open: ['bad', 'باز'], review: ['warn', 'در حال بررسی'], resolved: ['ok', 'حل‌شده']
  };
  Admin.statusHtml = function (st) { var m = STATUS[st] || ['', st]; return '<span class="badge-s ' + m[0] + '">' + m[1] + '</span>'; };

  /* ---------- مودال اقدام با دلیل (x-admin.reason-modal) ----------
     Admin.askReason('modalId', 'زیرعنوان').then(function (reason, $modal) {...}) — با انصراف، Promise رد می‌شود */
  Admin.askReason = function (id, subtitle) {
    var el = document.getElementById(id), $m = $(el), d = $.Deferred(), done = false;
    var $ta = $m.find('textarea'), $ok = $m.find('[data-r="submit"]'), need = $m.find('.modal-body-a').data('required') !== 0;
    $m.find('[data-r="title"]').text(subtitle || '');
    $ta.val(''); $m.find('.reason-chip').removeClass('active'); $ok.prop('disabled', need);
    var modal = bootstrap.Modal.getOrCreateInstance(el);
    $m.off('.rs');
    $m.on('click.rs', '.reason-chip', function () { $(this).addClass('active').siblings().removeClass('active'); $ta.val($(this).text()).trigger('input').trigger('focus'); });
    $m.on('input.rs', 'textarea', function () { $ok.prop('disabled', need && $.trim(this.value).length < 3); });
    $m.on('click.rs', '[data-r="submit"]', function () { done = true; d.resolve($.trim($ta.val()), $m); modal.hide(); });
    $m.on('hidden.bs.modal.rs', function () { if (!done) d.reject(); $m.off('.rs'); });
    modal.show();
    return d.promise();
  };

  /* ---------- لیست قابل فیلتر (سمت کاربر) ----------
     ساختار: <div data-filterable> ... <tr data-row data-status=".." data-search="متن جستجو"> ... </div>
     کنترل‌ها: دکمه [data-filter="status"][data-value="pending"] ، select[data-filter="sale"] ، input[data-filter-search]
     بک‌اندکار: این فیلترها را به پارامتر query و فیلتر سمت سرور تبدیل کند. */
  function normText(v) { return Admin.toEn(String(v == null ? '' : v)).toLowerCase().replace(/ي/g, 'ی').replace(/ك/g, 'ک'); }
  function initFilterable($root) {
    var state = {}, q = '', timer;
    function rows() { return $root.find('[data-row]'); }
    function counts() {
      var $r = rows();
      $root.find('[data-count-for]').each(function () {
        var p = String($(this).data('count-for')).split(':'), n = p[1] === 'all' ? $r.length : $r.filter(function () { return String($(this).attr('data-' + p[0])) === p[1]; }).length;
        $(this).text(Admin.toFa(n));
      });
    }
    function apply() {
      var shown = 0;
      rows().each(function () {
        var $r = $(this), ok = true;
        $.each(state, function (k, v) { if (v && v !== 'all' && String($r.attr('data-' + k)) !== v) ok = false; });
        if (ok && q && normText($r.attr('data-search')).indexOf(q) < 0) ok = false;
        $r.prop('hidden', !ok); if (ok) shown++;
      });
      $root.find('[data-result-count]').text(Admin.toFa(shown));
      $root.find('[data-empty]').prop('hidden', shown > 0);
      $root.find('[data-table]').prop('hidden', shown === 0);
      $root.trigger('filter:applied');
    }
    $root.on('click', 'button[data-filter]', function () {
      var $b = $(this), g = $b.data('filter');
      $root.find('button[data-filter="' + g + '"]').removeClass('active').attr('aria-pressed', 'false');
      $b.addClass('active').attr('aria-pressed', 'true'); state[g] = String($b.data('value')); apply();
    });
    $root.on('change', 'select[data-filter]', function () { state[$(this).data('filter')] = this.value; apply(); });
    $root.on('input', 'input[data-filter-search]', function () { var v = this.value; clearTimeout(timer); timer = setTimeout(function () { q = normText(v).trim(); apply(); }, 120); });
    $root.on('click', '[data-filter-reset]', function () {
      state = {}; q = ''; $root.find('input[data-filter-search]').val('');
      $root.find('select[data-filter]').val('all');
      $root.find('button[data-filter]').removeClass('active'); $root.find('button[data-filter][data-value="all"]').addClass('active');
      apply();
    });
    $root.find('button[data-filter].active').each(function () { state[$(this).data('filter')] = String($(this).data('value')); });
    $root.find('select[data-filter]').each(function () { state[$(this).data('filter')] = this.value; });
    $root.data('filterable', { apply: apply, counts: counts });
    counts(); apply();
  }
  Admin.refreshFilterable = function ($root) { var f = $root.data('filterable'); if (f) { f.counts(); f.apply(); } };

  /* ---------- راه‌اندازی ---------- */
  $(function () {
    formatAll(); greeting(); weekStrip(); tickCountdowns(); countUp();
    $('[data-filterable]').each(function () { initFilterable($(this)); });
    setInterval(tickCountdowns, 1000);
    $tb.toggleClass('scrolled', window.scrollY > 6);
  });
})(jQuery);
