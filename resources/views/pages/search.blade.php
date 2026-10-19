@extends('layouts.app')
@section('title', 'جستجو')
@section('no_topbar', '1')
@section('body_class', 'no-bottom-nav search-page')

@push('styles')<link rel="stylesheet" href="{{ asset('css/search.css') }}">@endpush

{{--
  صفحه کامل جستجو (اسکلت). طرح و داده‌های نهایی بعداً جایگزین می‌شود.
  ورودی‌ها: $scope (listings|dealers), $city, $cities[], $popular[], $categories[]
--}}
@section('content')
<div class="sp">
  <header class="sp-head">
    <a href="{{ url()->previous() === url()->current() ? route('home') : url()->previous() }}" class="icon-btn" aria-label="بازگشت"><x-icon name="arrow-left" flip /></a>
    <form class="sp-form" action="{{ $scope === 'dealers' ? route('dealers.index') : route('listings.index') }}" role="search" id="spForm">
      <x-icon name="search" class="sp-ic" />
      <input type="search" name="q" id="spInput" autocomplete="off" autofocus placeholder="{{ $scope === 'dealers' ? 'جست و جو در نمایشگاه ها' : 'جست و جو در خودروها' }}" enterkeyhint="search">
      <button type="button" class="sp-clear" id="spClear" aria-label="پاک کردن" hidden><x-icon name="x" class="icon-sm" /></button>
      <input type="hidden" name="city" id="spCityInput">
    </form>
  </header>

  <button type="button" class="sp-city" id="openCity">
    <span class="sp-city-ic"><x-icon name="pin" /></span>
    <span class="sp-city-txt"><small>شهر</small><b class="js-city-label">{{ $city }}</b></span>
    <span class="sp-city-act">تغییر <x-icon name="chevron-left" class="icon-sm" /></span>
  </button>

  <section class="sp-block" id="recentBlock" hidden>
    <div class="sp-title"><h2><x-icon name="history" class="icon-sm" /> جستجوهای اخیر</h2><button type="button" id="clearRecent">پاک کردن</button></div>
    <div class="sp-list" id="recentList"></div>
  </section>

  <section class="sp-block">
    <div class="sp-title"><h2><x-icon name="trending-up" class="icon-sm" /> پرطرفدارها</h2></div>
    <div class="sp-chips">
      @foreach($popular as $t)<a href="{{ route('listings.index', ['q' => $t]) }}" class="sp-chip" data-term="{{ $t }}">{{ $t }}</a>@endforeach
    </div>
  </section>

  <section class="sp-block">
    <div class="sp-title"><h2>دسته‌بندی</h2></div>
    <div class="sp-cats">
      @foreach($categories as $c)
        <a href="{{ route('listings.index', ['category' => $c['slug']]) }}" class="sp-cat"><span><x-icon :name="$c['icon']" /></span>{{ $c['title'] }}</a>
      @endforeach
    </div>
  </section>

  {{-- جای نتایج زنده (بعداً با طرح نهایی) --}}
  <section class="sp-block" id="liveResults" hidden></section>
</div>

{{-- انتخاب شهر: صفحه کامل (نه شیت پایین) --}}
<div class="city-page" id="cityPage" aria-hidden="true" role="dialog" aria-label="انتخاب شهر">
  <header class="sp-head">
    <button type="button" class="icon-btn" id="closeCity" aria-label="بازگشت"><x-icon name="arrow-left" flip /></button>
    <h1 class="cp-title">انتخاب شهر</h1>
  </header>
  <div class="cp-search"><x-icon name="search" class="icon-sm" /><input type="search" id="cityFilter" placeholder="جستجوی شهر…" autocomplete="off"></div>
  <div class="cp-list" id="cityList">
    @foreach($cities as $c)
      <button type="button" class="city-item {{ $c === $city ? 'active' : '' }}" data-city="{{ $c }}">{{ $c }} <x-icon name="check" /></button>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  var RK = 'recent_searches', $in = $('#spInput');
  function recent() { try { return JSON.parse(localStorage.getItem(RK) || '[]'); } catch (e) { return []; } }
  function saveRecent(t) { t = $.trim(t); if (!t) return; var r = recent().filter(function (x) { return x !== t; }); r.unshift(t); try { localStorage.setItem(RK, JSON.stringify(r.slice(0, 8))); } catch (e) {} }
  function drawRecent() {
    var r = recent(), $l = $('#recentList').empty();
    $('#recentBlock').prop('hidden', !r.length);
    r.forEach(function (t) { $l.append($('<a class="sp-row"></a>').attr('href', '{{ route('listings.index') }}?q=' + encodeURIComponent(t)).append('<svg class="icon icon-sm"><use href="#i-history"/></svg>').append($('<span></span>').text(t)).data('term', t)); });
  }
  drawRecent();
  var c = window.AppCity && AppCity.get(); if (c) $('#spCityInput').val(c); else $('#spCityInput').val('{{ $city }}');

  $in.on('input', function () { $('#spClear').prop('hidden', !this.value); });
  $('#spClear').on('click', function () { $in.val('').trigger('input').focus(); });
  $('#spForm').on('submit', function () { saveRecent($in.val()); });
  $(document).on('click', '.sp-chip, .sp-row', function () { saveRecent($(this).data('term') || $(this).text()); });
  $('#clearRecent').on('click', function () { try { localStorage.removeItem(RK); } catch (e) {} drawRecent(); });

  // صفحه انتخاب شهر
  function cityOpen(on) { $('#cityPage').toggleClass('open', on).attr('aria-hidden', !on); $('body').toggleClass('overflow-hidden', on); if (on) setTimeout(function () { $('#cityFilter').focus(); }, 300); }
  $('#openCity').on('click', function () { cityOpen(true); });
  $('#closeCity').on('click', function () { cityOpen(false); });
  $('#cityList').on('click', '.city-item', function () {
    var name = $(this).data('city'); AppCity.set(name); $('#spCityInput').val(name);
    $('#cityList .city-item').removeClass('active'); $(this).addClass('active'); cityOpen(false); App.toast('شهر: ' + name, 'info', 1500);
  });
  $('#cityFilter').on('input', function () { var q = $.trim(this.value); $('#cityList .city-item').each(function () { $(this).toggle($(this).data('city').indexOf(q) > -1); }); });
  $(document).on('keydown', function (e) { if (e.key === 'Escape') cityOpen(false); });
  // رویداد برای بک‌اندکار: پیشنهاد/نتایج زنده با تایپ. $(document).on('app:search-input', function (e, q) {...})
  $in.on('input', function () { $(document).trigger('app:search-input', [this.value]); });
})();
</script>
@endpush
