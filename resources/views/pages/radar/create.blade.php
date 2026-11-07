@extends('layouts.app')
@section('title', 'ثبت رادار ملک')
@section('page_title', 'رادار جدید')
@section('page_subtitle', 'مشخصات ملکِ دلخواه')
@section('back', route('radar.index'))
@section('body_class', 'no-bottom-nav')

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}"><link rel="stylesheet" href="{{ asset('css/radar.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script><script src="{{ asset('js/radar.js') }}"></script>@endpush

@section('content')
@php
  // فیلدهایی که در امتیاز رادار حساب می‌شوند (app/Http/Controllers/Concerns/RadarData.php)
  $rdSpecs = array_values(array_filter($lf['specs'], fn ($sp) => in_array($sp['name'], ['area_min', 'rent_max', 'rooms', 'year_from', 'parking', 'elevator', 'storage'], true)));
  $rdConfig = ['types' => $lf['types'], 'districts' => $lf['districts'], 'priceLabels' => $lf['price_labels']];
  $first = array_key_first($lf['categories']);
@endphp
@if(!$auth->check)
  <x-empty-state icon="sparkles" title="برای ثبت رادار وارد شوید" action="ورود / ثبت‌نام" :href="route('login', ['next' => url()->current()])" />
@else
<form id="radarForm" class="form-sections" novalidate
      data-config='@json($rdConfig)'
      data-preview-url="{{ route('radar.preview') }}" data-store-url="{{ route('radar.store') }}" data-after="{{ route('radar.index') }}">

  <section class="card-s fcard">
    <h3><span class="fc-n">۱</span> دسته</h3>
    <div class="seg seg-3">
      @foreach($lf['categories'] as $v => $t)
        <label><input type="radio" name="category" value="{{ $v }}" {{ $v === $first ? 'checked' : '' }}><span><x-icon :name="$lf['icons'][$v]" />{{ $t }}</span></label>
      @endforeach
    </div>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۲</span> مشخصات <span class="small text-muted-s fw-normal">(هرچه کمتر بنویسید، موارد بیشتری پیدا می‌شود)</span></h3>
    <div class="fgrid">
      <div><label class="form-label" for="rType">نوع ملک</label><select class="form-select" name="ptype" id="rType"><option value="">هر نوع</option></select></div>
      <div><label class="form-label" for="rCity">شهر</label><select class="form-select" name="city" id="rCity"><option value="">هر شهر</option>@foreach($lf['cities'] as $c)<option>{{ $c }}</option>@endforeach</select></div>
      <div><label class="form-label" for="rDistrict">محله</label><select class="form-select" name="district" id="rDistrict" disabled><option value="">هر محله</option></select></div>
      @foreach($rdSpecs as $sp)
        @php $cats = $sp['cats'] ?? []; @endphp
        <div data-cats="{{ implode(',', $cats) }}" {{ $cats && !in_array($first, $cats) ? 'hidden' : '' }}>
          @if($sp['type'] === 'check')
            <label class="ff-check mt-md-4"><input type="checkbox" name="{{ $sp['name'] }}" value="1"><span>{{ $sp['label'] }} داشته باشد</span></label>
          @else
            <label class="form-label" for="r_{{ $sp['name'] }}">{{ $sp['label'] }}</label>
            @if($sp['type'] === 'year')
              <select class="form-select" name="{{ $sp['name'] }}" id="r_{{ $sp['name'] }}"><option value="">مهم نیست</option>@for($y = 1405; $y >= 1360; $y--)<option value="{{ $y }}">{{ strtr((string) $y, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) }}</option>@endfor</select>
            @elseif($sp['type'] === 'number')
              <input class="form-control ltr" name="{{ $sp['name'] }}" id="r_{{ $sp['name'] }}" inputmode="numeric" autocomplete="off" placeholder="{{ $sp['ph'] ?? '' }}" data-money-field>
            @else
              <select class="form-select" name="{{ $sp['name'] }}" id="r_{{ $sp['name'] }}"><option value="">مهم نیست</option>@foreach($sp['options'] as $o)<option>{{ $o }}</option>@endforeach</select>
            @endif
          @endif
        </div>
      @endforeach
    </div>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۳</span> بودجه</h3>
    <label class="form-label" for="rPrice">سقف <span id="rPriceLabel">{{ $lf['price_labels'][$first] }}</span> (تومان)</label>
    <input class="form-control form-control-lg ltr" name="price_max" id="rPrice" inputmode="numeric" autocomplete="off" placeholder="مثلاً ۲۰٬۰۰۰٬۰۰۰٬۰۰۰" data-money-input data-words="#rPriceWords">
    <div class="price-words" id="rPriceWords" aria-live="polite"></div>
    <label class="form-label mt-3" for="rNote">توضیح (اختیاری)</label>
    <textarea class="form-control" name="note" id="rNote" rows="2" maxlength="200" placeholder="مثلاً «نورگیر، نزدیک مترو»"></textarea>
  </section>

  {{-- پیش‌نمایش زنده --}}
  <section class="rd-preview" id="rdPreview" aria-live="polite">
    <div class="rd-pv-head"><x-icon name="sparkles" /> <span id="pvText">با این مشخصات هم‌اکنون …</span></div>
    <div class="rd-pv-list" id="pvList"></div>
  </section>

  <div class="rd-submit">
    <p class="rd-keep mb-2"><x-icon name="info" class="icon-sm" /> رادار تا وقتی حذفش نکنید فعال می‌ماند و آگهی‌های جدید را هم بررسی می‌کند.</p>
    <button type="submit" class="btn btn-primary btn-lg w-100" id="rdSubmit">فعال‌سازی رادار</button>
  </div>
</form>
@endif
@endsection
