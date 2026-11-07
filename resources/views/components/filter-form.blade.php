{{--
  فرم فیلتر (صفحه «فیلترها» در موبایل / ستون کناری در دسکتاپ)
  $f = مقادیر فعلی (آرایه)، $lf = پیکربندی (دسته‌ها، نوع ملک، شهر و محله، فیلدهای مشخصات)، $count = تعداد نتایج فعلی
  $action = مقصد (listings.index یا search). رویداد JS: filters:change (تغییر فرم)
--}}
@props(['f' => [], 'lf', 'count' => 0, 'action' => null, 'sticky' => true, 'idp' => 'ff'])
@php
  $action = $action ?? route('listings.index');
  $cat = $f['category'] ?? '';
  $fa = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
  $val = fn ($k) => $f[$k] ?? '';
  $priceMax = $lf['max_price'][$cat] ?? $lf['max_price'][''];
  $catLabel = $lf['categories'][$cat] ?? '';
  $ffConfig = ['types' => $lf['types'], 'districts' => $lf['districts'], 'priceLabels' => $lf['price_labels'], 'maxPrice' => $lf['max_price'], 'categories' => $lf['categories']];
@endphp
<form class="ff" id="{{ $idp }}Form" action="{{ $action }}" method="get" data-count-url="{{ route('listings.count') }}" data-config='@json($ffConfig)'>
  @if(!empty($f['q']))<input type="hidden" name="q" value="{{ $f['q'] }}">@endif
  @if(!empty($f['sort']))<input type="hidden" name="sort" value="{{ $f['sort'] }}">@endif

  {{-- ۱) دسته، نوع ملک و موقعیت --}}
  <section class="ff-sec">
    <h2>دسته و موقعیت ملک</h2>
    <label class="form-label" for="{{ $idp }}Cat">دسته</label>
    <select class="form-select" name="category" id="{{ $idp }}Cat">
      <option value="">همه</option>
      @foreach($lf['categories'] as $k => $t)<option value="{{ $k }}" {{ $cat === $k ? 'selected' : '' }}>{{ $t }}</option>@endforeach
    </select>
    <label class="form-label" for="{{ $idp }}Type">نوع ملک</label>
    <select class="form-select" name="ptype" id="{{ $idp }}Type" data-sel="{{ $val('ptype') }}"><option value="">همه انواع</option>
      @foreach(($cat ? ($lf['types'][$cat] ?? []) : []) as $t)<option {{ $val('ptype') === $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach</select>
    <div class="ff-row2">
      <div><label class="form-label" for="{{ $idp }}City">شهر</label>
        <select class="form-select" name="city" id="{{ $idp }}City"><option value="">همه شهرها</option>@foreach($lf['cities'] as $c)<option {{ $val('city') === $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select></div>
      <div><label class="form-label" for="{{ $idp }}District">محله</label>
        <select class="form-select" name="district" id="{{ $idp }}District" data-sel="{{ $val('district') }}"><option value="">همه محله‌ها</option>
          @foreach(($lf['districts'][$val('city')] ?? []) as $d)<option {{ $val('district') === $d ? 'selected' : '' }}>{{ $d }}</option>@endforeach</select></div>
    </div>
  </section>

  {{-- ۲) قیمت (برچسب بر اساس دسته: قیمت کل / رهن / هر شب / هر متر) --}}
  @php $pLabel = $lf['price_labels'][$cat] ?? 'قیمت'; @endphp
  <section class="ff-sec">
    <h2>بازه <span data-price-label>{{ $pLabel }}</span></h2>
    <p class="ff-hint">همه مبالغ به تومان هستند</p>
    <div class="ff-row2">
      <div><label class="form-label" for="{{ $idp }}Pmin">حداقل <span data-price-label>{{ $pLabel }}</span></label><input class="form-control ltr" id="{{ $idp }}Pmin" name="price_min" inputmode="numeric" autocomplete="off" placeholder="۰" value="{{ $val('price_min') }}" data-money-field></div>
      <div><label class="form-label" for="{{ $idp }}Pmax">حداکثر <span data-price-label>{{ $pLabel }}</span></label><input class="form-control ltr" id="{{ $idp }}Pmax" name="price_max" inputmode="numeric" autocomplete="off" placeholder="{{ strtr(number_format($priceMax), $fa + [',' => '٬']) }}" value="{{ $val('price_max') }}" data-money-field></div>
    </div>
    <div class="rng" id="{{ $idp }}Range" data-max="{{ $priceMax }}">
      <div class="rng-track"><i class="rng-fill"></i></div>
      <input type="range" class="rng-a" min="0" max="100" step="1" value="0" aria-label="حداقل قیمت">
      <input type="range" class="rng-b" min="0" max="100" step="1" value="100" aria-label="حداکثر قیمت">
    </div>
  </section>

  {{-- ۳) مشخصات فنی (بر اساس دسته) --}}
  <section class="ff-sec" id="{{ $idp }}Specs">
    <h2>مشخصات <span data-cat-label>{{ $catLabel }}</span></h2>
    <div class="ff-grid">
      @foreach($lf['specs'] as $sp)
        @php $cats = $sp['cats'] ?? []; $visible = !$cat ? (empty($cats) || count($cats) >= 4) : (empty($cats) || in_array($cat, $cats)); @endphp
        <div class="ff-field {{ !empty($sp['half']) ? 'half' : 'full' }} {{ $visible ? '' : 'd-none' }}" data-cats="{{ implode(',', $cats) }}">
          @if($sp['type'] === 'check')
            <label class="ff-check"><input type="checkbox" name="{{ $sp['name'] }}" value="1" {{ $val($sp['name']) ? 'checked' : '' }}><span>{{ $sp['label'] }}</span></label>
          @else
          <label class="form-label" for="{{ $idp }}_{{ $sp['name'] }}">{{ $sp['label'] }}</label>
          @if($sp['type'] === 'year')
            <select class="form-select" name="{{ $sp['name'] }}" id="{{ $idp }}_{{ $sp['name'] }}"><option value="">همه</option>@for($y = 1404; $y >= 1370; $y--)<option value="{{ $y }}" {{ (string) $val($sp['name']) === (string) $y ? 'selected' : '' }}>{{ strtr((string) $y, $fa) }}</option>@endfor</select>
          @elseif($sp['type'] === 'number')
            <input class="form-control ltr" name="{{ $sp['name'] }}" id="{{ $idp }}_{{ $sp['name'] }}" inputmode="numeric" autocomplete="off" placeholder="{{ $sp['ph'] ?? '' }}" value="{{ $val($sp['name']) }}" data-money-field>
          @else
            <select class="form-select" name="{{ $sp['name'] }}" id="{{ $idp }}_{{ $sp['name'] }}"><option value="">همه</option>@foreach($sp['options'] as $o)<option {{ $val($sp['name']) === $o ? 'selected' : '' }}>{{ $o }}</option>@endforeach</select>
          @endif
          @endif
        </div>
      @endforeach
    </div>
  </section>

  {{-- ۴) ترجیحات --}}
  <section class="ff-sec">
    <h2>ترجیحات آگهی</h2>
    <label class="ff-switch"><span>فقط آگهی‌های دارای عکس</span><input type="checkbox" name="with_photo" value="1" {{ $val('with_photo') ? 'checked' : '' }}><i></i></label>
    <div class="ff-seg" role="radiogroup" aria-label="نوع آگهی">
      @foreach(['' => 'همه', 'instant' => 'فوری', 'auction' => 'مزایده'] as $v => $t)
        <label><input type="radio" name="sale_type" value="{{ $v }}" {{ (string) $val('sale_type') === (string) $v ? 'checked' : '' }}><span>{{ $t }}</span></label>
      @endforeach
    </div>
    @if($auth->agency)
      <label class="ff-switch"><span>فقط آگهی‌های آژانس من</span><input type="checkbox" name="mine" value="1" {{ $val('mine') ? 'checked' : '' }}><i></i></label>
    @endif
    <p class="ff-note"><x-icon name="shield" class="icon-sm" /> همه فروشندگان تأیید هویت شده‌اند</p>
  </section>

  <div class="ff-actions {{ $sticky ? 'is-sticky' : '' }}">
    <button type="submit" class="btn btn-primary btn-lg w-100" id="{{ $idp }}Submit" data-count="{{ $count }}">نمایش <span data-fa class="ff-n">{{ $count }}</span> آگهی</button>
  </div>
  <button type="button" class="ff-clear" id="{{ $idp }}Clear">پاک کردن همه فیلترها</button>
</form>
