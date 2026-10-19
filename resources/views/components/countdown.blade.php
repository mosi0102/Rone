{{--
  شمارنده معکوس. <x-countdown :end="$date" />
  inline = فقط متن  |  hms = فقط ساعت:دقیقه:ثانیه (ساعت کل، بدون روز)  |  light = جعبه‌های روشن (کارت مزایده)
--}}
@props(['end', 'inline' => false, 'hms' => false, 'light' => false])
<span {{ $attributes->class(['countdown', 'countdown-inline' => $inline, 'light' => $light]) }} data-countdown="{{ \Illuminate\Support\Carbon::parse($end)->toIso8601String() }}" @if($hms) data-units="hms" @endif></span>
