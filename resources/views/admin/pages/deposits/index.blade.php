@extends('admin.layouts.app')
@section('title', 'بیعانه‌ها و رزروها')

@push('scripts')<script src="{{ asset('admin-assets/js/deposits.js') }}"></script>@endpush

@section('content')
@php
  $cats = ['car' => ['سواری', 'car'], 'heavy' => ['سنگین', 'truck'], 'bike' => ['موتور', 'bike'], 'bicycle' => ['دوچرخه', 'bicycle']];
  $tabs = ['all' => 'همه', 'held' => 'در امانت', 'dispute' => 'اختلاف', 'released' => 'آزادشده', 'refunded' => 'برگشت‌خورده'];
  $counts = array_count_values(array_column($deposits, 'status'));
  $heldSum = array_sum(array_map(fn ($d) => in_array($d['status'], ['held', 'dispute'], true) ? $d['amount'] : 0, $deposits));
  $releasedSum = array_sum(array_map(fn ($d) => $d['status'] === 'released' ? $d['amount'] : 0, $deposits));
@endphp

<x-admin.page-head title="بیعانه‌ها و رزروها" subtitle="مبالغ در امانت، آزادسازی به نمایشگاه‌دار و برگشت به خریدار" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی بیعانه‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="lock" tone="sky" label="مجموع مبلغ در امانت" :value="$heldSum" unit="تومان" :money="true" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="clock" tone="peach" label="بیعانه‌ی در امانت" :value="$counts['held'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="alert-triangle" tone="sand" label="در اختلاف" :value="$counts['dispute'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="check-circle" tone="sage" label="آزادشده به نمایشگاه‌ها" :value="$releasedSum" unit="تومان" :money="true" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر بیعانه‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی خودرو، خریدار، نمایشگاه یا کد…" aria-label="جستجو در بیعانه‌ها" autocomplete="off">
      </div>
      <div class="seg" role="group" aria-label="نوع فروش">
        <button type="button" class="seg-btn active" data-filter="source" data-value="all" aria-pressed="true">همه</button>
        <button type="button" class="seg-btn" data-filter="source" data-value="instant" aria-pressed="false"><x-admin.icon name="bolt" class="icon-sm" /> فروش فوری</button>
        <button type="button" class="seg-btn" data-filter="source" data-value="auction" aria-pressed="false"><x-admin.icon name="gavel" class="icon-sm" /> مزایده</button>
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت بیعانه">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($deposits) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($deposits) }}</b> مورد</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست بیعانه‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>خودرو</th><th>خریدار</th><th>مبلغ بیعانه</th><th>ثبت / مهلت</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($deposits as $d)
            <tr data-row data-status="{{ $d['status'] }}" data-source="{{ $d['source'] }}" data-search="{{ $d['title'] }} {{ $d['dealer'] }} {{ $d['buyer']['name'] }} {{ $d['code'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="thumb" style="background-image:url('{{ $d['image'] }}')"></span>
                  <div><b>{{ $d['title'] }}</b><small>{{ $d['dealer'] }} • <span class="tnum" dir="ltr">{{ $d['code'] }}</span></small></div>
                </div>
              </td>
              <td data-label="خریدار">{{ $d['buyer']['name'] }}<small class="d-block text-muted tnum" data-fa>{{ $d['buyer']['phone'] }}</small></td>
              <td data-label="مبلغ بیعانه" class="price-cell"><x-admin.money :value="$d['amount']" /><small class="d-block text-muted"><span data-fa>{{ $d['percent'] }}</span>٪ از <x-admin.money :value="$d['price']" :unit="null" /></small></td>
              <td data-label="ثبت / مهلت">
                <small class="text-muted d-block">ثبت</small><span data-jdate="{{ $d['created_at'] }}" data-jfmt="date"></span>
                @if($d['status'] === 'held' && $d['hold_until'])<small class="d-block text-muted mt-1">مهلت: <span class="timer" data-countdown="{{ $d['hold_until'] }}"></span></small>@endif
              </td>
              <td data-label="وضعیت"><span data-status-cell><x-admin.status :status="$d['status']" /></span></td>
              <td class="td-act">
                <div class="row-actions">
                  <a class="btn btn-soft btn-sm" href="{{ route('admin.deposits.show', $d['id']) }}"><x-admin.icon name="eye" class="icon-sm" /> جزئیات</a>
                  <button type="button" class="btn btn-ok btn-sm act-held js-release" data-confirm="بیعانه به کیف پول نمایشگاه‌دار منتقل شود؟" data-confirm-text="این کار قابل بازگشت نیست." data-confirm-ok="آزادسازی بیعانه"><x-admin.icon name="check" class="icon-sm" /> آزادسازی</button>
                  <button type="button" class="btn btn-bad btn-sm act-held js-refund" data-title="{{ $d['title'] }}"><x-admin.icon name="undo" class="icon-sm" /> برگشت</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="موردی پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $deposits->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="3" />
  </section>
</div>

<x-admin.reason-modal id="refundModal" title="برگشت بیعانه به خریدار" submit="برگشت بیعانه" icon="undo" tone="info" label="دلیل برگشت (برای خریدار و نمایشگاه‌دار ارسال می‌شود)"
  :chips="['انصراف نمایشگاه‌دار از فروش', 'فروش خودرو به شخص دیگر', 'مغایرت خودرو با آگهی', 'درخواست خریدار در مهلت مجاز']">
  <p class="text-muted small mb-3">مبلغ بیعانه به کیف پول خریدار برگردانده می‌شود.</p>
</x-admin.reason-modal>
@endsection
