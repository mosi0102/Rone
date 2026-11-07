{{--
  کارت مزایده در لیست مزایده‌ها (طبق طرح).
  $a: id,title,image,category,area,rooms,year,extra,agency(name),verified,status('live'|'scheduled'),start_price,top_bid(nullable),
      starts_at,ends_at,entry_fee,paid(bool),own(bool)
--}}
@props(['a'])
@php
  $x = (object) $a; $live = $x->status === 'live'; $when = $live ? $x->ends_at : $x->starts_at;
  $own = $auth->agency && $auth->agency->id === ($x->agency_id ?? 0);   // مزایده خودِ مشاور املاک
  $show = route('auctions.show', $x->id); $entry = route('auctions.entry', $x->id);
@endphp
<article class="auc-card" data-cat="{{ $x->category }}" data-status="{{ $x->status }}">
  <a href="{{ $show }}" class="auc-main">
    <div class="auc-media">@if(!empty($x->image))<img src="{{ $x->image }}" alt="" loading="lazy">@else<x-icon name="building" />@endif</div>
    <div class="auc-info">
      <div class="auc-top">
        <span class="auc-badge {{ $live ? 'live' : 'soon' }}">{{ $live ? 'در حال برگزاری' : 'در انتظار شروع' }}</span>
        <span class="auc-agency">@if(!empty($x->verified))<x-icon name="shield" class="icon-sm" />@endif {{ $x->agency }}</span>
      </div>
      <h3 class="auc-title">{{ $x->title }}</h3>
      <x-specs class="auc-meta" :area="$x->area ?? null" :rooms="$x->rooms ?? null" :year="$x->year ?? null" :extra="$x->extra ?? null" />
      <div class="auc-start">قیمت شروع: <x-price :value="$x->start_price" /></div>
    </div>
  </a>

  <div class="auc-topbid">بالاترین پیشنهاد: @if(!empty($x->top_bid))<x-price :value="$x->top_bid" />@else<span class="muted">هنوز ثبت نشده</span>@endif</div>

  {{-- شمارنده فقط در ۲۴ ساعت آخر فعال است؛ قبل از آن تاریخ نمایش داده می‌شود --}}
  <div class="auc-timer" data-cd-wrap>
    <span class="cd-on">{{ $live ? 'زمان باقیمانده تا پایان :' : 'زمان باقیمانده تا شروع :' }}</span>
    <span class="cd-off">{{ $live ? 'زمان پایان :' : 'زمان شروع :' }}</span>
    <x-countdown :end="$when" hms light :window="86400" />
    <span class="cd-off auc-date" data-jfmt="datetime" data-jdate="{{ \Illuminate\Support\Carbon::parse($when)->toIso8601String() }}"></span>
  </div>

  @if($own)
    <a href="{{ $show }}" class="btn auc-cta outline"><x-icon name="layout" class="icon-sm" /> مزایده شما • مدیریت</a>
  @elseif(!empty($x->paid))
    @if($live)<a href="{{ $show }}" class="btn auc-cta solid"><x-icon name="gavel" class="icon-sm" /> ورود به مزایده و ثبت پیشنهاد</a>
    @else<div class="auc-cta done"><x-icon name="check-circle" class="icon-sm" /> ورود شما ثبت شد؛ پیشنهاد از زمان شروع فعال می‌شود</div>@endif
  @else
    <a href="{{ $entry }}" class="btn auc-cta {{ $live ? 'solid' : 'soft' }}">پرداخت و ورود به مزایده : <x-price :value="$x->entry_fee" /> <x-icon name="shield" class="icon-sm" /></a>
  @endif
</article>
