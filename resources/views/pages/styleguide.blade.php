@extends('layouts.app')
@section('title', 'راهنمای استایل')
@section('page_title', 'راهنمای استایل')
@section('back', route('home'))

@section('content')
  <x-section-head title="دکمه‌ها" />
  <div class="card-s pad d-flex flex-wrap gap-2">
    <button class="btn btn-primary">اصلی</button>
    <button class="btn btn-accent">رزرو با بیعانه</button>
    <button class="btn btn-outline-primary">حاشیه‌دار</button>
    <button class="btn btn-soft">ملایم</button>
    <button class="btn btn-primary" id="demoLoading">حالت بارگذاری</button>
    <button class="btn btn-primary" disabled>غیرفعال</button>
  </div>

  <x-section-head title="بج‌ها و وضعیت‌ها" />
  <div class="card-s pad d-flex flex-wrap gap-2">
    <span class="badge-s badge-instant"><x-icon name="bolt" /> فروش فوری</span>
    <span class="badge-s badge-auction"><x-icon name="gavel" /> مزایده</span>
    <span class="badge-s badge-success">رزرو شده</span>
    <span class="badge-s badge-warn">در انتظار تأیید</span>
    <span class="badge-s badge-danger">اختلاف</span>
    <span class="badge-s badge-info">در حال بررسی</span>
  </div>

  <x-section-head title="فرم" />
  <div class="card-s pad row g-3">
    <div class="col-md-6"><label class="form-label">شماره موبایل</label><input class="form-control ltr" placeholder="09123456789" inputmode="numeric"></div>
    <div class="col-md-6"><label class="form-label">کد تأیید</label><input class="form-control otp-input" maxlength="5" placeholder="•••••"></div>
  </div>

  <x-section-head title="شمارنده و کیف پول" />
  <div class="row g-3">
    <div class="col-lg-6"><div class="card-s pad d-flex justify-content-between align-items-center"><span class="fw-bold">پایان مزایده</span><x-countdown :end="now()->addHours(3)" /></div></div>
    <div class="col-lg-6"><div class="card-s pad d-flex justify-content-between align-items-center"><span class="fw-bold">فوری (کمتر از ۵ دقیقه)</span><x-countdown :end="now()->addMinutes(4)" /></div></div>
    <div class="col-12">
      <div class="wallet-card">
        <div class="label">موجودی قابل برداشت</div>
        <div class="amount"><x-price :value="12500000" /></div>
        <div class="sub">
          <div><div class="label">بلوکه‌شده (بیعانه)</div><div class="fw-bold"><x-price :value="40000000" /></div></div>
          <div><div class="label">در انتظار تأیید</div><div class="fw-bold"><x-price :value="0" /></div></div>
        </div>
      </div>
    </div>
  </div>

  <x-section-head title="لیست تراکنش‌ها" />
  <div class="list-s">
    <div class="li"><div class="li-icon out"><x-icon name="arrow-up" /></div><div class="li-main"><div class="li-title">بیعانه پژو ۲۰۷</div><div class="li-sub" data-jdate="{{ now()->subDay()->toIso8601String() }}"></div></div><x-price :value="40000000" class="fw-bold" /></div>
    <div class="li"><div class="li-icon in"><x-icon name="arrow-down" /></div><div class="li-main"><div class="li-title">شارژ کیف پول</div><div class="li-sub" data-jdate="{{ now()->subDays(3)->toIso8601String() }}"></div></div><x-price :value="50000000" class="fw-bold" /></div>
  </div>

  <x-section-head title="کارت آگهی" />
  <div class="row g-3">
    @foreach($items->take(4) as $item)<div class="col-6 col-lg-3"><x-listing-card :item="$item" /></div>@endforeach
  </div>

  <x-section-head title="اسکلتون، توست و شیت" />
  <div class="card-s pad">
    <div class="skeleton mb-2" style="height:16px;width:60%"></div>
    <div class="skeleton mb-3" style="height:16px;width:40%"></div>
    <div class="d-flex flex-wrap gap-2">
      <button class="btn btn-soft" data-t="success">توست موفق</button>
      <button class="btn btn-soft" data-t="error">توست خطا</button>
      <button class="btn btn-soft" onclick="App.sheet('demoSheet')">باز کردن شیت</button>
    </div>
  </div>

  <div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="demoSheet">
    <div class="sheet-handle"></div>
    <div class="offcanvas-header"><h5 class="offcanvas-title">تأیید رزرو</h5><button class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body pt-0"><p class="text-muted-s">مبلغ بیعانه از کیف پول شما بلوکه می‌شود.</p><button class="btn btn-accent btn-lg w-100">پرداخت بیعانه</button></div>
  </div>
@endsection

@push('scripts')
<script>
  $('#demoLoading').on('click', function () { var $b = $(this); App.loading($b); setTimeout(function () { App.loading($b, false); }, 1500); });
  $('[data-t]').on('click', function () { var t = $(this).data('t'); App.toast(t === 'success' ? 'عملیات با موفقیت انجام شد' : 'پرداخت ناموفق بود؛ دوباره تلاش کنید', t); });
</script>
@endpush
