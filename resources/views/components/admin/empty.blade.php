{{-- <x-admin.empty icon="inbox" title="موردی نیست" text="..." /> --}}
@props(['icon' => 'inbox', 'title', 'text' => null])
<div {{ $attributes->class(['empty']) }}>
  <div class="e-ic"><x-admin.icon :name="$icon" class="icon-lg" /></div>
  <h3>{{ $title }}</h3>@if($text)<p>{{ $text }}</p>@endif
  {{ $slot }}
</div>
