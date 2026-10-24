{{-- کارت کوچک خودرو. $car: image,title,year,km,extra,price,dealer{name,verified} ، url اختیاری --}}
@props(['car', 'showPrice' => true])
@php $c = (object) $car; $d = (object) $c->dealer; @endphp
<a href="{{ $c->url ?? $c->listing_url ?? '#' }}" class="card-s car-mini text-reset">
  <div class="cm-media"><img src="{{ $c->image }}" alt=""></div>
  <div class="cm-body">
    <h3>{{ $c->title }}</h3>
    <x-specs class="cm-meta" :year="$c->year" :km="$c->km" :extra="$c->extra ?? null" />
    <span class="cm-dealer">@if(!empty($d->verified))<x-icon name="shield" class="icon-sm" />@endif {{ $d->name }}</span>
    @if($showPrice)<span class="cm-price">قیمت آگهی: <x-price :value="$c->price" /></span>@endif
  </div>
</a>
