@extends('layouts.app')
@section('title', 'رادار: ' . $title)
@section('page_title', 'رادار خودرو')
@section('page_subtitle', $title)
@section('back', route('radar.index'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/radar.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/radar.js') }}"></script>@endpush

@section('content')
@php $off = $r['status'] !== 'active'; @endphp
<section class="rd-card rd-sum" data-radar="{{ $r['id'] }}" data-delete-url="{{ route('radar.destroy', $r['id']) }}" data-after="{{ route('radar.index') }}">
  <header class="rd-head">
    <div class="rd-ttl"><h3>{{ $title }}</h3><small>ثبت شده {{ $r['created'] }}</small></div>
    @if($off)<span class="badge-s badge-warn">متوقف توسط مدیریت</span>@else<span class="badge-s badge-success"><span class="rd-pulse" aria-hidden="true"></span>فعال</span>@endif
  </header>
  <div class="rd-chips">@foreach($chips as $ch)<span>{{ $ch }}</span>@endforeach</div>
  <p class="rd-keep"><x-icon name="info" class="icon-sm" /> این رادار تا وقتی حذفش نکنید فعال می‌ماند و آگهی‌های جدید هم بررسی می‌شوند.</p>
  <footer class="rd-act"><button type="button" class="btn btn-soft rd-del" data-del>حذف رادار</button></footer>
</section>

@if(count($items))
  <div class="seg-tabs" id="rdTabs" role="tablist" aria-label="سطح تطابق">
    <button type="button" class="on" data-min="0" role="tab">همه <span data-fa>{{ count($items) }}</span></button>
    <button type="button" data-min="80" role="tab">تطابق بالا <span data-fa>{{ $high }}</span></button>
  </div>
  <div class="rd-results" id="rdResults">
    @foreach($items as $i)
      <article class="rd-match" data-percent="{{ $i['percent'] }}">
        <x-listing-row :item="$i" />
        <div class="rd-why">
          <x-radar-pct :value="$i['percent']" :label="true" />
          <ul>@foreach($i['notes'] as $n)<li class="{{ $n['ok'] ? 'ok' : 'no' }}"><x-icon :name="$n['ok'] ? 'check' : 'alert'" class="icon-sm" />{{ $n['t'] }}</li>@endforeach</ul>
        </div>
      </article>
    @endforeach
  </div>
  <div class="d-none" id="rdNone"><x-empty-state icon="search" title="موردی با این سطح تطابق نیست" text="نزدیک‌ترین‌ها را در «همه» ببینید." /></div>
@else
  <x-empty-state icon="clock" title="هنوز مورد مشابهی نیست" text="اشکالی ندارد؛ رادار فعال می‌ماند و به‌محض ثبت آگهی‌ی مناسب خبرتان می‌کنیم." action="ثبت رادار دیگر" :href="route('radar.create')" />
@endif

<div class="modal fade" id="rdConfirm" tabindex="-1" aria-labelledby="rdConfirmT" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-3">
    <h5 id="rdConfirmT" class="mb-2">حذف این رادار؟</h5>
    <p class="text-muted-s mb-3">بعد از حذف، دیگر برای این مشخصات آگهی پیدا نمی‌کنیم و اعلانی نمی‌گیرید.</p>
    <div class="d-flex gap-2"><button type="button" class="btn btn-soft flex-fill" data-bs-dismiss="modal">انصراف</button><button type="button" class="btn btn-danger flex-fill" id="rdConfirmYes">حذف رادار</button></div>
  </div></div>
</div>
@endsection
