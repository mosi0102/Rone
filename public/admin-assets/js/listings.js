/* ==========================================================================
   listings.js — صفحه‌ی آگهی‌ها: تأیید، رد با دلیل، تعلیق، پنل جزئیات، انتخاب گروهی
   (نمایشی: بک‌اندکار به‌جای تغییر DOM، درخواست واقعی بفرستد و پاسخ را اعمال کند)
   ========================================================================== */
(function ($) {
  'use strict';

  var $root = $('[data-filterable]'), $drawer = $('#listingDrawer'), $reject = $('#rejectModal');
  var $current = null;   // ردیفِ باز در پنل جزئیات
  var drawer = bootstrap.Offcanvas.getOrCreateInstance($drawer[0]);
  var rejectModal = bootstrap.Modal.getOrCreateInstance($reject[0]);

  function rowOf(el) { var $r = $(el).closest('[data-row]'); return $r.length ? $r : $current; }
  function item($row) { return $row.data('item'); }

  /* ---------- تغییر وضعیت یک آگهی ---------- */
  function setStatus($row, status, reason) {
    var it = item($row); it.status = status; it.reason = reason || '';
    $row.attr('data-status', status).data('status', status);
    $row.find('[data-status-cell]').html(Admin.statusHtml(status));
    $row.find('[data-reason-note]').text(reason || '').prop('hidden', !reason);
    $row.find('.js-chk').prop('disabled', status !== 'pending').prop('checked', false);
    $row.removeClass('is-selected').addClass('row-flash'); setTimeout(function () { $row.removeClass('row-flash'); }, 1200);
    Admin.refreshFilterable($root); updateBulk();
  }

  /* ---------- اکشن‌ها ---------- */
  $(document).on('admin:confirmed', '.js-approve', function () { var $r = rowOf(this); setStatus($r, 'active'); drawer.hide(); Admin.toast('آگهی تأیید و منتشر شد', 'success'); });
  $(document).on('admin:confirmed', '.js-suspend', function () { var $r = rowOf(this); setStatus($r, 'suspended'); drawer.hide(); Admin.toast('آگهی تعلیق شد', 'info'); });
  $(document).on('admin:confirmed', '.js-activate', function () { var $r = rowOf(this); setStatus($r, 'active'); drawer.hide(); Admin.toast('آگهی دوباره فعال شد', 'success'); });

  $(document).on('click', '.js-reject', function () {
    var $r = rowOf(this); $reject.data('row', $r);
    $reject.find('[data-r="title"]').text(item($r).title);
    $('#rejectReason').val(''); $reject.find('.reason-chip').removeClass('active'); $('#rejectSubmit').prop('disabled', true);
    drawer.hide(); rejectModal.show();
  });
  $reject.on('click', '.reason-chip', function () {
    $(this).addClass('active').siblings().removeClass('active');
    $('#rejectReason').val($(this).text()).trigger('input').trigger('focus');
  });
  $('#rejectReason').on('input', function () { $('#rejectSubmit').prop('disabled', $.trim(this.value).length < 3); });
  $('#rejectSubmit').on('click', function () {
    var $r = $reject.data('row'); if (!$r) return;
    setStatus($r, 'rejected', $.trim($('#rejectReason').val())); rejectModal.hide();
    Admin.toast('آگهی رد شد و دلیل برای نمایشگاه‌دار ارسال می‌شود', 'info');
  });

  /* ---------- پنل جزئیات ---------- */
  function footButtons(st) {
    var b = '';
    if (st === 'pending') {
      b += '<button type="button" class="btn btn-ink js-approve" data-confirm="این آگهی تأیید و منتشر شود؟" data-confirm-ok="تأیید آگهی"><svg class="icon icon-sm"><use href="#a-check"/></svg> تأیید و انتشار</button>';
      b += '<button type="button" class="btn btn-bad js-reject"><svg class="icon icon-sm"><use href="#a-x"/></svg> رد آگهی</button>';
    } else if (st === 'active') {
      b += '<button type="button" class="btn btn-bad js-suspend" data-confirm="این آگهی تعلیق شود؟" data-confirm-text="آگهی از لیست‌ها پنهان می‌شود." data-confirm-ok="تعلیق آگهی" data-confirm-danger><svg class="icon icon-sm"><use href="#a-lock"/></svg> تعلیق آگهی</button>';
    } else if (st === 'suspended') {
      b += '<button type="button" class="btn btn-ink js-activate" data-confirm="آگهی دوباره فعال شود؟" data-confirm-ok="فعال‌سازی"><svg class="icon icon-sm"><use href="#a-undo"/></svg> فعال‌سازی</button>';
    }
    return b;
  }
  function fill($row) {
    var it = item($row), d = function (k) { return $drawer.find('[data-d="' + k + '"]'); };
    d('title').text(it.title); d('status').html(Admin.statusHtml(it.status)); d('sale').html(Admin.statusHtml(it.sale));
    d('code').text('کد ' + Admin.toFa(it.code)); d('price').text(Admin.money(it.price));
    d('desc').text(it.desc); d('dealer').text(it.dealer); d('city').text(it.city);
    d('reason').text(it.reason ? 'دلیل رد: ' + it.reason : '').prop('hidden', !it.reason);
    d('main').css('background-image', 'url("' + it.images[0] + '")');
    d('thumbs').html($.map(it.images, function (src, i) { return '<button type="button" class="' + (i ? '' : 'active') + '" style="background-image:url(\'' + src + '\')" aria-label="تصویر ' + Admin.toFa(i + 1) + '"></button>'; }).join(''));
    d('specs').html($.map(it.specs, function (s) { return '<div><dt>' + s[0] + '</dt><dd>' + Admin.toFa(s[1]) + '</dd></div>'; }).join(''));
    d('foot').html(footButtons(it.status));
  }
  $(document).on('click', '.js-view', function () { $current = rowOf(this); fill($current); drawer.show(); });
  $drawer.on('click', '[data-d="thumbs"] button', function () {
    var i = $(this).index(); $(this).addClass('active').siblings().removeClass('active');
    $drawer.find('[data-d="main"]').css('background-image', 'url("' + item($current).images[i] + '")');
  });
  // دوبار کلیک روی ردیف هم جزئیات را باز می‌کند
  $root.on('dblclick', '[data-row]', function (e) { if ($(e.target).closest('button,a,input').length) return; $current = $(this); fill($current); drawer.show(); });

  /* ---------- انتخاب گروهی ---------- */
  function selectable() { return $root.find('[data-row]:not([hidden]) .js-chk:not(:disabled)'); }
  function updateBulk() {
    var $sel = $root.find('.js-chk:checked'), n = $sel.length, $all = selectable();
    $('#bulkN').text(Admin.toFa(n)); $('#bulkBar').toggleClass('show', n > 0);
    $root.find('[data-row]').each(function () { $(this).toggleClass('is-selected', $(this).find('.js-chk').prop('checked')); });
    $('#chkAll').prop('checked', $all.length > 0 && $all.length === $all.filter(':checked').length).prop('disabled', $all.length === 0);
  }
  $root.on('change', '.js-chk', updateBulk);
  $('#chkAll').on('change', function () { selectable().prop('checked', this.checked); updateBulk(); });
  $root.on('filter:applied', function () { $root.find('[data-row][hidden] .js-chk').prop('checked', false); updateBulk(); });
  $('#bulkClear').on('click', function () { $root.find('.js-chk').prop('checked', false); updateBulk(); });
  $('#bulkApprove').on('click', function () {
    var $rows = $root.find('.js-chk:checked').closest('[data-row]'), n = $rows.length;
    Admin.confirm({ title: 'تأیید ' + Admin.toFa(n) + ' آگهی؟', text: 'همه‌ی آگهی‌های انتخاب‌شده منتشر می‌شوند.', ok: 'تأیید همه' }).then(function (yes) {
      if (!yes) return;
      $rows.each(function () { setStatus($(this), 'active'); });
      Admin.toast(Admin.toFa(n) + ' آگهی تأیید و منتشر شد', 'success');
    });
  });
  updateBulk();
})(jQuery);
