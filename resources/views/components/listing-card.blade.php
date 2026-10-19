{{--
  <x-listing-card :item="$listing" />
  فیلدهای مورد انتظار $item (آرایه یا آبجکت):
  id, title, url, image(nullable), price, year, km, city, dealer, sale_type ('instant'|'auction'|null), ends_at(nullable), favorited(bool)
--}}
@props(['item'])
@php $i = (object) $item; @endphp
<article class="card-s hoverable listing-card">
  <a href="{{ $i->url ?? '#' }}" class="d-block text-reset">
    <div class="lc-media">
      @if(!empty($i->image))<img src="{{ $i->image }}" alt="{{ $i->title }}" loading="lazy">@else<x-icon name="car" />@endif
      <div class="lc-badges">
        @if(($i->sale_type ?? null) === 'instant')<span class="badge-s badge-instant"><x-icon name="bolt" /> فروش فوری</span>@endif
        @if(($i->sale_type ?? null) === 'auction')<span class="badge-s badge-auction"><x-icon name="gavel" /> مزایده</span>@endif
      </div>
    </div>
    <div class="lc-body">
      <h3 class="lc-title">{{ $i->title }}</h3>
      <div class="lc-meta">
        <span><x-icon name="calendar" class="icon-sm" /> <span data-fa>{{ $i->year }}</span></span>
        <span><x-icon name="gauge" class="icon-sm" /> <span class="num" data-money="{{ $i->km }}">{{ $i->km }}</span> کیلومتر</span>
        <span><x-icon name="pin" class="icon-sm" /> {{ $i->city }}</span>
      </div>
      <div class="lc-price">
        @if(($i->sale_type ?? null) === 'auction')<small class="d-block mb-1">قیمت پایه</small>@endif
        <x-price :value="$i->price" />
      </div>
      <div class="lc-foot">
        <span class="d-inline-flex align-items-center gap-1"><x-icon name="store" class="icon-sm" /> {{ $i->dealer }}</span>
        @if(!empty($i->ends_at))<x-countdown :end="$i->ends_at" inline class="text-danger" />@endif
      </div>
    </div>
  </a>
  <button type="button" class="lc-fav {{ !empty($i->favorited) ? 'is-on' : '' }}" data-id="{{ $i->id }}" aria-label="نشان کردن" aria-pressed="{{ !empty($i->favorited) ? 'true' : 'false' }}"><x-icon name="heart" /></button>
</article>
