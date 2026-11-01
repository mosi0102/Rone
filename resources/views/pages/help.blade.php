@extends('layouts.app')
@section('title', 'راهنما')
@section('page_title', 'راهنما')
@section('page_subtitle', 'پرسش‌های پرتکرار')
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/account.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/help.js') }}"></script>@endpush

@section('content')
<div class="pf">
  <div class="hp-search"><x-icon name="search" /><input type="search" id="hpQ" class="form-control" placeholder="جستجو در راهنما…" aria-label="جستجو در راهنما"></div>
  <div class="chips" role="tablist" aria-label="گروه‌ها">
    <button type="button" class="chip active" data-g="all">همه</button>
    @foreach($groups as $k => [$title])<button type="button" class="chip" data-g="{{ $k }}">{{ $title }}</button>@endforeach
  </div>

  @foreach($groups as $k => [$title, $icon, $qs])
    <section class="hp-group" data-group="{{ $k }}">
      <h3 class="ac-sec"><x-icon :name="$icon" class="icon-sm" /> {{ $title }}</h3>
      <div class="accordion hp-acc" id="acc-{{ $k }}">
        @foreach($qs as $i => [$q, $a])
          <div class="accordion-item hp-item">
            <h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c-{{ $k }}-{{ $i }}" aria-expanded="false" aria-controls="c-{{ $k }}-{{ $i }}">{{ $q }}</button></h4>
            <div id="c-{{ $k }}-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#acc-{{ $k }}"><div class="accordion-body">{{ $a }}</div></div>
          </div>
        @endforeach
      </div>
    </section>
  @endforeach
  <div class="d-none" id="hpEmpty"><x-empty-state icon="search" title="نتیجه‌ای پیدا نشد" text="عبارت دیگری را جستجو کنید یا با پشتیبانی تماس بگیرید." /></div>

  <section class="ac-join hp-support">
    <x-icon name="phone" />
    <div><b>پشتیبانی</b><small>{{ $support['hours'] }}</small></div>
    <a href="tel:02191000000" class="btn btn-primary btn-sm ms-auto ltr">{{ $support['phone'] }}</a>
  </section>
</div>
@endsection
