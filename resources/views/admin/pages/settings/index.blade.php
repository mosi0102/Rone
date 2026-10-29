@extends('admin.layouts.app')
@section('title', 'تنظیمات تجاری')

@push('scripts')<script src="{{ asset('admin-assets/js/settings.js') }}"></script>@endpush

@section('content')
@php
  $d = $s['deposit']; $a = $s['auction']; $p = $s['pricing']; $m = $s['matching']; $o = $s['payout'];
  $cats = ['car' => ['سواری', 'car'], 'heavy' => ['سنگین', 'truck'], 'bike' => ['موتور', 'bike'], 'bicycle' => ['دوچرخه', 'bicycle']];
  $weights = ['model' => 'برند و مدل', 'price' => 'قیمت', 'year' => 'سال ساخت', 'mileage' => 'کارکرد', 'city' => 'شهر', 'extra' => 'سایر مشخصات'];
  // فیلد عددی با پسوند. $f(نام، برچسب، مقدار، پسوند، راهنما)
  $f = function ($name, $label, $val, $unit, $hint = null, $step = '1') {
    return compact('name', 'label', 'val', 'unit', 'hint', 'step');
  };
@endphp

<x-admin.page-head title="تنظیمات تجاری" subtitle="درصد بیعانه، قوانین مزایده، قیمت‌ها و تنظیمات تطابق درخواست‌ها" />

