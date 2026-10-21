{{--
  شمارنده معکوس. <x-countdown :end="$date" />
  inline = فقط متن | hms = ساعت:دقیقه:ثانیه (ساعت کل) | light = جعبه‌های روشن | big = بزرگ (کارت شمارنده)
  window = ثانیه؛ اگر زمان باقیمانده از این مقدار بیشتر باشد شمارنده غیرفعال می‌شود (مثلاً 86400 = ۲۴ ساعت)
         و والد با [data-cd-wrap] کلاس is-off می‌گیرد (برای نمایش جایگزین: .cd-off / .cd-on).
--}}
@props(['end', 'inline' => false, 'hms' => false, 'light' => false, 'big' => false, 'window' => 0])
<span {{ $attributes->class(['countdown', 'countdown-inline' => $inline, 'light' => $light, 'big' => $big]) }} data-countdown="{{ \Illuminate\Support\Carbon::parse($end)->toIso8601String() }}" @if($hms) data-units="hms" @endif @if($window) data-window="{{ (int) $window }}" @endif></span>
