@extends('layouts.app')
@section('title', $property->title)
@section('own_h1', 1)
@section('page_title', 'اطلاعات ملک')
@section('page_subtitle', $property->agency['name'])
@section('back', route('listings.index'))
@section('body_class', 'no-bottom-nav has-sticky-cta')
<x-map-assets />

@php
  $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
  $fa = fn ($n) => strtr(number_format((float) $n), $map + [',' => '٬']);
  // مشاور املاکِ صاحب آگهی «مدیریت» می‌بیند؛ بقیه (حتی مشاور املاک دیگر) مثل مشتری.
  $isOwner = $auth->agency && $auth->agency->id === $property->agency['id'];
  $loginUrl = route('login', ['next' => url()->current()]);
@endphp

@section('content')
{{-- بنر آژانس: مشخص می‌کند آگهی مال کدام آژانس است --}}
<x-agency-banner :agency="$property->agency" />
@if($isOwner)
  <div class="owner-banner"><x-icon name="store" class="icon-sm" /> این آگهی متعلق به آژانس شماست؛ ابزار مدیریت را در پایین می‌بینید.</div>
@endif
<div class="cd-grid">
  <div class="cd-main">
    <div class="cd-gallery">
      <x-gallery :images="$property->images" :badge="$property->sale_type === 'instant' ? 'فوری' : null" />
    </div>

    <div class="cd-specs">
      <x-section-head title="مشخصات ملک" />
      <div class="spec-grid">
        @foreach($property->specs as $sp)
          <div class="spec-card"><x-icon :name="$sp['icon']" /><span>{{ $sp['label'] }}</span><b>{{ $sp['value'] }}</b></div>
        @endforeach
      </div>
    </div>

    @if(!empty($property->details))
      <div class="card-s kv-list cd-details">
        @foreach($property->details as $row)
          <div class="kv">
            <span class="kv-k">{{ $row['label'] }}</span>
            <span class="kv-v {{ $row['status'] ?? '' }}">
              {{ $row['value'] }}
              @if(($row['status'] ?? null) === 'ok')<x-icon name="check-circle" class="icon-sm" />@endif
              @if(($row['status'] ?? null) === 'bad')<x-icon name="alert" class="icon-sm" />@endif
            </span>
          </div>
        @endforeach
      </div>
    @endif

    <div class="cd-desc">
      <x-section-head title="توضیحات آژانس" />
      <p>{{ $property->description }}</p>
    </div>

    {{-- موقعیت و آدرس --}}
    @if(!empty($property->lat))
      <section class="cd-map" aria-labelledby="mapTitle">
        <x-section-head title="موقعیت روی نقشه" :href="route('listings.map', ['category' => $property->category, 'city' => $property->city])" more="آگهی‌های اطراف" />
        <div class="cd-map-canvas" data-map-show data-lat="{{ $property->lat }}" data-lng="{{ $property->lng }}" role="img" aria-label="موقعیت ملک روی نقشه"></div>
        <p class="cd-addr"><x-icon name="pin" class="icon-sm" /><span>{{ $property->address }}</span></p>
        <div class="cd-route">
          <a class="btn btn-soft btn-sm" target="_blank" rel="noopener" href="https://nshn.ir/?lat={{ $property->lat }}&lng={{ $property->lng }}"><x-icon name="navigation" class="icon-sm" /> مسیریابی با نشان</a>
          <a class="btn btn-soft btn-sm" target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $property->lat }},{{ $property->lng }}"><x-icon name="navigation" class="icon-sm" /> گوگل مپ</a>
          <a class="btn btn-soft btn-sm" target="_blank" rel="noopener" href="https://waze.com/ul?ll={{ $property->lat }},{{ $property->lng }}&navigate=yes"><x-icon name="navigation" class="icon-sm" /> ویز</a>
        </div>
      </section>
    @endif


    @unless($isOwner)
      <div class="note-card cd-deposit"><span class="nc-ic"><x-icon name="shield" /></span><div><h3>معامله فقط در آژانس</h3><p>سایت هیچ مبلغی بابت ملک دریافت نمی‌کند. پیش از هر پرداختی، ملک را بازدید و سند را استعلام کنید و قرارداد را در آژانس با کد رهگیری ببندید.</p></div></div>
    @endunless
  </div>

  <aside class="cd-aside">
    <div class="cd-head">
      <h1 class="cd-title">{{ $property->title }}</h1>
      <p class="cd-sub">{{ $property->subtitle }}</p>

      <div class="price-box">
        <span>{{ $property->price_note }}</span>
        <b><x-listing-price :item="$property" /></b>
        @if(!empty($property->price_per_meter))<small class="d-block text-muted-s mt-1">هر متر: <x-price :value="$property->price_per_meter" /></small>@endif
      </div>

      <div class="cd-meta">
        <span>{{ $property->location }} • <span data-fa>{{ $property->views }}</span> بازدید</span>
        <span class="cd-meta-actions">
          <button type="button" class="btn-square" data-share data-title="{{ $property->title }}" aria-label="اشتراک‌گذاری"><x-icon name="share" /></button>
          <button type="button" class="btn-square {{ $property->favorited ? 'is-on' : '' }}" data-save data-id="{{ $property->id }}" aria-label="نشان کردن"><x-icon name="bookmark" /></button>
        </span>
      </div>

      @if($isOwner)
        <div class="owner-panel">
          <div class="op-head"><span class="badge-s badge-success">آگهی شما • فعال</span><span class="small text-muted-s">کد آگهی: <b data-fa>{{ $property->code }}</b></span></div>
          <div class="op-stats">
            <div><b data-fa>{{ $property->views }}</b><span>بازدید</span></div>
            <div><b data-fa>18</b><span>نشان‌شده</span></div>
            <div><b data-fa>5</b><span>تماس</span></div>
          </div>
          <div class="op-actions">
            <a href="{{ route('listings.edit', $property->id) }}" class="btn btn-primary"><x-icon name="pencil" class="icon-sm" /> ویرایش آگهی</a>
            <button type="button" class="btn btn-accent" data-bs-toggle="offcanvas" data-bs-target="#instantSheet"><x-icon name="bolt" class="icon-sm" /> آگهی فوری</button>
            @if(in_array($property->category, ['sale_res', 'sale_com', 'project'], true))<a href="{{ route('auctions.create', ['listing' => $property->id]) }}" class="btn btn-outline-primary"><x-icon name="gavel" class="icon-sm" /> ساخت مزایده</a>@endif
            <button type="button" class="btn btn-soft op-danger" data-bs-toggle="offcanvas" data-bs-target="#removeSheet"><x-icon name="trash" class="icon-sm" /> حذف / غیرفعال‌سازی</button>
          </div>
        </div>
      @elseif(!$auth->check)
        <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg w-100 cd-cta-desktop"><x-icon name="user" /> ورود برای دیدن اطلاعات تماس</a>
      @else
        <a href="tel:{{ $property->agency['phone'] }}" class="btn btn-primary btn-lg w-100 cd-cta-desktop"><x-icon name="phone" /> تماس با آژانس: <span class="ltr d-inline-block">{{ $property->agency['phone'] }}</span></a>
        <a href="{{ route('chat.start', ['listing' => $property->id]) }}" class="btn btn-outline-primary btn-lg w-100 mt-2 cd-cta-desktop"><x-icon name="message" /> گفتگو با آژانس</a>
      @endif
    </div>
  </aside>
