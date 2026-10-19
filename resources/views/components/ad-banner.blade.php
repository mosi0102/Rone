{{-- بنر تبلیغاتی. $ad: image(با گرادیان تیره سمت راست)، label، title، subtitle، cta، url --}}
@props(['ad'])
@php $a = (object) $ad; @endphp
<a href="{{ $a->url ?? '#' }}" class="ad-banner" rel="sponsored">
  @if(!empty($a->image))<img src="{{ $a->image }}" alt="" class="ad-img" loading="lazy">@endif
  <div class="ad-body">
    <span class="ad-label">{{ $a->label ?? 'تبلیغات' }}</span>
    <h3>{{ $a->title }}</h3>
    <p>{{ $a->subtitle ?? '' }}</p>
    <span class="ad-cta"><x-icon name="arrow-left" class="icon-sm" /> {{ $a->cta ?? 'مشاهده' }}</span>
  </div>
</a>
