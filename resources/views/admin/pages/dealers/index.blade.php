@extends('admin.layouts.app')
@section('title', 'نمایشگاه‌داران')

@push('scripts')<script src="{{ asset('admin-assets/js/people.js') }}"></script>@endpush

@section('content')
@php
  $tabs = ['all' => 'همه', 'pending' => 'در انتظار احراز هویت', 'verified' => 'تأییدشده', 'suspended' => 'تعلیق‌شده', 'rejected' => 'ردشده'];
  $counts = array_count_values(array_column($dealers, 'status'));
  $walletSum = array_sum(array_column($dealers, 'wallet'));
@endphp

<x-admin.page-head title="نمایشگاه‌داران" subtitle="بررسی مدارک احراز هویت، تأیید یا رد نمایشگاه‌ها و مدیریت وضعیت آن‌ها" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی نمایشگاه‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="user-check" tone="peach" label="منتظر احراز هویت" :value="$counts['pending'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="badge-check" tone="sage" label="نمایشگاه تأییدشده" :value="$counts['verified'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="alert-triangle" tone="sand" label="تعلیق‌شده" :value="$counts['suspended'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="wallet" tone="sky" label="جمع کیف پول نمایشگاه‌ها" :value="$walletSum" unit="تومان" :money="true" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر نمایشگاه‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی نام نمایشگاه، مدیر، شهر یا موبایل…" aria-label="جستجو در نمایشگاه‌ها" autocomplete="off">
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت نمایشگاه">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($dealers) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($dealers) }}</b> نمایشگاه</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست نمایشگاه‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>نمایشگاه</th><th>مدیر</th><th>آگهی / فروش</th><th>اشتراک</th><th>ثبت‌نام</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($dealers as $d)
            <tr data-row data-status="{{ $d['status'] }}" data-search="{{ $d['name'] }} {{ $d['owner'] }} {{ $d['city'] }} {{ $d['phone'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="thumb" style="background-image:url('{{ $d['logo'] }}')"></span>
                  <div><b>{{ $d['name'] }}</b><small>{{ $d['city'] }}</small></div>
                </div>
              </td>
              <td data-label="مدیر">{{ $d['owner'] }}<small class="d-block text-muted tnum" data-fa>{{ $d['phone'] }}</small></td>
              <td data-label="آگهی / فروش"><span data-fa>{{ $d['ads'] }}</span> آگهی<small class="d-block text-muted"><span data-fa>{{ $d['sales'] }}</span> فروش</small></td>
              <td data-label="اشتراک">@if($d['sub_days'])<span data-fa>{{ $d['sub_days'] }}</span> روز مانده @else<span class="text-muted">ندارد</span>@endif</td>
              <td data-label="ثبت‌نام"><span data-jdate="{{ $d['created_at'] }}" data-jfmt="date"></span></td>
              <td data-label="وضعیت">
                <span data-status-cell><x-admin.status :status="$d['status']" /></span>
                <span class="reason-note" data-reason-note @if(!$d['reason']) hidden @endif>{{ $d['reason'] }}</span>
              </td>
              <td class="td-act">
                <div class="row-actions">
                  <a class="btn btn-soft btn-sm" href="{{ route('admin.dealers.show', $d['id']) }}"><x-admin.icon name="eye" class="icon-sm" /> {{ $d['status'] === 'pending' ? 'بررسی مدارک' : 'جزئیات' }}</a>
                  <button type="button" class="btn btn-ok btn-sm" data-show="pending rejected" data-set="verified" data-confirm="هویت این نمایشگاه تأیید شود؟" data-confirm-ok="تأیید نمایشگاه" data-done="نمایشگاه تأیید شد"><x-admin.icon name="check" class="icon-sm" /> تأیید</button>
                  <button type="button" class="btn btn-bad btn-sm" data-show="pending" data-set="rejected" data-reason-modal="rejectDealer" data-title="{{ $d['name'] }}" data-done="درخواست رد شد"><x-admin.icon name="x" class="icon-sm" /> رد</button>
                  <button type="button" class="btn btn-bad btn-sm" data-show="verified" data-set="suspended" data-reason-modal="suspendDealer" data-title="{{ $d['name'] }}" data-done="نمایشگاه تعلیق شد"><x-admin.icon name="lock" class="icon-sm" /> تعلیق</button>
                  <button type="button" class="btn btn-ok btn-sm" data-show="suspended" data-set="verified" data-confirm="تعلیق این نمایشگاه برداشته شود؟" data-confirm-ok="رفع تعلیق" data-done="نمایشگاه دوباره فعال شد"><x-admin.icon name="undo" class="icon-sm" /> رفع تعلیق</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="نمایشگاهی پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $dealers->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="2" />
  </section>
</div>

<x-admin.reason-modal id="rejectDealer" title="رد احراز هویت" submit="رد درخواست" label="دلیل رد (برای نمایشگاه‌دار ارسال می‌شود)"
  :chips="['تصویر مجوز ناخوانا است', 'مشخصات با مدارک مغایرت دارد', 'مدارک ناقص است']" />
<x-admin.reason-modal id="suspendDealer" title="تعلیق نمایشگاه" submit="تعلیق نمایشگاه" icon="lock" label="دلیل تعلیق (برای نمایشگاه‌دار ارسال می‌شود)"
  :chips="['شکایت مکرر خریداران', 'تخلف از قوانین سایت', 'اطلاعات نادرست در آگهی‌ها']">
  <p class="text-muted small mb-3">آگهی‌های این نمایشگاه تا رفع تعلیق از دید کاربران پنهان می‌شود.</p>
</x-admin.reason-modal>
@endsection
