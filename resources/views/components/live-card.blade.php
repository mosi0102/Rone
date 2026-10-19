{{--
  کارت مزایده زنده. $item: id, title, url, image, price(بالاترین پیشنهاد), year, km, extra, ends_at, bids, bidders?(تعداد شرکت‌کننده)
--}}
@props(['item'])
@php $i = (object) $item; @endphp
<a href="{{ $i->url ?? '#' }}" class="live-card">
  <div class="lv-img">
    @if(!empty($i->image))<img src="{{ $i->image }}" alt="" loading="lazy" draggable="false">@else<x-icon name="car" />@endif
    <span class="lv-live"><i></i> زنده</span>
    @if(!empty($i->ends_at))<span class="lv-time"><x-icon name="clock" class="icon-sm" /><x-countdown :end="$i->ends_at" inline /></span>@endif
  </div>
  <div class="lv-body">
    <h3 class="lv-title">{{ $i->title }}</h3>
    <x-specs class="lv-meta" :year="$i->year ?? null" :km="$i->km ?? null" :extra="$i->extra ?? null" />
    <div class="lv-bid">
      <div>
        <small>بالاترین پیشنهاد</small>
        <b><x-price :value="$i->price" /></b>
      </div>
      @if(isset($i->bids))<span class="lv-up"><x-icon name="trending-up" class="icon-sm" /> <span data-fa>{{ $i->bids }}</span> پیشنهاد</span>@endif
    </div>
    <span class="lv-btn">ورود به مزایده <x-icon name="arrow-left" class="icon-sm" /></span>
  </div>
</a>
