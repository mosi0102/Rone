{{-- درصد تطابق: <x-radar-pct :value="92" /> — ≥۸۰ سبز، ≥۶۵ آبی، کمتر نارنجی (نزدیک) --}}
@props(['value', 'label' => false])
@php $tone = $value >= 80 ? 'high' : ($value >= 65 ? 'mid' : 'low'); @endphp
<span {{ $attributes->class(['rd-pct', 'is-' . $tone]) }} title="درصد تطابق"><b data-fa>{{ $value }}</b>٪@if($label) <small>تطابق</small>@endif</span>
