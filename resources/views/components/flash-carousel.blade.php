{{--
  کروسل فروش فوری (ساده): کارت‌ها با کشیدن عوض می‌شوند، نقطه‌های پایین جایگاه را نشان می‌دهند.
  <x-flash-carousel :items="$instant" :href="route('listings.index', ['type' => 'instant'])" />
  autoplay (میلی‌ثانیه؛ ۰ = خاموش). با لمس یا هاور موقتاً متوقف می‌شود.
--}}
@props(['items', 'href' => '#', 'autoplay' => 5000])
@once
  @push('styles')<link rel="stylesheet" href="{{ asset('css/flash-carousel.css') }}">@endpush
  @push('scripts')<script src="{{ asset('js/flash-carousel.js') }}"></script>@endpush
@endonce
<x-section-head title="فروش فوری" :href="$href" />
<section class="fl" data-flash data-autoplay="{{ $autoplay }}" aria-roledescription="carousel" aria-label="فروش فوری">
  <div class="fl-track" tabindex="0">
    @foreach($items as $item)<x-flash-card :item="$item" />@endforeach
  </div>
  @if(count($items) > 1)
    <div class="fl-dots" role="tablist">@foreach($items as $item)<button type="button" role="tab" aria-label="کارت {{ $loop->iteration }}"></button>@endforeach</div>
  @endif
</section>
