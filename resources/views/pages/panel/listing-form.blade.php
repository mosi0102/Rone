@extends('layouts.app')
@php $formCfg = ['types' => array_map(fn ($c) => $c['types'], $opt['categories']), 'districts' => $opt['districts'], 'priceLabels' => $opt['price_labels']]; $edit = (bool) $listing; $l = $listing; $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
@section('title', $edit ? 'ویرایش آگهی' : 'ثبت آگهی')
@section('page_title', $edit ? 'ویرایش آگهی' : 'ثبت آگهی جدید')
@section('page_subtitle', $edit ? 'کد آگهی: ' . strtr((string) $l->id, $map) : 'اطلاعات ملک را کامل وارد کنید')
@section('back', route('panel', ['tab' => 'listings']))
@section('body_class', 'no-bottom-nav panel-page has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script>@endpush
<x-map-assets />

@section('content')
@unless($auth->is_agency)
  <x-empty-state icon="store" title="ثبت آگهی فقط برای مشاوران املاک است" text="کاربران عادی می‌توانند آگهی‌ها را ببینند، با آژانس تماس بگیرند و در مزایده شرکت کنند." action="ثبت‌نام آژانس" :href="route('agency.join')" />
@else
<form id="listingForm" class="form-sections" onsubmit="return false" novalidate>

  {{-- ۱) دسته و نوع ملک --}}
  @php
    $cat = $l->category ?? 'sale_res';
    $built = 'sale_res,rent_res,sale_com,rent_com';
  @endphp
  <section class="card-s fcard">
    <h3><span class="fc-n">۱</span> دسته و نوع ملک</h3>
    <div class="seg seg-3">
      @foreach($opt['categories'] as $v => $c)
        <label><input type="radio" name="category" value="{{ $v }}" {{ $cat === $v ? 'checked' : '' }}><span><x-icon :name="$c['icon']" />{{ $c['short'] }}</span></label>
      @endforeach
    </div>
    <label class="form-label mt-3" for="ptype">نوع ملک <b class="req">*</b></label>
    <select class="form-select" id="ptype" required data-sel="{{ $l->type ?? '' }}"><option value="">انتخاب کنید</option>@foreach($opt['categories'][$cat]['types'] as $t)<option {{ ($l->type ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach</select>
  </section>

  {{-- ۲) موقعیت --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۲</span> موقعیت</h3>
    <div class="fgrid">
      <div><label class="form-label" for="city">شهر <b class="req">*</b></label>
        <select class="form-select" id="city" required><option value="">انتخاب کنید</option>@foreach($opt['cities'] as $c)<option {{ ($l->city ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select></div>
      <div><label class="form-label" for="district">محله <b class="req">*</b></label>
        <select class="form-select" id="district" required data-sel="{{ $l->district ?? '' }}"><option value="">انتخاب کنید</option>@foreach(($opt['districts'][$l->city ?? ''] ?? []) as $d)<option {{ ($l->district ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>@endforeach</select></div>
    </div>
    <label class="form-label mt-3" for="address">آدرس <b class="req">*</b></label>
    <textarea class="form-control" id="address" name="address" rows="2" required maxlength="250" placeholder="مثلاً خیابان اصلی، کوچه‌ی ۱۲، پلاک ۸">{{ $l->address ?? '' }}</textarea>

    {{-- لوکیشن روی نقشه: کلیک روی نقشه یا کشیدن نشانگر؛ خروجی lat/lng --}}
    <label class="form-label mt-3">موقعیت روی نقشه <b class="req">*</b></label>
    <div class="mp" id="mapPick" data-map-pick data-lat-input="#lat" data-lng-input="#lng" data-city-input="#city" data-district-input="#district"
         data-districts='@json($opt['district_coords'])' data-centers='@json($opt['city_centers'])'>
      <div class="mp-canvas" role="application" aria-label="نقشه‌ی انتخاب موقعیت ملک"></div>
      <div class="mp-bar">
        <span class="mp-hint">روی نقشه بزنید یا نشانگر را بکشید تا محل دقیق ملک ثبت شود.</span>
        <span class="mp-coords"></span>
        <button type="button" class="btn btn-soft btn-sm" data-locate><x-icon name="navigation" class="icon-sm" /> موقعیت من</button>
      </div>
    </div>
    <input type="hidden" id="lat" name="lat" value="{{ $l->lat ?? '' }}">
    <input type="hidden" id="lng" name="lng" value="{{ $l->lng ?? '' }}">
    <div class="form-text">موقعیت و آدرس در صفحه‌ی آگهی و در «مشاهده روی نقشه» نمایش داده می‌شود.</div>
  </section>

  {{-- ۳) مشخصات (فیلدها بر اساس دسته؛ data-cats) --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۳</span> مشخصات</h3>
    <div class="fgrid">
      <div><label class="form-label" for="area">متراژ (متر مربع) <b class="req">*</b></label><input class="form-control ltr" id="area" inputmode="numeric" required data-money-input value="{{ isset($l->area) ? number_format($l->area) : '' }}" placeholder="0"></div>
      <div data-cats="sale_res,rent_res,short,sale_com,rent_com"><label class="form-label" for="rooms">تعداد اتاق</label>
        <select class="form-select" id="rooms">@foreach(['بدون اتاق', '۱', '۲', '۳', '۴', '۵ و بیشتر'] as $r)<option {{ ($l->rooms ?? '۲') === $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach</select></div>
      <div data-cats="{{ $built }},short"><label class="form-label" for="year">سال ساخت</label>
        <select class="form-select" id="year"><option value="">انتخاب کنید</option>@for($y = 1405; $y >= 1350; $y--)<option value="{{ $y }}" {{ ($l->year ?? 0) == $y ? 'selected' : '' }}>{{ strtr((string) $y, $map) }}</option>@endfor</select></div>
      <div data-cats="{{ $built }},short"><label class="form-label" for="floor">طبقه</label>
        <select class="form-select" id="floor">@foreach(['زیرزمین', 'همکف', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۱۰ و بالاتر'] as $fl)<option {{ ($l->floor ?? 'همکف') === $fl ? 'selected' : '' }}>{{ $fl }}</option>@endforeach</select></div>
      <div data-cats="{{ $built }}"><label class="form-label" for="units">تعداد واحد در طبقه</label><input class="form-control ltr" id="units" inputmode="numeric" data-money-input value="{{ $l->units ?? '' }}" placeholder="2"></div>
      <div data-cats="sale_res,sale_com,project"><label class="form-label" for="deed">نوع سند</label>
        <select class="form-select" id="deed">@foreach(['تک‌برگ', 'منگوله‌دار', 'قولنامه‌ای', 'اوقافی'] as $d)<option {{ ($l->deed ?? 'تک‌برگ') === $d ? 'selected' : '' }}>{{ $d }}</option>@endforeach</select></div>
      <div data-cats="project"><label class="form-label" for="stage">مرحله‌ی ساخت</label>
        <select class="form-select" id="stage">@foreach(['پروانه ساخت', 'فونداسیون', 'سفت‌کاری', 'نازک‌کاری', 'آماده تحویل'] as $st)<option {{ ($l->stage ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>@endforeach</select></div>
      <div data-cats="project"><label class="form-label" for="delivery">تاریخ تحویل</label><input class="form-control" id="delivery" value="{{ $l->delivery ?? '' }}" placeholder="مثلاً اسفند ۱۴۰۶"></div>
      <div data-cats="short"><label class="form-label" for="capacity">ظرفیت (نفر)</label><input class="form-control ltr" id="capacity" inputmode="numeric" data-money-input value="{{ $l->capacity ?? '' }}" placeholder="4"></div>
      <div data-cats="short"><label class="form-label" for="min_nights">حداقل اقامت (شب)</label><input class="form-control ltr" id="min_nights" inputmode="numeric" data-money-input value="{{ $l->min_nights ?? '' }}" placeholder="1"></div>
    </div>
  </section>

  {{-- ۴) امکانات --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۴</span> امکانات <span class="small text-muted-s fw-normal">(اختیاری، اعتماد مشتری را بالا می‌برد)</span></h3>
    <div class="amenity-list">
      @foreach($opt['amenities'] as $key => [$label, $icon])
        <label class="amenity"><input type="checkbox" name="amenities[]" value="{{ $key }}" {{ in_array($key, $l->amenities ?? [], true) ? 'checked' : '' }}><span><x-icon :name="$icon" class="icon-sm" /> {{ $label }}</span></label>
      @endforeach
    </div>
  </section>

  {{-- ۵) تصاویر --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۵</span> تصاویر <b class="req">*</b></h3>
    <div class="uploader" data-uploader data-max="12" data-existing='@json($l->images ?? [])'>
      <div class="up-grid">
        <label class="up-add" tabindex="0"><x-icon name="camera" /><span>افزودن تصویر</span><input type="file" class="d-none" accept="image/*" multiple></label>
      </div>
      <div class="up-foot"><span class="small text-muted-s"><x-icon name="info" class="icon-sm" /> حداقل ۳ تصویر: نما، پذیرایی، آشپزخانه. تصویر اول، تصویر اصلی آگهی است.</span><span class="up-count small fw-bold"></span></div>
    </div>
  </section>

  {{-- ۶) قیمت (بر اساس دسته: قیمت کل / رهن و اجاره / هر شب / هر متر) --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۶</span> قیمت</h3>
    <label class="form-label" for="price"><span id="priceLabel">{{ $opt['price_labels'][$cat] }}</span> (تومان) <b class="req">*</b></label>
    <input class="form-control form-control-lg ltr" id="price" inputmode="numeric" required data-money-input data-words="#priceWords" value="{{ isset($l->price) ? number_format($l->price) : '' }}" placeholder="0">
    <div class="form-text price-words" id="priceWords"></div>
    <div data-cats="rent_res,rent_com">
      <label class="form-label mt-3" for="rent">اجاره‌ی ماهانه (تومان)</label>
      <input class="form-control ltr" id="rent" inputmode="numeric" data-money-input data-words="#rentWords" value="{{ isset($l->rent) ? number_format($l->rent) : '' }}" placeholder="0">
      <div class="form-text" id="rentWords">برای رهن کامل خالی بگذارید.</div>
      <label class="switch mt-3"><input type="checkbox" id="convertible" {{ ($l->convertible ?? true) ? 'checked' : '' }}><i></i><span>رهن و اجاره قابل تبدیل است</span></label>
    </div>
    <label class="switch mt-3"><input type="checkbox" id="negotiable" {{ ($l->negotiable ?? true) ? 'checked' : '' }}><i></i><span>قابل مذاکره</span></label>
  </section>

  {{-- ۷) توضیحات --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۷</span> توضیحات</h3>
    <textarea class="form-control" rows="5" maxlength="600" aria-label="توضیحات آگهی" data-count="#descCount" placeholder="ویژگی‌های ملک، دسترسی‌ها، وضعیت بازسازی، شرایط بازدید…">{{ $l->description ?? '' }}</textarea>
    <div class="form-text text-end" id="descCount"></div>
    <div class="form-text"><x-icon name="lock" class="icon-sm" /> شماره تماس شما در آگهی نمایش داده نمی‌شود؛ مشتری از طریق شماره‌ی آژانس یا گفتگوی سایت با شما تماس می‌گیرد.</div>
  </section>

  {{-- ۸) نوع نمایش --}}
  <section class="card-s fcard">
    <h3><span class="fc-n">۸</span> نوع نمایش آگهی</h3>
    <div class="opt-cards">
      <label class="opt-card"><input type="radio" name="sale_type" value="normal" {{ ($l->sale_type ?? 'normal') === 'normal' ? 'checked' : '' }}><span class="oc-ic"><x-icon name="building" /></span><span class="oc-t"><b>آگهی معمولی</b><small>بدون هزینه اضافه؛ در محدوده اشتراک شما</small></span></label>
      <label class="opt-card"><input type="radio" name="sale_type" value="instant" {{ ($l->sale_type ?? '') === 'instant' ? 'checked' : '' }}><span class="oc-ic acc"><x-icon name="bolt" /></span><span class="oc-t"><b>آگهی فوری</b><small>نمایش ویژه در صفحه خانه • از <x-price :value="450000" /></small></span></label>
      <span data-cats="sale_res,sale_com,project"><label class="opt-card"><input type="radio" name="sale_type" value="auction" {{ ($l->sale_type ?? '') === 'auction' ? 'checked' : '' }}><span class="oc-ic"><x-icon name="gavel" /></span><span class="oc-t"><b>مزایده</b><small>قیمت پایه، زمان شروع و پایان را شما تعیین می‌کنید</small></span></label></span>
    </div>

    <div class="sale-panel d-none" data-sale-panel="instant">
      <div class="plan-pick">
        <label class="plan"><input type="radio" name="inst_plan" checked><span><b>۲۴ ساعت</b><small>از لحظه‌ی انتشار</small></span><x-price :value="450000" /></label>
      </div>

    <div class="sale-panel d-none" data-sale-panel="auction">
      <div class="fgrid">
        <div><label class="form-label">قیمت پایه (تومان) <b class="req">*</b></label><input class="form-control ltr" inputmode="numeric" data-money-input data-words="#baseWords" placeholder="0"><div class="form-text" id="baseWords"></div></div>
        <div><label class="form-label">ورودی مزایده (تومان)</label><input class="form-control ltr" inputmode="numeric" data-money-input data-words="#entryWords" placeholder="0"><div class="form-text" id="entryWords">مبلغی که هر شرکت‌کننده برای ورود می‌پردازد.</div></div>
        <div><label class="form-label">شروع <b class="req">*</b></label><input type="datetime-local" class="form-control ltr" data-jpreview="#startJ"><div class="form-text text-primary fw-bold" id="startJ"></div></div>
        <div><label class="form-label">پایان <b class="req">*</b></label><input type="datetime-local" class="form-control ltr" data-jpreview="#endJ"><div class="form-text text-primary fw-bold" id="endJ"></div></div>
      </div>
      <p class="small text-muted-s mt-2 mb-0"><x-icon name="info" class="icon-sm" /> هزینه‌ی ساخت مزایده (۲٬۰۰۰٬۰۰۰ تومان) هنگام انتشار از کیف پول کسر می‌شود. مزایده فقط برای آگهی‌های فروش است؛ برنده برای قرارداد به آژانس مراجعه می‌کند و سایت پول ملک را دریافت نمی‌کند.</p>
    </div>
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
  var costs = { normal: 'رایگان', instant: '۴۵۰٬۰۰۰ تومان (۲۴ ساعت)', auction: '۲٬۰۰۰٬۰۰۰ تومان (ساخت مزایده)' };
  var formCfg = @json($formCfg);
  function refill($sel, items) { var keep = $sel.val() || $sel.data('sel'); $sel.empty().append('<option value="">انتخاب کنید</option>'); (items || []).forEach(function (t) { $sel.append($('<option>').text(t)); }); if (keep && (items || []).indexOf(keep) > -1) $sel.val(keep); }
  $('#listingForm').on('change', '[name=category]', function () { var c = $('[name=category]:checked').val(); refill($('#ptype'), formCfg.types[c]); $('#priceLabel').text(formCfg.priceLabels[c] || 'قیمت'); });
  $('#city').on('change', function () { refill($('#district'), formCfg.districts[this.value]); });
  $(window).on('listing:sale-type', function (e, v) { $('#costLine').text(costs[v] || ''); });
  function checkForm() {
    var bad = [];
    $('#listingForm [required]').each(function () { var empty = !$.trim(this.value); $(this).toggleClass('is-invalid', empty); if (empty) bad.push(this); });
    if ($('.up-item').length < 1) { bad.push($('.uploader')[0]); $('.uploader').addClass('is-invalid'); }
    if (!$('#lat').val() || !$('#lng').val()) { bad.push($('#mapPick')[0]); $('#mapPick').addClass('is-invalid'); }
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
  @if(!empty($l->rent))$('#rentWords').text(App.moneyWords({{ $l->rent }}));@endif
</script>
@endpush
