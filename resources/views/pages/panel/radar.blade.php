@extends('layouts.app')
@section('title', 'درخواست‌های مشتریان')
@section('page_title', 'درخواست‌های مشتریان')
@section('page_subtitle', 'رادار ملک — مشتریانی که دنبال ملک می‌گردند')
@section('back', route('panel'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/deal.css') }}"><link rel="stylesheet" href="{{ asset('css/radar.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/radar.js') }}"></script>@endpush

@section('content')
@if(!$auth->is_agency)
  <x-empty-state icon="store" title="این بخش مخصوص مشاوران املاک است" text="برای دیدن درخواست‌های مشتریان، با حساب مشاور املاک وارد شوید." action="ثبت‌نام آژانس" :href="route('agency.join')" />
@else
  @if($related)
    <section class="note-card mt-0 mb-3">
      <span class="nc-ic"><x-icon name="bell-ring" /></span>
      <div><h3><span data-fa>{{ $related }}</span> درخواست با آگهی‌های شما مطابقت دارد</h3><p>با «پیشنهاد آگهی» به مشتری اطلاع بدهید؛ ارتباط فقط از طریق گفتگوی سامانه انجام می‌شود.</p></div>
    </section>
  @endif

  <form class="rd-filter" id="rdFilter" onsubmit="return false">
    <div class="rd-cats" role="tablist" aria-label="دسته">
      <button type="button" class="on" data-cat="all" role="tab">همه</button>
      @foreach($cats as $k => $t)<button type="button" data-cat="{{ $k }}" role="tab">{{ $t }}</button>@endforeach
    </div>
    <div class="rd-fgrid">
      <select class="form-select" id="fCity" aria-label="شهر"><option value="">همه شهرها</option>@foreach($cities as $c)<option>{{ $c }}</option>@endforeach</select>
      <select class="form-select" id="fSort" aria-label="مرتب‌سازی"><option value="new">جدیدترین</option><option value="match">بیشترین تطابق با آگهی من</option><option value="budget">بیشترین بودجه</option></select>
    </div>
    <label class="form-check rd-check"><input class="form-check-input" type="checkbox" id="fMine"><span class="form-check-label">فقط مرتبط با آگهی‌های من</span></label>
  </form>
  <p class="rd-count"><b data-fa id="rdCount">{{ count($requests) }}</b> درخواست</p>

  <div class="rd-list" id="rdBuyer">
    @foreach($requests as $q)
      <article class="rd-card" data-id="{{ $q['id'] }}" data-cat="{{ $q['cat'] }}" data-city="{{ $q['city'] }}" data-hours="{{ $q['hours'] }}" data-budget="{{ $q['budget'] }}" data-match="{{ $q['mine']['percent'] ?? 0 }}" data-mine="{{ $q['mine'] ? 1 : 0 }}">
        <header class="rd-head">
          <span class="rd-cat"><x-icon :name="($icons ?? [])[$q['cat']] ?? 'building'" /></span>
          <div class="rd-ttl"><h3>{{ $q['title'] }}</h3><small>{{ $q['buyer'] }} • {{ $q['posted'] }}</small></div>
          @if($q['mine'])<x-radar-pct :value="$q['mine']['percent']" :label="true" />@endif
        </header>
        <div class="rd-chips">@foreach($q['chips'] as $ch)<span>{{ $ch }}</span>@endforeach</div>
        @if($q['mine'])
          <div class="rd-mine"><img src="{{ $q['mine']['image'] }}" alt="" loading="lazy"><div><small>نزدیک‌ترین آگهی شما</small><b>{{ $q['mine']['title'] }}</b><span><x-price :value="$q['mine']['price']" /></span></div></div>
          <footer class="rd-act">
            @if($q['offered'])<button type="button" class="btn btn-soft w-100" disabled><x-icon name="check" /> پیشنهاد ارسال شد</button>
            @else<button type="button" class="btn btn-primary w-100" data-offer data-url="{{ route('panel.radar.offer', $q['id']) }}" data-title="{{ $q['mine']['title'] }}">پیشنهاد آگهی به مشتری</button>@endif
          </footer>
        @else
          <p class="rd-none"><x-icon name="info" class="icon-sm" /> آگهی مطابقی ندارید. با ثبت آگهی‌ی مناسب، به مشتری اطلاع داده می‌شود.</p>
          <footer class="rd-act"><a href="{{ route('listings.create') }}" class="btn btn-soft w-100">ثبت آگهی مرتبط</a></footer>
        @endif
      </article>
    @endforeach
  </div>
  <div class="d-none" id="rdBuyerEmpty"><x-empty-state icon="search" title="درخواستی با این فیلتر نیست" text="فیلترها را کم کنید." /></div>
  <p class="rd-keep mt-3"><x-icon name="shield" class="icon-sm" /> هویت مشتری نمایش داده نمی‌شود؛ پیشنهاد شما به‌صورت اعلان برای او ارسال می‌شود و خودش با آژانس تماس می‌گیرد.</p>
@endif
@endsection
