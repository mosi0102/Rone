@extends('layouts.app')
@section('title', 'پنل نمایشگاه')
@section('page_title', 'پنل نمایشگاه')
@section('page_subtitle', $dealer->name)
@section('body_class', 'panel-page')

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}"><link rel="stylesheet" href="{{ asset('css/deal.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script>@endpush

@section('content')
@unless($auth->is_dealer)
  <x-empty-state icon="store" title="این بخش مخصوص نمایشگاه‌داران است" text="برای ثبت آگهی و مدیریت نمایشگاه، ابتدا نمایشگاه خود را ثبت و احراز هویت کنید." action="ثبت‌نام نمایشگاه" :href="route('dealer.join')" />
@else
  {{-- اشتراک --}}
  <section class="plan-card">
    <div class="pc-top">
      <div>
        <span class="pc-l">{{ $auth->dealer->plan_name }}</span>
        <b><span data-fa>{{ $auth->dealer->plan_days_left }}</span> روز تا پایان اشتراک</b>
      </div>
      <button type="button" class="btn btn-accent btn-sm" data-bs-toggle="offcanvas" data-bs-target="#renewSheet">تمدید اشتراک</button>
    </div>
    <div class="pc-bar" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $auth->dealer->plan_total_days }}" aria-valuenow="{{ $auth->dealer->plan_days_left }}"><i style="width: {{ round($auth->dealer->plan_days_left / $auth->dealer->plan_total_days * 100) }}%"></i></div>
    <p class="pc-note"><x-icon name="info" class="icon-sm" /> با پایان اشتراک، آگهی‌های شما از لیست‌ها پنهان می‌شود (قابل تمدید).</p>
  </section>

  <div class="pc-quick">
    <a href="{{ route('listings.create') }}" class="btn btn-primary"><x-icon name="plus" class="icon-sm" /> افزودن آگهی</a>
    <a href="{{ route('auctions.create') }}" class="btn btn-outline-primary"><x-icon name="gavel" class="icon-sm" /> ساخت مزایده</a>
    <a href="{{ route('dealers.show', $auth->dealer->id) }}" class="btn btn-soft"><x-icon name="eye" class="icon-sm" /> پروفایل عمومی</a>
    <a href="{{ route('panel.radar') }}" class="btn btn-soft"><x-icon name="sparkles" class="icon-sm" /> درخواست خریداران</a>
  </div>

  @if(!empty($radarRelated))
    <a href="{{ route('panel.radar') }}" class="note-card text-reset" style="text-decoration:none">
      <span class="nc-ic"><x-icon name="bell-ring" /></span>
      <div><h3><span data-fa>{{ $radarRelated }}</span> خریدار دنبال خودرویی شبیه آگهی‌های شماست</h3><p>درخواست‌ها را ببینید و آگهی مناسب را پیشنهاد دهید.</p></div>
    </a>
  @endif

  <div class="pill-tabs scroll-tabs mt-3" data-tabs="#panes" data-initial="{{ $tab }}" role="tablist">
    <button type="button" class="pill-tab" data-tab="overview" role="tab"><x-icon name="chart" class="icon-sm" /> آمار و ارقام</button>
    <button type="button" class="pill-tab" data-tab="listings" role="tab"><x-icon name="car" class="icon-sm" /> آگهی‌ها</button>
    <button type="button" class="pill-tab" data-tab="auctions" role="tab"><x-icon name="gavel" class="icon-sm" /> مزایده‌ها</button>
    <button type="button" class="pill-tab" data-tab="deals" role="tab"><x-icon name="shield" class="icon-sm" /> معاملات</button>
    <button type="button" class="pill-tab" data-tab="finance" role="tab"><x-icon name="receipt" class="icon-sm" /> مالی</button>
    <button type="button" class="pill-tab" data-tab="settings" role="tab"><x-icon name="settings" class="icon-sm" /> تنظیمات</button>
  </div>

  <div id="panes" class="panes">
    {{-- ===== آمار و ارقام ===== --}}
    <section data-pane="overview" class="pane">
      <div class="kpi-grid">
        @foreach($kpis as $k)<x-kpi :icon="$k['icon']" :label="$k['label']" :value="$k['value']" :delta="$k['delta']" :up="$k['up']" />@endforeach
      </div>

      <div class="row g-3 mt-0">
        <div class="col-lg-7">
          <div class="card-s pad chart-card">
            <div class="cc-head"><h3>بازدید آگهی‌ها</h3><span class="small text-muted-s">۷ روز گذشته</span></div>
            @php $max = max($series['values']); @endphp
            <div class="bars" data-animate-in>
              @foreach($series['values'] as $v)
                <div class="bar" style="--h: {{ round($v / $max * 100) }}%" tabindex="0" aria-label="{{ $series['labels'][$loop->index] }}: {{ $v }}">
                  <b data-fa>{{ $v }}</b><i></i><span>{{ $series['labels'][$loop->index] }}</span>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card-s pad h-100">
            <div class="cc-head"><h3>مسیر تبدیل</h3><span class="small text-muted-s">از بازدید تا معامله</span></div>
            <div class="funnel" data-animate-in>
              @foreach($funnel as $f)
                <div class="fn-row">
                  <span class="fn-l">{{ $f['label'] }}</span>
                  <div class="fn-track"><i style="--w: {{ max(4, round($f['value'] / $funnel[0]['value'] * 100)) }}%"></i></div>
                  <b class="num" data-money="{{ $f['value'] }}">{{ $f['value'] }}</b>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <x-section-head title="آخرین رویدادها" />
      <div class="list-s">
        @foreach($activity as $a)
          <div class="li">
            <div class="li-icon {{ $a['tone'] === 'ok' ? 'in' : ($a['tone'] === 'warn' ? 'out' : '') }}"><x-icon :name="$a['icon']" /></div>
            <div class="li-main"><div class="li-title">{{ $a['text'] }}</div><div class="li-sub">{{ $a['time'] }}</div></div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ===== آگهی‌ها ===== --}}
    <section data-pane="listings" class="pane d-none">
      <div class="chips mb-2" id="plFilter">
        <button class="chip active" data-value="all">همه</button>
        <button class="chip" data-value="active">فعال</button>
        <button class="chip" data-value="auction">در مزایده</button>
        <button class="chip" data-value="draft">پیش‌نویس</button>
        <button class="chip" data-value="expired">منقضی</button>
      </div>
      <div class="pl-list" id="plList">
        @foreach($listings as $item)<x-panel-listing-row :item="$item" />@endforeach
      </div>
      <div class="d-none" id="plEmpty"><x-empty-state icon="car" title="آگهی‌ای در این دسته نیست" action="افزودن آگهی" :href="route('listings.create')" /></div>
    </section>

    {{-- ===== مزایده‌ها ===== --}}
    <section data-pane="auctions" class="pane d-none">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="m-0 fs-6 fw-bold">مزایده‌های من</h3>
        <a href="{{ route('auctions.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="icon-sm" /> مزایده جدید</a>
      </div>
      <div class="pl-list">
        @foreach($auctions as $a)
          <article class="pl-row">
            <a href="{{ route('auctions.show', $a['id']) }}" class="stretched-link" aria-label="{{ $a['title'] }}"></a>
            <div class="pl-media">@if($a['image'])<img src="{{ $a['image'] }}" alt="" loading="lazy">@else<x-icon name="car" />@endif</div>
            <div class="pl-body">
              <div class="pl-top"><h3>{{ $a['title'] }}</h3><x-status-badge :status="$a['status']" /></div>
              <div class="au-grid">
                <div><span>قیمت پایه</span><b><x-price :value="$a['base']" :unit="null" /></b></div>
                <div><span>بالاترین پیشنهاد</span><b>@if($a['top'])<x-price :value="$a['top']" :unit="null" />@else—@endif</b></div>
                <div><span>شرکت‌کننده</span><b data-fa>{{ $a['bidders'] }}</b></div>
              </div>
              <div class="pl-meta">
                @if($a['status'] === 'live')<span class="ar-chip"><x-icon name="clock" class="icon-sm" /> <x-countdown :end="$a['ends_at']" inline /></span>
                @elseif($a['status'] === 'scheduled')<span class="pl-exp">شروع: <span data-jdate="{{ $a['ends_at']->toIso8601String() }}" data-time></span></span>
                @else<span class="pl-exp">پایان: <span data-jdate="{{ $a['ends_at']->toIso8601String() }}"></span></span>@endif
              </div>
            </div>
          </article>
        @endforeach
      </div>
    </section>

    {{-- ===== معاملات (بیعانه‌های امانی) ===== --}}
    <section data-pane="deals" class="pane d-none">
      @php $dl = collect($deals); $heldSum = $dl->where('status', 'held')->sum('amount'); $doneSum = $dl->where('status', 'completed')->sum('amount'); @endphp
      <div class="kpi-grid kpi-3">
        <x-kpi icon="clock" label="در انتظار تعیین وضعیت" :value="$dl->where('status', 'held')->count()" />
        <x-kpi icon="shield" label="بیعانه در امانت (تومان)" :value="$heldSum" />
        <x-kpi icon="check-circle" label="منتقل‌شده به شما (تومان)" :value="$doneSum" />
      </div>
      <p class="small text-muted-s"><x-icon name="info" class="icon-sm" /> بیعانه فقط پس از تأیید خریدار به کیف پول شما منتقل می‌شود؛ تا آن زمان نزد پلتفرم امانت است.</p>
      <div class="deal-list">
        @forelse($deals as $deal)<x-deal-row :deal="$deal" seller />@empty<x-empty-state icon="shield" title="هنوز بیعانه‌ای دریافت نکرده‌اید" />@endforelse
      </div>
    </section>

    {{-- ===== مالی ===== --}}
    <section data-pane="finance" class="pane d-none">
      <div class="wallet-card">
        <div class="label">موجودی قابل تسویه</div>
        <div class="amount"><x-price :value="$finance['available']" /></div>
        <div class="sub">
          <div><div class="label">بیعانه‌های در انتظار آزادسازی</div><div class="fw-bold"><x-price :value="$finance['pending']" /></div></div>
          <div><div class="label">درآمد این ماه</div><div class="fw-bold"><x-price :value="$finance['month_income']" /></div></div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <button type="button" class="btn btn-accent flex-fill" data-bs-toggle="offcanvas" data-bs-target="#settleSheet">درخواست تسویه</button>
          <a href="{{ route('account.wallet') }}" class="btn btn-soft flex-fill" style="--bs-btn-bg: rgba(255,255,255,.14); --bs-btn-color:#fff; --bs-btn-hover-bg: rgba(255,255,255,.24); --bs-btn-hover-color:#fff">شارژ کیف پول</a>
        </div>
      </div>
      <p class="small text-muted-s mt-2"><x-icon name="info" class="icon-sm" /> بیعانه پس از تأیید خریدار یا انقضای مهلت، به موجودی قابل تسویه منتقل می‌شود. کارمزد خدمات سایت برگشت‌ناپذیر است.</p>
      <x-section-head title="تراکنش‌ها" :href="route('account.wallet')" more="همه" />
      <div class="list-s">
        @foreach($finance['tx'] as $t)
          <div class="li">
            <div class="li-icon {{ $t['in'] ? 'in' : 'out' }}"><x-icon :name="$t['in'] ? 'arrow-down' : 'arrow-up'" /></div>
            <div class="li-main"><div class="li-title">{{ $t['title'] }}</div><div class="li-sub" data-jdate="{{ $t['date']->toIso8601String() }}" data-time></div></div>
            <b class="{{ $t['in'] ? 'text-success' : '' }}">{{ $t['in'] ? '+' : '−' }}<x-price :value="$t['amount']" :unit="null" /></b>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ===== تنظیمات و ویرایش اطلاعات ===== --}}
    <section data-pane="settings" class="pane d-none">
      <form class="form-sections" id="dealerForm" onsubmit="return false">
        <div class="card-s fcard">
          <h3>تصویر نمایشگاه</h3>
          <label class="dropzone" for="coverFile2"><img id="coverPreview2" src="{{ $dealer->cover }}" alt=""><span class="dz-hint"><x-icon name="upload-cloud" /> تغییر تصویر <small>نسبت ۲ به ۱، حداکثر ۵ مگابایت</small></span></label>
          <input type="file" id="coverFile2" class="d-none" accept="image/*" data-preview="#coverPreview2">
        </div>
        <div class="card-s fcard">
          <h3>اطلاعات نمایشگاه</h3>
          <div class="fgrid">
            <div><label class="form-label">نام نمایشگاه</label><input class="form-control" value="{{ $dealer->name }}" readonly><div class="form-text">تغییر نام نیاز به تأیید مجدد دارد؛ از پشتیبانی درخواست دهید.</div></div>
            <div><label class="form-label">نام مدیر</label><input class="form-control" value="{{ $dealer->manager }}"></div>
            <div><label class="form-label">تلفن هماهنگی</label><input class="form-control ltr" value="{{ $dealer->phone }}" inputmode="tel"><div class="form-text">فقط بعد از پرداخت بیعانه به خریدار نمایش داده می‌شود.</div></div>
            <div><label class="form-label">ساعات مراجعه حضوری</label><input class="form-control" value="{{ $dealer->hours }}"></div>
          </div>
          <label class="form-label mt-3">آدرس</label>
          <input class="form-control" value="{{ $dealer->address }}">
          <label class="form-label mt-3">درباره نمایشگاه</label>
          <textarea class="form-control" rows="4" maxlength="300" data-count="#aboutCount">{{ $dealer->about }}</textarea>
          <div class="form-text text-end" id="aboutCount"></div>
        </div>
        <div class="card-s fcard">
          <h3>ویدیو معرفی <span class="small text-muted-s fw-normal">(اختیاری)</span></h3>
          <input type="file" class="form-control" accept="video/mp4,video/webm">
          <div class="form-text">حداکثر ۵۰ مگابایت، مدت تا ۲ دقیقه.</div>
        </div>
        <button type="button" class="btn btn-primary btn-lg w-100" id="saveDealer">ذخیره تغییرات</button>
      </form>
    </section>
  </div>

  {{-- تمدید اشتراک --}}
  <div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="renewSheet" aria-labelledby="renewTitle">
    <div class="sheet-handle"></div>
    <div class="offcanvas-header"><h5 class="offcanvas-title" id="renewTitle">تمدید اشتراک</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
    <div class="offcanvas-body pt-0">
      <div class="plan-pick">
        <label class="plan"><input type="radio" name="renew" checked><span><b>۱ ماهه</b><small>تا ۳۰ آگهی فعال</small></span><x-price :value="1500000" /></label>
        <label class="plan"><input type="radio" name="renew"><span><b>۳ ماهه</b><small>۱۰٪ تخفیف</small></span><x-price :value="4000000" /></label>
        <label class="plan"><input type="radio" name="renew"><span><b>۱۲ ماهه</b><small>۲۵٪ تخفیف + نشان نمایشگاه برتر</small></span><x-price :value="13500000" /></label>
      </div>
      <button type="button" class="btn btn-accent btn-lg w-100 mt-3" id="payRenew">پرداخت از کیف پول</button>
    </div>
  </div>

  {{-- تسویه --}}
  <div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="settleSheet" aria-labelledby="settleTitle">
    <div class="sheet-handle"></div>
    <div class="offcanvas-header"><h5 class="offcanvas-title" id="settleTitle">درخواست تسویه</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
    <div class="offcanvas-body pt-0">
      <label class="form-label">مبلغ (تومان)</label>
      <input class="form-control form-control-lg ltr" inputmode="numeric" data-money-input data-words="#settleWords" placeholder="0">
      <div class="form-text" id="settleWords"></div>
      <div class="pay-rows mt-3"><div class="pay-row"><div><b>واریز به شبا</b><small class="ltr d-block text-end">{{ $finance['iban'] }}</small></div><x-icon name="check-circle" class="text-success" /></div></div>
      <p class="small text-muted-s mt-2">تسویه حداکثر تا ۲۴ ساعت کاری انجام می‌شود.</p>
      <button type="button" class="btn btn-primary btn-lg w-100" id="paySettle">ثبت درخواست</button>
    </div>
  </div>
