{{--
  ردیف آگهی. عکس سمت راست (طبق طرح)، متن کنار آن.
  $item: id, title, url, image, price, rent, price_label, area, rooms, year, extra, agency, verified, sale_type ('instant'|null), category (برای فیلتر تب‌ها)
--}}
@props(['item', 'edit' => null])
@php $i = (object) $item; @endphp
<article class="listing-row" data-category="{{ $i->category ?? '' }}">
  <a href="{{ $i->url ?? '#' }}" class="lr-link">
    <div class="lr-media">
      @if(!empty($i->image))<img src="{{ $i->image }}" alt="" loading="lazy">@else<x-icon name="building" />@endif
      @if(($i->sale_type ?? null) === 'instant')<span class="lr-tag" title="فوری"><x-icon name="bolt" class="icon-sm" /></span>@endif
      @if(($i->sale_type ?? null) === 'auction')<span class="lr-tag lr-tag-auction" title="مزایده"><x-icon name="gavel" class="icon-sm" /></span>@endif
    </div>
    <div class="lr-body">
      <h3 class="lr-title">{{ $i->title }}</h3>
      <x-specs class="lr-meta" :area="$i->area ?? null" :rooms="$i->rooms ?? null" :year="$i->year ?? null" :extra="$i->extra ?? null" />
      <div class="lr-price"><x-listing-price :item="$i" /></div>
      <div class="lr-agency">
        @if(!empty($i->verified))<x-icon name="shield" class="icon-sm" title="آژانس تأییدشده" />@endif
        <span>{{ $i->agency }}</span>
      </div>
    </div>
  </a>
  @if($edit)<a href="{{ $edit }}" class="lr-edit" aria-label="ویرایش آگهی"><x-icon name="pencil" class="icon-sm" /> ویرایش</a>@endif
</article>
