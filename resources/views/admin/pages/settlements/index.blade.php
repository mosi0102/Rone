@extends('admin.layouts.app')
@section('title', 'تسویه‌ها')

@push('scripts')<script src="{{ asset('admin-assets/js/people.js') }}"></script>@endpush

@section('content')
@php
  $tabs = ['all' => 'همه', 'unpaid' => 'در انتظار پرداخت', 'paid' => 'پرداخت‌شده', 'rejected' => 'ردشده'];
  $counts = array_count_values(array_column($items, 'status'));
  $sum = fn ($st) => array_sum(array_map(fn ($i) => $i['status'] === $st ? $i['amount'] : 0, $items));
@endphp

<x-admin.page-head title="تسویه‌ها" subtitle="درخواست‌های برداشت نمایشگاه‌داران از کیف پول" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی تسویه‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="clock" tone="peach" label="درخواست در انتظار" :value="$counts['unpaid'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="banknote" tone="sky" label="مبلغ در انتظار پرداخت" :value="$sum('unpaid')" unit="تومان" :money="true" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="check-circle" tone="sage" label="پرداخت‌شده" :value="$sum('paid')" unit="تومان" :money="true" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="x-circle" tone="sand" label="ردشده" :value="$counts['rejected'] ?? 0" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر تسویه‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی نمایشگاه، مدیر یا شبا…" aria-label="جستجو در تسویه‌ها" autocomplete="off">
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت تسویه">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($items) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($items) }}</b> درخواست</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست درخواست‌های برداشت">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>نمایشگاه</th><th>مبلغ</th><th>حساب مقصد</th><th>درخواست</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($items as $i)
            <tr data-row data-status="{{ $i['status'] }}" data-search="{{ $i['dealer'] }} {{ $i['owner'] }} {{ $i['iban'] }}">
              <td class="td-main"><div class="cell-main"><span class="lr-ic t-sky"><x-admin.icon name="store" /></span><div><b>{{ $i['dealer'] }}</b><small>{{ $i['owner'] }}</small></div></div></td>
              <td data-label="مبلغ" class="price-cell"><x-admin.money :value="$i['amount']" /></td>
              <td data-label="حساب مقصد">بانک {{ $i['bank'] }}<small class="d-block text-muted tnum" dir="ltr" style="text-align:end">{{ $i['iban'] }}</small></td>
              <td data-label="درخواست"><span data-jdate="{{ $i['at'] }}" data-jfmt="date"></span></td>
              <td data-label="وضعیت">
                <span data-status-cell><x-admin.status :status="$i['status']" /></span>
                <span class="reason-note" data-reason-note @if(!$i['note']) hidden @endif>{{ $i['note'] }}</span>
              </td>
              <td class="td-act">
                <div class="row-actions">
                  <button type="button" class="btn btn-ok btn-sm" data-show="unpaid" data-set="paid" data-reason-modal="payModal" data-title="{{ $i['dealer'] }}" data-done="تسویه ثبت و نمایشگاه‌دار مطلع شد"><x-admin.icon name="check" class="icon-sm" /> ثبت پرداخت</button>
                  <button type="button" class="btn btn-bad btn-sm" data-show="unpaid" data-set="rejected" data-reason-modal="rejectPay" data-title="{{ $i['dealer'] }}" data-done="درخواست رد شد و مبلغ به کیف پول برگشت"><x-admin.icon name="x" class="icon-sm" /> رد</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="درخواستی پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
  </section>
</div>

<x-admin.reason-modal id="payModal" title="ثبت پرداخت تسویه" submit="ثبت پرداخت" icon="check-circle" tone="info" label="کد پیگیری بانکی" placeholder="مثلاً ۷۴۸۲۹۱۰۳">
  <p class="text-muted small mb-3">پس از واریز به حساب مقصد، کد پیگیری را ثبت کنید تا برای نمایشگاه‌دار ارسال شود.</p>
</x-admin.reason-modal>
<x-admin.reason-modal id="rejectPay" title="رد درخواست برداشت" submit="رد درخواست" label="دلیل رد (برای نمایشگاه‌دار ارسال می‌شود)"
  :chips="['شماره‌ی شبا با نام صاحب حساب مطابقت ندارد', 'موجودی قابل برداشت کافی نیست', 'در انتظار تعیین تکلیف اختلاف']">
  <p class="text-muted small mb-3">مبلغ به کیف پول نمایشگاه‌دار برمی‌گردد.</p>
</x-admin.reason-modal>
@endsection
