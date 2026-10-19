{{--
  جستجو + انتخاب شهر (کارت سفید: شهر | کادر جستجو | دکمه ذره‌بین).
  <x-search-bar placeholder="..." scope="listings|dealers" :city="$city" />
  scope به /search?scope=…&city=…&q=… فرستاده می‌شود؛ با action مقصد دیگری بدهید. رویداد JS تغییر شهر: app:city
--}}
@props(['placeholder' => 'جست و جو در خودروها', 'scope' => 'listings', 'action' => null, 'city' => 'سبزوار', 'cities' => []])
@php $cities = count($cities) ? $cities : ['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز', 'کرج', 'سبزوار', 'قم', 'اهواز', 'رشت']; @endphp
@once
  @push('scripts')<script src="{{ asset('js/city.js') }}"></script>@endpush
@endonce
<form class="search-card" action="{{ $action ?? route('search') }}" role="search">
  <input type="hidden" name="scope" value="{{ $scope }}">
  <input type="hidden" name="city" id="cityInput" value="{{ $city }}">
  <button type="button" class="sc-city" data-bs-toggle="offcanvas" data-bs-target="#citySheet" aria-label="انتخاب شهر">
    <x-icon name="pin" /> <span id="cityLabel">{{ $city }}</span>
  </button>
  <span class="sc-vr" aria-hidden="true"></span>
  <input type="text" name="q" class="sc-input" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}" enterkeyhint="search" inputmode="search" autocomplete="off" autocapitalize="off" spellcheck="false">
  <button type="submit" class="sc-go" aria-label="جستجو"><x-icon name="search" /></button>
</form>

<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="citySheet" aria-labelledby="cityTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="cityTitle">انتخاب شهر</h5>
    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button>
  </div>
  <div class="offcanvas-body pt-0">
    <input type="text" class="form-control" id="cityFilter" placeholder="جستجوی شهر…" autocomplete="off" enterkeyhint="search">
    <div class="city-list" id="cityList">
      @foreach($cities as $c)
        <button type="button" class="city-item {{ $c === $city ? 'active' : '' }}" data-city="{{ $c }}">{{ $c }} <x-icon name="check" /></button>
      @endforeach
    </div>
  </div>
</div>
