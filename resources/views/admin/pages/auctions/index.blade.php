@extends('admin.layouts.app')
@section('title', 'مزایده‌ها')

@push('scripts')<script src="{{ asset('admin-assets/js/auctions.js') }}"></script>@endpush

@section('content')
@php
  $cats = ['car' => ['سواری', 'car'], 'heavy' => ['سنگین', 'truck'], 'bike' => ['موتور', 'bike']];
  $tabs = ['all' => 'همه', 'live' => 'زنده', 'scheduled' => 'زمان‌بندی‌شده', 'awaiting_pay' => 'منتظر پرداخت', 'defaulted' => 'عدم پرداخت', 'ended' => 'پایان‌یافته', 'cancelled' => 'لغوشده'];
  $counts = array_count_values(array_column($auctions, 'status'));
  $overdue = count(array_filter($auctions, fn ($a) => $a['pay'] === 'overdue'));
@endphp

<x-admin.page-head title="مزایده‌ها" subtitle="پایش مزایده‌های زنده، پرداخت برنده‌ها و مدیریت تخلفات" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی مزایده‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="gavel" tone="sage" label="مزایده‌ی زنده" :value="$counts['live'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="calendar" tone="sky" label="زمان‌بندی‌شده" :value="$counts['scheduled'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="clock" tone="peach" label="منتظر پرداخت برنده" :value="$counts['awaiting_pay'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="alert-triangle" tone="sand" label="مهلت‌گذشته (نیازمند اقدام)" :value="$overdue" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر مزایده‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی خودرو، نمایشگاه یا کد…" aria-label="جستجو در مزایده‌ها" autocomplete="off">
      </div>
      <div class="seg" role="group" aria-label="دسته‌ی خودرو">
        <button type="button" class="seg-btn active" data-filter="type" data-value="all" aria-pressed="true">همه</button>
        @foreach($cats as $slug => [$label, $icon])
          <button type="button" class="seg-btn" data-filter="type" data-value="{{ $slug }}" aria-pressed="false"><x-admin.icon :name="$icon" class="icon-sm" /> {{ $label }}</button>
        @endforeach
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت مزایده">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($auctions) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($auctions) }}</b> مزایده</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست مزایده‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>خودرو</th><th>قیمت پایه</th><th>بالاترین پیشنهاد</th><th>پیشنهادها</th><th>زمان</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($auctions as $a)
            <tr data-row data-status="{{ $a['status'] }}" data-type="{{ $a['cat'] }}" data-search="{{ $a['title'] }} {{ $a['dealer'] }} {{ $a['code'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="thumb" style="background-image:url('{{ $a['image'] }}')"></span>
                  <div><b>{{ $a['title'] }}</b><small>{{ $a['dealer'] }} • کد <span data-fa>{{ $a['code'] }}</span></small></div>
                </div>
              </td>
              <td data-label="قیمت پایه" class="price-cell"><x-admin.money :value="$a['base']" /></td>
              <td data-label="بالاترین پیشنهاد" class="price-cell">
                @if($a['top'])<x-admin.money :value="$a['top']" /><small class="d-block text-muted">{{ $a['leader'] }}</small>@else<span class="text-muted">بدون پیشنهاد</span>@endif
              </td>
              <td data-label="پیشنهادها"><span data-fa>{{ $a['bids_count'] }}</span> پیشنهاد<small class="d-block text-muted"><span data-fa>{{ $a['participants'] }}</span> شرکت‌کننده</small></td>
              <td data-label="زمان">
                @if($a['status'] === 'live')
                  <small class="text-muted d-block">پایان تا</small><span class="timer fw-bold" data-countdown="{{ $a['ends_at'] }}"></span>
                @elseif($a['status'] === 'scheduled')
                  <small class="text-muted d-block">شروع تا</small><span class="timer fw-bold" data-countdown="{{ $a['starts_at'] }}"></span>
                @else
                  <small class="text-muted d-block">پایان</small><span data-jdate="{{ $a['ends_at'] }}" data-jfmt="date"></span>
                @endif
              </td>
              <td data-label="وضعیت">
                <span data-status-cell><x-admin.status :status="$a['status']" /></span>
                @if($a['status'] === 'awaiting_pay')
                  @if($a['pay'] === 'overdue')<span class="reason-note">مهلت پرداخت تمام شده</span>
                  @else<small class="d-block text-muted mt-1">مهلت پرداخت: <span class="timer" data-countdown="{{ $a['pay_deadline'] }}"></span></small>@endif
                @endif
              </td>
              <td class="td-act">
                <div class="row-actions">
                  <a class="btn btn-soft btn-sm" href="{{ route('admin.auctions.show', $a['id']) }}"><x-admin.icon name="eye" class="icon-sm" /> جزئیات</a>
                  <button type="button" class="btn btn-bad btn-sm act-cancel js-cancel" data-title="{{ $a['title'] }}"><x-admin.icon name="x" class="icon-sm" /> لغو</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="مزایده‌ای پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $auctions->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="3" />
  </section>
</div>

<x-admin.reason-modal id="cancelModal" title="لغو مزایده" submit="لغو مزایده" label="دلیل لغو (برای نمایشگاه‌دار و شرکت‌کنندگان ارسال می‌شود)"
  :chips="['درخواست نمایشگاه‌دار', 'مغایرت اطلاعات خودرو', 'تخلف از قوانین سایت', 'مشکل فنی']">
  <p class="text-muted small mb-3">ورودی پرداخت‌شده‌ی شرکت‌کنندگان به کیف پول آن‌ها برگردانده می‌شود.</p>
</x-admin.reason-modal>
@endsection
