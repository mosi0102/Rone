@extends('layouts.app')
@section('title', $car->title)
@section('page_title', 'اطلاعات خودرو')
@section('page_subtitle', $car->dealer['name'])
@section('back', route('listings.index'))
@section('body_class', 'no-bottom-nav has-sticky-cta')

@php
  $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
  $fa = fn ($n) => strtr(number_format((float) $n), $map + [',' => '٬']);
  // نمایشگاه‌دارِ صاحب آگهی «مدیریت» می‌بیند؛ بقیه (حتی نمایشگاه‌دار دیگر) مثل خریدار.
  $isOwner = $auth->dealer && $auth->dealer->id === $car->dealer['id'];
  $loginUrl = route('login', ['next' => url()->current()]);
@endphp

@section('content')
{{-- بنر نمایشگاه: مشخص می‌کند آگهی مال کدام نمایشگاه است --}}
<x-dealer-banner :dealer="$car->dealer" />
@if($isOwner)
  <div class="owner-banner"><x-icon name="store" class="icon-sm" /> این آگهی متعلق به نمایشگاه شماست؛ ابزار مدیریت را در پایین می‌بینید.</div>
@endif
<div class="cd-grid">
  <div class="cd-main">
    <div class="cd-gallery">
      <x-gallery :images="$car->images" :badge="$car->sale_type === 'instant' ? 'فروش فوری' : null" />
    </div>

    <div class="cd-specs">
      <x-section-head title="مشخصات خودرو" />
      <div class="spec-grid">
        @foreach($car->specs as $sp)
          <div class="spec-card"><x-icon :name="$sp['icon']" /><span>{{ $sp['label'] }}</span><b>{{ $sp['value'] }}</b></div>
        @endforeach
      </div>
    </div>

    @if(!empty($car->details))
      <div class="card-s kv-list cd-details">
        @foreach($car->details as $row)
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
      <x-section-head title="توضیحات نمایشگاه" />
      <p>{{ $car->description }}</p>
    </div>


    @unless($isOwner)<x-deposit-info class="cd-deposit" />@endunless
  </div>

  <aside class="cd-aside">
    <div class="cd-head">
      <h1 class="cd-title">{{ $car->title }}</h1>
      <p class="cd-sub">{{ $car->subtitle }}</p>

      <div class="price-box">
        <span>{{ $car->price_note }}</span>
        <b><x-price :value="$car->price" /></b>
      </div>

      <div class="cd-meta">
        <span>{{ $car->location }} • <span data-fa>{{ $car->views }}</span> بازدید</span>
        <span class="cd-meta-actions">
          <button type="button" class="btn-square" data-share data-title="{{ $car->title }}" aria-label="اشتراک‌گذاری"><x-icon name="share" /></button>
          <button type="button" class="btn-square {{ $car->favorited ? 'is-on' : '' }}" data-save data-id="{{ $car->id }}" aria-label="نشان کردن"><x-icon name="bookmark" /></button>
        </span>
      </div>

      @if($isOwner)
        <div class="owner-panel">
          <div class="op-head"><span class="badge-s badge-success">آگهی شما • فعال</span><span class="small text-muted-s">کد آگهی: <b data-fa>{{ $car->code }}</b></span></div>
          <div class="op-stats">
            <div><b data-fa>{{ $car->views }}</b><span>بازدید</span></div>
            <div><b data-fa>18</b><span>نشان‌شده</span></div>
            <div><b data-fa>2</b><span>بیعانه</span></div>
          </div>
          <div class="op-actions">
            <a href="{{ route('listings.edit', $car->id) }}" class="btn btn-primary"><x-icon name="pencil" class="icon-sm" /> ویرایش آگهی</a>
            <button type="button" class="btn btn-accent" data-bs-toggle="offcanvas" data-bs-target="#instantSheet"><x-icon name="bolt" class="icon-sm" /> فروش فوری</button>
            <a href="{{ route('auctions.create', ['listing' => $car->id]) }}" class="btn btn-outline-primary"><x-icon name="gavel" class="icon-sm" /> ساخت مزایده</a>
            <button type="button" class="btn btn-soft op-danger" data-bs-toggle="offcanvas" data-bs-target="#removeSheet"><x-icon name="trash" class="icon-sm" /> حذف / غیرفعال‌سازی</button>
          </div>
        </div>
      @elseif(!$auth->check)
        <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg w-100 cd-cta-desktop"><x-icon name="user" /> ورود برای پرداخت بیعانه</a>
      @else
        <a href="{{ route('listings.deposit', $car->id) }}" class="btn btn-primary btn-lg w-100 cd-cta-desktop"><x-icon name="shield" /> پرداخت بیعانه و ادامه فرایند</a>
        <a href="{{ route('chat.start', ['listing' => $car->id]) }}" class="btn btn-outline-primary btn-lg w-100 mt-2 cd-cta-desktop"><x-icon name="message" /> گفتگو با نمایشگاه</a>
      @endif
    </div>
  </aside>
