@props(['name', 'flip' => false])
<svg {{ $attributes->class(['icon', 'icon-flip' => $flip]) }} aria-hidden="true"><use href="#i-{{ $name }}"/></svg>
