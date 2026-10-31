@extends('layouts.app')
@section('title', 'فهرست مزایده‌ها')
@section('page_title', 'فهرست مزایده‌ها')
@section('page_subtitle', 'مشاهده مزایده های فعال')

@push('styles')<link rel="stylesheet" href="{{ asset('css/auction.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/auction.js') }}"></script>@endpush

@section('content')
@php
  $all = collect($auctions); $liveN = $all->where('status', 'live')->count(); $soonN = $all->where('status', 'scheduled')->count();
@endphp
<h2 class="auc-h">فرصت‌های مزایده</h2>
<p class="auc-sum"><span data-fa>{{ $all->count() }}</span> مزایده • <span data-fa>{{ $liveN }}</span> در حال برگزاری • <span data-fa>{{ $soonN }}</span> پیش رو</p>

{{-- دسته‌بندی --}}
<div class="auc-cats" id="aucCats" role="tablist" aria-label="دسته‌بندی">
  <button type="button" class="on" data-cat="all" role="tab"><span>همه</span></button>
  <button type="button" data-cat="car" role="tab"><x-icon name="car" />سواری</button>
  <button type="button" data-cat="heavy" role="tab"><x-icon name="truck" />سنگین</button>
  <button type="button" data-cat="bike" role="tab"><x-icon name="bike" />موتور</button>
  <button type="button" data-cat="bicycle" role="tab"><x-icon name="bicycle" />دوچرخه</button>
</div>

{{-- وضعیت --}}
<div class="seg-tabs" id="aucStatus" role="tablist" aria-label="وضعیت مزایده">
  <button type="button" class="on" data-status="all" role="tab">همه <span data-fa>{{ $all->count() }}</span></button>
  <button type="button" data-status="live" role="tab">در حال برگزاری <span data-fa>{{ $liveN }}</span></button>
  <button type="button" data-status="scheduled" role="tab">پیش رو <span data-fa>{{ $soonN }}</span></button>
</div>

<div class="auc-list" id="aucList">
  @foreach($auctions as $a)<x-auction-card :a="$a" />@endforeach
</div>
<div class="d-none" id="aucEmpty"><x-empty-state icon="gavel" title="مزایده‌ای در این دسته نیست" text="دسته یا وضعیت دیگری را انتخاب کنید." /></div>

<section class="note-card">
  <span class="nc-ic"><x-icon name="clock" /></span>
  <div>
    <h3>شمارش معکوس از ۲۴ ساعت قبل</h3>
    <p>تایمر شروع فقط در ۲۴ ساعت مانده به زمان تعیین‌شده فعال می‌شود. پیش از شروع، ثبت پیشنهاد امکان‌پذیر نیست.</p>
  </div>
</section>
@endsection
