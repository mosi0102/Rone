{{--
  بنر تصویری مدیریت‌شده از ادمین (متن روی خود عکس است). <x-banner-slider :banners="$banners" />
  هر بنر: image, url(nullable), alt. چند بنر = محو شدن خودکار هر ۵ ثانیه با نقطه‌های راهنما.
--}}
@props(['banners', 'full' => true])
@once
  @push('scripts')<script src="{{ asset('js/banner-slider.js') }}"></script>@endpush
@endonce
<section {{ $attributes->class(['bslider', 'is-full' => $full]) }} data-bslider aria-roledescription="carousel">
  @foreach($banners as $b)
    @php $b = (object) $b; @endphp
    <a href="{{ $b->url ?? '#' }}" class="bs-slide {{ $loop->first ? 'is-active' : '' }}" aria-label="{{ $b->alt ?? 'بنر' }}">
      <img src="{{ $b->image }}" alt="{{ $b->alt ?? '' }}" @if(!$loop->first) loading="lazy" @endif>
    </a>
  @endforeach
  @if(count($banners) > 1)
    <div class="bs-dots">@foreach($banners as $b)<button type="button" class="{{ $loop->first ? 'is-active' : '' }}" aria-label="بنر {{ $loop->iteration }}"></button>@endforeach</div>
  @endif
</section>