{{-- بک‌اندکار: action را به مسیر ذخیره‌ی واقعی وصل کند؛ نام فیلدها همان name هاست --}}
<form id="settingsForm" method="post" action="#" novalidate>
  @csrf
  <div class="dash">
    <div class="dash-main">

      <section class="card-a rise" id="s-deposit" style="--d:1">
        <div class="card-head"><h2>بیعانه</h2></div>
        <p class="text-muted small">درصد بیعانه از قیمت خودرو گرفته می‌شود و تا مشخص‌شدن نتیجه‌ی معامله در کیف پول سایت نگه داشته می‌شود.</p>
        <div class="row g-3">
          @foreach($cats as $slug => [$label, $icon])
            <div class="col-6 col-md-3">
              <label class="form-label" for="dep_{{ $slug }}"><x-admin.icon :name="$icon" class="icon-sm" /> {{ $label }}</label>
              <div class="input-group"><input class="form-control" id="dep_{{ $slug }}" name="deposit[{{ $slug }}]" value="{{ $d[$slug] }}" inputmode="decimal" data-num data-min="0.1" data-max="50"><span class="input-group-text">٪</span></div>
            </div>
          @endforeach
        </div>
        <div class="row g-3 mt-1">
          <div class="col-md-4"><label class="form-label" for="dep_fee">هزینه‌ی خدمات (برگشت‌ناپذیر)</label><div class="input-group"><input class="form-control" id="dep_fee" name="deposit[service_fee]" value="{{ $d['service_fee'] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          <div class="col-md-4"><label class="form-label" for="dep_min">حداقل مبلغ بیعانه</label><div class="input-group"><input class="form-control" id="dep_min" name="deposit[min_amount]" value="{{ $d['min_amount'] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          <div class="col-md-4"><label class="form-label" for="dep_hold">مهلت تعیین‌تکلیف معامله</label><div class="input-group"><input class="form-control" id="dep_hold" name="deposit[hold_hours]" value="{{ $d['hold_hours'] }}" inputmode="numeric" data-num data-min="1" data-max="240"><span class="input-group-text">ساعت</span></div><div class="form-text">پس از این مدت، بیعانه برای بررسی به ادمین ارجاع می‌شود.</div></div>
        </div>
      </section>

      <section class="card-a rise" id="s-auction" style="--d:2">
        <div class="card-head"><h2>مزایده</h2></div>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label" for="a_entry">هزینه‌ی ورود به مزایده</label><div class="input-group"><input class="form-control" id="a_entry" name="auction[entry_fee]" value="{{ $a['entry_fee'] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_step">حداقل گام پیشنهاد</label><div class="input-group"><input class="form-control" id="a_step" name="auction[min_step_percent]" value="{{ $a['min_step_percent'] }}" inputmode="decimal" data-num data-min="0.1" data-max="20"><span class="input-group-text">٪ قیمت پایه</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_ext">تمدید خودکار با پیشنهاد لحظه‌ی آخر</label><div class="input-group"><input class="form-control" id="a_ext" name="auction[extend_minutes]" value="{{ $a['extend_minutes'] }}" inputmode="numeric" data-num data-min="0" data-max="30"><span class="input-group-text">دقیقه</span></div><div class="form-text">۰ یعنی بدون تمدید.</div></div>
          <div class="col-md-4"><label class="form-label" for="a_pay">مهلت پرداخت بیعانه‌ی برنده</label><div class="input-group"><input class="form-control" id="a_pay" name="auction[pay_hours]" value="{{ $a['pay_hours'] }}" inputmode="numeric" data-num data-min="1" data-max="168"><span class="input-group-text">ساعت</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_ban">مدت محرومیت پیش‌فرض (عدم پرداخت)</label><div class="input-group"><input class="form-control" id="a_ban" name="auction[ban_days]" value="{{ $a['ban_days'] }}" inputmode="numeric" data-num data-min="1" data-max="365"><span class="input-group-text">روز</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_dur">حداکثر طول مزایده</label><div class="input-group"><input class="form-control" id="a_dur" name="auction[max_duration_hours]" value="{{ $a['max_duration_hours'] }}" inputmode="numeric" data-num data-min="1" data-max="240"><span class="input-group-text">ساعت</span></div></div>
        </div>
      </section>

      <section class="card-a rise" id="s-pricing" style="--d:3">
        <div class="card-head"><h2>اشتراک و هزینه‌ی آگهی</h2></div>
        <div class="row g-3">
          @foreach(['sub_30' => 'اشتراک ۳۰ روزه', 'sub_90' => 'اشتراک ۹۰ روزه', 'sub_365' => 'اشتراک ۳۶۵ روزه', 'instant_fee' => 'هزینه‌ی فروش فوری (هر آگهی)', 'auction_listing_fee' => 'هزینه‌ی ثبت مزایده (هر آگهی)'] as $k => $label)
            <div class="col-md-4"><label class="form-label" for="p_{{ $k }}">{{ $label }}</label><div class="input-group"><input class="form-control" id="p_{{ $k }}" name="pricing[{{ $k }}]" value="{{ $p[$k] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          @endforeach
        </div>
      </section>

      <section class="card-a rise" id="s-matching" style="--d:4">
        <div class="card-head"><h2>تطابق درخواست‌های خرید</h2></div>
        <div class="row g-3 mb-3">
          <div class="col-md-6"><label class="form-label" for="m_min">حداقل درصد تطابق برای نمایش به کاربر</label><div class="input-group"><input class="form-control" id="m_min" name="matching[min_percent]" value="{{ $m['min_percent'] }}" inputmode="numeric" data-num data-min="10" data-max="100"><span class="input-group-text">٪</span></div></div>
          <div class="col-md-6"><label class="form-label" for="m_max">سقف درخواست فعال هر کاربر</label><div class="input-group"><input class="form-control" id="m_max" name="matching[max_requests]" value="{{ $m['max_requests'] }}" inputmode="numeric" data-num data-min="1" data-max="50"><span class="input-group-text">درخواست</span></div></div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2"><b>وزن معیارها در محاسبه‌ی درصد تطابق</b><span class="weight-sum" data-weight-sum>جمع: <b data-fa>۱۰۰</b>٪</span></div>
        <div class="weights">
          @foreach($weights as $k => $label)
            <div class="weight-row"><label for="w_{{ $k }}">{{ $label }}</label><input type="range" class="form-range" id="w_{{ $k }}" name="matching[w][{{ $k }}]" min="0" max="100" step="5" value="{{ $m['w'][$k] }}" data-weight><output data-fa>{{ $m['w'][$k] }}</output><span>٪</span></div>
          @endforeach
        </div>
        <div class="form-text">جمع وزن‌ها باید دقیقاً ۱۰۰٪ باشد.</div>
      </section>

      <section class="card-a rise" id="s-payout" style="--d:5">
        <div class="card-head"><h2>برداشت نمایشگاه‌داران</h2></div>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label" for="o_min">حداقل مبلغ برداشت</label><div class="input-group"><input class="form-control" id="o_min" name="payout[min_withdraw]" value="{{ $o['min_withdraw'] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          <div class="col-md-6"><label class="form-label" for="o_h">مدت زمان پردازش تسویه</label><div class="input-group"><input class="form-control" id="o_h" name="payout[process_hours]" value="{{ $o['process_hours'] }}" inputmode="numeric" data-num data-min="1" data-max="240"><span class="input-group-text">ساعت</span></div></div>
        </div>
      </section>
    </div>

    <aside class="dash-aside">
      <section class="card-a rise" style="--d:2">
        <div class="card-head"><h2>بخش‌ها</h2></div>
        <nav class="set-nav" aria-label="بخش‌های تنظیمات">
          <a href="#s-deposit">بیعانه</a><a href="#s-auction">مزایده</a><a href="#s-pricing">اشتراک و هزینه‌ها</a><a href="#s-matching">تطابق درخواست‌ها</a><a href="#s-payout">برداشت</a>
        </nav>
      </section>
    </aside>
  </div>

  <div class="sticky-save" data-save-bar hidden>
    <span><x-admin.icon name="info" class="icon-sm" /> تغییرات ذخیره‌نشده دارید</span>
    <button type="button" class="btn btn-soft btn-sm" data-reset>انصراف</button>
    <button type="submit" class="btn btn-ink btn-sm" data-save>ذخیره‌ی تنظیمات</button>
  </div>
</form>
@endsection
