@extends('layouts.app')
@php
  $isSearch = $mode === 'search';
  $fa = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
  $myDealer = $auth->dealer_mode ? $auth->dealer->id : null;     // نمایشگاه‌دار: دکمه ویرایش روی آگهی‌های خودش
  $hidden = collect($query)->except(['q', 'page'])->all();
  $popular = ['پژو ۲۰۷', 'ولوو FH500', 'یاماها NMAX', 'سمند', 'تیبا', 'کامیون کشنده', 'هیوندای النترا'];   // جست‌وجوهای پرطرفدار (از بک‌اند)
@endphp
@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', $subtitle)
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/listings.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/filters.js') }}"></script><script src="{{ asset('js/listings.js') }}"></script>@endpush

@section('content')
<div class="ls-layout">
  {{-- ستون فیلتر (فقط دسکتاپ؛ در موبایل صفحه جداگانه «فیلترها» باز می‌شود) --}}
  <aside class="ls-aside d-none d-lg-block" aria-label="فیلترها">
    <x-filter-form :f="$query" :lf="$lf" :count="$total" :action="route($isSearch ? 'search' : 'listings.index')" :sticky="false" idp="side" />
  </aside>

  <div class="ls-main">
    {{-- تب دسته --}}
    <nav class="seg-tabs ls-tabs" aria-label="دسته‌بندی">
      @foreach($tabs as $t)<a href="{{ $t['url'] }}" class="{{ $t['on'] ? 'on' : '' }}" @if($t['on']) aria-current="true" @endif>{{ $t['title'] }}</a>@endforeach
    </nav>

    {{-- جست‌وجو --}}
    <form class="ls-search" action="{{ route('search') }}" role="search" id="lsSearch">
      @foreach($hidden as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
      <button type="submit" class="ls-go" aria-label="جست‌وجو"><x-icon name="search" /></button>
      <input type="text" class="ls-input" name="q" id="lsInput" value="{{ $q }}" placeholder="جست‌وجو در آگهی‌ها…" autocomplete="off" enterkeyhint="search" inputmode="search" aria-label="جست‌وجو در آگهی‌ها">
      @if($q)<a href="{{ $clear_url }}" class="ls-x" aria-label="پاک کردن عبارت"><x-icon name="x" class="icon-sm" /></a>@endif
      <div class="ls-suggest" id="lsSuggest" hidden data-popular="{{ json_encode($popular, JSON_UNESCAPED_UNICODE) }}"></div>
    </form>

    {{-- فیلترها و مرتب‌سازی --}}
    <div class="ls-bar">
      <a href="{{ $filters_url }}" class="ls-filter-btn d-lg-none"><x-icon name="sliders" /> فیلترها @if($filter_count)<span class="cnt" data-fa>{{ $filter_count }}</span>@endif</a>
      <label class="ls-sort">
        <span class="visually-hidden">مرتب‌سازی</span>
        <select id="sortSel" data-base="{{ route($isSearch ? 'search' : 'listings.index') }}">
          @foreach($sorts as $k => $t)<option value="{{ $k }}" {{ $sort === $k ? 'selected' : '' }}>مرتب‌سازی: {{ $t }}</option>@endforeach
        </select>
      </label>
    </div>

    {{-- چیپ‌های فیلتر فعال --}}
    @if(count($chips))
      <div class="ls-chips" aria-label="فیلترهای فعال">
        @foreach($chips as $c)<a href="{{ $c['url'] }}" class="ls-chip" aria-label="حذف {{ $c['label'] }}">{{ $c['label'] }} <span aria-hidden="true">×</span></a>@endforeach
      </div>
    @endif

    <div class="ls-head">
      <h2><span data-fa>{{ number_format($total) }}</span> آگهی پیدا شد</h2>
      @if(count($chips) || $q)<a href="{{ $clear_url }}" class="ls-reset">حذف فیلترها</a>@endif
    </div>

    {{-- نتایج --}}
    <div class="ls-list" id="resultList">
      @forelse($items as $item)
        @php $mine = $myDealer && ($item['dealer_id'] ?? 0) === $myDealer; @endphp
        @if(!empty($item['featured']))
          <x-listing-feature :item="$item" :edit="$mine ? route('listings.edit', 24817) : null" />
        @else
          <x-listing-row :item="$item" :edit="$mine ? route('listings.edit', 24817) : null" />
        @endif
      @empty
        <x-empty-state icon="search" title="آگهی‌ای پیدا نشد" text="فیلترها را کم کنید یا عبارت دیگری جست‌وجو کنید." action="حذف فیلترها" :href="$clear_url" />
      @endforelse
    </div>

    @if($has_more)
      <button type="button" class="btn btn-outline-primary w-100 ls-more" id="loadMore" data-next="{{ $next_url }}">نمایش آگهی‌های بیشتر</button>
    @endif
  </div>
</div>
@endsection
