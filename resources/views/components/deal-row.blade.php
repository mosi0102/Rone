{{--
  ردیف معامله در «معاملات من» (خریدار) و تب «معاملات» پنل (فروشنده، seller).
  $deal: id,status,title,image,amount,fee,paid_at,decide_by,dealer{name},buyer{name}
--}}
@props(['deal', 'seller' => false])
@php
  $d = (object) $deal;
  $map = ['held' => ['warn', 'در انتظار تعیین وضعیت'], 'completed' => ['success', 'انجام شد'], 'refunded' => ['info', 'بازگردانده شد'], 'review' => ['warn', 'اختلاف در حال بررسی']];
  [$cls, $label] = $map[$d->status] ?? ['', $d->status];
  $payout = $d->amount;   // مبلغی که به نمایشگاه می‌رسد (هزینه خدمات جدا و برگشت‌ناپذیر است)
@endphp
<article class="card-s deal-row" data-status="{{ $d->status }}">
  <a href="{{ route('deals.show', $d->id) }}" class="dr-main text-reset">
    <div class="dr-media"><img src="{{ $d->image }}" alt="" loading="lazy"></div>
    <div class="dr-body">
      <div class="dr-top"><span class="badge-s badge-{{ $cls }}">{{ $label }}</span><span class="dr-id">شناسه <span data-fa>{{ $d->tx }}</span></span></div>
      <h3>{{ $d->title }}</h3>
      <span class="dr-who">@if($seller)<x-icon name="user" class="icon-sm" /> {{ $d->buyer['name'] }}@else<x-icon name="shield" class="icon-sm" /> {{ $d->dealer['name'] }}@endif</span>
      <span class="dr-amt">{{ $seller ? 'سهم نمایشگاه' : 'بیعانه' }}: <x-price :value="$payout" /></span>
    </div>
  </a>
  @if($d->status === 'held')
    <div class="dr-foot" data-cd-wrap>
      <span>مهلت تعیین وضعیت :</span>
      <x-countdown :end="$d->decide_by" hms light />
    </div>
    @if($seller)
      <a href="{{ route('deals.show', $d->id) }}" class="btn btn-soft w-100">مشاهده و پیگیری</a>
    @else
      <a href="{{ route('deals.decide', $d->id) }}" class="btn btn-primary w-100">تعیین وضعیت معامله</a>
    @endif
  @else
    <a href="{{ route('deals.show', $d->id) }}" class="btn btn-soft w-100">{{ $d->status === 'review' ? 'پیگیری بررسی' : 'مشاهده رسید' }}</a>
  @endif
</article>
