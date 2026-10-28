{{-- <x-admin.icon name="search" /> — آیکن از اسپرایت partials/icons --}}
@props(['name'])
<svg {{ $attributes->class(['icon']) }} aria-hidden="true"><use href="#a-{{ $name }}"/></svg>
