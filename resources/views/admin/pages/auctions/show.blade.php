@extends('admin.layouts.app')
@section('title', 'مزایده ' . $a['title'])

@push('scripts')<script src="{{ asset('admin-assets/js/auction-show.js') }}"></script>@endpush

@section('content')
@php
  $fa = fn ($v) => strtr((string) $v, array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬']));
  $st = $a['status'];
  $live = $st === 'live'; $scheduled = $st === 'scheduled';
  $hasWinner = in_array($st, ['awaiting_pay', 'defaulted', 'ended'], true);
  $topPrice = $a['top'] ?: $a['base'];
  $depositAmount = (int) round($a['top'] * $a['deposit_percent'] / 100);
  // وضعیت قدم‌های تایم‌لاین: done | current | wait | fail
  $steps = [['ثبت و زمان‌بندی', 'done'], ['شروع مزایده', 'wait'], ['پایان مزایده', 'wait'], ['پرداخت بیعانه‌ی برنده', 'wait'], ['هماهنگی و معامله', 'wait']];
  $map = [
    'scheduled' => ['done', 'current', 'wait', 'wait', 'wait'], 'live' => ['done', 'done', 'current', 'wait', 'wait'],
    'awaiting_pay' => ['done', 'done', 'done', 'current', 'wait'], 'defaulted' => ['done', 'done', 'done', 'fail', 'wait'],
    'ended' => ['done', 'done', 'done', 'done', 'done'], 'cancelled' => ['done', 'wait', 'wait', 'wait', 'wait'],
  ];
  foreach ($steps as $i => $s) { $steps[$i][1] = $map[$st][$i]; }
  // اختلاف هر پیشنهاد با پیشنهاد قبلی
  $rows = $a['bids']; $n = count($rows);
  foreach ($rows as $i => $b) { $prev = $i + 1 < $n ? $rows[$i + 1]['amount'] : $a['base']; $rows[$i]['delta'] = $b['amount'] - $prev; }
@endphp

<x-admin.page-head :title="$a['title']" :subtitle="'مزایده‌ی کد ' . $fa($a['code']) . ' • ' . $a['dealer']">
  <a href="{{ route('admin.auctions.index') }}" class="btn btn-soft btn-sm"><x-admin.icon name="arrow-left" class="icon-sm" style="transform:scaleX(-1)" /> بازگشت به لیست</a>
  <span data-d="status"><x-admin.status :status="$st" /></span>
  @if($live || $scheduled)<button type="button" class="btn btn-bad btn-sm" id="cancelBtn"><x-admin.icon name="x" class="icon-sm" /> لغو مزایده</button>@endif
</x-admin.page-head>

<div class="dash">
  <div class="dash-main">

    {{-- ===== بالاترین پیشنهاد و زمان ===== --}}
    <section class="card-a auction-hero rise" style="--d:1" data-auction data-status="{{ $st }}" data-step="{{ $a['step'] }}" data-base="{{ $a['base'] }}" data-entry="{{ $a['entry'] }}"
             data-ends="{{ $live ? $a['ends_at'] : ($scheduled ? $a['starts_at'] : '') }}" data-users="{{ json_encode($a['users'], JSON_UNESCAPED_UNICODE) }}" @if($live) data-simulate @endif>
      <div class="ah-price">
        <span class="ah-l" data-ah-label>{{ $a['top'] ? 'بالاترین پیشنهاد' : 'قیمت پایه' }}</span>
        <div class="ah-v"><span data-top data-money="{{ $topPrice }}">{{ $fa(number_format($topPrice)) }}</span> <small>تومان</small></div>
        <div class="ah-who"><x-admin.icon name="user" class="icon-sm" /> <span data-leader>{{ $a['leader'] ?? 'هنوز پیشنهادی ثبت نشده' }}</span></div>
        <small class="text-muted">حداقل گام پیشنهاد: <x-admin.money :value="$a['step']" /></small>
      </div>
      <div class="ah-time">
        @if($live)
          <span class="ah-l" data-time-label>پایان مزایده تا</span>
          <div class="ah-t timer" data-countdown="{{ $a['ends_at'] }}" data-main-timer></div>
          <span class="live-pill" data-live-pill><i></i> اتصال زنده</span>
        @elseif($scheduled)
          <span class="ah-l" data-time-label>شروع مزایده تا</span>
          <div class="ah-t timer" data-countdown="{{ $a['starts_at'] }}" data-main-timer></div>
          <small class="text-muted"><span data-jdate="{{ $a['starts_at'] }}"></span></small>
        @else
          <span class="ah-l" data-time-label>{{ $st === 'cancelled' ? 'لغو شد' : 'پایان‌یافته در' }}</span>
          <div class="ah-t ah-t-sm"><span data-jdate="{{ $a['ends_at'] }}" data-jfmt="date"></span></div>
        @endif
      </div>
    </section>

    {{-- ===== شاخص‌ها ===== --}}
    <section class="row g-3" aria-label="آمار مزایده">
      <div class="col-6 rise" style="--d:2"><x-admin.kpi icon="wallet" tone="sand" label="قیمت پایه" :value="$a['base']" unit="تومان" :money="true" /></div>
      <div class="col-6 rise" style="--d:3" data-kpi="bids"><x-admin.kpi icon="gavel" tone="sage" label="تعداد پیشنهادها" :value="$a['bids_count']" /></div>
      <div class="col-6 rise" style="--d:4" data-kpi="people"><x-admin.kpi icon="users" tone="sky" label="شرکت‌کنندگان" :value="$a['participants']" /></div>
      <div class="col-6 rise" style="--d:5" data-kpi="entry"><x-admin.kpi icon="receipt" tone="peach" label="درآمد ورودی" :value="$a['entry'] * $a['participants']" unit="تومان" :money="true" /></div>
    </section>

    {{-- ===== تاریخچه‌ی پیشنهادها ===== --}}
    <section class="card-a rise" style="--d:3" aria-labelledby="bidsTitle">
      <div class="card-head">
        <h2 id="bidsTitle">تاریخچه‌ی پیشنهادها</h2>
        @if($live)<span class="live-pill ms-auto"><i></i> زنده</span>@endif
      </div>
      <ul class="bid-list adm-scroll" id="bidFeed" aria-live="polite">
        @foreach($rows as $i => $b)
          <li class="bid-row {{ $i === 0 ? 'is-top' : '' }}">
            <span class="rank" data-fa>{{ $b['n'] }}</span>
            <span class="avatar sm">{{ mb_substr($b['user']['name'], 0, 1) }}</span>
            <div class="who"><b>{{ $b['user']['name'] }}</b><small class="tnum" data-fa>{{ $b['user']['phone'] }}</small></div>
            <div class="amt"><b><span data-money="{{ $b['amount'] }}">{{ $fa(number_format($b['amount'])) }}</span> <small>تومان</small></b><small><span class="up">+<span data-money="{{ $b['delta'] }}">{{ $fa(number_format($b['delta'])) }}</span></span> • <span data-jdate="{{ $b['at'] }}" data-jfmt="time"></span></small></div>
          </li>
        @endforeach
      </ul>
      <div data-no-bids @if(count($rows)) hidden @endif>
        <x-admin.empty icon="gavel" title="هنوز پیشنهادی ثبت نشده" text="پس از شروع مزایده، پیشنهادها به‌صورت لحظه‌ای اینجا نمایش داده می‌شوند." />
      </div>
    </section>
  </div>

  {{-- ===== ستون کناری ===== --}}
  <aside class="dash-aside" aria-label="جزئیات و وضعیت">

    @if($st === 'cancelled')
      <section class="card-a rise" style="--d:2"><div class="note-box bad"><x-admin.icon name="alert" /><div><b>این مزایده لغو شده است</b><p>دلیل: {{ $a['cancel_reason'] }}</p></div></div></section>
    @endif

    {{-- پرداخت بیعانه‌ی برنده --}}
    @if($hasWinner)
    <section class="card-a rise" style="--d:2" data-pay-card aria-labelledby="payTitle">
      <div class="card-head"><h2 id="payTitle">برنده و پرداخت بیعانه</h2></div>
      <div class="list-row card-a flat p-2 mb-3">
        <span class="avatar">{{ mb_substr($a['winner']['name'], 0, 1) }}</span>
        <span class="lr-body"><b>{{ $a['winner']['name'] }}</b><small class="tnum" data-fa>{{ $a['winner']['phone'] }}</small></span>
        <a href="{{ route('admin.users.index') }}" class="lr-end">پروفایل <x-admin.icon name="chevron-left" class="icon-sm" /></a>
      </div>
      <dl class="info-list">
        <div><dt>مبلغ برنده‌شده</dt><dd><x-admin.money :value="$a['top']" /></dd></div>
        <div><dt>بیعانه (<span data-fa>{{ $a['deposit_percent'] }}</span>٪ قابل‌برگشت)</dt><dd><x-admin.money :value="$depositAmount" /></dd></div>
        <div><dt>هزینه‌ی خدمات (برگشت‌ناپذیر)</dt><dd><x-admin.money :value="$a['service_fee']" /></dd></div>
        <div class="total"><dt>جمع قابل پرداخت</dt><dd><x-admin.money :value="$depositAmount + $a['service_fee']" /></dd></div>
      </dl>

      <div data-pay-state="waiting" @if($a['pay'] !== 'waiting') hidden @endif>
        <div class="note-box warn"><x-admin.icon name="clock" /><div><b>در انتظار پرداخت بیعانه</b><p>مهلت باقی‌مانده: <span class="timer fw-bold" data-countdown="{{ $a['pay_deadline'] }}"></span></p></div></div>
      </div>
      <div data-pay-state="overdue" @if($a['pay'] !== 'overdue') hidden @endif>
        <div class="note-box bad"><x-admin.icon name="alert-triangle" /><div><b>مهلت پرداخت تمام شده است</b><p>برنده بیعانه را پرداخت نکرده. می‌توانید او را از مزایده‌ها محروم کنید.</p></div></div>
        <button type="button" class="btn btn-danger w-100 mt-3" id="banBtn"><x-admin.icon name="lock" class="icon-sm" /> بن کردن برنده</button>
      </div>
      <div data-pay-state="banned" @if($a['pay'] !== 'banned') hidden @endif>
        <div class="note-box bad"><x-admin.icon name="lock" /><div><b>برنده از مزایده‌ها محروم شده</b><p>تا <span data-jdate="{{ $a['banned_until'] ?? now()->toIso8601String() }}" data-jfmt="date"></span></p></div></div>
        <button type="button" class="btn btn-soft w-100 mt-3" id="unbanBtn"><x-admin.icon name="undo" class="icon-sm" /> رفع محرومیت</button>
      </div>
      <div data-pay-state="paid" @if($a['pay'] !== 'paid') hidden @endif>
        <div class="note-box ok"><x-admin.icon name="check-circle" /><div><b>بیعانه پرداخت شد</b><p>اطلاعات نمایشگاه برای هماهنگی معامله به برنده ارسال شده است.</p></div></div>
      </div>
    </section>
    @endif

    {{-- خودرو و نمایشگاه --}}
    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>خودرو و نمایشگاه</h2></div>
      <div class="gal-main mb-3" style="background-image:url('{{ $a['image'] }}'); aspect-ratio: 16/9"></div>
      <div class="list-row card-a flat p-2">
        <span class="lr-ic t-sky"><x-admin.icon name="store" /></span>
        <span class="lr-body"><b>{{ $a['dealer'] }}</b><small>کد آگهی <span data-fa>{{ $a['code'] }}</span></small></span>
        <a href="{{ route('admin.listings.index') }}" class="lr-end">آگهی <x-admin.icon name="chevron-left" class="icon-sm" /></a>
      </div>
    </section>

    {{-- تایم‌لاین --}}
    <section class="card-a rise" style="--d:4" aria-labelledby="tlTitle">
      <div class="card-head"><h2 id="tlTitle">روند مزایده</h2></div>
      <ol class="tline" data-tline>
        @foreach($steps as [$label, $state])
          <li class="tl-step is-{{ $state }}"><span class="tl-dot">@if($state === 'done')<x-admin.icon name="check" />@elseif($state === 'fail')<x-admin.icon name="x" />@endif</span><span class="tl-l">{{ $label }}</span></li>
        @endforeach
      </ol>
    </section>
  </aside>
</div>

<x-admin.reason-modal id="cancelModal" title="لغو مزایده" submit="لغو مزایده" label="دلیل لغو (برای نمایشگاه‌دار و شرکت‌کنندگان ارسال می‌شود)"
  :chips="['درخواست نمایشگاه‌دار', 'مغایرت اطلاعات خودرو', 'تخلف از قوانین سایت', 'مشکل فنی']">
  <p class="text-muted small mb-3">ورودی پرداخت‌شده‌ی شرکت‌کنندگان به کیف پول آن‌ها برگردانده می‌شود.</p>
</x-admin.reason-modal>

<x-admin.reason-modal id="banModal" title="بن کردن برنده" submit="بن کردن" icon="lock" label="دلیل محرومیت (برای کاربر ارسال می‌شود)"
  :chips="['عدم پرداخت بیعانه در مهلت مقرر', 'اختلال عمدی در مزایده']">
  <label class="form-label" for="banDays">مدت محرومیت از مزایده‌ها</label>
  <select class="form-select mb-3" id="banDays">
    <option value="7">۷ روز</option><option value="14">۱۴ روز</option><option value="30" selected>۳۰ روز (پیش‌فرض تنظیمات)</option><option value="90">۹۰ روز</option><option value="180">۱۸۰ روز</option>
  </select>
</x-admin.reason-modal>
@endsection
