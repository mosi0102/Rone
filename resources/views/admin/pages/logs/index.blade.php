@extends('admin.layouts.app')
@section('title', 'گزارش فعالیت')

@section('content')
@php
  $types = ['listings' => ['آگهی', 'list-checks', 'sky'], 'auctions' => ['مزایده', 'gavel', 'peach'], 'deals' => ['معاملات', 'shield-check', 'sage'], 'people' => ['افراد', 'users', 'sky'],
            'finance' => ['مالی', 'wallet', 'sage'], 'chats' => ['گفتگو', 'message', 'sand'], 'settings' => ['سیستم', 'settings', 'sand']];
  $tabs = ['all' => 'همه'] + array_map(fn ($t) => $t[0], $types);
@endphp

<x-admin.page-head title="گزارش فعالیت" subtitle="ثبت همه‌ی اقدام‌های مدیر برای پیگیری و حسابرسی (فقط خواندنی)" />

<div data-filterable>
  <section class="card-a filters rise" style="--d:1">
    <div class="filters-row">
      <div class="search-pill"><x-admin.icon name="search" /><input type="search" data-filter-search placeholder="جستجو در اقدام یا هدف…" aria-label="جستجو در گزارش" autocomplete="off"></div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="نوع اقدام">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="type" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($logs) }}</b> رویداد</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:2">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>اقدام</th><th>هدف</th><th>زمان</th><th>آی‌پی</th></tr></thead>
        <tbody>
          @foreach($logs as $l)
            @php [$label, $icon, $tone] = $types[$l['type']]; @endphp
            <tr data-row data-type="{{ $l['type'] }}" data-search="{{ $l['action'] }} {{ $l['target'] }}">
              <td class="td-main"><div class="cell-main"><span class="lr-ic t-{{ $tone }}"><x-admin.icon :name="$icon" /></span><div><b>{{ $l['action'] }}</b><small>{{ $label }} • مدیر سایت</small></div></div></td>
              <td data-label="هدف">{{ $l['target'] }}</td>
              <td data-label="زمان"><span data-jdate="{{ $l['at'] }}" data-jfmt="date"></span> <small class="text-muted" data-jdate="{{ $l['at'] }}" data-jfmt="time"></small></td>
              <td data-label="آی‌پی"><span class="tnum text-muted" dir="ltr">{{ $l['ip'] }}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="رویدادی پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $logs->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="12" />
  </section>
</div>
@endsection
