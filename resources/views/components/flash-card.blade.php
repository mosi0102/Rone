{{--
  اسلاید فروش فوری (طبق طرح نهایی)
  $item: id,title,url,image,price,old_price(nullable),year,km,extra,dealer,verified,location,posted,favorited
--}}
@props(['item'])
@php $i = (object) $item; @endphp
<div class="fl-slide">
  <article class="fl-card">
    <div class="fl-media">
      @if(!empty($i->image))<img src="{{ $i->image }}" alt="{{ $i->title }}" loading="lazy">@else<x-icon name="car" />@endif
      <span class="fl-badge"><x-icon name="bolt" class="icon-sm" /> فروش فوری</span>
      <button type="button" class="fl-save {{ !empty($i->favorited) ? 'is-on' : '' }}" data-save data-id="{{ $i->id }}" aria-label="نشان کردن" aria-pressed="{{ !empty($i->favorited) ? 'true' : 'false' }}"><x-icon name="bookmark" /></button>
    </div>
    <a href="{{ $i->url ?? '#' }}" class="fl-body">
      <div class="fl-row">
        <h3 class="fl-title">{{ $i->title }}</h3>
        <span class="fl-dealer">@if(!empty($i->verified))<x-icon name="shield" class="icon-sm" />@endif {{ $i->dealer }}</span>
      </div>
      <x-specs class="fl-meta" :year="$i->year ?? null" :km="$i->km ?? null" :extra="$i->extra ?? null" />
      <div class="fl-row fl-prices">
        <div class="fl-price"><x-price :value="$i->price" /></div>
        @if(!empty($i->old_price))<del class="fl-old"><x-price :value="$i->old_price" /></del>@endif
      </div>
      <div class="fl-loc">{{ $i->location ?? '' }}@if(!empty($i->posted)) • {{ $i->posted }}@endif</div>
    </a>
  </article>
</div>
