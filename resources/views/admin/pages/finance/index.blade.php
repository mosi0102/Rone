@extends('admin.layouts.app')
@section('title', 'مالی و تراکنش‌ها')

@section('content')
@php
  $types = ['entry' => ['ورودی مزایده', 'gavel', 'sky'], 'deposit' => ['بیعانه', 'shield-check', 'sage'], 'fee' => ['کارمزد خدمات', 'receipt', 'peach'], 'subscription' => ['اشتراک', 'badge-check', 'sky'],
            'release' => ['آزادسازی بیعانه', 'check-circle', 'sage'], 'refund' => ['برگشت بیعانه', 'undo', 'sand'], 'withdraw' => ['برداشت', 'banknote', 'peach']];
  $tabs = ['all' => 'همه', 'entry' => 'ورودی مزایده', 'deposit' => 'بیعانه', 'fee' => 'کارمزد', 'subscription' => 'اشتراک', 'withdraw' => 'برداشت'];
  $paid = array_filter($txs, fn ($t) => $t['status'] === 'paid');
  $income = array_sum(array_map(fn ($t) => in_array($t['type'], ['entry', 'fee', 'subscription'], true) ? $t['amount'] : 0, $paid));
  $escrow = array_sum(array_map(fn ($t) => $t['type'] === 'deposit' ? $t['amount'] : 0, $paid)) + array_sum(array_map(fn ($t) => in_array($t['type'], ['release', 'refund'], true) ? $t['amount'] : 0, $paid));
  $out = abs(array_sum(array_map(fn ($t) => $t['type'] === 'withdraw' ? $t['amount'] : 0, $paid)));
  $failed = count(array_filter($txs, fn ($t) => $t['status'] === 'failed'));
@endphp

<x-admin.page-head title="مالی و تراکنش‌ها" subtitle="درآمد سایت، بیعانه‌های در امانت و گردش کیف پول‌ها" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی مالی">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="trending-up" tone="sage" label="درآمد سایت (ورودی، کارمزد، اشتراک)" :value="$income" unit="تومان" :money="true" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="lock" tone="sky" label="مانده‌ی بیعانه در امانت" :value="max($escrow, 0)" unit="تومان" :money="true" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="banknote" tone="peach" label="برداشت‌های پرداخت‌شده" :value="$out" unit="تومان" :money="true" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="alert-triangle" tone="sand" label="پرداخت ناموفق" :value="$failed" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر تراکنش‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی شرح، نام یا کد پیگیری…" aria-label="جستجو در تراکنش‌ها" autocomplete="off">
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="نوع تراکنش">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="type" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($txs) }}</b> تراکنش</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست تراکنش‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>تراکنش</th><th>طرف حساب</th><th>مبلغ</th><th>زمان</th><th>وضعیت</th></tr></thead>
        <tbody>
          @foreach($txs as $t)
            @php [$label, $icon, $tone] = $types[$t['type']]; @endphp
            <tr data-row data-type="{{ $t['type'] }}" data-status="{{ $t['status'] }}" data-search="{{ $t['title'] }} {{ $t['party'] }} {{ $t['ref'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="lr-ic t-{{ $tone }}"><x-admin.icon :name="$icon" /></span>
                  <div><b>{{ $t['title'] }}</b><small>{{ $label }} • <span class="tnum" dir="ltr">{{ $t['ref'] }}</span></small></div>
                </div>
              </td>
              <td data-label="طرف حساب">{{ $t['party'] }}</td>
              <td data-label="مبلغ" class="price-cell {{ $t['amount'] < 0 ? 'text-danger' : '' }}"><x-admin.money :value="abs($t['amount'])" />@if($t['amount'] < 0)<small class="d-block text-muted">خروجی</small>@endif</td>
              <td data-label="زمان"><span data-jdate="{{ $t['at'] }}" data-jfmt="date"></span></td>
              <td data-label="وضعیت"><x-admin.status :status="$t['status']" /></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="تراکنشی پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $txs->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="8" />
  </section>
</div>
@endsection
