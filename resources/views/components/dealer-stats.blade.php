{{-- <x-dealer-stats :stats="[['value'=>12,'unit'=>'آگهی','label'=>'فعال'], ...]" /> --}}
@props(['stats'])
@php $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
<div {{ $attributes->class('stat-grid') }}>
  @foreach($stats as $st)
    @php $st = (object) $st; @endphp
    <div class="stat-box">
      <b>{{ strtr((string) $st->value, $map) }} {{ $st->unit ?? '' }}</b>
      <span>{{ $st->label }}</span>
    </div>
  @endforeach
</div>
