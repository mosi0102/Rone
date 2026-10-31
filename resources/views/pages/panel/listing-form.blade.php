@extends('layouts.app')
@php $edit = (bool) $listing; $l = $listing; $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
@section('title', $edit ? 'ویرایش آگهی' : 'ثبت آگهی')
@section('page_title', $edit ? 'ویرایش آگهی' : 'ثبت آگهی جدید')
@section('page_subtitle', $edit ? 'کد آگهی: ' . strtr((string) $l->id, $map) : 'اطلاعات خودرو را کامل وارد کنید')
@section('back', route('panel', ['tab' => 'listings']))
@section('body_class', 'no-bottom-nav panel-page has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script>@endpush

@section('content')
@unless($auth->is_dealer)
  <x-empty-state icon="store" title="ثبت آگهی فقط برای نمایشگاه‌داران است" text="کاربران عادی می‌توانند آگهی‌ها را ببینند، بیعانه بدهند و در مزایده شرکت کنند." action="ثبت‌نام نمایشگاه" :href="route('dealer.join')" />
@else
<form id="listingForm" class="form-sections" onsubmit="return false" novalidate>

  {{-- ۱) نوع --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۱</span> نوع وسیله</h3>
    <div class="seg seg-4">
      @foreach([['car','سواری','car'],['heavy','سنگین','truck'],['bike','موتور','bike'],['bicycle','دوچرخه','bicycle']] as [$v,$t,$ic])
        <label><input type="radio" name="category" value="{{ $v }}" {{ ($l->category ?? 'car') === $v ? 'checked' : '' }}><span><x-icon :name="$ic" />{{ $t }}</span></label>
      @endforeach
    </div>
  </section>

  {{-- ۲) مشخصات --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۲</span> مشخصات</h3>
    <div class="fgrid">
      <div><label class="form-label" for="brand">برند <b class="req">*</b></label>
        <select class="form-select" id="brand" required><option value="">انتخاب کنید</option>@foreach($opt['brands'] as $b)<option {{ ($l->brand ?? '') === $b ? 'selected' : '' }}>{{ $b }}</option>@endforeach</select></div>
      <div><label class="form-label" for="model">مدل / تیپ <b class="req">*</b></label><input class="form-control" id="model" required value="{{ $l->model ?? '' }}" placeholder="مثلاً ۲۰۷ اتوماتیک"></div>
      <div><label class="form-label" for="year">سال ساخت <b class="req">*</b></label>
        <select class="form-select" id="year" required><option value="">انتخاب کنید</option>@for($y = 1404; $y >= 1370; $y--)<option value="{{ $y }}" {{ ($l->year ?? 0) == $y ? 'selected' : '' }}>{{ strtr((string) $y, $map) }}</option>@endfor</select></div>
      <div><label class="form-label" for="km">کارکرد (کیلومتر) <b class="req">*</b></label><input class="form-control ltr" id="km" inputmode="numeric" required data-money-input value="{{ isset($l->km) ? number_format($l->km) : '' }}" placeholder="0"></div>
      <div><label class="form-label" for="color">رنگ</label>
        <select class="form-select" id="color"><option value="">انتخاب کنید</option>@foreach($opt['colors'] as $c)<option {{ ($l->color ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select></div>
      <div data-cats="car,heavy,bike"><label class="form-label" for="fuel">سوخت</label>
        <select class="form-select" id="fuel">@foreach(['بنزین','دیزل','دوگانه‌سوز','هیبرید','برقی'] as $f)<option {{ ($l->fuel ?? 'بنزین') === $f ? 'selected' : '' }}>{{ $f }}</option>@endforeach</select></div>
    </div>
    <div class="mt-3" data-cats="car,heavy">
      <label class="form-label mt-3">گیربکس</label>
      <div class="seg seg-2">
        <label><input type="radio" name="gearbox" value="auto" {{ ($l->gearbox ?? 'auto') === 'auto' ? 'checked' : '' }}><span>اتوماتیک</span></label>
        <label><input type="radio" name="gearbox" value="manual" {{ ($l->gearbox ?? '') === 'manual' ? 'checked' : '' }}><span>دنده‌ای</span></label>
      </div>
    </div>
    <div class="fgrid mt-3" data-cats="bicycle" hidden>
      <div><label class="form-label" for="btype">نوع دوچرخه</label>
        <select class="form-select" id="btype">@foreach(['کوهستان','جاده','شهری','برقی'] as $t)<option {{ ($l->btype ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach</select></div>
      <div><label class="form-label" for="wheel">سایز طوقه (اینچ)</label>
        <select class="form-select" id="wheel">@foreach(['۲۶','۲۷٫۵','۲۹'] as $t)<option {{ ($l->wheel ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach</select></div>
    </div>
  </section>

  {{-- ۳) وضعیت فنی --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۳</span> وضعیت فنی <span class="small text-muted-s fw-normal">(اختیاری، اعتماد خریدار را بالا می‌برد)</span></h3>
    <div class="cond-list">
      @foreach(['بدنه', 'موتور', 'شاسی جلو', 'شاسی عقب', 'گیربکس'] as $part)
        <div class="cond"><span>{{ $part }}</span><select class="form-select form-select-sm"><option value="">نامشخص</option>@foreach($opt['conditions'] as $c)<option>{{ $c }}</option>@endforeach</select></div>
      @endforeach
    </div>
  </section>

  {{-- ۴) تصاویر --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۴</span> تصاویر <b class="req">*</b></h3>
    <div class="uploader" data-uploader data-max="12" data-existing='@json($l->images ?? [])'>
      <div class="up-grid">
        <label class="up-add" tabindex="0"><x-icon name="camera" /><span>افزودن تصویر</span><input type="file" class="d-none" accept="image/*" multiple></label>
      </div>
      <div class="up-foot"><span class="small text-muted-s"><x-icon name="info" class="icon-sm" /> حداقل ۳ تصویر: روبرو، داخل، پشت. تصویر اول، تصویر اصلی آگهی است.</span><span class="up-count small fw-bold"></span></div>
    </div>
  </section>

  {{-- ۵) قیمت --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۵</span> قیمت</h3>
    <label class="form-label" for="price">قیمت (تومان) <b class="req">*</b></label>
    <input class="form-control form-control-lg ltr" id="price" inputmode="numeric" required data-money-input data-words="#priceWords" value="{{ isset($l->price) ? number_format($l->price) : '' }}" placeholder="0">
    <div class="form-text price-words" id="priceWords">{{ isset($l->price) ? '' : '' }}</div>
    <label class="switch mt-3"><input type="checkbox" id="negotiable" {{ ($l->negotiable ?? true) ? 'checked' : '' }}><i></i><span>قابل مذاکره</span></label>
  </section>

  {{-- ۶) توضیحات --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۶</span> توضیحات</h3>
    <textarea class="form-control" rows="5" maxlength="600" data-count="#descCount" placeholder="وضعیت خودرو، سرویس‌ها، بیمه، شرایط بازدید…">{{ $l->description ?? '' }}</textarea>
    <div class="form-text text-end" id="descCount"></div>
    <div class="form-text"><x-icon name="lock" class="icon-sm" /> شماره تماس شما در آگهی نمایش داده نمی‌شود؛ ارتباط خریدار فقط بعد از پرداخت بیعانه برقرار می‌شود.</div>
  </section>

  {{-- ۷) نوع فروش --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۷</span> نوع نمایش آگهی</h3>
    <div class="opt-cards">
      <label class="opt-card"><input type="radio" name="sale_type" value="normal" {{ ($l->sale_type ?? 'normal') === 'normal' ? 'checked' : '' }}><span class="oc-ic"><x-icon name="car" /></span><span class="oc-t"><b>آگهی معمولی</b><small>بدون هزینه اضافه؛ در محدوده اشتراک شما</small></span></label>
      <label class="opt-card"><input type="radio" name="sale_type" value="instant" {{ ($l->sale_type ?? '') === 'instant' ? 'checked' : '' }}><span class="oc-ic acc"><x-icon name="bolt" /></span><span class="oc-t"><b>فروش فوری</b><small>نمایش ویژه در صفحه خانه • از <x-price :value="450000" /></small></span></label>
      <label class="opt-card"><input type="radio" name="sale_type" value="auction" {{ ($l->sale_type ?? '') === 'auction' ? 'checked' : '' }}><span class="oc-ic"><x-icon name="gavel" /></span><span class="oc-t"><b>مزایده</b><small>قیمت پایه، زمان شروع و پایان را شما تعیین می‌کنید</small></span></label>
    </div>

    <div class="sale-panel d-none" data-sale-panel="instant">
      <div class="plan-pick">
        <label class="plan"><input type="radio" name="inst_plan" checked><span><b>۳ روزه</b></span><x-price :value="450000" /></label>
        <label class="plan"><input type="radio" name="inst_plan"><span><b>۷ روزه</b><small>پیشنهاد ما</small></span><x-price :value="900000" /></label>
        <label class="plan"><input type="radio" name="inst_plan"><span><b>۱۴ روزه</b></span><x-price :value="1500000" /></label>
      </div>
    </div>

    <div class="sale-panel d-none" data-sale-panel="auction">
      <div class="fgrid">
        <div><label class="form-label">قیمت پایه (تومان) <b class="req">*</b></label><input class="form-control ltr" inputmode="numeric" data-money-input data-words="#baseWords" placeholder="0"><div class="form-text" id="baseWords"></div></div>
        <div><label class="form-label">ورودی مزایده (تومان)</label><input class="form-control ltr" inputmode="numeric" data-money-input data-words="#entryWords" placeholder="0"><div class="form-text" id="entryWords">مبلغی که هر شرکت‌کننده برای ورود می‌پردازد.</div></div>
        <div><label class="form-label">شروع <b class="req">*</b></label><input type="datetime-local" class="form-control ltr" data-jpreview="#startJ"><div class="form-text text-primary fw-bold" id="startJ"></div></div>
        <div><label class="form-label">پایان <b class="req">*</b></label><input type="datetime-local" class="form-control ltr" data-jpreview="#endJ"><div class="form-text text-primary fw-bold" id="endJ"></div></div>
      </div>
      <p class="small text-muted-s mt-2 mb-0"><x-icon name="info" class="icon-sm" /> هزینه ساخت مزایده هنگام انتشار از کیف پول کسر می‌شود. پس از پایان، برنده باید در مهلت مقرر بیعانه بپردازد.</p>
    </div>
  </section>

  <div class="sticky-cta fs-cta">
    <div class="fs-sum"><span class="small text-muted-s">هزینه این آگهی</span><b id="costLine">رایگان</b></div>
    <button type="button" class="btn btn-soft" id="saveDraft">پیش‌نویس</button>
    <button type="button" class="btn btn-primary btn-lg" id="publish">{{ $edit ? 'ذخیره تغییرات' : 'ثبت و انتشار' }}</button>
  </div>
</form>
@endunless
@endsection

@push('scripts')
<script>
  var costs = { normal: 'رایگان', instant: 'از ۴۵۰٬۰۰۰ تومان', auction: 'طبق تعرفه مزایده' };
  $(window).on('listing:sale-type', function (e, v) { $('#costLine').text(costs[v] || ''); });
  function checkForm() {
    var bad = [];
    $('#listingForm [required]').each(function () { var empty = !$.trim(this.value); $(this).toggleClass('is-invalid', empty); if (empty) bad.push(this); });
    if ($('.up-item').length < 1) { bad.push($('.uploader')[0]); $('.uploader').addClass('is-invalid'); }
    if (bad.length) { bad[0].scrollIntoView({ behavior: 'smooth', block: 'center' }); App.toast('فیلدهای الزامی را کامل کنید', 'error'); return false; }
    return true;
  }
  $('#listingForm').on('input change', '.is-invalid', function () { $(this).removeClass('is-invalid'); });
  $(document).on('listing:images', function () { $('.uploader').removeClass('is-invalid'); });
  $('#publish').on('click', function () {
    if (!checkForm()) return; var $b = $(this); App.loading($b);
    $(document).trigger('app:listing-submit', [{ id: {{ $edit ? $l->id : 'null' }}, publish: true }]);
    setTimeout(function () { App.loading($b, false); App.toast('{{ $edit ? 'تغییرات ذخیره شد' : 'آگهی ثبت شد' }} (نمایشی)', 'success'); setTimeout(function () { location.href = '{{ route('panel', ['tab' => 'listings']) }}'; }, 900); }, 1100);
  });
  $('#saveDraft').on('click', function () { $(document).trigger('app:listing-submit', [{ id: {{ $edit ? $l->id : 'null' }}, publish: false }]); App.toast('پیش‌نویس ذخیره شد (نمایشی)', 'info'); });
  $('#priceWords').text(App.moneyWords({{ $l->price ?? 0 }}));
</script>
@endpush
