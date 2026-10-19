@props(['icon' => 'inbox', 'title', 'text' => null, 'action' => null, 'href' => '#'])
<div class="empty-state">
  <div class="es-icon"><x-icon :name="$icon" /></div>
  <h3>{{ $title }}</h3>
  @if($text)<p class="mb-3">{{ $text }}</p>@endif
  @if($action)<a href="{{ $href }}" class="btn btn-primary">{{ $action }}</a>@endif
</div>
