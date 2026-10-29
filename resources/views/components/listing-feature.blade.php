{{--
  کارت بزرگ آگهی ویژه (نتایج جست‌وجو / لیست آگهی‌ها). ساختار مثل کارت فروش فوری، با برچسب «ویژه».
  $item: id,title,url,image,price,old_price,year,km,extra,dealer,verified,location,posted,favorited,sale_type,featured
  edit = لینک ویرایش (فقط صاحب آگهی)
--}}
@props(['item', 'edit' => null])
@php $i = (object) $item; @endphp
<article class="fl-card lf-card" data-category="{{ $i->category ?? '' }}">
  <div class="fl-media">
    @if(!empty($i->image))<img src="{{ $i->image }}" alt="{{ $i->title }}" loading="lazy">@else<x-icon name="car" />@endif
    @if(!empty($i->featured))<span class="fl-badge"><x-icon name="crown" class="icon-sm" /> ویژه</span>
    @elseif(($i->sale_type ?? null) === 'instant')<span class="fl-badge"><x-icon name="bolt" class="icon-sm" /> فروش فوری</span>
    @elseif(($i->sale_type ?? null) === 'auction')<span class="fl-badge"><x-icon name="gavel" class="icon-sm" /> مزایده</span>@endif
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
  @if($edit)<a href="{{ $edit }}" class="lr-edit lf-edit" aria-label="ویرایش آگهی"><x-icon name="pencil" class="icon-sm" /> ویرایش</a>@endif
</article>
