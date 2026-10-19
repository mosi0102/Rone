{{-- <x-specs :year="1402" :km="24000" extra="مشکی" /> → «۱۴۰۲ • ۲۴٬۰۰۰ کیلومتر • مشکی» (ارقام فارسی در سمت سرور) --}}
@props(['year' => null, 'km' => null, 'extra' => null])
@php
  $map = array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬']);
  $parts = [];
  if ($year) $parts[] = strtr((string) $year, $map);
  if (!is_null($km)) $parts[] = strtr(number_format((int) $km), $map) . ' کیلومتر';
  if ($extra) $parts[] = $extra;
@endphp
<span {{ $attributes->class('specs') }}>{{ implode(' • ', $parts) }}</span>
