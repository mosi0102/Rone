{{-- <x-specs :area="120" :rooms="2" :year="1402" extra="پارکینگ" /> → «۱۲۰ متر • ۲ اتاق • ساخت ۱۴۰۲ • پارکینگ» (ارقام فارسی در سمت سرور) --}}
@props(['area' => null, 'rooms' => null, 'year' => null, 'extra' => null])
@php
  $map = array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬']);
  $parts = [];
  if ($area) $parts[] = strtr(number_format((int) $area), $map) . ' متر';
  if (!is_null($rooms)) $parts[] = (int) $rooms === 0 ? 'بدون اتاق' : strtr((string) $rooms, $map) . ' اتاق';
  if ($year) $parts[] = 'ساخت ' . strtr((string) $year, $map);
  if ($extra) $parts[] = $extra;
@endphp
<span {{ $attributes->class('specs') }}>{{ implode(' • ', $parts) }}</span>
