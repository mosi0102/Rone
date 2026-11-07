@extends('admin.layouts.app')
@section('title', 'مشاوران املاک')

@push('scripts')<script src="{{ asset('admin-assets/js/people.js') }}"></script>@endpush

@section('content')
@php
  $tabs = ['all' => 'همه', 'pending' => 'در انتظار احراز هویت', 'verified' => 'تأییدشده', 'suspended' => 'تعلیق‌شده', 'rejected' => 'ردشده'];
  $counts = array_count_values(array_column($agencies, 'status'));
  $walletSum = array_sum(array_column($agencies, 'wallet'));
@endphp

<x-admin.page-head title="مشاوران املاک" subtitle="بررسی مدارک احراز هویت، تأیید یا رد آژانس‌ها و مدیریت وضعیت آن‌ها" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی آژانس‌ها">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="user-check" tone="peach" label="منتظر احراز هویت" :value="$counts['pending'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="badge-check" tone="sage" label="آژانس تأییدشده" :value="$counts['verified'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="alert-triangle" tone="sand" label="تعلیق‌شده" :value="$counts['suspended'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="wallet" tone="sky" label="جمع کیف پول آژانس‌ها" :value="$walletSum" unit="تومان" :money="true" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر آژانس‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی نام آژانس، مدیر، شهر یا موبایل…" aria-label="جستجو در آژانس‌ها" autocomplete="off">
      </div>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت آژانس">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($agencies) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($agencies) }}</b> آژانس</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست آژانس‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>آژانس</th><th>مدیر</th><th>آگهی / فروش</th><th>اشتراک</th><th>ثبت‌نام</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($agencies as $d)
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
                  <a class="btn btn-soft btn-sm" href="{{ route('admin.agencies.show', $d['id']) }}"><x-admin.icon name="eye" class="icon-sm" /> {{ $d['status'] === 'pending' ? 'بررسی مدارک' : 'جزئیات' }}</a>
                  <button type="button" class="btn btn-ok btn-sm" data-show="pending rejected" data-set="verified" data-confirm="هویت این آژانس تأیید شود؟" data-confirm-ok="تأیید آژانس" data-done="آژانس تأیید شد"><x-admin.icon name="check" class="icon-sm" /> تأیید</button>
                  <button type="button" class="btn btn-bad btn-sm" data-show="pending" data-set="rejected" data-reason-modal="rejectAgency" data-title="{{ $d['name'] }}" data-done="درخواست رد شد"><x-admin.icon name="x" class="icon-sm" /> رد</button>
                  <button type="button" class="btn btn-bad btn-sm" data-show="verified" data-set="suspended" data-reason-modal="suspendAgency" data-title="{{ $d['name'] }}" data-done="آژانس تعلیق شد"><x-admin.icon name="lock" class="icon-sm" /> تعلیق</button>
                  <button type="button" class="btn btn-ok btn-sm" data-show="suspended" data-set="verified" data-confirm="تعلیق این آژانس برداشته شود؟" data-confirm-ok="رفع تعلیق" data-done="آژانس دوباره فعال شد"><x-admin.icon name="undo" class="icon-sm" /> رفع تعلیق</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="آژانسی پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $agencies->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="2" />
  </section>
</div>

<x-admin.reason-modal id="rejectAgency" title="رد احراز هویت" submit="رد درخواست" label="دلیل رد (برای مشاور املاک ارسال می‌شود)"
  :chips="['تصویر مجوز ناخوانا است', 'مشخصات با مدارک مغایرت دارد', 'مدارک ناقص است']" />
<x-admin.reason-modal id="suspendAgency" title="تعلیق آژانس" submit="تعلیق آژانس" icon="lock" label="دلیل تعلیق (برای مشاور املاک ارسال می‌شود)"
  :chips="['شکایت مکرر مشتریان', 'تخلف از قوانین سایت', 'اطلاعات نادرست در آگهی‌ها']">
  <p class="text-muted small mb-3">آگهی‌های این آژانس تا رفع تعلیق از دید کاربران پنهان می‌شود.</p>
</x-admin.reason-modal>
@endsection
