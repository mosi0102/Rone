{{--
  کارت نمایشگاه ویژه.
  $dealer: id, name, manager, address, cover, url, verified, featured, stats[{value,unit,label}]
--}}
@props(['dealer'])
@php $d = (object) $dealer; @endphp
<article class="dealer-card">
  <a href="{{ $d->url ?? '#' }}" class="dc-link">
    <div class="dc-media">
      @if(!empty($d->cover))<img src="{{ $d->cover }}" alt="{{ $d->name }}" loading="lazy">@else<x-icon name="store" />@endif
      @if(!empty($d->featured))<span class="dc-flash"><x-icon name="crown" class="icon-sm" /> ویژه</span>@endif
      @if(!empty($d->verified))<span class="dc-verified"><x-icon name="shield" class="icon-sm" /> هویت و مجوز تأییدشده</span>@endif
    </div>
    <div class="dc-body">
      <div class="dc-top">
        <h3>{{ $d->name }}</h3>
        @if(!empty($d->manager))<span class="dc-mgr">مدیریت : {{ $d->manager }}</span>@endif
      </div>
      <p class="dc-addr">{{ $d->address }}</p>
      <x-dealer-stats :stats="$d->stats" />
    </div>
  </a>
</article>
