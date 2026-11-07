{{--
  <x-listing-card :item="$listing" />
  فیلدهای مورد انتظار $item (آرایه یا آبجکت):
  id, title, url, image(nullable), price, rent(nullable), price_label, area, rooms, year, city, agency, sale_type ('instant'|'auction'|null), favorited(bool)
--}}
@props(['item'])
@php $i = (object) $item; @endphp
<article class="card-s hoverable listing-card">
  <a href="{{ $i->url ?? '#' }}" class="d-block text-reset">
    <div class="lc-media">
      @if(!empty($i->image))<img src="{{ $i->image }}" alt="{{ $i->title }}" loading="lazy" decoding="async">@else<x-icon name="building" />@endif
      <div class="lc-badges">
        @if(($i->sale_type ?? null) === 'instant')<span class="badge-s badge-instant"><x-icon name="bolt" /> فوری</span>@endif
        @if(($i->sale_type ?? null) === 'auction')<span class="badge-s badge-auction"><x-icon name="gavel" /> مزایده</span>@endif
      </div>
    </div>
    <div class="lc-body">
      <h3 class="lc-title">{{ $i->title }}</h3>
      <div class="lc-meta">
        @if(!empty($i->area))<span><x-icon name="ruler" class="icon-sm" /> <span data-fa>{{ $i->area }}</span> متر</span>@endif
        @if(isset($i->rooms))<span><x-icon name="bed" class="icon-sm" /> {{ (int) $i->rooms === 0 ? 'بدون اتاق' : '' }}@if((int) $i->rooms > 0)<span data-fa>{{ $i->rooms }}</span> اتاق@endif</span>@endif
        <span><x-icon name="pin" class="icon-sm" /> {{ $i->city ?? $i->location ?? '' }}</span>
      </div>
      <div class="lc-price"><x-listing-price :item="$i" /></div>
      <div class="lc-foot">
        <span class="d-inline-flex align-items-center gap-1"><x-icon name="store" class="icon-sm" /> {{ $i->agency }}</span>
      </div>
    </div>
  </a>
  <button type="button" class="lc-fav {{ !empty($i->favorited) ? 'is-on' : '' }}" data-id="{{ $i->id }}" aria-label="نشان کردن" aria-pressed="{{ !empty($i->favorited) ? 'true' : 'false' }}"><x-icon name="heart" /></button>
</article>
