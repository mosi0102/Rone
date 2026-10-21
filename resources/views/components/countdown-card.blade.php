{{--
  کارت بزرگ شمارنده (جزئیات مزایده). tone: blue (تا شروع) | green (تا پایان)
  اگر بیش از window ثانیه مانده باشد، شمارنده خاموش و فقط تاریخ دیده می‌شود.
--}}
@props(['end', 'title', 'tone' => 'blue', 'dateLabel' => 'شروع:', 'window' => 86400, 'offText' => 'شمارش معکوس از ۲۴ ساعت قبل از شروع فعال می‌شود.'])
<section class="cdcard {{ $tone }}" data-cd-wrap>
  <h2>{{ $title }}</h2>
  <div class="cd-on"><x-countdown :end="$end" hms big :window="$window" /></div>
  <p class="cd-off cdc-off">{{ $offText }}</p>
  <div class="cdc-date">{{ $dateLabel }} <span data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($end)->toIso8601String() }}"></span></div>
</section>