</div>

{{-- نوار پایین موبایل --}}
<div class="sticky-cta cd-cta-mobile {{ $isOwner ? 'd-none' : '' }}">
  @if($isOwner)
    {{-- صاحب آگهی: ابزار مدیریت داخل خود صفحه است؛ نوار ثابت پایین لازم نیست --}}
  @elseif(!$auth->check)
    <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg flex-fill"><x-icon name="user" /> ورود برای پرداخت بیعانه</a>
  @else
    <a href="{{ route('chat.start', ['listing' => $car->id]) }}" class="btn btn-outline-primary btn-lg" aria-label="گفتگو با نمایشگاه"><x-icon name="message" /></a>
    <a href="{{ route('listings.deposit', $car->id) }}" class="btn btn-primary btn-lg flex-fill"><x-icon name="shield" /> پرداخت بیعانه و ادامه فرایند</a>
  @endif
</div>


@if($isOwner)
{{-- خرید فروش فوری --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="instantSheet" aria-labelledby="instantTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="instantTitle">فروش فوری</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p class="small text-muted-s mb-3">آگهی شما در بخش «فروش فوری» صفحه خانه و لیست آگهی‌ها با برچسب ویژه نمایش داده می‌شود.</p>
    <div class="plan-pick" id="instantPlans">
      <label class="plan"><input type="radio" name="inst" value="450000" data-days="3" checked><span><b>۳ روزه</b><small>مناسب فروش سریع</small></span><x-price :value="450000" /></label>
      <label class="plan"><input type="radio" name="inst" value="900000" data-days="7"><span><b>۷ روزه</b><small>پیشنهاد ما</small></span><x-price :value="900000" /></label>
      <label class="plan"><input type="radio" name="inst" value="1500000" data-days="14"><span><b>۱۴ روزه</b><small>بیشترین دیده‌شدن</small></span><x-price :value="1500000" /></label>
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
    <p class="small text-muted-s mb-1">اگر برای این آگهی بیعانه فعال وجود داشته باشد، حذف ممکن نیست.</p>
    <button type="button" class="btn btn-soft btn-lg" data-bs-dismiss="offcanvas" id="deactivateListing">غیرفعال‌سازی موقت (فروخته شد)</button>
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
  $('#payInstant').on('click', function () { var $b = $(this); App.loading($b); $(document).trigger('app:instant', [{{ $car->id }}, $('input[name=inst]:checked').val()]); setTimeout(function () { App.loading($b, false); App.sheet('instantSheet', 'hide'); App.toast('فروش فوری فعال شد (نمایشی)', 'success'); }, 1100); });
  $('#deactivateListing').on('click', function () { $(document).trigger('app:listing-deactivate', [{{ $car->id }}]); App.toast('آگهی غیرفعال شد (نمایشی)', 'info'); });
  $('#deleteListing').on('click', function () { $(document).trigger('app:listing-delete', [{{ $car->id }}]); App.toast('درخواست حذف ارسال شد (نمایشی)', 'warn'); App.sheet('removeSheet', 'hide'); });
</script>
@endpush
