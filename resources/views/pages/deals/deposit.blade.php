@extends('layouts.app')
@php
  $c = $car; $total = $c->amount + $c->fee; $enough = $auth->wallet >= $total;
  $brand = config('app.brand', 'آروان');
  $loginUrl = route('login', ['next' => url()->current()]);
@endphp
@section('title', 'پرداخت بیعانه امانی')
@section('page_title', 'پرداخت بیعانه امانی')
@section('page_subtitle', $c->dealer['name'])
@section('back', $c->url)
@section('body_class', 'no-bottom-nav has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/deal.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/deal.js') }}"></script>@endpush

@section('content')
<div class="dl-wrap" id="depositPage" data-next="{{ route('deals.show', 101) }}">
  <x-car-mini :car="$c" />

  <section class="pay-hero">
    <div class="ph-line"><span class="ph-ic"><x-icon name="shield-check" /></span><span>بیعانهٔ امانی نزد {{ $brand }} به امانت می‌ماند.</span></div>
    <div class="ph-amt"><x-price :value="$c->amount" /></div>
    <p>فقط بیعانه پرداخت می‌کنید؛ نه کل بهای خودرو.</p>
  </section>

  <x-receipt title="خلاصهٔ پرداخت" :rows="[
    ['k' => 'قیمت درج‌شدهٔ خودرو', 'money' => $c->price],
    ['note' => 'قیمت خودرو فقط برای اطلاع است و در پرداخت فعلی دریافت نمی‌شود.'],
    ['k' => 'نوع پرداخت', 'v' => 'بیعانهٔ امانی خرید'],
    ['k' => 'محل نگهداری', 'v' => 'پلتفرم ' . $brand],
    ['sep' => true],
    ['k' => 'مبلغ بیعانه', 'money' => $c->amount],
    ['k' => 'هزینهٔ خدمات ' . $brand, 'money' => $c->fee],
    ['sep' => true],
    ['k' => 'مبلغ کل پرداختی', 'money' => $total, 'big' => true],
  ]" />

  <x-escrow-how :dealer="$c->dealer['name']" />

  <section class="card-s dl-card contract-card">
    <span class="cc-ic"><x-icon name="file" /></span>
    <div>
      <h2>قولنامهٔ الکترونیکی</h2>
      <p>با پرداخت بیعانه، قولنامهٔ الکترونیکی برای خریدار و فروشنده ثبت می‌شود.</p>
      <a href="#" class="more-link" data-bs-toggle="offcanvas" data-bs-target="#contractSheet">مشاهدهٔ قولنامه <x-icon name="arrow-left" class="icon-sm" /></a>
    </div>
  </section>

  @if($auth->check)
    <section class="card-s dl-card">
      <h2>روش پرداخت</h2>
      <div class="pm-list">
        <label class="pm"><input type="radio" name="method" value="wallet" {{ $enough ? 'checked' : 'disabled' }}><span class="pm-b"><x-icon name="wallet" /><span class="pm-t"><b>کیف پول</b><small>موجودی: <x-price :value="$auth->wallet" /></small></span></span></label>
        <label class="pm"><input type="radio" name="method" value="gateway" {{ $enough ? '' : 'checked' }}><span class="pm-b"><x-icon name="shield" /><span class="pm-t"><b>درگاه بانکی</b><small>پرداخت آنلاین با کارت</small></span></span></label>
      </div>
      @unless($enough)<p class="form-text text-danger mt-2 mb-0">موجودی کیف پول برای این مبلغ کافی نیست؛ با درگاه بانکی پرداخت کنید یا <a href="{{ route('account.wallet') }}">کیف پول را شارژ کنید</a>.</p>@endunless
    </section>

    <div class="terms-row">
      <label class="tr-ck"><input type="checkbox" id="agreeDeposit"><i><x-icon name="check" class="icon-sm" /></i><span>شرایط بیعانهٔ امانی را خوانده‌ام و می‌پذیریم.</span></label>
      <a href="#" class="more-link" data-bs-toggle="offcanvas" data-bs-target="#termsSheet"><x-icon name="arrow-left" class="icon-sm" /> مشاهدهٔ ضوابط و شرایط</a>
    </div>

    <div class="sticky-cta">
      <button type="button" class="btn btn-primary btn-lg flex-fill" id="payDeposit" data-total="{{ $total }}" data-listing="{{ $c->id }}"><x-icon name="shield" /> پرداخت بیعانهٔ <x-price :value="$c->amount" /></button>
    </div>
    <p class="dl-foot">پس از پرداخت موفق، مسیر هماهنگی همین آگهی فعال می‌شود.</p>
  @else
    <div class="sticky-cta"><a href="{{ $loginUrl }}" class="btn btn-primary btn-lg flex-fill"><x-icon name="user" /> ورود برای پرداخت بیعانه</a></div>
  @endif
</div>

{{-- قولنامه --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="contractSheet" aria-labelledby="contractTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="contractTitle">پیش‌نمایش قولنامهٔ الکترونیکی</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <ol class="clauses">
      <li><b>طرفین:</b> خریدار (کاربر واردشده) و {{ $c->dealer['name'] }} به‌عنوان فروشنده.</li>
      <li><b>موضوع:</b> {{ $c->title }} — آگهی شمارهٔ <span data-fa>{{ $c->id }}</span>.</li>
      <li><b>بیعانه:</b> <x-price :value="$c->amount" /> که تا تعیین نتیجه نزد پلتفرم امانت می‌ماند.</li>
      <li><b>انجام معامله:</b> پس از تأیید خریدار، بیعانه به فروشنده منتقل می‌شود.</li>
      <li><b>عدم انجام معامله:</b> بیعانه به خریدار برمی‌گردد؛ هزینهٔ خدمات پلتفرم برگشت‌پذیر نیست.</li>
      <li>قیمت نهایی و شرایط تحویل در دیدار حضوری و بین طرفین توافق می‌شود؛ بهای خودرو از طریق پلتفرم پرداخت نمی‌شود.</li>
    </ol>
    <button class="btn btn-primary w-100" data-bs-dismiss="offcanvas">متوجه شدم</button>
  </div>
</div>

{{-- شرایط --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="termsSheet" aria-labelledby="termsTitle2">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="termsTitle2">ضوابط و شرایط بیعانهٔ امانی</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <ul class="ps-3 d-grid gap-2 mb-3">
      <li>مبلغ بیعانه تا تعیین وضعیت معامله نزد پلتفرم نگهداری می‌شود و به نمایشگاه پرداخت نمی‌شود.</li>
      <li>پس از پرداخت، اطلاعات تماس و آدرس نمایشگاه برای هماهنگی نمایش داده می‌شود و حدود ۳ روز برای دیدار و تصمیم فرصت دارید.</li>
      <li>باید در همین مهلت «انجام شد» یا «انجام نشد / اختلاف» را ثبت کنید.</li>
      <li>هزینهٔ خدمات پلتفرم در هر حالت برگشت‌پذیر نیست.</li>
    </ul>
    <button class="btn btn-primary w-100" data-bs-dismiss="offcanvas">متوجه شدم</button>
  </div>
</div>
@endsection
