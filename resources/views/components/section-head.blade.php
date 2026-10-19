@props(['title', 'icon' => null, 'href' => null, 'more' => 'مشاهده همه'])
<div class="section-head">
  <h2>@if($icon)<x-icon :name="$icon" />@endif {{ $title }}</h2>
  @if($href)<a href="{{ $href }}" class="more">{{ $more }} <x-icon name="chevron-left" class="icon-sm" /></a>@endif
</div>
