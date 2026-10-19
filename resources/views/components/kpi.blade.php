{{-- <x-kpi icon="eye" label="بازدید ۷ روز" :value="1240" :delta="12" :up="true" /> ؛ delta = تغییر نسبت به دوره قبل --}}
@props(['icon' => 'chart', 'label', 'value', 'delta' => null, 'up' => true])
<div class="kpi">
  <span class="kpi-ic"><x-icon :name="$icon" /></span>
  <b>@if(is_numeric($value))<span class="num" data-money="{{ $value }}">{{ $value }}</span>@else{{ $value }}@endif</b>
  <span class="kpi-l">{{ $label }}</span>
  @if(!is_null($delta))<span class="kpi-d {{ $up ? 'up' : 'down' }}"><x-icon name="{{ $up ? 'arrow-up' : 'arrow-down' }}" class="icon-sm" /> <span data-fa>{{ $delta }}</span>@if(is_numeric($value) || str_contains((string) $value, '٪'))٪@endif</span>@endif
</div>
