@extends('admin.layouts.app')
@section('title', 'اختلاف ' . $x['code'])

@push('scripts')<script src="{{ asset('admin-assets/js/disputes.js') }}"></script>@endpush

@section('content')
@php
  $d = $x['deposit']; $st = $x['status']; $done = $st === 'resolved';
  $names = ['buyer' => 'خریدار', 'dealer' => 'نمایشگاه‌دار'];
  $other = $x['by'] === 'buyer' ? 'dealer' : 'buyer';
  $resTxt = ['release' => 'بیعانه به نمایشگاه‌دار آزاد شد', 'refund' => 'بیعانه به خریدار برگشت', 'split' => 'بیعانه بین طرفین تقسیم شد'];
@endphp

<x-admin.page-head :title="$x['subject']" :subtitle="'پرونده‌ی ' . $x['code'] . ' • ' . $d['title']">
  <a href="{{ route('admin.disputes.index') }}" class="btn btn-soft btn-sm"><x-admin.icon name="arrow-left" class="icon-sm" style="transform:scaleX(-1)" /> بازگشت به لیست</a>
  <span data-d="status"><x-admin.status :status="$st" /></span>
</x-admin.page-head>

<div class="dash">
  <div class="dash-main">
    <section class="card-a rise" style="--d:1">
      <div class="card-head"><h2>ادعای طرفین</h2></div>
      <div class="d-flex flex-column gap-3">
        <div class="claim"><h3>ادعای {{ $names[$x['by']] }} ({{ $x['by'] === 'buyer' ? $d['buyer']['name'] : $d['dealer'] }})</h3><p>{{ $x['text'] }}</p></div>
        <div class="claim"><h3>پاسخ {{ $names[$other] }}</h3><p>{{ $x['reply'] ?? 'هنوز پاسخی ثبت نشده است.' }}</p></div>
      </div>
      @if(count($x['evidence']))
        <h3 class="fs-6 mt-4 mb-2">مدارک پیوست</h3>
        <div class="evid">@foreach($x['evidence'] as $f)<span style="background-image:url('{{ asset('img/mock/' . $f) }}')"></span>@endforeach</div>
      @endif
    </section>

    <section class="card-a rise" style="--d:2">
      <div class="card-head"><h2>بخشی از گفتگوی طرفین</h2><a class="more" href="{{ route('admin.chats.index') }}">گفتگوی کامل <x-admin.icon name="chevron-left" class="icon-sm" /></a></div>
      <ul class="chat-bubbles">
        @foreach($x['messages'] as $m)
          <li class="cb {{ $m['from'] }}"><small>{{ $names[$m['from']] }} • <span data-jdate="{{ $m['at'] }}" data-jfmt="time"></span></small>{{ $m['text'] }}</li>
        @endforeach
      </ul>
    </section>
  </div>

  <aside class="dash-aside">
    <section class="card-a rise" style="--d:2" data-dispute data-amount="{{ $d['amount'] }}">
      <div class="card-head"><h2>تصمیم نهایی</h2></div>
      <dl class="info-list">
        <div><dt>مبلغ بیعانه</dt><dd><x-admin.money :value="$d['amount']" /></dd></div>
        <div><dt>خریدار</dt><dd>{{ $d['buyer']['name'] }}</dd></div>
        <div><dt>نمایشگاه‌دار</dt><dd>{{ $d['dealer'] }}</dd></div>
      </dl>
      @if($done)
        <div class="note-box ok"><x-admin.icon name="check-circle" /><div><b>{{ $resTxt[$x['resolution']['type']] }}</b><p>{{ $x['resolution']['note'] }}</p></div></div>
      @else
        <div data-decide>
          <div class="decide" role="radiogroup" aria-label="نتیجه">
            <label><input class="form-check-input" type="radio" name="res" value="refund"><span><b>برگشت کامل به خریدار</b><small>ادعای خریدار وارد است</small></span></label>
            <label><input class="form-check-input" type="radio" name="res" value="release"><span><b>آزادسازی به نمایشگاه‌دار</b><small>ادعا وارد نیست</small></span></label>
            <label><input class="form-check-input" type="radio" name="res" value="split"><span><b>تقسیم بین طرفین</b><small>درصد سهم خریدار را مشخص کنید</small></span></label>
          </div>
          <div class="split-box" data-split hidden>
            <label class="form-label" for="splitRange">سهم خریدار: <b data-split-v data-fa>۵۰</b>٪</label>
            <input type="range" class="form-range" id="splitRange" min="10" max="90" step="10" value="50">
            <small class="text-muted d-block">خریدار <b data-buyer-amt></b> • نمایشگاه‌دار <b data-dealer-amt></b> تومان</small>
          </div>
          <label class="form-label" for="resNote">توضیح تصمیم (برای هر دو طرف ارسال می‌شود)</label>
          <textarea class="form-control mb-3" id="resNote" rows="3" maxlength="400" placeholder="دلیل تصمیم را بنویسید…"></textarea>
          <button type="button" class="btn btn-ink w-100" id="resolveBtn" disabled>ثبت تصمیم نهایی</button>
        </div>
        <div data-resolved hidden class="note-box ok"><x-admin.icon name="check-circle" /><div><b data-res-title></b><p>پرونده بسته شد و برای دو طرف پیام ارسال می‌شود.</p></div></div>
      @endif
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>بیعانه</h2></div>
      <div class="gal-main mb-3" style="background-image:url('{{ $d['image'] }}'); aspect-ratio: 16/9"></div>
      <a href="{{ route('admin.deposits.show', $d['id']) }}" class="btn btn-soft w-100">مشاهده‌ی بیعانه <span class="tnum" dir="ltr">{{ $d['code'] }}</span></a>
    </section>
  </aside>
</div>
@endsection
