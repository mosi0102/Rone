{{--
  مرورگر آگهی‌ها (جست‌وجو + فیلتر + مرتب‌سازی + دسته + نتایج) — هم داخل صفحه، هم پاسخ درخواست AJAX.
  ناحیه‌های data-lb-region را js/listing-browser.js بدون رفرش جایگزین می‌کند.
  $embed = true: داخل لایه‌ی جست‌وجوی صفحه‌ی اول (کادر جست‌وجو را لایه دارد).
--}}
@php
  $isSearch = $mode === 'search';
  $myAgency = $auth->is_agency ? $auth->agency->id : null;     // مشاور املاک: دکمه ویرایش روی آگهی‌های خودش
  $popular = ['آپارتمان نوساز', 'رهن کامل', 'ویلا', 'مغازه', 'سوئیت مبله', 'پیش‌فروش', 'سعادت‌آباد'];   // جست‌وجوهای پرطرفدار (از بک‌اند)
  $action = route($isSearch ? 'search' : 'listings.index');
@endphp
<div class="ls-layout" data-lb data-lb-action="{{ $action }}" data-lb-sort="{{ $sort }}">
  {{-- ستون فیلتر (دسکتاپ) --}}
  <aside class="ls-aside d-none d-lg-block" aria-label="فیلترها" data-lb-region="filters-side">
    <x-filter-form :f="$query" :lf="$lf" :count="$total" :action="$action" :sticky="false" idp="side" />
  </aside>

  <div class="ls-main">
    <nav class="seg-tabs ls-tabs" aria-label="دسته‌بندی" data-lb-region="tabs">
      @foreach($tabs as $t)<a href="{{ $t['url'] }}" data-lb-link class="{{ $t['on'] ? 'on' : '' }}" @if($t['on']) aria-current="true" @endif>{{ $t['title'] }}</a>@endforeach
    </nav>

    @unless($embed)
      <form class="ls-search" action="{{ route('search') }}" role="search" id="lsSearch" data-lb-search>
        <button type="submit" class="ls-go" aria-label="جست‌وجو"><x-icon name="search" /></button>
        <input type="text" class="ls-input" name="q" id="lsInput" value="{{ $q }}" placeholder="جست‌وجو در آگهی‌ها…" autocomplete="off" enterkeyhint="search" inputmode="search" aria-label="جست‌وجو در آگهی‌ها">
        <button type="button" class="ls-x" id="lsClear" aria-label="پاک کردن عبارت" {{ $q ? '' : 'hidden' }}><x-icon name="x" class="icon-sm" /></button>
        <div class="ls-suggest" id="lsSuggest" hidden data-popular="{{ json_encode($popular, JSON_UNESCAPED_UNICODE) }}"></div>
      </form>
    @endunless

    <div class="ls-bar" data-lb-region="bar">
      <a href="{{ $filters_url }}" class="ls-filter-btn d-lg-none" data-lb-open-filters><x-icon name="sliders" /> فیلترها @if($filter_count)<span class="cnt" data-fa>{{ $filter_count }}</span>@endif</a>
      <a href="{{ route('listings.map', array_intersect_key($query, array_flip(['category', 'city', 'sale_type', 'ptype', 'district', 'q']))) }}" class="ls-filter-btn ls-map-btn" aria-label="مشاهده روی نقشه"><x-icon name="pin" /> <span class="d-none d-sm-inline">نقشه</span></a>
      <label class="ls-sort">
        <span class="visually-hidden">مرتب‌سازی</span>
        <select class="lb-sort" id="sortSel">
          @foreach($sorts as $k => $t)<option value="{{ $k }}" {{ $sort === $k ? 'selected' : '' }}>مرتب‌سازی: {{ $t }}</option>@endforeach
        </select>
      </label>
    </div>

    <div class="ls-chips" aria-label="فیلترهای فعال" data-lb-region="chips" data-n="{{ count($chips) }}">
      @foreach($chips as $c)<a href="{{ $c['url'] }}" data-lb-link class="ls-chip" aria-label="حذف {{ $c['label'] }}">{{ $c['label'] }} <span aria-hidden="true">×</span></a>@endforeach
    </div>

    <div class="ls-head" data-lb-region="head">
      <h2><span data-fa>{{ number_format($total) }}</span> آگهی پیدا شد</h2>
      @if(count($chips) || $q)<a href="{{ $clear_url }}" data-lb-link class="ls-reset">حذف فیلترها</a>@endif
    </div>

    <div class="ls-list" id="resultList" data-lb-region="list">
      @if(count($items))
        @foreach($items as $item)
          @php $mine = $myAgency && ($item['agency_id'] ?? 0) === $myAgency; @endphp
          @if(!empty($item['featured']))
            <x-listing-feature :item="$item" :edit="$mine ? route('listings.edit', 24817) : null" />
          @else
            <x-listing-row :item="$item" :edit="$mine ? route('listings.edit', 24817) : null" />
          @endif
        @endforeach
      @else
        <x-empty-state icon="search" title="آگهی‌ای پیدا نشد" text="فیلترها را کم کنید یا عبارت دیگری جست‌وجو کنید." action="حذف فیلترها" :href="$clear_url" />
      @endif
    </div>

    <div data-lb-region="more">
      @if($has_more)<button type="button" class="btn btn-outline-primary w-100 ls-more" id="loadMore" data-lb-more data-next="{{ $next_url }}">نمایش آگهی‌های بیشتر</button>@endif
    </div>
  </div>

  {{-- فیلترها در موبایل: لایه‌ی تمام‌صفحه داخل همین صفحه (بدون رفرش) --}}
  <div class="ls-sheet" role="dialog" aria-modal="true" aria-label="فیلترها" aria-hidden="true">
    <header class="sp-head"><button type="button" class="icon-btn" data-lb-close-filters aria-label="بستن فیلترها"><x-icon name="arrow-left" flip /></button><h2 class="cp-title">فیلترها</h2></header>
    <div class="ff-page" data-lb-region="filters-sheet">
      <x-filter-form :f="$query" :lf="$lf" :count="$total" :action="$action" idp="sheet" />
    </div>
  </div>
</div>
