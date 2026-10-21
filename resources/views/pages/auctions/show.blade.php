@extends('layouts.app')
@php
  $a = $auction;
  $isOwner = $auth->dealer && $auth->dealer->id === $a->dealer['id'];   // صاحب مزایده (نمایشگاه فروشنده)
  $live = $a->status === 'live'; $sched = $a->status === 'scheduled'; $ended = $a->status === 'ended';
  $paid = !empty($a->paid);                                              // ورودی همین مزایده را پرداخت کرده؟
  $canBid = $live && $paid && !$isOwner && $auth->check;
  $min = empty($a->top_bid) ? $a->start_price : $a->top_bid + $a->min_step;
  $loginUrl = route('login', ['next' => url()->current()]);
  $fa = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
@endphp
@section('title', $a->title . ' | مزایده')
@section('page_title', 'جزئیات مزایده')
@section('page_subtitle', 'مشاهده جزئیات کامل مزایده')
@section('back', route('auctions.index'))
@section('body_class', 'no-bottom-nav')

@push('styles')<link rel="stylesheet" href="{{ asset('css/auction.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/auction.js') }}"></script>@endpush

@section('content')
{{-- بنر نمایشگاه فروشنده --}}
<x-dealer-banner :dealer="$a->dealer" />

<div class="au-grid">
  {{-- ===== ستون چپ/بالا: خودرو ===== --}}
  <div class="au-left">
    <x-gallery :images="$a->images" />

    <div class="au-title">
      <h1>{{ $a->title }}</h1>
      <p>{{ $a->subtitle }}</p>
    </div>

    <a href="{{ $a->listing_url }}" class="car-link">
      <span class="cl-ic"><x-icon name="car" /></span>
      <div><b>مشاهده اطلاعات کامل خودرو</b><span>رفتن به آگهی خودرو <x-icon name="arrow-left" class="icon-sm" /></span></div>
    </a>
  </div>

  {{-- ===== ستون راست/پایین: وضعیت مزایده ===== --}}
  <div class="au-right">

    @if($isOwner)
      <div class="owner-banner"><x-icon name="store" class="icon-sm" /> این مزایده متعلق به نمایشگاه شماست؛ شما نمی‌توانید پیشنهاد بدهید.</div>
    @endif

    {{-- شمارنده --}}
    @if($sched)
      <x-countdown-card :end="$a->starts_at" title="تا شروع مزایده" tone="blue" date-label="شروع:" />
    @elseif($live)
      <x-countdown-card :end="$a->ends_at" title="تا پایان مزایده" tone="green" date-label="پایان:" :window="0" />
    @endif

    {{-- ====== صاحب مزایده ====== --}}
    @if($isOwner)
      <section class="card-s owner-auc">
        <div class="op-head"><span class="badge-s badge-primary">مدیریت مزایده</span><span class="small text-muted-s">شناسه: <b data-fa>{{ $a->id }}</b></span></div>
        <div class="op-stats4">
          <div><b data-fa>{{ $a->participants }}</b><span>شرکت‌کننده</span></div>
          <div><b data-fa>{{ $a->bids_count }}</b><span>پیشنهاد</span></div>
          <div><b>@if($a->top_bid)<span class="num" data-money="{{ $a->top_bid }}">{{ $a->top_bid }}</span>@else—@endif</b><span>بالاترین (تومان)</span></div>
          <div><b><span class="num" data-money="{{ $a->participants * $a->entry_fee }}">{{ $a->participants * $a->entry_fee }}</span></b><span>درآمد ورودی (تومان)</span></div>
        </div>
        <div class="op-actions">
          @if($sched)
            <a href="{{ route('auctions.create', ['edit' => $a->id]) }}" class="btn btn-primary"><x-icon name="pencil" class="icon-sm" /> ویرایش مزایده</a>
            <button type="button" class="btn btn-soft op-danger" data-bs-toggle="offcanvas" data-bs-target="#cancelSheet"><x-icon name="x-circle" class="icon-sm" /> لغو مزایده</button>
          @endif
          <a href="{{ route('listings.edit', $a->listing_id) }}" class="btn btn-outline-primary"><x-icon name="car" class="icon-sm" /> مدیریت آگهی خودرو</a>
        </div>
      </section>
      @if($live || $ended)<x-bid-list :bids="collect($a->bids)->map(fn ($b) => array_merge($b, ['mine' => false, 'user' => !empty($b['mine']) ? 'کاربر ۱۸۰۰۰' : $b['user']]))->all()" />@endif
      @if($live)<div id="bidFeed" hidden data-auction="{{ $a->id }}" data-top="{{ (int) $a->top_bid }}" data-step="{{ $a->min_step }}" data-min="{{ $min }}" data-demo="1"></div>@endif
      @if($sched)
        <section class="note-card"><span class="nc-ic"><x-icon name="clock" /></span><div><h3>هنوز شروع نشده</h3><p>تا زمان شروع می‌توانید مزایده را ویرایش یا لغو کنید. بعد از شروع، تغییر ممکن نیست.</p></div></section>
      @endif

    {{-- ====== قبل از شروع ====== --}}
    @elseif($sched)
      <section class="card-s pad info-card">
        <h2 class="bc-t">اطلاعات این مزایده</h2>
        <div class="kv-list-plain">
          <div class="kv"><span class="kv-k">قیمت شروع</span><span class="kv-v blue"><x-price :value="$a->start_price" /></span></div>
          <div class="kv"><span class="kv-k">ورودی همین مزایده</span><span class="kv-v"><x-price :value="$a->entry_fee" /></span></div>
          <div class="kv"><span class="kv-k">زمان شروع</span><span class="kv-v" data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($a->starts_at)->toIso8601String() }}"></span></div>
          <div class="kv"><span class="kv-k">زمان پایان</span><span class="kv-v" data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($a->ends_at)->toIso8601String() }}"></span></div>
        </div>
        <p class="info-note">قیمت اصلی: <b class="green"><x-price :value="$a->original_price" /></b>؛ با قیمت شروع مزایده متفاوت است.</p>
      </section>

      <section class="note-card">
        <span class="nc-ic"><x-icon name="clock" /></span>
        <div>
          <h3>هنوز شروع نشده</h3>
          <p>این مزایده در <span data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($a->starts_at)->toIso8601String() }}"></span> شروع می‌شود. تا آن زمان هیچ پیشنهادی قابل ثبت نیست.</p>
        </div>
      </section>

      @if(!$auth->check)
        <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg w-100"><x-icon name="user" /> ورود برای شرکت در مزایده</a>
      @elseif($paid)
        <div class="paid-card"><x-icon name="check-circle" /><div><b>ورودی شما پرداخت شده</b><span>از لحظه شروع می‌توانید پیشنهاد بدهید.</span></div></div>
      @else
        <a href="{{ route('auctions.entry', $a->id) }}" class="btn btn-primary btn-lg w-100">پرداخت و ورود به مزایده : <x-price :value="$a->entry_fee" /> <x-icon name="shield" /></a>
      @endif
      <button type="button" class="btn btn-locked w-100" disabled><x-icon name="lock" class="icon-sm" /> ثبت پیشنهاد • پس از شروع</button>

    {{-- ====== در حال برگزاری ====== --}}
    @elseif($live)
      @if($canBid)
        <section class="card-s bid-box" id="bidBox" data-auction="{{ $a->id }}" data-top="{{ (int) $a->top_bid }}" data-step="{{ $a->min_step }}" data-min="{{ $min }}" data-mine="{{ (int) ($a->my_last_bid ?? 0) }}" data-demo="1">
          <h2 class="bc-t">پیشنهاد شما</h2>
          <label class="form-label" for="bidInput">مبلغ پیشنهاد (تومان) • نمونه</label>
          <input id="bidInput" class="form-control form-control-lg bid-input ltr" inputmode="numeric" autocomplete="off" value="{{ number_format($min) }}" aria-describedby="bidHint">
          <div class="bid-chips" aria-label="افزودن سریع">
            <button type="button" data-add="1">+ <span class="num" data-money="{{ $a->min_step }}">{{ $a->min_step }}</span></button>
            <button type="button" data-add="2">+ <span class="num" data-money="{{ $a->min_step * 2 }}">{{ $a->min_step * 2 }}</span></button>
            <button type="button" data-add="4">+ <span class="num" data-money="{{ $a->min_step * 4 }}">{{ $a->min_step * 4 }}</span></button>
          </div>
          <p class="bid-hint" id="bidHint">حداقل پیشنهاد بعدی: <b id="minBid"><x-price :value="$min" /></b></p>
          <p class="bid-mine" id="mineLine" @if(empty($a->my_last_bid)) hidden @endif>آخرین پیشنهاد شما: <b class="green" id="myBid">@if(!empty($a->my_last_bid))<x-price :value="$a->my_last_bid" />@endif</b></p>
          <div class="text-danger small mb-2" id="bidErr" role="alert"></div>
          <button type="button" class="btn btn-primary btn-lg w-100" id="bidBtn">ثبت پیشنهاد <span id="bidBtnAmt"></span> تومان</button>
        </section>
        <x-bid-list :bids="$a->bids" />
      @else
        {{-- قفل: بدون ارسال داده واقعی پیشنهادها به مرورگر --}}
        <section class="locked-wrap">
          <div class="lock-body" aria-hidden="true">
            <div class="card-s bid-box"><h2 class="bc-t">پیشنهاد شما</h2><div class="sk-input"></div><div class="sk-btn"></div></div>
            <div class="card-s bid-card"><h2 class="bc-t">آخرین پیشنهادها</h2>
              @for($i = 0; $i < 3; $i++)<div class="bid-row"><div class="br-main"><b>••• ••• •••</b><span>کاربر ••••</span></div><time class="br-time">••:••</time></div>@endfor
            </div>
          </div>
          <div class="lock-overlay">
            <span class="lo-ic"><x-icon name="lock" /></span>
            <h3>پیشنهاد قیمت قفل است</h3>
            <p>برای ثبت پیشنهاد و دیدن پیشنهادهای دیگران، ابتدا ورودی این مزایده را پرداخت کنید.</p>
            @if(!$auth->check)
              <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg w-100"><x-icon name="user" /> ورود برای شرکت</a>
            @else
              <a href="{{ route('auctions.entry', $a->id) }}" class="btn btn-primary btn-lg w-100">پرداخت و ورود : <x-price :value="$a->entry_fee" /></a>
            @endif
          </div>
        </section>
      @endif

      <section class="note-card">
        <span class="nc-ic"><x-icon name="shield" /></span>
        <div><h3>پس از برنده‌شدن، مسیر امانی ادامه دارد</h3><p>ثبت پیشنهاد یا پرداخت ورودی، تماس مستقیم با نمایشگاه را فعال نمی‌کند. ادامهٔ خرید با بیعانهٔ امانی و هماهنگی از طریق پلتفرم خواهد بود.</p></div>
      </section>

    {{-- ====== پایان‌یافته ====== --}}
    @else
      <section class="card-s pad result-card {{ !empty($a->won) ? 'won' : '' }}">
        <span class="rc-ic"><x-icon name="trophy" /></span>
        <h2>مزایده پایان یافت</h2>
        <p class="rc-sub">بالاترین پیشنهاد: <b><x-price :value="$a->top_bid" /></b> • <span data-fa>{{ $a->bids_count }}</span> پیشنهاد</p>
        @if(!empty($a->won))
          <div class="rc-win"><b>تبریک! شما برنده شدید</b><p>برای نهایی‌شدن خرید، در مهلت زیر بیعانهٔ امانی را بپردازید؛ در غیر این صورت مدتی از شرکت در مزایده‌ها محروم می‌شوید.</p></div>
          <x-countdown-card :end="$a->deposit_deadline" title="مهلت پرداخت بیعانه" tone="blue" date-label="تا:" :window="0" />
          <a href="{{ $a->listing_url }}" class="btn btn-primary btn-lg w-100"><x-icon name="shield" /> پرداخت بیعانه</a>
        @elseif($paid)
          <p class="text-muted-s mb-0">برندهٔ این مزایده کاربر دیگری بود. از شرکت شما سپاسگزاریم.</p>
        @else
          <p class="text-muted-s mb-0">این مزایده به پایان رسیده است. مزایده‌های فعال را ببینید.</p>
          <a href="{{ route('auctions.index') }}" class="btn btn-primary btn-lg w-100 mt-3">فهرست مزایده‌ها</a>
        @endif
      </section>
    @endif
  </div>
