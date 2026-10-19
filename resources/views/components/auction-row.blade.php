{{--
  کارت مزایده صفحه خانه (طبق طرح نهایی): عکس راست، مشخصات و قیمت، و زیرش «زمان باقیمانده تا شروع/پایان».
  $item: id,title,url,image,price,year,km,extra, status('scheduled'|'live'), starts_at / ends_at
--}}
@props(['item'])
@php
  $i = (object) $item;
  $live = ($i->status ?? 'scheduled') === 'live';
  $when = $live ? ($i->ends_at ?? null) : ($i->starts_at ?? null);
@endphp
<a href="{{ $i->url ?? '#' }}" class="auction-row">
  <div class="ar-main">
    <div class="ar-media">@if(!empty($i->image))<img src="{{ $i->image }}" alt="" loading="lazy">@else<x-icon name="car" />@endif</div>
    <div class="ar-body">
      <h3 class="ar-title">{{ $i->title }}</h3>
      <x-specs class="ar-meta" :year="$i->year ?? null" :km="$i->km ?? null" :extra="$i->extra ?? null" />
      <div class="ar-price"><x-price :value="$i->price" /></div>
    </div>
  </div>
  @if($when)
    <div class="ar-timer">
      <span>{{ $live ? 'زمان باقیمانده تا پایان :' : 'زمان باقیمانده تا شروع :' }}</span>
      <x-countdown :end="$when" hms light />
    </div>
  @endif
</a>
