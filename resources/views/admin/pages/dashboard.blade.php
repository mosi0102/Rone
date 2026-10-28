@extends('admin.layouts.app')
@section('title', 'داشبورد')

@section('content')
@php
  $fa = fn ($v) => strtr((string) $v, array_combine(['0','1','2','3','4','5','6','7','8','9',','], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٬']));
  $maxBar = max(1, collect($revenue)->map(fn ($r) => max($r['a'], $r['b']))->max());
  $tones = ['sage' => 't-sage', 'peach' => 't-peach', 'sky' => 't-sky', 'sand' => 't-sand', 'rose' => 't-rose'];
@endphp

<div class="dash">
  {{-- ===== ستون اصلی ===== --}}
  <div class="dash-main">

    <section class="hello rise">
      <h1><span data-greet>سلام</span>، {{ $admin['first_name'] }}</h1>
      <p>امروز <b data-fa>{{ $pendingTotal }}</b> مورد منتظر بررسی شماست. صف‌ها را مرور کنید و کارهای فوری را اول انجام دهید.</p>
      <a href="{{ route('admin.listings.index') }}" class="btn btn-ink">مشاهده‌ی صف بررسی <x-admin.icon name="arrow-left" class="icon-sm" /></a>
    </section>

    {{-- کارت‌های برجسته --}}
    <section class="hero-cards" aria-label="خلاصه‌ی امروز">
      <div class="hc hc-white rise" style="--d:1">
        <div class="hc-body">
          <div class="ring" role="img" aria-label="{{ $today['percent'] }} درصد مزایده‌های امروز پایان یافته">
            <svg viewBox="0 0 120 120"><circle class="ring-bg" cx="60" cy="60" r="52" pathLength="100"/><circle class="ring-fg" cx="60" cy="60" r="52" pathLength="100" style="--p:{{ $today['percent'] }}"/></svg>
            <b><span data-count="{{ $today['percent'] }}">{{ $fa($today['percent']) }}</span>٪</b>
          </div>
          <h3 class="text-center">مزایده‌های امروز</h3>
          <small class="text-center"><span data-fa>{{ $today['done'] }}</span> از <span data-fa>{{ $today['total'] }}</span> مزایده پایان یافته</small>
        </div>
      </div>

      <a href="{{ route('admin.deposits.index') }}" class="hc hc-peach rise" style="--d:2">
        <i class="blob b1"></i><i class="blob b2"></i>
        <div class="hc-body">
          <div class="hc-big"><span data-fa>{{ str_replace('.', '٫', (string) round($escrow / 1000000000, 1)) }}</span><small>میلیارد تومان</small></div>
          <h3>بیعانه‌های در امانت</h3>
          <small><span data-fa>{{ $escrowCount }}</span> معامله در انتظار تأیید نهایی</small>
        </div>
      </a>

      <a href="{{ route('admin.auctions.index') }}" class="hc hc-sage rise" style="--d:3">
        <i class="blob b1"></i><i class="blob b2"></i>
        <div class="hc-body">
          <div class="hc-top"><span class="live-pill"><i></i> زنده</span><span class="round-go"><x-admin.icon name="arrow-left" class="icon-sm" /></span></div>
          <div class="hc-big"><span data-count="{{ $liveCount }}">{{ $fa($liveCount) }}</span><small>مزایده‌ی در حال برگزاری</small></div>
          <h3>مزایده‌ی زنده</h3>
          <small>مشاهده‌ی پیشنهادها به‌صورت لحظه‌ای</small>
        </div>
      </a>
    </section>

    {{-- شاخص‌ها --}}
    <section class="row g-3" aria-label="شاخص‌های کلیدی">
      <div class="col-6 col-xl-3 rise" style="--d:2"><x-admin.kpi icon="users" tone="sky" label="کاربران" :value="$kpi['users']" :delta="$kpi['users_delta']" /></div>
      <div class="col-6 col-xl-3 rise" style="--d:3"><x-admin.kpi icon="store" tone="sage" label="نمایشگاه‌داران" :value="$kpi['dealers']" :delta="$kpi['dealers_delta']" /></div>
      <div class="col-6 col-xl-3 rise" style="--d:4"><x-admin.kpi icon="list-checks" tone="peach" label="آگهی‌های فعال" :value="$kpi['listings']" :delta="$kpi['listings_delta']" /></div>
      <div class="col-6 col-xl-3 rise" style="--d:5"><x-admin.kpi icon="wallet" tone="sand" label="درآمد این ماه" :value="$kpi['revenue']" unit="میلیون تومان" :delta="$kpi['revenue_delta']" /></div>
    </section>

    {{-- صف‌های نیازمند بررسی --}}
    <section class="rise" style="--d:3" aria-labelledby="queueTitle">
      <div class="card-head"><h2 id="queueTitle">نیاز به بررسی</h2></div>
      <div class="tiles">
        @foreach($queues as $q)
          <a href="{{ route($q['route']) }}" class="chip-tile">
            <span class="ct-n {{ $q['count'] ? '' : 'zero' }}" data-fa>{{ $q['count'] }}</span>
            <span class="ct-ic kpi-ic {{ $tones[$q['tone']] }}"><x-admin.icon :name="$q['icon']" /></span>
            {{ $q['label'] }}
          </a>
        @endforeach
      </div>
    </section>

    {{-- نمودار درآمد --}}
    <section class="card-a rise" style="--d:4" aria-labelledby="revTitle">
      <div class="card-head">
        <h2 id="revTitle">درآمد هفته‌ی اخیر</h2>
        <span class="range-pill ms-auto"><x-admin.icon name="calendar" class="icon-sm" /> ۷ روز گذشته</span>
      </div>
      <div class="chart-wrap">
        <div class="bars" role="img" aria-label="نمودار درآمد روزانه از اشتراک‌ها و هزینه‌ی مزایده‌ها">
          @foreach($revenue as $i => $r)
            <div class="bar-col" tabindex="0" style="--i:{{ $i }}" data-tip="اشتراک: {{ $fa(number_format($r['a'])) }}&#10;مزایده: {{ $fa(number_format($r['b'])) }} (میلیون تومان)">
              <div class="bar-pair">
                <i class="bar a" style="--h:{{ round($r['a'] / $maxBar * 100) }}%"></i>
                <i class="bar b" style="--h:{{ round($r['b'] / $maxBar * 100) }}%"></i>
              </div>
              <span>{{ $r['day'] }}</span>
            </div>
          @endforeach
        </div>
        <div class="chart-side">
          <div><div class="cs-n"><span data-count="{{ $revTotal }}">{{ $fa($revTotal) }}</span> <small>میلیون</small></div><div class="cs-l"><i style="background:#8bbd68"></i> فروش اشتراک</div></div>
          <div><div class="cs-n"><span data-count="{{ $revAuction }}">{{ $fa($revAuction) }}</span> <small>میلیون</small></div><div class="cs-l"><i style="background:#f5a85b"></i> مزایده و فروش فوری</div></div>
        </div>
      </div>
    </section>

    {{-- آگهی‌های در انتظار تأیید --}}
    <section class="card-a rise" style="--d:5" aria-labelledby="pendTitle">
      <div class="card-head">
        <h2 id="pendTitle">آگهی‌های در انتظار تأیید</h2>
        <a href="{{ route('admin.listings.index') }}" class="more">همه‌ی آگهی‌ها <x-admin.icon name="chevron-left" class="icon-sm" /></a>
      </div>
      @if(count($pending))
      <div class="tbl-wrap adm-scroll">
        <table class="tbl tbl-cards">
          <thead><tr><th>آگهی</th><th>قیمت</th><th>ثبت</th><th></th></tr></thead>
          <tbody>
            @foreach($pending as $p)
              <tr data-removable>
                <td class="td-main"><div class="cell-main"><span class="thumb" style="background-image:url('{{ $p['image'] }}')"></span><div><b>{{ $p['title'] }}</b><small>{{ $p['dealer'] }} • {{ $p['meta'] }}</small></div></div></td>
                <td data-label="قیمت"><x-admin.money :value="$p['price']" /></td>
                <td data-label="ثبت"><span class="text-muted" data-fa>{{ $p['posted'] }}</span></td>
                <td class="td-act">
                  <div class="row-actions">
                    <button type="button" class="btn btn-ok btn-sm" data-confirm="این آگهی تأیید و منتشر شود؟" data-confirm-ok="تأیید آگهی" data-done="آگهی تأیید و منتشر شد"><x-admin.icon name="check" class="icon-sm" /> تأیید</button>
                    <button type="button" class="btn btn-bad btn-sm" data-confirm="این آگهی رد شود؟" data-confirm-text="دلیل رد برای نمایشگاه‌دار ارسال می‌شود." data-confirm-ok="رد آگهی" data-confirm-danger data-done="آگهی رد شد"><x-admin.icon name="x" class="icon-sm" /> رد</button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
        <x-admin.empty icon="check-circle" title="همه‌ی آگهی‌ها بررسی شده‌اند" text="آگهی جدیدی در انتظار تأیید نیست." />
      @endif
    </section>
  </div>

  {{-- ===== ستون کناری ===== --}}
  <aside class="dash-aside" aria-label="برنامه و فعالیت‌ها">
    <section class="card-a rise" style="--d:2">
      <div class="card-head"><h2>این هفته</h2><span class="range-pill ms-auto" data-today></span></div>
      <div class="week" data-week="{{ $weekMarks }}"></div>
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>مزایده‌های پیش‌رو</h2><a href="{{ route('admin.auctions.index') }}" class="more">همه</a></div>
      <ul class="list-rows">
        @foreach($upcoming as $a)
          <li><a href="{{ route('admin.auctions.index') }}" class="list-row">
            <span class="lr-ic {{ $tones[$a['tone']] }}"><x-admin.icon :name="$a['icon']" /></span>
            <span class="lr-body"><b>{{ $a['title'] }}</b><small>{{ $a['dealer'] }}</small></span>
            <span class="lr-end">@if($a['live'])<span class="live-dot"></span>@else<x-admin.icon name="clock" class="icon-sm" />@endif<span class="timer" data-countdown="{{ $a['at'] }}"></span></span>
          </a></li>
        @endforeach
      </ul>
    </section>

    <section class="card-a rise" style="--d:4">
      <div class="card-head"><h2>آخرین فعالیت‌ها</h2><a href="{{ route('admin.logs.index') }}" class="more">همه</a></div>
      <ul class="list-rows">
        @foreach($activity as $x)
          <li class="list-row">
            <span class="lr-ic {{ $tones[$x['tone']] }}"><x-admin.icon :name="$x['icon']" /></span>
            <span class="lr-body"><b>{{ $x['title'] }}</b><small>{{ $x['sub'] }}</small></span>
            <span class="lr-end text-muted fw-normal" data-fa>{{ $x['ago'] }}</span>
          </li>
        @endforeach
      </ul>
    </section>
  </aside>
</div>
@endsection