</div>

{{-- تأیید ثبت پیشنهاد --}}
@if($canBid)
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="bidConfirm" aria-labelledby="bidConfirmTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="bidConfirmTitle">تأیید پیشنهاد</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p class="mb-2">پیشنهاد شما: <b class="fs-5 text-primary" id="confirmAmt"></b> تومان</p>
    <p class="small text-muted-s">پیشنهاد ثبت‌شده قابل پس‌گرفتن نیست. در صورت برنده‌شدن باید بیعانهٔ امانی را در مهلت مقرر بپردازید.</p>
    <button type="button" class="btn btn-primary btn-lg w-100" id="bidConfirmBtn">بله، ثبت کن</button>
    <button type="button" class="btn btn-soft btn-lg w-100 mt-2" data-bs-dismiss="offcanvas">انصراف</button>
  </div>
</div>
@endif

{{-- لغو مزایده (صاحب) --}}
@if($isOwner && $sched)
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="cancelSheet" aria-labelledby="cancelTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="cancelTitle">لغو مزایده</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p class="small text-muted-s">با لغو مزایده، ورودی پرداخت‌شدهٔ شرکت‌کنندگان به کیف پولشان برمی‌گردد و آگهی به حالت عادی برمی‌گردد.</p>
    <button type="button" class="btn btn-danger btn-lg w-100" id="cancelAuction" data-id="{{ $a->id }}">لغو مزایده</button>
    <button type="button" class="btn btn-soft btn-lg w-100 mt-2" data-bs-dismiss="offcanvas">انصراف</button>
  </div>
</div>
@endif
@endsection