@endunless
@endsection

@push('scripts')
<script>
  // فیلتر آگهی‌ها (نمایشی؛ بک‌اندکار با AJAX/پارامتر جایگزین کند)
  $('#plFilter').on('click', '.chip', function () {
    var v = $(this).data('value'), n = 0;
    $('#plList .pl-row').each(function () { var ok = v === 'all' || $(this).data('status') === v; $(this).toggle(ok); if (ok) n++; });
    $('#plEmpty').toggleClass('d-none', n > 0);
  });
  $('#saveDealer').on('click', function () { var $b = $(this); App.loading($b); $(document).trigger('app:dealer-save'); setTimeout(function () { App.loading($b, false); App.toast('اطلاعات نمایشگاه ذخیره شد (نمایشی)', 'success'); }, 900); });
  $('#payRenew').on('click', function () { var $b = $(this); App.loading($b); $(document).trigger('app:renew'); setTimeout(function () { App.loading($b, false); App.sheet('renewSheet', 'hide'); App.toast('اشتراک تمدید شد (نمایشی)', 'success'); }, 1000); });
  $('#paySettle').on('click', function () { var $b = $(this); App.loading($b); $(document).trigger('app:settle'); setTimeout(function () { App.loading($b, false); App.sheet('settleSheet', 'hide'); App.toast('درخواست تسویه ثبت شد (نمایشی)', 'success'); }, 1000); });
  $(document).on('click', '[data-delete]', function (e) { e.preventDefault(); $(document).trigger('app:listing-delete', [$(this).data('delete')]); App.toast('درخواست حذف ارسال شد (نمایشی)', 'warn'); });
  $(document).on('click', '[data-renew]', function (e) { e.preventDefault(); App.toast('تمدید آگهی (نمایشی)', 'info'); });
</script>
@endpush
