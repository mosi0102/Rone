@extends('admin.layouts.app')
@section('title', 'آگهی‌ها')

@push('scripts')<script src="{{ asset('admin-assets/js/listings.js') }}"></script>@endpush

@section('content')
@php
  $cats = ['car' => ['سواری', 'car'], 'heavy' => ['سنگین', 'truck'], 'bike' => ['موتور', 'bike'], 'bicycle' => ['دوچرخه', 'bicycle']];
  $tabs = ['all' => 'همه', 'pending' => 'در انتظار تأیید', 'active' => 'فعال', 'rejected' => 'ردشده', 'suspended' => 'تعلیق‌شده', 'sold' => 'فروخته‌شده', 'expired' => 'منقضی'];
  $counts = array_count_values(array_column($listings, 'status'));
@endphp

<x-admin.page-head title="آگهی‌ها" subtitle="بررسی، تأیید و مدیریت آگهی‌های نمایشگاه‌داران" />

<div data-filterable>
  {{-- ===== فیلترها ===== --}}
  <section class="card-a filters rise" style="--d:1" aria-label="فیلتر آگهی‌ها">
    <div class="filters-row">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی عنوان، نمایشگاه، شهر یا کد آگهی…" aria-label="جستجو در آگهی‌ها" autocomplete="off">
      </div>
      <div class="seg" role="group" aria-label="دسته‌ی خودرو">
        <button type="button" class="seg-btn active" data-filter="type" data-value="all" aria-pressed="true">همه</button>
        @foreach($cats as $slug => [$label, $icon])
          <button type="button" class="seg-btn" data-filter="type" data-value="{{ $slug }}" aria-pressed="false"><x-admin.icon :name="$icon" class="icon-sm" /> {{ $label }}</button>
        @endforeach
      </div>
      <select class="form-select sel" data-filter="sale" aria-label="نوع فروش">
        <option value="all">همه‌ی انواع فروش</option>
        <option value="normal">فروش عادی</option>
        <option value="instant">فروش فوری</option>
        <option value="auction">مزایده</option>
      </select>
    </div>
    <div class="filters-row">
      <div class="seg" role="group" aria-label="وضعیت آگهی">
        @foreach($tabs as $slug => $label)
          <button type="button" class="seg-btn {{ $slug === 'pending' ? 'active' : '' }}" data-filter="status" data-value="{{ $slug }}" aria-pressed="{{ $slug === 'pending' ? 'true' : 'false' }}">{{ $label }} <span class="n" data-count-for="status:{{ $slug }}" data-fa>{{ $slug === 'all' ? count($listings) : ($counts[$slug] ?? 0) }}</span></button>
        @endforeach
      </div>
      <div class="result-count">نمایش <b data-result-count data-fa>{{ count($listings) }}</b> آگهی</div>
    </div>
  </section>

  {{-- ===== جدول ===== --}}
  <section class="card-a rise" style="--d:2" aria-label="لیست آگهی‌ها">
    <div class="tbl-wrap adm-scroll" data-table>
      <table class="tbl tbl-cards">
        <thead>
          <tr>
            <th class="td-chk"><input type="checkbox" class="form-check-input chk" id="chkAll" aria-label="انتخاب همه‌ی آگهی‌های در انتظار"></th>
            <th>آگهی</th><th>دسته</th><th>قیمت</th><th>نوع فروش</th><th>وضعیت</th><th>ثبت</th><th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($listings as $l)
            <tr data-row data-id="{{ $l['id'] }}" data-status="{{ $l['status'] }}" data-type="{{ $l['cat'] }}" data-sale="{{ $l['sale'] }}"
                data-search="{{ $l['title'] }} {{ $l['dealer'] }} {{ $l['city'] }} {{ $l['code'] }}" data-item="{{ json_encode($l, JSON_UNESCAPED_UNICODE) }}">
              <td class="td-chk"><input type="checkbox" class="form-check-input chk js-chk" aria-label="انتخاب آگهی {{ $l['title'] }}" @if($l['status'] !== 'pending') disabled @endif></td>
              <td class="td-main">
                <div class="cell-main">
                  <span class="thumb" style="background-image:url('{{ $l['images'][0] }}')"></span>
                  <div><b>{{ $l['title'] }}</b><small>{{ $l['dealer'] }} • {{ $l['city'] }}</small></div>
                </div>
              </td>
              <td data-label="دسته"><span class="cat-cell"><x-admin.icon :name="$cats[$l['cat']][1]" class="icon-sm" /> {{ $cats[$l['cat']][0] }}</span></td>
              <td data-label="قیمت" class="price-cell"><x-admin.money :value="$l['price']" /></td>
              <td data-label="نوع فروش"><x-admin.status :status="$l['sale']" /></td>
              <td data-label="وضعیت"><span data-status-cell><x-admin.status :status="$l['status']" /></span><span class="reason-note" data-reason-note @if(empty($l['reason'])) hidden @endif>{{ $l['reason'] ?? '' }}</span></td>
              <td data-label="ثبت"><div class="text-muted"><span data-fa>{{ $l['posted'] }}</span><small class="d-block">کد <span data-fa>{{ $l['code'] }}</span></small></div></td>
              <td class="td-act">
                <div class="row-actions">
                  <button type="button" class="btn btn-soft btn-sm btn-sq js-view" aria-label="مشاهده‌ی جزئیات" title="مشاهده‌ی جزئیات"><x-admin.icon name="eye" class="icon-sm" /><span class="d-md-none">مشاهده</span></button>
                  <button type="button" class="btn btn-ok btn-sm act-pending js-approve" data-confirm="این آگهی تأیید و منتشر شود؟" data-confirm-ok="تأیید آگهی"><x-admin.icon name="check" class="icon-sm" /> تأیید</button>
                  <button type="button" class="btn btn-bad btn-sm act-pending js-reject"><x-admin.icon name="x" class="icon-sm" /> رد</button>
                  <button type="button" class="btn btn-bad btn-sm act-active js-suspend" data-confirm="این آگهی تعلیق شود؟" data-confirm-text="آگهی از لیست‌ها پنهان می‌شود." data-confirm-ok="تعلیق آگهی" data-confirm-danger><x-admin.icon name="lock" class="icon-sm" /> تعلیق</button>
                  <button type="button" class="btn btn-ok btn-sm act-suspended js-activate" data-confirm="آگهی دوباره فعال شود؟" data-confirm-ok="فعال‌سازی"><x-admin.icon name="undo" class="icon-sm" /> فعال‌سازی</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div data-empty hidden>
      <x-admin.empty icon="search" title="آگهی‌ای پیدا نشد" text="فیلترها یا عبارت جستجو را تغییر دهید.">
        <button type="button" class="btn btn-soft btn-sm mt-3" data-filter-reset>پاک‌کردن فیلترها</button>
      </x-admin.empty>
    </div>

    {{-- بک‌اندکار: با $listings->links() جایگزین شود --}}
    <x-admin.pagination :page="1" :pages="4" />
  </section>
