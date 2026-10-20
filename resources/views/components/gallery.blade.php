{{--
  گالری آگهی. <x-gallery :images="$car->images" badge="فروش فوری" />
  images: آرایه‌ای از URL (یا ['src'=>..., 'thumb'=>...]). سوایپ، تامبنیل همگام، نمایش تمام‌صفحه با کلیک.
--}}
@props(['images', 'badge' => null])
@once
  @push('styles')<link rel="stylesheet" href="{{ asset('css/listing-detail.css') }}">@endpush
  @push('scripts')<script src="{{ asset('js/gallery.js') }}"></script>@endpush
@endonce
@php $imgs = collect($images)->map(fn ($im) => is_array($im) ? $im : ['src' => $im, 'thumb' => $im])->values(); @endphp
<section class="gal" data-gallery aria-roledescription="carousel" aria-label="تصاویر آگهی">
  <div class="gal-main">
    <div class="gal-track">
      @foreach($imgs as $im)
        <div class="gal-slide"><img src="{{ $im['src'] }}" alt="تصویر {{ $loop->iteration }}" data-full="{{ $im['src'] }}" @if(!$loop->first) loading="lazy" @endif></div>
      @endforeach
    </div>
    @if($badge)<span class="gal-badge"><x-icon name="bolt" class="icon-sm" /> {{ $badge }}</span>@endif
    <span class="gal-count"><x-icon name="image" class="icon-sm" /> <span data-cur>۱</span> از <span data-fa>{{ $imgs->count() }}</span></span>
    <button type="button" class="gal-nav gal-prev" data-dir="prev" aria-label="قبلی"><x-icon name="chevron-right" /></button>
    <button type="button" class="gal-nav gal-next" data-dir="next" aria-label="بعدی"><x-icon name="chevron-left" /></button>
  </div>
  @if($imgs->count() > 1)
    <div class="gal-thumbs" role="tablist">
      @foreach($imgs as $im)
        <button type="button" class="gal-thumb {{ $loop->first ? 'active' : '' }}" role="tab" aria-label="تصویر {{ $loop->iteration }}"><img src="{{ $im['thumb'] }}" alt="" loading="lazy"></button>
      @endforeach
    </div>
  @endif
</section>
@once
  <div class="modal fade gal-lightbox" id="galLightbox" tabindex="-1" aria-hidden="true" aria-label="نمایش تصاویر">
    <div class="modal-dialog modal-fullscreen m-0">
      <div class="modal-content">
        <button type="button" class="gl-close" data-bs-dismiss="modal" aria-label="بستن"><x-icon name="x" /></button>
        <span class="gl-count"><span data-cur>۱</span> / <span data-total></span></span>
        <div class="gl-track"></div>
        <button type="button" class="gal-nav gal-prev" data-dir="prev" aria-label="قبلی"><x-icon name="chevron-right" /></button>
        <button type="button" class="gal-nav gal-next" data-dir="next" aria-label="بعدی"><x-icon name="chevron-left" /></button>
      </div>
    </div>
  </div>
@endonce
