@extends('layouts.app')
@php
  $d = $deal; $dl = (object) $d->dealer; $brand = config('app.brand', 'آروان');
  $reasons = ['خودرو با توضیحات آگهی تفاوت داشت', 'نمایشگاه پاسخ نداد / در دسترس نبود', 'روی قیمت یا شرایط توافق نشد', 'خودرو قبلاً فروخته شده بود', 'تصمیم به خرید را تغییر دادم', 'دلیل دیگر'];
@endphp
@section('title', 'تعیین وضعیت معامله')
@section('page_title', 'تعیین وضعیت معامله')
@section('page_subtitle', $d->title . ' • ' . $dl->name)
@section('back', route('deals.show', $d->id))
@section('body_class', 'no-bottom-nav has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/deal.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/deal.js') }}"></script>@endpush

@section('content')
@if($d->status !== 'held')
  <x-empty-state icon="check-circle" title="وضعیت این معامله قبلاً مشخص شده" text="برای دیدن نتیجه به صفحه معامله بروید." action="مشاهده معامله" :href="route('deals.show', $d->id)" />
@else
<div class="dl-wrap" id="decidePage" data-deal="{{ $d->id }}" data-ok="{{ route('deals.show', $d->id) }}?state=completed" data-dispute="{{ route('deals.show', $d->id) }}?state=review">
  <section class="card-s dl-card">
    <h2>نتیجهٔ دیدار و معامله چه بود؟</h2>
    <p class="mb-0 text-muted-s">فقط شما می‌توانید وضعیت را تعیین کنید. تا آن زمان بیعانه (<x-price :value="$d->amount" />) نزد پلتفرم امانت است.</p>
  </section>

  <div class="big-opts" role="radiogroup" aria-label="وضعیت معامله">
    <label class="big-opt ok">
      <input type="radio" name="result" value="completed">
      <span class="bo-body">
        <span class="bo-ic"><x-icon name="check-circle" /></span>
        <span class="bo-t"><b>معامله انجام شد</b><small>خودرو را خریدم؛ بیعانه به نمایشگاه منتقل شود.</small></span>
        <span class="bo-ck"><x-icon name="check" class="icon-sm" /></span>
      </span>
      <span class="bo-more"><x-icon name="info" class="icon-sm" /> بیعانه (<x-price :value="$d->amount" />) به نمایشگاه {{ $dl->name }} منتقل می‌شود. این کار برگشت‌پذیر نیست.</span>
    </label>
    <label class="big-opt no">
      <input type="radio" name="result" value="dispute">
      <span class="bo-body">
        <span class="bo-ic"><x-icon name="x-circle" /></span>
        <span class="bo-t"><b>معامله انجام نشد / اختلاف دارم</b><small>خرید انجام نشد یا مشکلی وجود داشت.</small></span>
        <span class="bo-ck"><x-icon name="check" class="icon-sm" /></span>
      </span>
      <span class="bo-more"><x-icon name="info" class="icon-sm" /> درخواست شما بررسی می‌شود و پس از تأیید، بیعانه به شما برمی‌گردد. هزینهٔ خدمات {{ $brand }} برگشت‌پذیر نیست.</span>
    </label>
  </div>

  {{-- فقط برای «اختلاف» --}}
  <section class="card-s dl-card d-none" id="disputeBox">
    <h2>دلیل را انتخاب کنید <b class="req">*</b></h2>
    <div class="reasons">
      @foreach($reasons as $r)
        <label class="reason"><input type="radio" name="reason" value="{{ $r }}"><span>{{ $r }}</span></label>
      @endforeach
    </div>
    <label class="form-label mt-3" for="reasonNote">توضیح (اختیاری)</label>
    <textarea class="form-control" id="reasonNote" rows="3" maxlength="500" placeholder="هر چه برای بررسی لازم است بنویسید…"></textarea>
    <label class="proof" for="proofFile"><x-icon name="camera" class="icon-sm" /> <span id="proofName">افزودن عکس یا مدرک (اختیاری)</span></label>
    <input type="file" id="proofFile" class="d-none" accept="image/*,application/pdf">
  </section>

  <section class="note-card">
    <span class="nc-ic"><x-icon name="clock" /></span>
    <div><h3>تا پایان مهلت تصمیم بگیرید</h3><p>زمان باقیمانده: <x-countdown :end="$d->decide_by" hms inline /> — اگر وضعیت را تعیین نکنید، پشتیبانی با شما تماس می‌گیرد.</p></div>
  </section>

  <div class="sticky-cta">
    <button type="button" class="btn btn-primary btn-lg flex-fill" id="submitDecision" disabled>یک گزینه را انتخاب کنید</button>
  </div>
</div>

{{-- تأیید نهایی --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="decideConfirm" aria-labelledby="dcTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="dcTitle">مطمئن هستید؟</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p id="dcText" class="mb-3"></p>
    <button type="button" class="btn btn-primary btn-lg w-100" id="dcYes">بله، ثبت شود</button>
    <button type="button" class="btn btn-soft btn-lg w-100 mt-2" data-bs-dismiss="offcanvas">انصراف</button>
  </div>
</div>
@endif
@endsection