</div>

{{-- نوار انتخاب گروهی --}}
<div class="bulk-bar" id="bulkBar" role="region" aria-label="عملیات گروهی">
  <span><b id="bulkN" data-fa>۰</b> آگهی انتخاب شد</span>
  <button type="button" class="btn btn-ok btn-sm" id="bulkApprove"><x-admin.icon name="check" class="icon-sm" /> تأیید انتخاب‌شده‌ها</button>
  <button type="button" class="btn btn-link btn-sm" id="bulkClear">لغو</button>
</div>

{{-- پنل جزئیات آگهی --}}
<div class="offcanvas offcanvas-end detail" tabindex="-1" id="listingDrawer" aria-labelledby="drawerTitle">
  <div class="offcanvas-header">
    <h2 class="offcanvas-title" id="drawerTitle">جزئیات آگهی</h2>
    <button type="button" class="icon-btn" data-bs-dismiss="offcanvas" aria-label="بستن"><x-admin.icon name="x" /></button>
  </div>
  <div class="offcanvas-body">
   <div class="detail-scroll adm-scroll">
    <div><div class="gal-main" data-d="main"></div><div class="gal-thumbs" data-d="thumbs"></div></div>
    <div class="detail-title">
      <h3 data-d="title"></h3>
      <span data-d="status"></span><span data-d="sale"></span><span class="text-muted small" data-d="code"></span>
    </div>
    <div class="detail-price"><span data-d="price"></span> <small>تومان</small></div>
    <dl class="spec-grid" data-d="specs"></dl>
    <p class="desc" data-d="desc"></p>
    <div class="list-row card-a flat p-2">
      <span class="lr-ic t-sky"><x-admin.icon name="store" /></span>
      <span class="lr-body"><b data-d="dealer"></b><small data-d="city"></small></span>
      <a href="{{ route('admin.dealers.index') }}" class="lr-end">پروفایل <x-admin.icon name="chevron-left" class="icon-sm" /></a>
    </div>
    <div class="reason-note" data-d="reason" hidden></div>
   </div>
   <div class="detail-foot" data-d="foot"></div>
  </div>
</div>

{{-- مودال رد آگهی --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-head-a">
        <span class="c-ic"><x-admin.icon name="x-circle" class="icon-lg" /></span>
        <div><h3 id="rejectTitle">رد آگهی</h3><p data-r="title"></p></div>
      </div>
      <div class="modal-body-a">
        <label class="form-label" for="rejectReason">دلیل رد (برای نمایشگاه‌دار ارسال می‌شود)</label>
        <div class="reason-chips">
          @foreach(['تصاویر نامناسب یا ناکافی', 'اطلاعات آگهی ناقص است', 'قیمت نامعقول', 'آگهی تکراری است', 'مغایرت با قوانین سایت'] as $r)
            <button type="button" class="reason-chip">{{ $r }}</button>
          @endforeach
        </div>
        <textarea class="form-control" id="rejectReason" rows="3" maxlength="300" placeholder="توضیح بیشتر (اختیاری)…"></textarea>
      </div>
      <div class="modal-foot-a">
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">انصراف</button>
        <button type="button" class="btn btn-danger" id="rejectSubmit" disabled>رد آگهی</button>
      </div>
    </div>
  </div>
</div>
@endsection
