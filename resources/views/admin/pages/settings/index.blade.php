@extends('admin.layouts.app')
@section('title', 'تنظیمات تجاری')

@push('scripts')<script src="{{ asset('admin-assets/js/settings.js') }}"></script>@endpush

@section('content')
@php
  $a = $s['auction']; $p = $s['pricing']; $m = $s['matching']; $o = $s['payout'];
  $cats = $propertyCats;
  $weights = ['type' => 'نوع ملک', 'location' => 'شهر و محله', 'price' => 'قیمت / رهن', 'area' => 'متراژ', 'rooms' => 'تعداد اتاق', 'extra' => 'امکانات و سال ساخت'];
  // فیلد عددی با پسوند. $f(نام، برچسب، مقدار، پسوند، راهنما)
  $f = function ($name, $label, $val, $unit, $hint = null, $step = '1') {
    return compact('name', 'label', 'val', 'unit', 'hint', 'step');
  };
@endphp

<x-admin.page-head title="تنظیمات تجاری" subtitle="قوانین مزایده، قیمت اشتراک و آگهی فوری و تنظیمات تطابق درخواست‌های ملک" />

{{-- بک‌اندکار: action را به مسیر ذخیره‌ی واقعی وصل کند؛ نام فیلدها همان name هاست --}}
<form id="settingsForm" method="post" action="#" novalidate>
  @csrf
  <div class="dash">
    <div class="dash-main">

      <section class="card-a rise" id="s-auction" style="--d:1">
        <div class="card-head"><h2>مزایده</h2></div>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label" for="a_entry">هزینه‌ی ورود به مزایده</label><div class="input-group"><input class="form-control" id="a_entry" name="auction[entry_fee]" value="{{ $a['entry_fee'] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_step">حداقل گام پیشنهاد</label><div class="input-group"><input class="form-control" id="a_step" name="auction[min_step_percent]" value="{{ $a['min_step_percent'] }}" inputmode="decimal" data-num data-min="0.1" data-max="20"><span class="input-group-text">٪ قیمت پایه</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_ext">تمدید خودکار با پیشنهاد لحظه‌ی آخر</label><div class="input-group"><input class="form-control" id="a_ext" name="auction[extend_minutes]" value="{{ $a['extend_minutes'] }}" inputmode="numeric" data-num data-min="0" data-max="30"><span class="input-group-text">دقیقه</span></div><div class="form-text">۰ یعنی بدون تمدید.</div></div>
          <div class="col-md-4"><label class="form-label" for="a_pay">مهلت مراجعه‌ی برنده برای قرارداد</label><div class="input-group"><input class="form-control" id="a_pay" name="auction[contract_hours]" value="{{ $a['contract_hours'] }}" inputmode="numeric" data-num data-min="1" data-max="168"><span class="input-group-text">ساعت</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_ban">مدت محرومیت پیش‌فرض (عدم مراجعه)</label><div class="input-group"><input class="form-control" id="a_ban" name="auction[ban_days]" value="{{ $a['ban_days'] }}" inputmode="numeric" data-num data-min="1" data-max="365"><span class="input-group-text">روز</span></div></div>
          <div class="col-md-4"><label class="form-label" for="a_dur">حداکثر طول مزایده</label><div class="input-group"><input class="form-control" id="a_dur" name="auction[max_duration_hours]" value="{{ $a['max_duration_hours'] }}" inputmode="numeric" data-num data-min="1" data-max="240"><span class="input-group-text">ساعت</span></div></div>
        </div>
      </section>

      <section class="card-a rise" id="s-pricing" style="--d:3">
        <div class="card-head"><h2>اشتراک و هزینه‌ی آگهی</h2></div>
        <div class="row g-3">
          @foreach(['sub_30' => 'اشتراک ۳۰ روزه', 'sub_90' => 'اشتراک ۹۰ روزه', 'sub_365' => 'اشتراک ۳۶۵ روزه', 'instant_fee' => 'هزینه‌ی آگهی فوری (۲۴ ساعت)', 'auction_create_fee' => 'هزینه‌ی ساخت مزایده'] as $k => $label)
            <div class="col-md-4"><label class="form-label" for="p_{{ $k }}">{{ $label }}</label><div class="input-group"><input class="form-control" id="p_{{ $k }}" name="pricing[{{ $k }}]" value="{{ $p[$k] }}" inputmode="numeric" data-num data-money data-min="0"><span class="input-group-text">تومان</span></div></div>
          @endforeach
        </div>
      </section>

      <section class="card-a rise" id="s-matching" style="--d:4">
        <div class="card-head"><h2>تطابق درخواست‌های ملک</h2></div>
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
        <div class="card-head"><h2>برداشت مشاوران املاک</h2></div>
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
          <a href="#s-auction">مزایده</a><a href="#s-pricing">اشتراک و هزینه‌ها</a><a href="#s-matching">تطابق درخواست‌ها</a><a href="#s-payout">برداشت</a>
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
