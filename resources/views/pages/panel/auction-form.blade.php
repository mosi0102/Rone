@extends('layouts.app')
@php $isEdit = (bool) $edit; $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
@section('title', $isEdit ? 'ویرایش مزایده' : 'ساخت مزایده')
@section('page_title', $isEdit ? 'ویرایش مزایده' : 'ساخت مزایده')
@section('page_subtitle', 'قیمت پایه و زمان مزایده را تعیین کنید')
@section('back', route('panel', ['tab' => 'auctions']))
@section('body_class', 'no-bottom-nav panel-page has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}"><link rel="stylesheet" href="{{ asset('css/auction.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script>@endpush

@section('content')
@unless($auth->is_dealer)
  <x-empty-state icon="store" title="ساخت مزایده فقط برای نمایشگاه‌داران است" text="کاربران عادی می‌توانند در مزایده‌ها شرکت کنند و پیشنهاد بدهند." action="ثبت‌نام نمایشگاه" :href="route('dealer.join')" />
@else
<form id="auctionForm" class="form-sections" onsubmit="return false" novalidate>

  <section class="card-s fcard">
    <h3><span class="fc-n">۱</span> انتخاب آگهی <b class="req">*</b></h3>
    <div class="pick-list" id="pickList">
      @foreach($listings as $l)
        <label class="pick">
          <input type="radio" name="listing" value="{{ $l['id'] }}" data-price="{{ $l['price'] }}" {{ (string) $selected === (string) $l['id'] || ($loop->first && !$selected) ? 'checked' : '' }}>
          <span class="pk-body">
            <img src="{{ $l['image'] }}" alt="">
            <span class="pk-t"><b>{{ $l['title'] }}</b><small><x-specs :year="$l['year']" :km="$l['km']" /></small><small>قیمت آگهی: <x-price :value="$l['price']" /></small></span>
            <span class="pk-ck"><x-icon name="check" class="icon-sm" /></span>
          </span>
        </label>
      @endforeach
    </div>
    <p class="form-text mt-2">فقط آگهی‌های فعال و بدون مزایده در فهرست هستند.</p>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۲</span> قیمت و ورودی</h3>
    <div class="fgrid">
      <div>
        <label class="form-label" for="startPrice">قیمت شروع (تومان) <b class="req">*</b></label>
        <input class="form-control form-control-lg ltr" id="startPrice" inputmode="numeric" required data-money-input data-words="#startWords" placeholder="0">
        <div class="form-text price-words" id="startWords"></div>
        <div class="form-text" id="startHint"></div>
      </div>
      <div>
        <label class="form-label" for="stepSel">حداقل افزایش هر پیشنهاد</label>
        <select class="form-select" id="stepSel"><option value="1000000">۱ میلیون تومان</option><option value="5000000" selected>۵ میلیون تومان</option><option value="10000000">۱۰ میلیون تومان</option><option value="50000000">۵۰ میلیون تومان</option></select>
      </div>
      <div>
        <label class="form-label" for="entryFee">ورودی مزایده (تومان)</label>
        <input class="form-control ltr" id="entryFee" inputmode="numeric" data-money-input data-words="#entryWords" value="۵۰٬۰۰۰">
        <div class="form-text" id="entryWords">هر شرکت‌کننده برای ورود این مبلغ را می‌پردازد (جزو قیمت خودرو نیست).</div>
      </div>
    </div>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۳</span> زمان مزایده</h3>
    <div class="fgrid">
      <div>
        <label class="form-label" for="startAt">شروع <b class="req">*</b></label>
        <input type="datetime-local" class="form-control ltr" id="startAt" required data-jpreview="#startJ">
        <div class="form-text text-primary fw-bold" id="startJ"></div>
        <div class="quick-chips" data-target="#startAt">
          <button type="button" data-hours="3">۳ ساعت دیگر</button><button type="button" data-hours="24">فردا همین ساعت</button><button type="button" data-hours="72">۳ روز دیگر</button>
        </div>
      </div>
      <div>
        <label class="form-label" for="endAt">پایان <b class="req">*</b></label>
        <input type="datetime-local" class="form-control ltr" id="endAt" required data-jpreview="#endJ">
        <div class="form-text text-primary fw-bold" id="endJ"></div>
        <div class="quick-chips" id="durChips"><button type="button" data-dur="1">۱ ساعت</button><button type="button" data-dur="2">۲ ساعت</button><button type="button" data-dur="6">۶ ساعت</button><button type="button" data-dur="24">۲۴ ساعت</button></div>
      </div>
    </div>
    <p class="form-text mt-3 mb-0"><x-icon name="info" class="icon-sm" /> شروع حداقل ۲ ساعت بعد باشد. شمارش معکوس برای کاربران فقط ۲۴ ساعت مانده به شروع فعال می‌شود و کاربران تا زمان شروع نمی‌توانند پیشنهاد بدهند.</p>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۴</span> هزینه و انتشار</h3>
    <div class="pay-rows">
      <div class="pay-row"><div><b>هزینه ساخت مزایده</b><small>از کیف پول کسر می‌شود</small></div><x-price :value="$create_fee" /></div>
      <div class="pay-row wallet"><b><x-icon name="wallet" class="icon-sm" /> موجودی کیف پول</b><x-price :value="$auth->wallet" /></div>
    </div>
    @if($auth->wallet < $create_fee)<p class="small text-danger mt-2 mb-0">موجودی کافی نیست؛ ابتدا کیف پول را شارژ کنید.</p>@endif
  </section>

  <div class="sticky-cta fs-cta">
    <div class="fs-sum"><span class="small text-muted-s">هزینه ساخت</span><b><span data-fa>{{ number_format($create_fee) }}</span> تومان</b></div>
    @if($auth->wallet >= $create_fee)
      <button type="button" class="btn btn-primary btn-lg" id="publishAuction">{{ $isEdit ? 'ذخیره تغییرات' : 'ایجاد مزایده' }}</button>
    @else
      <a href="{{ route('account.wallet') }}" class="btn btn-accent btn-lg">شارژ کیف پول</a>
    @endif
  </div>
</form>
@endunless
@endsection

@push('scripts')
<script>
  (function () {
    function pad(n) { return String(n).padStart(2, '0'); }
    function local(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes()); }
    function setStart(h) { var d = new Date(Date.now() + h * 3600e3); d.setMinutes(0, 0, 0); $('#startAt').val(local(d)).trigger('input'); }
    $('.quick-chips[data-target]').on('click', 'button', function () { setStart($(this).data('hours')); $('#startAt').removeClass('is-invalid'); });
    $('#durChips').on('click', 'button', function () {
      var s = $('#startAt').val(); if (!s) { App.toast('ابتدا زمان شروع را انتخاب کنید', 'info'); return; }
      $('#endAt').val(local(new Date(new Date(s).getTime() + $(this).data('dur') * 3600e3))).trigger('input').removeClass('is-invalid');
    });
    // یادآوری: قیمت شروع نسبت به قیمت آگهی
    function hint() { var p = +$('input[name=listing]:checked').data('price') || 0, v = parseInt(String($('#startPrice').val()).replace(/[۰-۹]/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d); }).replace(/\D/g, '') || '0', 10);
      $('#startHint').text(p && v ? (v > p ? 'قیمت شروع از قیمت آگهی بیشتر است.' : 'قیمت آگهی: ' + App.money(p) + ' تومان') : (p ? 'قیمت آگهی: ' + App.money(p) + ' تومان' : '')); }
    $('input[name=listing]').on('change', hint); $('#startPrice').on('input', hint); hint();
    $('#auctionForm').on('input change', '.is-invalid', function () { $(this).removeClass('is-invalid'); });

    $('#publishAuction').on('click', function () {
      var bad = [], now = Date.now(), s = new Date($('#startAt').val()).getTime(), e = new Date($('#endAt').val()).getTime();
      if (!$('#startPrice').val()) bad.push(['#startPrice', 'قیمت شروع را وارد کنید']);
      if (!$('#startAt').val()) bad.push(['#startAt', 'زمان شروع را انتخاب کنید']); else if (s < now + 2 * 3600e3) bad.push(['#startAt', 'شروع باید حداقل ۲ ساعت بعد باشد']);
      if (!$('#endAt').val()) bad.push(['#endAt', 'زمان پایان را انتخاب کنید']); else if (e < s + 30 * 60e3) bad.push(['#endAt', 'مدت مزایده حداقل ۳۰ دقیقه است']);
      $('#auctionForm .is-invalid').removeClass('is-invalid');
      if (bad.length) { bad.forEach(function (b) { $(b[0]).addClass('is-invalid'); }); $(bad[0][0])[0].scrollIntoView({ behavior: 'smooth', block: 'center' }); App.toast(bad[0][1], 'error'); return; }
      var $b = $(this); App.loading($b); $(document).trigger('app:auction-create', [{ edit: {{ $isEdit ? (int) $edit : 'null' }}, listing: $('input[name=listing]:checked').val() }]);
      setTimeout(function () { App.loading($b, false); App.toast('{{ $isEdit ? 'مزایده ذخیره شد' : 'مزایده ایجاد شد' }} (نمایشی)', 'success'); setTimeout(function () { location.href = '{{ route('panel', ['tab' => 'auctions']) }}'; }, 900); }, 1100);
    });
  })();
</script>
@endpush
