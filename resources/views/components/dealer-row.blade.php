{{-- ردیف نمایشگاه. $dealer: id, name, manager, area (مثل «تهران، سعادت‌آباد»), active_count, image, url --}}
@props(['dealer'])
@php $d = (object) $dealer; $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
<article class="listing-row dealer-row">
  <a href="{{ $d->url ?? '#' }}" class="lr-link">
    <div class="lr-media">
      @if(!empty($d->image))<img src="{{ $d->image }}" alt="" loading="lazy">@else<x-icon name="store" />@endif
    </div>
    <div class="lr-body">
      <h3 class="lr-title">{{ $d->name }}</h3>
      @if(!empty($d->manager))<span class="lr-meta">مدیریت : {{ $d->manager }}</span>@endif
      <span class="lr-meta">{{ $d->area }} • {{ strtr((string) $d->active_count, $map) }} آگهی فعال</span>
      <span class="dr-more">مشاهده نمایشگاه و موجودی <x-icon name="arrow-left" class="icon-sm" /></span>
    </div>
  </a>
</article>
