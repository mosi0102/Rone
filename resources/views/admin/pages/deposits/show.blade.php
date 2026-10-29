@extends('admin.layouts.app')
@section('title', 'بیعانه ' . $d['code'])

@push('scripts')<script src="{{ asset('admin-assets/js/deposits.js') }}"></script>@endpush

@section('content')
@php $st = $d['status']; $held = $st === 'held'; @endphp

<x-admin.page-head :title="$d['title']" :subtitle="'بیعانه‌ی ' . $d['code'] . ' • ' . $d['dealer']">
  <a href="{{ route('admin.deposits.index') }}" class="btn btn-soft btn-sm"><x-admin.icon name="arrow-left" class="icon-sm" style="transform:scaleX(-1)" /> بازگشت به لیست</a>
  <span data-d="status"><x-admin.status :status="$st" /></span>
</x-admin.page-head>

<div class="dash" data-deposit-page>
  <div class="dash-main">
    <section class="card-a dep-hero auction-hero rise" style="--d:1" data-deposit data-status="{{ $st }}">
      <div class="ah-price">
        <span class="ah-l">مبلغ بیعانه</span>
        <div class="ah-v"><span data-money="{{ $d['amount'] }}" class="tnum">{{ strtr(number_format($d['amount']), array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬'])) }}</span> <small>تومان</small></div>
        <small class="text-muted"><span data-fa>{{ $d['percent'] }}</span>٪ از قیمت <x-admin.money :value="$d['price']" /></small>
      </div>
      @if($held && $d['hold_until'])
      <div class="ah-time" data-hold-box>
        <span class="ah-l">مهلت هماهنگی معامله</span>
        <div class="ah-t ah-t-sm timer" data-countdown="{{ $d['hold_until'] }}"></div>
      </div>
      @endif
    </section>

    <section class="card-a rise" style="--d:2">
      <div class="card-head"><h2>طرفین معامله</h2></div>
      <div class="party">
        <div><small>خریدار</small><b>{{ $d['buyer']['name'] }}</b><div class="text-muted tnum" data-fa>{{ $d['buyer']['phone'] }}</div></div>
        <div><small>نمایشگاه‌دار</small><b>{{ $d['dealer'] }}</b><div class="text-muted">کد آگهی <span data-fa>{{ $d['listing_code'] }}</span></div></div>
      </div>
      <a href="{{ route('admin.chats.index') }}" class="btn btn-soft btn-sm mt-3"><x-admin.icon name="message" class="icon-sm" /> مشاهده‌ی گفتگوی طرفین</a>
    </section>

    <section class="card-a rise" style="--d:3" aria-labelledby="evTitle">
      <div class="card-head"><h2 id="evTitle">رویدادها</h2></div>
      <ul class="timeline" id="depEvents">
        @foreach($d['events'] as $e)
          <li class="{{ $e['tone'] }}"><b>{{ $e['label'] }}</b><small data-jdate="{{ $e['t'] }}"></small></li>
        @endforeach
      </ul>
    </section>
  </div>

  <aside class="dash-aside" aria-label="اقدام و خودرو">
    <section class="card-a rise" style="--d:2">
      <div class="card-head"><h2>تصمیم درباره‌ی بیعانه</h2></div>
      @if($held)
        <p class="text-muted small">فقط وقتی بیعانه در امانت است می‌توان آن را آزاد یا برگشت داد.</p>
        <button type="button" class="btn btn-ok w-100 mb-2 act-held js-release" data-confirm="بیعانه به کیف پول نمایشگاه‌دار منتقل شود؟" data-confirm-text="این کار قابل بازگشت نیست." data-confirm-ok="آزادسازی بیعانه"><x-admin.icon name="check" class="icon-sm" /> آزادسازی به نمایشگاه‌دار</button>
        <button type="button" class="btn btn-bad w-100 act-held js-refund" data-title="{{ $d['title'] }}"><x-admin.icon name="undo" class="icon-sm" /> برگشت به خریدار</button>
      @elseif($st === 'dispute')
        <div class="note-box bad"><x-admin.icon name="alert-triangle" /><div><b>بیعانه در اختلاف است</b><p>تصمیم‌گیری از صفحه‌ی اختلاف انجام می‌شود.</p></div></div>
        <a href="{{ route('admin.disputes.index') }}" class="btn btn-ink w-100 mt-3">رفتن به اختلاف‌ها</a>
      @else
        <div class="note-box"><x-admin.icon name="info" /><div><b>این بیعانه نهایی شده است</b></div></div>
      @endif
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>خودرو</h2></div>
      <div class="gal-main mb-3" style="background-image:url('{{ $d['image'] }}'); aspect-ratio: 16/9"></div>
      <dl class="info-list mb-0">
        <div><dt>نوع فروش</dt><dd>{{ $d['source'] === 'auction' ? 'مزایده' : 'فروش فوری' }}</dd></div>
        <div><dt>ثبت بیعانه</dt><dd><span data-jdate="{{ $d['created_at'] }}" data-jfmt="date"></span></dd></div>
      </dl>
    </section>
  </aside>
</div>

<x-admin.reason-modal id="refundModal" title="برگشت بیعانه به خریدار" submit="برگشت بیعانه" icon="undo" tone="info" label="دلیل برگشت (برای خریدار و نمایشگاه‌دار ارسال می‌شود)"
  :chips="['انصراف نمایشگاه‌دار از فروش', 'فروش خودرو به شخص دیگر', 'مغایرت خودرو با آگهی', 'درخواست خریدار در مهلت مجاز']">
  <p class="text-muted small mb-3">مبلغ بیعانه به کیف پول خریدار برگردانده می‌شود.</p>
</x-admin.reason-modal>
@endsection