</div>

{{-- نوار پایین موبایل --}}
<div class="sticky-cta cd-cta-mobile {{ $isOwner ? 'd-none' : '' }}">
  @if($isOwner)
    {{-- صاحب آگهی: ابزار مدیریت داخل خود صفحه است؛ نوار ثابت پایین لازم نیست --}}
  @elseif(!$auth->check)
    <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg flex-fill"><x-icon name="user" /> ورود برای دیدن اطلاعات تماس</a>
  @else
    <a href="{{ route('chat.start', ['listing' => $property->id]) }}" class="btn btn-outline-primary btn-lg" aria-label="گفتگو با آژانس"><x-icon name="message" /></a>
    <a href="tel:{{ $property->agency['phone'] }}" class="btn btn-primary btn-lg flex-fill"><x-icon name="phone" /> تماس با آژانس</a>
  @endif
</div>


@if($isOwner)
{{-- خرید آگهی فوری --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="instantSheet" aria-labelledby="instantTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="instantTitle">آگهی فوری</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p class="small text-muted-s mb-3">به مدت ۲۴ ساعت، آگهی شما در بخش «آگهی‌های فوری» صفحه خانه و لیست آگهی‌ها با برچسب ویژه نمایش داده می‌شود.</p>
    <div class="plan-pick" id="instantPlans">
      <label class="plan"><input type="radio" name="inst" value="450000" data-hours="24" checked><span><b>۲۴ ساعت</b><small>نمایش ویژه در صفحه خانه و بالای نتایج</small></span><x-price :value="450000" /></label>
    </div>
    <div class="pay-row wallet mt-3 rounded-3"><b><x-icon name="wallet" class="icon-sm" /> موجودی کیف پول</b><x-price :value="$auth->wallet" /></div>
    <button type="button" class="btn btn-accent btn-lg w-100 mt-3" id="payInstant">پرداخت و فعال‌سازی</button>
  </div>
</div>

{{-- حذف / غیرفعال‌سازی --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="removeSheet" aria-labelledby="removeTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="removeTitle">حذف یا غیرفعال‌سازی آگهی</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0 d-grid gap-2">
    <p class="small text-muted-s mb-1">اگر برای این آگهی مزایده‌ی فعال وجود داشته باشد، حذف ممکن نیست.</p>
    <button type="button" class="btn btn-soft btn-lg" data-bs-dismiss="offcanvas" id="deactivateListing">غیرفعال‌سازی موقت (معامله شد)</button>
    <button type="button" class="btn btn-danger btn-lg" id="deleteListing">حذف دائمی آگهی</button>
  </div>
</div>
@endif

@if(!empty($similar))
  <x-section-head title="آگهی‌های مشابه" :href="route('listings.index')" />
  <div class="lr-list">@foreach($similar as $item)<x-listing-row :item="$item" />@endforeach</div>
@endif
@endsection

@push('scripts')
<script>
  $('#payInstant').on('click', function () { var $b = $(this); App.loading($b); $(document).trigger('app:instant', [{{ $property->id }}, $('input[name=inst]:checked').val()]); setTimeout(function () { App.loading($b, false); App.sheet('instantSheet', 'hide'); App.toast('آگهی فوری برای ۲۴ ساعت فعال شد (نمایشی)', 'success'); }, 1100); });
  $('#deactivateListing').on('click', function () { $(document).trigger('app:listing-deactivate', [{{ $property->id }}]); App.toast('آگهی غیرفعال شد (نمایشی)', 'info'); });
  $('#deleteListing').on('click', function () { $(document).trigger('app:listing-delete', [{{ $property->id }}]); App.toast('درخواست حذف ارسال شد (نمایشی)', 'warn'); App.sheet('removeSheet', 'hide'); });
</script>
@endpush
