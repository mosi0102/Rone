@extends('admin.layouts.app')
@section('title', 'کاربران')

@push('scripts')<script src="{{ asset('admin-assets/js/people.js') }}"></script>@endpush

@section('content')
@php
  $tabs = ['all' => 'همه', 'active' => 'فعال', 'banned' => 'مسدود'];
  $counts = array_count_values(array_column($users, 'status'));
  $heldSum = array_sum(array_column($users, 'held'));
  $aucBan = count(array_filter($users, fn ($u) => $u['auction_ban_days']));
@endphp

<x-admin.page-head title="کاربران" subtitle="خریداران و شرکت‌کنندگان مزایده؛ کیف پول، محرومیت‌ها و مسدودسازی" />

<section class="row g-3 mb-3" aria-label="خلاصه‌ی کاربران">
  <div class="col-6 col-xl-3 rise" style="--d:1"><x-admin.kpi icon="users" tone="sky" label="کل کاربران" :value="count($users)" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="lock" tone="sand" label="مسدود" :value="$counts['banned'] ?? 0" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="gavel" tone="peach" label="محروم از مزایده" :value="$aucBan" /></div>
  <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="shield-check" tone="sage" label="بیعانه‌ی در امانت" :value="$heldSum" unit="تومان" :money="true" /></div>
</section>

<div data-filterable>
  <section class="card-a filters rise" style="--d:2" aria-label="فیلتر کاربران">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی نام، موبایل یا شهر…" aria-label="جستجو در کاربران" autocomplete="off">
      </div>
      <div class="seg" role="group" aria-label="وضعیت کاربر">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'all' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'all' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($users) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($users) }}</b> کاربر</div>
    </div>
  </section>

  <section class="card-a rise" style="--d:3" aria-label="لیست کاربران">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead><tr><th>کاربر</th><th>کیف پول</th><th>بیعانه‌ی در امانت</th><th>مزایده</th><th>وضعیت</th><th></th></tr></thead>
        <tbody>
          @foreach($users as $u)
            <tr data-row data-status="{{ $u['status'] }}" data-search="{{ $u['name'] }} {{ $u['phone'] }} {{ $u['city'] }}">
              <td class="td-main">
                <div class="cell-main">
                  <span class="avatar">{{ mb_substr($u['name'], 0, 1) }}</span>
                  <div><b>{{ $u['name'] }}</b><small><span class="tnum" data-fa>{{ $u['phone'] }}</span> • {{ $u['city'] }}</small></div>
                </div>
              </td>
              <td data-label="کیف پول" class="price-cell"><x-admin.money :value="$u['wallet']" /></td>
              <td data-label="بیعانه‌ی در امانت" class="price-cell">@if($u['held'])<x-admin.money :value="$u['held']" />@else<span class="text-muted">—</span>@endif</td>
              <td data-label="مزایده"><span data-fa>{{ $u['auctions'] }}</span> شرکت@if($u['auction_ban_days'])<span class="reason-note">محروم از مزایده (<span data-fa>{{ $u['auction_ban_days'] }}</span> روز دیگر)</span>@endif</td>
              <td data-label="وضعیت">
                <span data-status-cell><x-admin.status :status="$u['status']" /></span>
                <span class="reason-note" data-reason-note @if(!$u['reason']) hidden @endif>{{ $u['reason'] }}</span>
              </td>
              <td class="td-act">
                <div class="row-actions">
                  <a class="btn btn-soft btn-sm" href="{{ route('admin.users.show', $u['id']) }}"><x-admin.icon name="eye" class="icon-sm" /> جزئیات</a>
                  <button type="button" class="btn btn-bad btn-sm" data-show="active" data-set="banned" data-reason-modal="banUser" data-title="{{ $u['name'] }}" data-done="کاربر مسدود شد"><x-admin.icon name="lock" class="icon-sm" /> مسدود</button>
                  <button type="button" class="btn btn-ok btn-sm" data-show="banned" data-set="active" data-confirm="مسدودیت این کاربر برداشته شود؟" data-confirm-ok="رفع مسدودیت" data-done="مسدودیت کاربر برداشته شد"><x-admin.icon name="undo" class="icon-sm" /> رفع مسدودی</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div data-empty hidden>
      <x-admin.empty icon="search" title="کاربری پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>
    {{-- بک‌اندکار: با $users->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="5" />
  </section>
</div>

<x-admin.reason-modal id="banUser" title="مسدودسازی کاربر" submit="مسدود کردن" icon="lock" label="دلیل مسدودسازی (برای کاربر ارسال می‌شود)"
  :chips="['انتشار اطلاعات تماس در گفتگو', 'کلاهبرداری یا تلاش برای آن', 'تخلف مکرر از قوانین']">
  <p class="text-muted small mb-3">کاربر مسدود نمی‌تواند وارد حساب شود؛ بیعانه‌های در امانت او تا تعیین تکلیف نگه داشته می‌شود.</p>
</x-admin.reason-modal>
@endsection
