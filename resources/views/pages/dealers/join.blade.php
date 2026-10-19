@extends('layouts.app')
@section('title', 'ثبت‌نام نمایشگاه')
@section('page_title', 'همکاری با ' . config('app.name'))
@section('page_subtitle', 'نمایشگاه خود را ثبت و احراز هویت کنید')
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script>@endpush

@section('content')
@if($auth->is_dealer)
  <x-empty-state icon="badge-check" title="شما نمایشگاه‌دار هستید" text="برای ثبت آگهی و مدیریت نمایشگاه به پنل بروید." action="ورود به پنل نمایشگاه" :href="route('panel')" />
@else
  <section class="join-hero">
    <h1>نمایشگاه‌دار هستید؟</h1>
    <p>به بازار تخصصی نمایشگاه‌های خودرو بپیوندید؛ آگهی بگذارید، مزایده برگزار کنید و بیعانه امن دریافت کنید.</p>
  </section>

  <div class="benefits">
    <div class="benefit"><span class="dm-ic" style="width:44px;height:44px;border-radius:14px"><x-icon name="shield" /></span><div><h3>بیعانه امانی</h3><p>پول خریدار نزد پلتفرم می‌ماند و بعد از معامله آزاد می‌شود.</p></div></div>
    <div class="benefit"><span class="dm-ic" style="width:44px;height:44px;border-radius:14px"><x-icon name="gavel" /></span><div><h3>مزایده زنده</h3><p>با تعیین قیمت پایه، رقابت واقعی میان خریداران ایجاد کنید.</p></div></div>
    <div class="benefit"><span class="dm-ic" style="width:44px;height:44px;border-radius:14px"><x-icon name="chart" /></span><div><h3>آمار و گزارش مالی</h3><p>بازدید، بیعانه و درآمد نمایشگاه را در پنل ببینید.</p></div></div>
  </div>

  <x-section-head title="مراحل شروع" />
  <ol class="timeline card-s pad">
    <li><div><b>ثبت اطلاعات و مدارک</b><span>اطلاعات نمایشگاه و مدارک هویتی را بارگذاری کنید.</span></div></li>
    <li><div><b>احراز هویت</b><span>تیم ما حداکثر طی ۲۴ ساعت کاری بررسی می‌کند.</span></div></li>
    <li><div><b>خرید اشتراک</b><span>اشتراک زمانی را فعال کنید.</span></div></li>
    <li><div><b>انتشار آگهی</b><span>تا پایان اشتراک آگهی بگذارید و مزایده بسازید.</span></div></li>
  </ol>

  @if(!$auth->check)
    <div class="card-s pad text-center mt-3">
      <p class="mb-3">برای ثبت‌نام نمایشگاه ابتدا وارد حساب خود شوید.</p>
      <a href="{{ route('login', ['next' => url()->current()]) }}" class="btn btn-primary btn-lg w-100">ورود / ثبت‌نام با شماره موبایل</a>
    </div>
  @else
  <section class="wizard card-s pad" data-wizard id="joinWizard">
    <div class="wz-progress" aria-hidden="true">
      <span class="wz-dot" data-dot><i>۱</i> اطلاعات</span><span class="wz-bar"><i></i></span>
      <span class="wz-dot" data-dot><i>۲</i> مدارک</span><span class="wz-bar"><i></i></span>
      <span class="wz-dot" data-dot><i>۳</i> ارسال</span>
    </div>

    <div data-step>
      <div class="fgrid">
        <div><label class="form-label">نام نمایشگاه <b class="req">*</b></label><input class="form-control" required placeholder="مثلاً اتوگالری هشت"></div>
        <div><label class="form-label">نام و نام خانوادگی مدیر <b class="req">*</b></label><input class="form-control" required></div>
        <div><label class="form-label">شهر <b class="req">*</b></label><select class="form-select" required><option value="">انتخاب کنید</option>@foreach(['تهران','مشهد','اصفهان','شیراز','تبریز','کرج','سبزوار','قم'] as $c)<option>{{ $c }}</option>@endforeach</select></div>
        <div><label class="form-label">تلفن نمایشگاه <b class="req">*</b></label><input class="form-control ltr" inputmode="tel" required placeholder="02122345678"></div>
      </div>
      <label class="form-label mt-3">نوع فعالیت</label>
      <div class="chips-check">
        <label><input type="checkbox" checked><span><x-icon name="car" class="icon-sm" /> سواری</span></label>
        <label><input type="checkbox"><span><x-icon name="truck" class="icon-sm" /> سنگین</span></label>
        <label><input type="checkbox"><span><x-icon name="bike" class="icon-sm" /> موتورسیکلت</span></label>
      </div>
      <label class="form-label mt-3">آدرس نمایشگاه <b class="req">*</b></label>
      <input class="form-control" required placeholder="خیابان، پلاک، نزدیک‌ترین نشانی">
      <div class="wz-nav"><button type="button" class="btn btn-primary btn-lg" data-next>مرحله بعد</button></div>
    </div>

    <div data-step class="d-none">
      <p class="small text-muted-s">تصاویر باید واضح و کامل باشند. اطلاعات شما فقط برای احراز هویت استفاده می‌شود.</p>
      <div class="d-grid gap-2">
        @foreach([['lic','پروانه کسب / مجوز نمایشگاه'],['idc','کارت ملی مدیر'],['shop','عکس سردر نمایشگاه']] as [$k,$t])
          <label class="doc-up"><img id="prev_{{ $k }}" src="{{ asset('img/logo.png') }}" alt=""><span class="du-t">{{ $t }} <b class="req">*</b><small>برای انتخاب یا عکس‌گرفتن لمس کنید</small></span><x-icon name="upload-cloud" class="text-primary"/><input type="file" accept="image/*" required data-preview="#prev_{{ $k }}"></label>
        @endforeach
      </div>
      <div class="wz-nav"><button type="button" class="btn btn-soft btn-lg" data-prev>قبلی</button><button type="button" class="btn btn-primary btn-lg" data-next>مرحله بعد</button></div>
    </div>

    <div data-step class="d-none">
      <div class="pay-rows mb-3">
        <div class="pay-row"><div><b>بررسی مدارک</b><small>حداکثر ۲۴ ساعت کاری</small></div><x-icon name="clock" /></div>
        <div class="pay-row"><div><b>ثبت‌نام</b><small>رایگان؛ هزینه فقط برای اشتراک و خدمات آگهی</small></div><x-icon name="check-circle" class="text-success" /></div>
      </div>
      <label class="d-flex align-items-start gap-2 mb-2"><input type="checkbox" required style="width:20px;height:20px;flex-shrink:0;margin-top:2px;accent-color:var(--primary)"><span class="small">صحت اطلاعات را تأیید می‌کنم و قوانین نمایشگاه‌داران را می‌پذیرم.</span></label>
      <div class="wz-nav"><button type="button" class="btn btn-soft btn-lg" data-prev>قبلی</button><button type="button" class="btn btn-accent btn-lg" id="joinSubmit">ارسال برای احراز هویت</button></div>
    </div>

    <div data-step class="d-none wz-done">
      <div class="ok-ring"><x-icon name="check" /></div>
      <h2 class="fs-5">درخواست شما ثبت شد</h2>
      <p class="text-muted-s">نتیجه احراز هویت تا ۲۴ ساعت کاری به شما اطلاع داده می‌شود. پس از تأیید، اشتراک را فعال کنید و آگهی بگذارید.</p>
      <x-status-badge status="pending" />
      <a href="{{ route('home') }}" class="btn btn-primary btn-lg w-100 mt-3">بازگشت به خانه</a>
    </div>
  </section>
  @endif
@endif
@endsection

@push('scripts')
<script>
  $('#joinSubmit').on('click', function () {
    var $w = $('#joinWizard'), ok = true;
    $w.find('[data-step]').eq(2).find('[required]').each(function () { if (!this.checked) { ok = false; $(this).addClass('is-invalid'); } });
    if (!ok) { App.toast('پذیرش قوانین الزامی است', 'error'); return; }
    var $b = $(this); App.loading($b); $(document).trigger('app:dealer-join');
    setTimeout(function () { App.loading($b, false); $w.data('go')(3); $w.find('.wz-progress').hide(); }, 1200);
  });
</script>
@endpush
