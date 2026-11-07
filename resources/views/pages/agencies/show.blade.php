@extends('layouts.app')
@section('title', $agency->name)
@section('page_title', 'پروفایل آژانس')
@section('page_subtitle', 'مشاهده اطلاعات کامل آژانس')
@section('back', route('agencies.index'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/agency-profile.css') }}">@endpush

@section('content')
@php $isOwner = $auth->agency && $auth->agency->id == $agency->id; @endphp
@if($isOwner)
  {{-- ابزار مدیریت — فقط صاحب آژانس می‌بیند؛ بازدیدکنندگان نمای عمومی را می‌بینند --}}
  <section class="owner-bar" aria-label="مدیریت آژانس">
    <div class="ob-head"><span class="badge-s badge-primary"><x-icon name="store" /> نمای عمومی آژانس شما</span><a href="{{ route('panel') }}" class="ob-link">پنل و آمار <x-icon name="arrow-left" class="icon-sm" /></a></div>
    <div class="ob-grid">
      <a href="{{ route('listings.create') }}"><span><x-icon name="plus" /></span>افزودن آگهی</a>
      <a href="{{ route('panel', ['tab' => 'settings']) }}"><span><x-icon name="pencil" /></span>ویرایش اطلاعات</a>
      <button type="button" data-bs-toggle="offcanvas" data-bs-target="#coverSheet"><span><x-icon name="camera" /></span>تغییر تصویر</button>
      <a href="{{ route('panel', ['tab' => 'overview']) }}"><span><x-icon name="chart" /></span>آمار و ارقام</a>
    </div>
  </section>
@endif
<div class="dp-layout">
  <div class="dp-main">
    {{-- کارت اصلی --}}
    <section class="card-s dp-card">
      <div class="dp-cover">
        @if($agency->cover)<img src="{{ $agency->cover }}" alt="{{ $agency->name }}">@else<x-icon name="store" />@endif
        @if($agency->verified)<span class="dc-verified"><x-icon name="shield" class="icon-sm" /> هویت و مجوز تأییدشده</span>@endif
        @if($isOwner)<button type="button" class="dp-cam" data-bs-toggle="offcanvas" data-bs-target="#coverSheet" aria-label="تغییر تصویر آژانس"><x-icon name="camera" class="icon-sm" /> تغییر تصویر</button>@endif
      </div>
      <div class="dp-card-body">
        <div class="dc-top">
          <h2 class="dp-name">{{ $agency->name }}</h2>
          <span class="dc-mgr">مدیریت : {{ $agency->manager }}</span>
        </div>
        <x-agency-stats :stats="$agency->stats" class="mt-3" />
        @if($agency->video)
          <div class="dp-video">
            <p>{{ $agency->name }} دارای ویدیو معرفی می‌باشد.</p>
            <button type="button" class="dp-video-link" data-bs-toggle="modal" data-bs-target="#videoModal" data-video="{{ $agency->video }}">مشاهده ویدیو <x-icon name="arrow-left" class="icon-sm" /></button>
            <span class="dp-video-ic"><x-icon name="play" /></span>
          </div>
        @endif
      </div>
    </section>

    @if($agency->verified)
      <section class="dp-verified-note">
        <span class="dp-vi"><x-icon name="shield" /></span>
        <div>
          <h3>آژانس تأییدشده</h3>
          <p>هویت و مجوز {{ $agency->short_name }} توسط {{ config('app.name') }} تأیید شده.</p>
        </div>
      </section>
    @endif

    {{-- اطلاعات --}}
    <section class="card-s dp-info">
      <p class="dp-about">{{ $agency->about }}</p>
      <div class="dp-row"><span class="dp-ic"><x-icon name="pin" class="icon-sm" /></span><span>{{ $agency->address }}</span></div>
      <div class="dp-row"><span class="dp-ic"><x-icon name="clock" class="icon-sm" /></span><span>زمان مراجعه حضوری : {{ $agency->hours }}</span></div>
    </section>

  </div>

  <div class="dp-side">
    {{-- موجودی --}}
    <x-section-head title="موجودی آژانس" />
    <div class="pill-tabs" id="stockTabs" role="tablist">
      @foreach($tabs as $t)
        <button type="button" class="pill-tab {{ $loop->first ? 'active' : '' }}" data-cat="{{ $t['slug'] }}" role="tab"><span>{{ $t['title'] }}</span> <span data-fa>{{ $t['count'] }}</span></button>
      @endforeach
    </div>
    <div class="lr-list mt-3" id="stockList">
      @foreach($stock as $item)<x-listing-row :item="$item" :edit="$isOwner ? route('listings.edit', $item['id']) : null" />@endforeach
    </div>
    <div class="d-none" id="stockEmpty"><x-empty-state icon="building" title="آگهی‌ای در این دسته نیست" /></div>
    @if($isOwner)<a href="{{ route('listings.create') }}" class="btn btn-primary btn-lg w-100 mt-3"><x-icon name="plus" /> افزودن آگهی جدید</a>@endif
    <a href="{{ route('listings.index', ['agency' => $agency->id]) }}" class="soft-btn mt-3">مشاهده همه <span data-fa>{{ $agency->active_count }}</span> آگهی</a>
    <a href="{{ route('listings.map', ['agency' => $agency->id]) }}" class="soft-btn mt-2"><x-icon name="pin" class="icon-sm" /> آگهی‌های این آژانس روی نقشه</a>

    <div class="note-card mt-3"><span class="nc-ic"><x-icon name="phone" /></span><div><h3>تماس با آژانس</h3><p>شماره‌ی تماس آژانس در صفحه‌ی هر آگهی (پس از ورود) نمایش داده می‌شود؛ گفتگوی سایت هم در دسترس است.</p></div></div>
  </div>
</div>

{{-- ویدیو معرفی --}}
@if($agency->video)
<div class="modal fade" id="videoModal" tabindex="-1" aria-label="ویدیو معرفی" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-black border-0 overflow-hidden" style="border-radius:var(--r-lg)">
      <button type="button" class="btn-close btn-close-white position-absolute m-3 z-3" data-bs-dismiss="modal" aria-label="بستن" style="top:0;inset-inline-start:0"></button>
      <video id="agencyVideo" controls playsinline preload="none" class="w-100" style="max-height:80dvh"></video>
    </div>
  </div>
</div>
@endif
@if($isOwner)
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="coverSheet" aria-labelledby="coverTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="coverTitle">تغییر تصویر آژانس</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <label class="dropzone" for="coverFile">
      <img id="coverPreview" src="{{ $agency->cover }}" alt="">
      <span class="dz-hint"><x-icon name="upload-cloud" /> انتخاب تصویر جدید <small>JPG یا PNG، نسبت ۲ به ۱، حداکثر ۵ مگابایت</small></span>
    </label>
    <input type="file" id="coverFile" class="d-none" accept="image/*" data-preview="#coverPreview">
    <button type="button" class="btn btn-primary btn-lg w-100 mt-3" id="saveCover" disabled>ذخیره تصویر</button>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/panel.js') }}"></script>
<script>
  $('#coverFile').on('change', function () { $('#saveCover').prop('disabled', !this.files.length); });
  $('#saveCover').on('click', function () { var $b = $(this); App.loading($b); $(document).trigger('app:cover-save', [$('#coverFile')[0].files[0]]); setTimeout(function () { App.loading($b, false); App.sheet('coverSheet', 'hide'); App.toast('تصویر آژانس به‌روز شد (نمایشی)', 'success'); }, 1000); });
</script>
<script>
  // فیلتر موجودی بر اساس تب (نمایشی؛ بک‌اندکار می‌تواند با AJAX جایگزین کند)
  $('#stockTabs').on('click', '.pill-tab', function () {
    var cat = $(this).data('cat'), shown = 0;
    $(this).addClass('active').siblings().removeClass('active');
    $('#stockList .listing-row').each(function () {
      var ok = cat === 'all' || $(this).data('category') === cat; $(this).toggle(ok); if (ok) shown++;
    });
    $('#stockEmpty').toggleClass('d-none', shown > 0);
  });
  // ویدیو فقط هنگام باز شدن مودال بارگذاری می‌شود
  var v = document.getElementById('agencyVideo');
  $('#videoModal').on('show.bs.modal', function (e) { v.src = $(e.relatedTarget).data('video'); v.play().catch(function () {}); })
                  .on('hidden.bs.modal', function () { v.pause(); v.removeAttribute('src'); v.load(); });
</script>
@endpush
