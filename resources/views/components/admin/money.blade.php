{{-- <x-admin.money :value="985000000" /> — رندر سمت سرور با ارقام فارسی؛ JS هم دوباره قالب‌بندی می‌کند --}}
@props(['value', 'unit' => 'تومان'])
@php $fa = strtr(number_format((float) $value), array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬'])); @endphp
<span {{ $attributes }}><span class="tnum" data-money="{{ (int) $value }}">{{ $fa }}</span>@if($unit) <small>{{ $unit }}</small>@endif</span>
