{{--
  ردیف آگهی. عکس سمت راست (طبق طرح)، متن کنار آن.
  $item: id, title, url, image, price, year, km, extra, dealer, verified, sale_type ('instant'|'auction'|null), category (برای فیلتر تب‌ها)
--}}
@props(['item'])
@php $i = (object) $item; @endphp
<article class="listing-row" data-category="{{ $i->category ?? '' }}">
  <a href="{{ $i->url ?? '#' }}" class="lr-link">
    <div class="lr-media">
      @if(!empty($i->image))<img src="{{ $i->image }}" alt="" loading="lazy">@else<x-icon name="car" />@endif
      @if(($i->sale_type ?? null) === 'instant')<span class="lr-tag" title="فروش فوری"><x-icon name="bolt" class="icon-sm" /></span>@endif
      @if(($i->sale_type ?? null) === 'auction')<span class="lr-tag lr-tag-auction" title="مزایده"><x-icon name="gavel" class="icon-sm" /></span>@endif
    </div>
    <div class="lr-body">
      <h3 class="lr-title">{{ $i->title }}</h3>
      <x-specs class="lr-meta" :year="$i->year ?? null" :km="$i->km ?? null" :extra="$i->extra ?? null" />
      <div class="lr-price"><x-price :value="$i->price" /></div>
      <div class="lr-dealer">
        @if(!empty($i->verified))<x-icon name="shield" class="icon-sm" title="نمایشگاه تأییدشده" />@endif
        <span>{{ $i->dealer }}</span>
      </div>
    </div>
  </a>
</article>
