@extends('admin.layouts.app')
@section('title', 'درخواست‌های خرید')

@push('scripts')<script src="{{ asset('admin-assets/js/requests.js') }}"></script>@endpush

@section('content')
@php
  $cats = ['car' => ['سواری', 'car'], 'heavy' => ['سنگین', 'truck'], 'bike' => ['موتور', 'bike'], 'bicycle' => ['دوچرخه', 'bicycle']];
  $tabs = ['all' => 'همه', 'active' => 'فعال', 'suspended' => 'تعلیق‌شده', 'cancelled' => 'لغوشده'];
  $counts = array_count_values(array_column($requests, 'status'));
  $withMatch = count(array_filter($requests, fn ($r) => $r['status'] === 'active' && $r['best'] > 0));
  $noMatch = ($counts['active'] ?? 0) - $withMatch;
  $pill = fn ($p) => $p >= 85 ? 'ok' : ($p >= 60 ? 'info' : 'warn');
@endphp

<x-admin.page-head title="درخواست‌های خرید" subtitle="درخواست‌های «این مشخصات را می‌خواهم» که کاربران ثبت کرده‌اند و تطابق آن‌ها با آگهی‌ها" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی درخواست‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="target" tone="sky" label="درخواست فعال" :value="$counts['active'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="check-circle" tone="sage" label="دارای آگهی مشابه" :value="$withMatch" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="search" tone="peach" label="بدون آگهی مشابه (تقاضای تأمین‌نشده)" :value="$noMatch" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="x-circle" tone="sand" label="لغو / تعلیق‌شده" :value="($counts['cancelled'] ?? 0) + ($counts['suspended'] ?? 0)" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر درخواست‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی مشخصات، کاربر، شهر یا کد…" aria-label="جستجو در درخواست‌ها" autocomplete="off">
      </div>
      <div class="seg" role="group" aria-label="دسته‌ی خودرو">
        <button type="button" class="seg-btn active" data-filter="type" data-value="all" aria-pressed="true">همه</button>
        @foreach($cats as $slug => [$label, $icon])
          <button type="button" class="seg-btn" data-filter="type" data-value="{{ $slug }}" aria-pressed="false"><x-admin.icon :name="$icon" class="icon-sm" /> {{ $label }}</button>
        @endforeach
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت درخواست">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($requests) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($requests) }}</b> درخواست</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست درخواست‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>درخواست</th><th>کاربر</th><th>بودجه</th><th>آگهی‌های مشابه</th><th>ثبت</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($requests as $r)
            <tr data-row data-status="{{ $r['status'] }}" data-type="{{ $r['cat'] }}" data-search="{{ $r['summary'] }} {{ $r['model'] }} {{ $r['user']['name'] }} {{ $r['city'] }} {{ $r['code'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="thumb"><x-admin.icon :name="$cats[$r['cat']][1]" /></span>
                  <div><b>{{ $r['summary'] }}</b><small>{{ $cats[$r['cat']][0] }} • {{ $r['city'] }} • <span class="tnum" dir="ltr">{{ $r['code'] }}</span></small></div>
                </div>
              </td>
              <td data-label="کاربر">{{ $r['user']['name'] }}<small class="d-block text-muted tnum" data-fa>{{ $r['user']['phone'] }}</small></td>
              <td data-label="بودجه" class="price-cell">تا <x-admin.money :value="$r['budget']" /></td>
              <td data-label="آگهی‌های مشابه">
                @if(count($r['matches']))
                  <span class="match-pill {{ $pill($r['best']) }}"><span data-fa>{{ $r['best'] }}</span>٪ تطابق</span>
                  <small class="d-block text-muted mt-1"><span data-fa>{{ count($r['matches']) }}</span> آگهی</small>
                @else<span class="text-muted">موردی نیست</span>@endif
              </td>
              <td data-label="ثبت"><span data-jdate="{{ $r['created_at'] }}" data-jfmt="date"></span></td>
              <td data-label="وضعیت"><span data-status-cell><x-admin.status :status="$r['status']" /></span></td>
              <td class="td-act">
                <div class="row-actions">
                  <a class="btn btn-soft btn-sm" href="{{ route('admin.requests.show', $r['id']) }}"><x-admin.icon name="eye" class="icon-sm" /> جزئیات</a>
                  <button type="button" class="btn btn-bad btn-sm act-active js-suspend" data-title="{{ $r['summary'] }}"><x-admin.icon name="x" class="icon-sm" /> تعلیق</button>
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
    {{-- بک‌اندکار: با $requests->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="3" />
  </section>
</div>

<x-admin.reason-modal id="suspendModal" title="تعلیق درخواست" submit="تعلیق درخواست" label="دلیل تعلیق (برای کاربر ارسال می‌شود)"
  :chips="['مشخصات نامعتبر یا نامرتبط', 'درخواست تکراری', 'تخلف از قوانین سایت']">
  <p class="text-muted small mb-3">با تعلیق، جستجوی آگهی‌های مشابه و اعلان‌های این درخواست متوقف می‌شود.</p>
</x-admin.reason-modal>
@endsection
