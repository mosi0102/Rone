@extends('layouts.app')
@section('title', 'مشاهده روی نقشه')
@section('page_title', 'آگهی‌ها روی نقشه')
@section('page_subtitle', 'دسته را انتخاب کنید و روی قیمت‌ها بزنید')
@section('back', route('listings.index', $query))
<x-map-assets />

@section('content')
@php $cat = $query['category'] ?? ''; @endphp
<h1 class="visually-hidden">مشاهده‌ی آگهی‌ها روی نقشه</h1>
<div class="mb" data-map-browse data-url="{{ route('listings.map.data') }}" data-query='@json((object) $query)'>
  <div class="mb-canvas" role="application" aria-label="نقشه‌ی آگهی‌ها"></div>

  <div class="mb-top">
    <div class="mb-cats" role="group" aria-label="دسته‌ی آگهی">
      <button type="button" data-mb-cat="" class="{{ $cat === '' ? 'on' : '' }}" aria-pressed="{{ $cat === '' ? 'true' : 'false' }}">همه</button>
      @foreach($lf['short_titles'] as $k => $t)
        <button type="button" data-mb-cat="{{ $k }}" class="{{ $cat === $k ? 'on' : '' }}" aria-pressed="{{ $cat === $k ? 'true' : 'false' }}"><x-icon :name="$lf['icons'][$k]" />{{ $t }}</button>
      @endforeach
    </div>
    <div class="mb-row">
      <select name="city" data-mb-field aria-label="شهر">
        <option value="">همه‌ی شهرها</option>
        @foreach($lf['cities'] as $c)<option {{ ($query['city'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach
      </select>
      <select name="sale_type" data-mb-field aria-label="نوع آگهی">
        @foreach(['' => 'همه‌ی آگهی‌ها', 'instant' => 'فقط فوری', 'auction' => 'فقط مزایده'] as $v => $t)<option value="{{ $v }}" {{ ($query['sale_type'] ?? '') === $v ? 'selected' : '' }}>{{ $t }}</option>@endforeach
      </select>
    </div>
  </div>

  <p class="mb-empty" data-mb-empty hidden>آگهی‌ای با این فیلتر پیدا نشد.</p>
  <span class="mb-count"><span data-mb-count>۰</span> آگهی روی نقشه</span>
  <div class="mb-tools">
    <button type="button" class="btn btn-light btn-sm" data-locate aria-label="موقعیت من"><x-icon name="navigation" class="icon-sm" /></button>
    <a href="{{ route('listings.index', $query) }}" class="btn btn-primary btn-sm"><x-icon name="layout" class="icon-sm" /> نمایش فهرست</a>
  </div>

  <a class="mb-card" hidden href="#">
    <img src="" alt="" loading="lazy">
    <span class="mbc-b">
      <span class="mbc-badge" hidden></span>
      <b class="mbc-t"></b>
      <span class="mbc-m"></span>
      <span class="mbc-p"></span>
      <span class="mbc-l"></span>
    </span>
  </a>
</div>
@endsection
