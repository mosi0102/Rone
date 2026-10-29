@extends('admin.layouts.app')
@section('title', 'درخواست ' . $r['code'])

@push('scripts')<script src="{{ asset('admin-assets/js/requests.js') }}"></script>@endpush

@section('content')
@php
  $cats = ['car' => 'سواری', 'heavy' => 'سنگین', 'bike' => 'موتور', 'bicycle' => 'دوچرخه'];
  $st = $r['status'];
  $pill = fn ($p) => $p >= 85 ? 'ok' : ($p >= 60 ? 'info' : 'warn');
@endphp

<x-admin.page-head :title="$r['summary']" :subtitle="'درخواست ' . $r['code'] . ' • ' . $r['user']['name']">
  <a href="{{ route('admin.requests.index') }}" class="btn btn-soft btn-sm"><x-admin.icon name="arrow-left" class="icon-sm" style="transform:scaleX(-1)" /> بازگشت به لیست</a>
  <span data-d="status"><x-admin.status :status="$st" /></span>
</x-admin.page-head>

<div class="dash">
  <div class="dash-main">
    <section class="card-a rise" style="--d:1" aria-labelledby="mTitle">
      <div class="card-head"><h2 id="mTitle">آگهی‌های نزدیک به درخواست</h2><span class="ms-auto text-muted small"><span data-fa>{{ count($r['matches']) }}</span> مورد</span></div>
      @if(count($r['matches']))
        <ul class="list-rows">
          @foreach($r['matches'] as $m)
            <li>
              <a class="list-row match-row" href="{{ route('admin.listings.index') }}">
                <span class="thumb" style="background-image:url('{{ $m['image'] }}')"></span>
                <span class="lr-body"><b>{{ $m['title'] }}</b><small>{{ $m['dealer'] }} • {{ $m['city'] }} • <x-admin.money :value="$m['price']" /></small></span>
                <span class="match-meter"><span class="match-pill {{ $pill($m['percent']) }}"><span data-fa>{{ $m['percent'] }}</span>٪</span><i style="--p:{{ $m['percent'] }}%"></i></span>
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <x-admin.empty icon="search" title="هنوز آگهی مشابهی پیدا نشده" text="تا فعال بودن درخواست، با هر آگهی جدید دوباره بررسی می‌شود و به کاربر اعلان می‌رسد." />
      @endif
    </section>
  </div>

  <aside class="dash-aside">
    <section class="card-a rise" style="--d:2" aria-labelledby="cTitle">
      <div class="card-head"><h2 id="cTitle">مشخصات درخواستی</h2></div>
      <dl class="info-list">
        <div><dt>دسته</dt><dd>{{ $cats[$r['cat']] }}</dd></div>
        <div><dt>برند / مدل</dt><dd>{{ $r['model'] }}</dd></div>
        <div><dt>سال ساخت</dt><dd>{{ $r['year_from'] }} به بعد</dd></div>
        <div><dt>سقف بودجه</dt><dd><x-admin.money :value="$r['budget']" /></dd></div>
        <div><dt>سقف کارکرد</dt><dd>{{ $r['km_max'] }}</dd></div>
        <div><dt>شهر</dt><dd>{{ $r['city'] }}</dd></div>
        <div><dt>ثبت</dt><dd><span data-jdate="{{ $r['created_at'] }}" data-jfmt="date"></span></dd></div>
      </dl>
      @if($st === 'active')
        <button type="button" class="btn btn-bad w-100 act-active js-suspend" data-title="{{ $r['summary'] }}"><x-admin.icon name="x" class="icon-sm" /> تعلیق درخواست</button>
      @elseif($st === 'suspended')
        <div class="note-box bad"><x-admin.icon name="alert" /><div><b>این درخواست تعلیق شده است</b><p>جستجو و اعلان آن متوقف است.</p></div></div>
      @else
        <div class="note-box"><x-admin.icon name="info" /><div><b>کاربر این درخواست را لغو کرده است</b></div></div>
      @endif
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>کاربر</h2></div>
      <div class="list-row card-a flat p-2">
        <span class="avatar">{{ mb_substr($r['user']['name'], 0, 1) }}</span>
        <span class="lr-body"><b>{{ $r['user']['name'] }}</b><small class="tnum" data-fa>{{ $r['user']['phone'] }}</small></span>
        <a href="{{ route('admin.users.index') }}" class="lr-end">پروفایل <x-admin.icon name="chevron-left" class="icon-sm" /></a>
      </div>
    </section>
  </aside>
</div>

<x-admin.reason-modal id="suspendModal" title="تعلیق درخواست" submit="تعلیق درخواست" label="دلیل تعلیق (برای کاربر ارسال می‌شود)"
  :chips="['مشخصات نامعتبر یا نامرتبط', 'درخواست تکراری', 'تخلف از قوانین سایت']">
  <p class="text-muted small mb-3">با تعلیق، جستجوی آگهی‌های مشابه و اعلان‌های این درخواست متوقف می‌شود.</p>
</x-admin.reason-modal>
@endsection
