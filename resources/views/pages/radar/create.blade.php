@extends('layouts.app')
@section('title', 'ثبت رادار خودرو')
@section('page_title', 'رادار جدید')
@section('page_subtitle', 'مشخصات خودروی دلخواه')
@section('back', route('radar.index'))
@section('body_class', 'no-bottom-nav')

@push('styles')<link rel="stylesheet" href="{{ asset('css/panel.css') }}"><link rel="stylesheet" href="{{ asset('css/radar.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/panel.js') }}"></script><script src="{{ asset('js/radar.js') }}"></script>@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="sparkles" title="برای ثبت رادار وارد شوید" action="ورود / ثبت‌نام" :href="route('login', ['next' => url()->current()])" />
@else
<form id="radarForm" class="form-sections" novalidate
      data-config='@json(['brands' => $lf['brands'], 'maxPrice' => $lf['max_price'], 'categories' => $lf['categories']])'
      data-preview-url="{{ route('radar.preview') }}" data-store-url="{{ route('radar.store') }}" data-after="{{ route('radar.index') }}">

  <section class="card-s fcard">
    <h3><span class="fc-n">۱</span> نوع وسیله</h3>
    <div class="seg seg-4">
      @foreach(['car' => ['سواری', 'car'], 'heavy' => ['سنگین', 'truck'], 'bike' => ['موتور', 'bike'], 'bicycle' => ['دوچرخه', 'bicycle']] as $v => [$t, $ic])
        <label><input type="radio" name="category" value="{{ $v }}" {{ $v === 'car' ? 'checked' : '' }}><span><x-icon :name="$ic" />{{ $t }}</span></label>
      @endforeach
    </div>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۲</span> مشخصات <span class="small text-muted-s fw-normal">(هرچه کمتر بنویسید، موارد بیشتری پیدا می‌شود)</span></h3>
    <div class="fgrid">
      <div><label class="form-label" for="rBrand">برند</label><select class="form-select" name="brand" id="rBrand"><option value="">هر برند</option></select></div>
      <div><label class="form-label" for="rModel">مدل</label><select class="form-select" name="model" id="rModel" disabled><option value="">هر مدل</option></select></div>
      @foreach($lf['specs'] as $sp)
        @php $cats = $sp['cats'] ?? []; @endphp
        <div data-cats="{{ implode(',', $cats) }}" {{ $cats && !in_array('car', $cats) ? 'hidden' : '' }}>
          <label class="form-label" for="r_{{ $sp['name'] }}">{{ $sp['label'] }}</label>
          @if($sp['type'] === 'year')
            <select class="form-select" name="{{ $sp['name'] }}" id="r_{{ $sp['name'] }}"><option value="">مهم نیست</option>@for($y = 1404; $y >= 1370; $y--)<option value="{{ $y }}">{{ strtr((string) $y, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) }}</option>@endfor</select>
          @elseif($sp['type'] === 'number')
            <input class="form-control ltr" name="{{ $sp['name'] }}" id="r_{{ $sp['name'] }}" inputmode="numeric" autocomplete="off" placeholder="{{ $sp['ph'] ?? '' }}" data-money-field>
          @else
            <select class="form-select" name="{{ $sp['name'] }}" id="r_{{ $sp['name'] }}"><option value="">مهم نیست</option>@foreach($sp['options'] as $o)<option>{{ $o }}</option>@endforeach</select>
          @endif
        </div>
      @endforeach
      <div><label class="form-label" for="rCity">شهر</label><select class="form-select" name="city" id="rCity"><option value="">هر شهر</option>@foreach($lf['cities'] as $c)<option>{{ $c }}</option>@endforeach</select></div>
    </div>
  </section>

  <section class="card-s fcard">
    <h3><span class="fc-n">۳</span> بودجه</h3>
    <label class="form-label" for="rPrice">سقف قیمت (تومان)</label>
    <input class="form-control form-control-lg ltr" name="price_max" id="rPrice" inputmode="numeric" autocomplete="off" placeholder="مثلاً ۱٬۰۰۰٬۰۰۰٬۰۰۰" data-money-input data-words="#rPriceWords">
    <div class="price-words" id="rPriceWords" aria-live="polite"></div>
    <label class="form-label mt-3" for="rNote">توضیح (اختیاری)</label>
    <textarea class="form-control" name="note" id="rNote" rows="2" maxlength="200" placeholder="مثلاً «بدون رنگ و تک‌مالک»"></textarea>
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
