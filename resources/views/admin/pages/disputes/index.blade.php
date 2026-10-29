@extends('admin.layouts.app')
@section('title', 'اختلاف‌ها')

@section('content')
@php
  $tabs = ['all' => 'همه', 'open' => 'باز', 'review' => 'در حال بررسی', 'resolved' => 'حل‌شده'];
  $counts = array_count_values(array_column($disputes, 'status'));
  $heldSum = array_sum(array_map(fn ($x) => $x['status'] !== 'resolved' ? $x['deposit']['amount'] : 0, $disputes));
  $by = ['buyer' => 'خریدار', 'dealer' => 'نمایشگاه‌دار'];
@endphp

<x-admin.page-head title="اختلاف‌ها" subtitle="بررسی شکایت خریدار و نمایشگاه‌دار و تعیین تکلیف بیعانه" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی اختلاف‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="alert-triangle" tone="sand" label="اختلاف باز" :value="$counts['open'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="search" tone="peach" label="در حال بررسی" :value="$counts['review'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="check-circle" tone="sage" label="حل‌شده" :value="$counts['resolved'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="lock" tone="sky" label="مبلغ مسدود" :value="$heldSum" unit="تومان" :money="true" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر اختلاف‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی خودرو، کاربر یا کد…" aria-label="جستجو در اختلاف‌ها" autocomplete="off">
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت اختلاف">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($disputes) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($disputes) }}</b> پرونده</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست اختلاف‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>پرونده</th><th>شاکی</th><th>مبلغ بیعانه</th><th>ثبت</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($disputes as $x)
            <tr data-row data-status="{{ $x['status'] }}" data-search="{{ $x['deposit']['title'] }} {{ $x['deposit']['buyer']['name'] }} {{ $x['deposit']['dealer'] }} {{ $x['code'] }} {{ $x['subject'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="thumb" style="background-image:url('{{ $x['deposit']['image'] }}')"></span>
                  <div><b>{{ $x['subject'] }}</b><small>{{ $x['deposit']['title'] }} • <span class="tnum" dir="ltr">{{ $x['code'] }}</span></small></div>
                </div>
              </td>
              <td data-label="شاکی">{{ $by[$x['by']] }}<small class="d-block text-muted">{{ $x['by'] === 'buyer' ? $x['deposit']['buyer']['name'] : $x['deposit']['dealer'] }}</small></td>
              <td data-label="مبلغ بیعانه" class="price-cell"><x-admin.money :value="$x['deposit']['amount']" /></td>
              <td data-label="ثبت"><span data-jdate="{{ $x['opened_at'] }}" data-jfmt="date"></span>@if($x['status'] !== 'resolved')<small class="d-block text-muted"><span data-fa>{{ $x['age_hours'] }}</span> ساعت پیش</small>@endif</td>
              <td data-label="وضعیت"><x-admin.status :status="$x['status']" /></td>
              <td class="td-act"><div class="row-actions"><a class="btn {{ $x['status'] === 'resolved' ? 'btn-soft' : 'btn-ink' }} btn-sm" href="{{ route('admin.disputes.show', $x['id']) }}">{{ $x['status'] === 'resolved' ? 'مشاهده' : 'بررسی پرونده' }}</a></div></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="پرونده‌ای پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
  </section>
</div>
@endsection
