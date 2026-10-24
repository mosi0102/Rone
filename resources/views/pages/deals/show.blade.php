@extends('layouts.app')
@php
  $d = $deal; $st = $d->status; $dl = (object) $d->dealer;
  $brand = config('app.brand', 'آروان');
  $isSeller = $auth->dealer && $auth->dealer->id === $dl->id;            // نمایشگاه فروشنده همین معامله
  $fa = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
  $en = array_combine(['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], ['0','1','2','3','4','5','6','7','8','9']);
  $tel = 'tel:' . strtr($isSeller ? $d->buyer['phone'] : $dl->phone, $en);
  $adLine = strtr((string) $d->listing_id, $fa) . ' • ' . $d->title;
  // مراحل: ۱ پرداخت ۲ هماهنگی ۳ تعیین وضعیت ۴ نتیجه
  $step = ['held' => 2, 'review' => 4, 'completed' => 5, 'refunded' => 5][$st] ?? 2;
@endphp
@section('title', 'معامله ' . $d->title)
@section('page_title', $isSeller ? 'جزئیات معامله' : 'پرداخت بیعانه امانی')
@section('page_subtitle', $isSeller ? 'خریدار: ' . $d->buyer['name'] : $dl->name)
@section('back', $isSeller ? route('panel', ['tab' => 'deals']) : route('account.reservations'))
@section('body_class', 'no-bottom-nav' . ($st === 'held' && !$isSeller ? ' has-sticky-cta' : ''))

@push('styles')<link rel="stylesheet" href="{{ asset('css/deal.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/deal.js') }}"></script>@endpush

@section('content')
<div class="dl-wrap">

  {{-- ===== وضعیت بالای صفحه ===== --}}
  @if($isSeller)
    @if($st === 'held')<x-deal-hero tone="info" icon="shield-check" title="بیعانه در امانت پلتفرم است" text="خریدار بیعانه را پرداخت کرده؛ پس از تأیید او، مبلغ به کیف پول شما منتقل می‌شود." :amount="$d->amount" />
    @elseif($st === 'completed')<x-deal-hero tone="green" icon="check" title="بیعانه به کیف پول شما منتقل شد." text="خریدار انجام معامله را تأیید کرد." :amount="$d->amount" />
    @elseif($st === 'refunded')<x-deal-hero tone="info" icon="undo" title="بیعانه به خریدار بازگشت" text="معامله انجام نشد؛ مبلغ به خریدار برگردانده شد." :amount="$d->amount" />
    @else<x-deal-hero tone="amber" icon="clock" title="خریدار اختلاف ثبت کرد" text="تا اعلام نتیجه، بیعانه نزد پلتفرم امانت می‌ماند." :amount="$d->amount" />@endif
  @else
    @if($st === 'held')<x-deal-hero tone="info" icon="shield-check" title="بیعانه نزد پلتفرم به امانت است." text="پرداخت موفق بود." :amount="$d->amount" />
    @elseif($st === 'completed')<x-deal-hero tone="green" icon="check" title="معامله انجام شد." :text="'ضمن تبریک بابت خرید خودرو، بیعانهٔ امانی به نمایشگاه ' . $dl->name . ' منتقل شد.'" :amount="$d->amount" />
    @elseif($st === 'refunded')<x-deal-hero tone="info" icon="undo" title="بیعانه بازگردانده شد" text="معامله انجام نشد؛ مبلغ بیعانه به خریدار بازگشت." :amount="$d->amount" />
    @else<x-deal-hero tone="amber" icon="clock" title="اختلاف شما در حال بررسی است" text="مبلغ بیعانه تا اعلام نتیجه نزد پلتفرم امانت می‌ماند. نتیجه حداکثر تا ۴۸ ساعت اعلام می‌شود." :amount="$d->amount" />@endif
  @endif

  {{-- مراحل --}}
  <ol class="dl-steps" aria-label="مراحل معامله">
    @foreach(['پرداخت بیعانه', 'هماهنگی و دیدار', 'تعیین وضعیت', 'نتیجه'] as $i => $label)
      <li class="{{ $step > $i + 1 ? 'done' : ($step === $i + 1 ? 'cur' : '') }}"><i>@if($step > $i + 1)<x-icon name="check" class="icon-sm" />@else<span data-fa>{{ $i + 1 }}</span>@endif</i><span>{{ $label }}</span></li>
    @endforeach
  </ol>

  {{-- مهلت --}}
  @if($st === 'held')
    <section class="dl-deadline" data-cd-wrap>
      <div><b>{{ $isSeller ? 'مهلت تعیین وضعیت توسط خریدار' : 'مهلت دیدار و تعیین وضعیت' }}</b><span>{{ $isSeller ? 'فقط خریدار می‌تواند وضعیت را تعیین کند.' : 'تا پایان مهلت «انجام شد» یا «انجام نشد» را ثبت کنید.' }}</span></div>
      <x-countdown :end="$d->decide_by" hms light />
    </section>
  @endif

  {{-- ===== اختلاف (بررسی) ===== --}}
  @if($st === 'review')
    <section class="card-s dl-card dispute-card">
      <h2>اختلاف ثبت‌شده</h2>
      <div class="rc-row"><span class="rc-k">دلیل</span><b class="rc-v">{{ $d->reason }}</b></div>
      @if($d->reason_note)<p class="rc-inline-note">«{{ $d->reason_note }}»</p>@endif
      <div class="rc-row"><span class="rc-k">زمان ثبت</span><b class="rc-v" data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($d->decided_at)->toIso8601String() }}"></b></div>
    </section>
  @endif

  {{-- ===== رسید ===== --}}
  @php
    $rows = [];
    if ($st === 'held') {
        $rows = [['k' => 'مبلغ بیعانه', 'money' => $d->amount]];
        if (!$isSeller) $rows[] = ['k' => 'هزینهٔ خدمات ' . $brand, 'money' => $d->fee];
        $rows[] = ['sep' => true];
        $rows[] = ['k' => 'شناسهٔ تراکنش', 'v' => $d->tx]; $rows[] = ['k' => 'آگهی مرتبط', 'v' => $adLine];
        $rows[] = ['k' => $isSeller ? 'پرداخت خریدار' : 'پرداخت اولیه', 'date' => $d->paid_at];
        $rows[] = ['k' => 'محل نگهداری', 'v' => 'پلتفرم ' . $brand];
        if ($isSeller) $rows[] = ['k' => 'پرداخت به شما', 'v' => 'پس از تأیید خریدار'];
        $rows[] = ['note' => $isSeller ? 'هزینهٔ خدمات را خریدار پرداخت کرده و از مبلغ شما کم نمی‌شود.' : 'این مبلغ بیعانه است، نه کل بهای خودرو.'];
    } else {
        $rows = [['k' => 'مبلغ بیعانه', 'money' => $d->amount], ['sep' => true], ['k' => 'شناسهٔ تراکنش', 'v' => $d->tx], ['k' => 'آگهی مرتبط', 'v' => $adLine], ['k' => 'پرداخت اولیه', 'date' => $d->paid_at]];
        if ($st !== 'review') {
            $rows[] = ['k' => 'ثبت وضعیت نهایی', 'date' => $d->decided_at];
            $rows[] = ['k' => 'دریافت‌کننده', 'v' => $st === 'completed' ? ($isSeller ? 'شما (نمایشگاه ' . $dl->name . ')' : 'نمایشگاه ' . $dl->name) : ($isSeller ? 'خریدار' : 'خریدار')];
        } else { $rows[] = ['k' => 'محل نگهداری', 'v' => 'پلتفرم ' . $brand]; }
        $rows[] = ['note' => 'مبلغ پرداخت‌شده تنها بابت بیعانه بود، نه مبلغ کل خودرو.'];
        if ($st === 'refunded' && !$isSeller) $rows[] = ['note' => 'هزینهٔ خدمات ' . $brand . ' برگشت‌پذیر نیست.'];
    }
  @endphp
  <x-receipt id="receipt" title="رسید بیعانهٔ امانی" :rows="$rows" />

  {{-- خودرو --}}
  <x-car-mini :car="$d" />

  {{-- ===== هماهنگی (فقط در امانت) ===== --}}
  @if($st === 'held' && !$isSeller)
    <section class="card-s dl-card coord">
      <h2>حالا هماهنگی را آغاز کنید</h2>
      <p>پس از پرداخت بیعانه، شما می‌توانید با مدیریت نمایشگاه ارتباط برقرار کنید.</p>
      <div class="co-row"><span class="co-ic"><x-icon name="pin" class="icon-sm" /></span><span>{{ $dl->address }}</span></div>
      <div class="co-row"><span class="co-ic"><x-icon name="clock" class="icon-sm" /></span><span>زمان مراجعه حضوری : {{ $dl->hours }}</span></div>
      <div class="co-row"><span class="co-ic"><x-icon name="phone" class="icon-sm" /></span><span>شماره تماس : <b class="ltr d-inline-block">{{ $dl->phone }}</b></span></div>
      <div class="co-actions">
        <a href="{{ $dl->map_url }}" target="_blank" rel="noopener" class="btn btn-soft"><x-icon name="navigation" class="icon-sm" /> مسیریابی</a>
        <a href="{{ $tel }}" class="btn btn-primary"><x-icon name="phone" class="icon-sm" /> تماس با نمایشگاه</a>
      </div>
    </section>
  @elseif($st === 'held' && $isSeller)
    <section class="card-s dl-card coord">
      <h2>هماهنگی با خریدار</h2>
      <p>خریدار بیعانه را پرداخت کرده و اطلاعات تماس او برای هماهنگی در دسترس شماست.</p>
      <div class="co-row"><span class="co-ic"><x-icon name="user" class="icon-sm" /></span><span>{{ $d->buyer['name'] }}</span></div>
      <div class="co-row"><span class="co-ic"><x-icon name="phone" class="icon-sm" /></span><span>شماره تماس : <b class="ltr d-inline-block">{{ $d->buyer['phone'] }}</b></span></div>
      <div class="co-actions">
        <button type="button" class="btn btn-soft" id="remindBuyer" data-deal="{{ $d->id }}"><x-icon name="bell-ring" class="icon-sm" /> یادآوری به خریدار</button>
        <a href="{{ $tel }}" class="btn btn-primary"><x-icon name="phone" class="icon-sm" /> تماس با خریدار</a>
      </div>
    </section>
  @endif

  {{-- ===== توضیح امانت ===== --}}
  @if($st === 'held')
    @if(!$isSeller)<x-escrow-how :dealer="$dl->name" />@endif
    <section class="note-card">
      <span class="nc-ic"><x-icon name="shield" /></span>
      <div>
        <h3>تا تعیین نتیجه، مبلغ امانی است</h3>
        <p>{{ $isSeller ? 'پس از تأیید خریدار، بیعانه به کیف پول شما منتقل می‌شود؛ در غیر این صورت به خریدار برمی‌گردد.' : 'هماهنگی و ادامهٔ فرایند فقط در پلتفرم انجام می‌شود؛ در این وضعیت انتقالی به نمایشگاه ثبت نشده است.' }}</p>
      </div>
    </section>
  @elseif(!$isSeller)
    <p class="dl-foot">این رسید فقط انتقال بیعانه را نشان می‌دهد؛ بیعانه، تمام مبلغ فروش خودرو نیست.</p>
  @endif

  {{-- ===== دکمه‌ها ===== --}}
  @if($st === 'held' && !$isSeller)
    <div class="sticky-cta">
      <button type="button" class="btn btn-light-outline" data-print>رسید تراکنش</button>
      <a href="{{ route('deals.decide', $d->id) }}" class="btn btn-primary btn-lg flex-fill">تعیین وضعیت معامله</a>
    </div>
  @elseif($st === 'held' && $isSeller)
    <button type="button" class="btn btn-soft btn-lg w-100" data-bs-toggle="offcanvas" data-bs-target="#reportSheet"><x-icon name="alert" class="icon-sm" /> گزارش مشکل</button>
  @elseif($st === 'review')
    @if($isSeller)
      <button type="button" class="btn btn-primary btn-lg w-100" data-bs-toggle="offcanvas" data-bs-target="#statementSheet"><x-icon name="message" class="icon-sm" /> ارسال توضیح نمایشگاه</button>
    @else
      <a href="{{ route('help') }}" class="btn btn-primary btn-lg w-100">پیگیری با پشتیبانی</a>
    @endif
    <a href="{{ $isSeller ? route('panel', ['tab' => 'deals']) : route('account.reservations') }}" class="btn btn-soft btn-lg w-100">{{ $isSeller ? 'بازگشت به معاملات' : 'بازگشت به معاملات من' }}</a>
  @else
    <button type="button" class="btn btn-primary btn-lg w-100" data-print>{{ $isSeller ? 'مشاهده و ذخیرهٔ رسید' : 'مشاهده و ذخیرهٔ رسید انتقال بیعانه' }}</button>
    <a href="{{ $isSeller ? route('panel', ['tab' => 'deals']) : route('account.reservations') }}" class="btn btn-soft btn-lg w-100">{{ $isSeller ? 'بازگشت به معاملات' : 'بازگشت به معاملات من' }}</a>
  @endif
</div>

{{-- فروشنده: گزارش مشکل / توضیح --}}
@if($isSeller)
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="reportSheet" aria-labelledby="reportTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="reportTitle">گزارش مشکل</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <label class="form-label" for="reportText">مشکل را بنویسید</label>
    <textarea class="form-control" id="reportText" rows="4" maxlength="400" placeholder="مثلاً خریدار پاسخ تماس نمی‌دهد…"></textarea>
    <button type="button" class="btn btn-primary btn-lg w-100 mt-3" id="sendReport" data-deal="{{ $d->id }}">ارسال به پشتیبانی</button>
  </div>
</div>
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="statementSheet" aria-labelledby="stmtTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="stmtTitle">توضیح نمایشگاه دربارهٔ اختلاف</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p class="small text-muted-s">پشتیبانی توضیح شما را همراه ادعای خریدار بررسی می‌کند.</p>
    <textarea class="form-control" id="stmtText" rows="5" maxlength="600" placeholder="روایت خود را بنویسید…"></textarea>
    <button type="button" class="btn btn-primary btn-lg w-100 mt-3" id="sendStatement" data-deal="{{ $d->id }}">ارسال توضیح</button>
  </div>
</div>
@endif
@endsection
