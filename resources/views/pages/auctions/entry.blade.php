@extends('layouts.app')
@php
  $a = $auction; $fee = $a->entry_fee; $enough = $auth->wallet >= $fee; $live = $a->status === 'live';
  $loginUrl = route('login', ['next' => url()->current()]);
@endphp
@section('title', 'پرداخت ورودی مزایده')
@section('page_title', 'پرداخت ورودی مزایده')
@section('page_subtitle', 'پرداخت فقط برای مزایده ' . $a->short_title)
@section('back', route('auctions.show', $a->id))
@section('body_class', 'no-bottom-nav has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/auction.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/auction.js') }}"></script>@endpush

@section('content')
<div class="entry-wrap" id="entryWrap">
  <section class="card-s entry-car">
    <div class="ec-media"><img src="{{ $a->image }}" alt=""></div>
    <div class="ec-body">
      <span class="auc-badge {{ $live ? 'live' : 'soon' }}">{{ $live ? 'در حال برگزاری' : 'در انتظار شروع' }}</span>
      <h3>{{ $a->title }}</h3>
      <x-specs class="auc-meta" :year="$a->year" :km="$a->km" :extra="$a->extra" />
      <span class="auc-dealer">@if($a->dealer['verified'])<x-icon name="shield" class="icon-sm" />@endif {{ $a->dealer['name'] }}</span>
    </div>
  </section>

  <section class="card-s pad entry-sum">
    <h2>خلاصهٔ سفارش</h2>
    <div class="kv"><span class="kv-k">خودرو</span><span class="kv-v">{{ $a->title }}</span></div>
    <div class="kv"><span class="kv-k">{{ $live ? 'پایان مزایده' : 'شروع مزایده' }}</span><span class="kv-v" data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($live ? $a->ends_at : $a->starts_at)->toIso8601String() }}"></span></div>
    <div class="kv"><span class="kv-k">قیمت شروع • نمونه</span><span class="kv-v"><x-price :value="$a->start_price" /></span></div>
    <p class="es-note">قیمت شروع صرفاً برای اطلاع است؛ اکنون بهای خودرو پرداخت نمی‌شود.</p>
    <div class="es-total"><span>هزینهٔ ورود مزایده</span><b><x-price :value="$fee" /></b></div>
  </section>

  <section class="card-s pad entry-scope">
    <h2>دسترسی محدود به همین مزایده</h2>
    <p>پرداخت موفق امکان شرکت در مزایدهٔ آگهی <span data-fa>{{ $a->listing_id }}</span> را فراهم می‌کند؛ برای سایر مزایده‌ها ورودی جداگانه لازم است.</p>
    <p class="es-red">این هزینه، قیمت خرید یا بیعانهٔ امانی نیست.</p>
  </section>

  <section class="warn-card">
    <span class="wc-ic"><x-icon name="lock" /></span>
    <div>
      <h3>{{ $live ? 'ثبت پیشنهاد بعد از پرداخت' : 'ثبت پیشنهاد پس از شروع' }}</h3>
      <p>{{ $live ? 'با پرداخت ورودی، بخش پیشنهاد قیمت و لیست پیشنهادها برای شما باز می‌شود.' : 'با پرداخت ورودی هم تا زمان شروع مزایده، ارسال پیشنهاد فعال نمی‌شود.' }}</p>
    </div>
  </section>

  <a href="#" class="terms-link" data-bs-toggle="offcanvas" data-bs-target="#termsSheet">مشاهدهٔ شرایط و سیاست هزینهٔ ورودی <x-icon name="arrow-left" class="icon-sm" /></a>

  @if(!$auth->check)
    <div class="sticky-cta"><a href="{{ $loginUrl }}" class="btn btn-primary btn-lg flex-fill"><x-icon name="user" /> ورود برای پرداخت</a></div>
  @elseif(!$enough)
    <div class="pay-low">
      <div class="pay-row wallet rounded-3 mb-2"><b><x-icon name="wallet" class="icon-sm" /> موجودی کیف پول</b><x-price :value="$auth->wallet" /></div>
      <p class="small text-danger mb-2">موجودی کیف پول برای این پرداخت کافی نیست.</p>
    </div>
    <div class="sticky-cta"><a href="{{ route('account.wallet') }}" class="btn btn-accent btn-lg flex-fill"><x-icon name="plus" /> شارژ کیف پول</a></div>
  @else
    <div class="pay-row wallet rounded-3"><b><x-icon name="wallet" class="icon-sm" /> پرداخت از کیف پول • موجودی</b><x-price :value="$auth->wallet" /></div>
    <div class="sticky-cta"><button type="button" class="btn btn-primary btn-lg flex-fill" id="payEntry" data-auction="{{ $a->id }}" data-fee="{{ $fee }}"><x-icon name="shield" /> پرداخت هزینه ورود به مزایده</button></div>
  @endif
</div>

{{-- پس از پرداخت --}}
<div class="d-none wz-done" id="entryDone">
  <div class="ok-ring"><x-icon name="check" /></div>
  <h2 class="fs-5">ورود شما به مزایده ثبت شد</h2>
  <p class="text-muted-s">{{ $live ? 'همین حالا می‌توانید پیشنهاد بدهید.' : 'پس از شروع مزایده می‌توانید پیشنهاد قیمت ثبت کنید.' }}</p>
  <a href="{{ route('auctions.show', $a->id) }}?paid=1" class="btn btn-primary btn-lg w-100">بازگشت به مزایده</a>
</div>

<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="termsSheet" aria-labelledby="termsTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="termsTitle">شرایط هزینهٔ ورودی</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <ul class="ps-3 d-grid gap-2 mb-3">
      <li>هزینهٔ ورود فقط برای همین مزایده است و بابت خدمات برگزاری دریافت می‌شود.</li>
      <li>این مبلغ جزو قیمت خودرو یا بیعانه نیست و پس از پرداخت قابل بازگشت نیست؛ مگر مزایده توسط نمایشگاه لغو شود.</li>
      <li>برنده مزایده باید در مهلت مقرر بیعانهٔ امانی را بپردازد؛ در غیر این صورت مدتی از شرکت در مزایده‌ها محروم می‌شود.</li>
    </ul>
    <button class="btn btn-primary w-100" data-bs-dismiss="offcanvas">متوجه شدم</button>
  </div>
</div>
@endsection
