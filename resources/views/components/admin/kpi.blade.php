{{-- <x-admin.kpi icon="users" tone="sky" label="کاربران" :value="1240" :delta="8" /> — tone: sage|peach|sky|sand --}}
@props(['icon', 'label', 'value', 'tone' => 'sky', 'delta' => null, 'unit' => null, 'money' => false])
<div {{ $attributes->class(['card-a', 'kpi']) }}>
  <span class="kpi-ic t-{{ $tone }}"><x-admin.icon :name="$icon" class="icon-lg" /></span>
  <div class="min-w-0">
    <div class="kpi-l">{{ $label }}</div>
    <div class="kpi-v"><span data-count="{{ (int) $value }}" @if($money) data-count-money @endif>{{ strtr($money ? number_format($value) : (string) $value, array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬'])) }}</span>@if($unit)<small>{{ $unit }}</small>@endif</div>
    @if(!is_null($delta))
      <span class="delta {{ $delta >= 0 ? 'up' : 'down' }}"><x-admin.icon :name="$delta >= 0 ? 'trending-up' : 'trending-down'" /><span data-fa>{{ abs($delta) }}</span>٪</span>
    @endif
  </div>
</div>
