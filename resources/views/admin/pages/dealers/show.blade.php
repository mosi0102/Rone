@extends('admin.layouts.app')
@section('title', $d['name'])

@push('scripts')<script src="{{ asset('admin-assets/js/people.js') }}"></script>@endpush

@section('content')
@php $st = $d['status']; @endphp

<x-admin.page-head :title="$d['name']" :subtitle="$d['owner'] . ' • ' . $d['city']">
  <a href="{{ route('admin.dealers.index') }}" class="btn btn-soft btn-sm"><x-admin.icon name="arrow-left" class="icon-sm" style="transform:scaleX(-1)" /> بازگشت به لیست</a>
  <span data-d="status"><x-admin.status :status="$st" /></span>
</x-admin.page-head>

<div class="dash" data-entity data-status="{{ $st }}">
  <div class="dash-main">
    <section class="row g-3" aria-label="آمار نمایشگاه">
      <div class="col-6 rise" style="--d:1"><x-admin.kpi icon="list-checks" tone="sky" label="آگهی فعال" :value="$d['ads']" /></div>
      <div class="col-6 rise" style="--d:2"><x-admin.kpi icon="receipt" tone="sage" label="فروش موفق" :value="$d['sales']" /></div>
      <div class="col-6 rise" style="--d:3"><x-admin.kpi icon="wallet" tone="peach" label="موجودی کیف پول" :value="$d['wallet']" unit="تومان" :money="true" /></div>
      <div class="col-6 rise" style="--d:4"><x-admin.kpi icon="clock" tone="sand" label="اشتراک باقی‌مانده" :value="(int) $d['sub_days']" unit="روز" /></div>
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>مدارک احراز هویت</h2></div>
      <div class="row g-3">
        <div class="col-sm-6"><small class="text-muted d-block mb-1">مجوز کسب</small><a href="{{ $d['license'] }}" target="_blank" rel="noopener" class="doc-img" style="background-image:url('{{ $d['license'] }}')" aria-label="مشاهده‌ی مجوز"></a></div>
        <div class="col-sm-6"><small class="text-muted d-block mb-1">کارت ملی مدیر</small><a href="{{ $d['id_card'] }}" target="_blank" rel="noopener" class="doc-img" style="background-image:url('{{ $d['id_card'] }}')" aria-label="مشاهده‌ی کارت ملی"></a></div>
      </div>
    </section>
  </div>

  <aside class="dash-aside">
    <section class="card-a rise" style="--d:2">
      <div class="card-head"><h2>اقدام</h2></div>
      <div class="note-box bad mb-3" data-show="rejected suspended"><x-admin.icon name="alert" /><div><b>دلیل وضعیت فعلی</b><p data-reason-note>{{ $d['reason'] }}</p></div></div>
      <div class="note-box warn mb-3" data-show="pending"><x-admin.icon name="clock" /><div><b>در انتظار بررسی مدارک</b><p>پس از تأیید، نمایشگاه می‌تواند اشتراک بخرد و آگهی ثبت کند.</p></div></div>
      <div class="d-grid gap-2">
        <button type="button" class="btn btn-ok" data-show="pending rejected" data-set="verified" data-confirm="هویت این نمایشگاه تأیید شود؟" data-confirm-ok="تأیید نمایشگاه" data-done="نمایشگاه تأیید شد"><x-admin.icon name="check" class="icon-sm" /> تأیید نمایشگاه</button>
        <button type="button" class="btn btn-bad" data-show="pending" data-set="rejected" data-reason-modal="rejectDealer" data-title="{{ $d['name'] }}" data-done="درخواست رد شد"><x-admin.icon name="x" class="icon-sm" /> رد درخواست</button>
        <button type="button" class="btn btn-bad" data-show="verified" data-set="suspended" data-reason-modal="suspendDealer" data-title="{{ $d['name'] }}" data-done="نمایشگاه تعلیق شد"><x-admin.icon name="lock" class="icon-sm" /> تعلیق نمایشگاه</button>
        <button type="button" class="btn btn-ok" data-show="suspended" data-set="verified" data-confirm="تعلیق این نمایشگاه برداشته شود؟" data-confirm-ok="رفع تعلیق" data-done="نمایشگاه دوباره فعال شد"><x-admin.icon name="undo" class="icon-sm" /> رفع تعلیق</button>
      </div>
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>مشخصات</h2></div>
      <dl class="info-list mb-0">
        <div><dt>مدیر</dt><dd>{{ $d['owner'] }}</dd></div>
        <div><dt>موبایل</dt><dd class="tnum" data-fa>{{ $d['phone'] }}</dd></div>
        <div><dt>کد ملی</dt><dd class="tnum" data-fa>{{ $d['national_id'] }}</dd></div>
        <div><dt>شهر</dt><dd>{{ $d['city'] }}</dd></div>
        <div><dt>نشانی</dt><dd>{{ $d['address'] }}</dd></div>
        <div><dt>ثبت‌نام</dt><dd><span data-jdate="{{ $d['created_at'] }}" data-jfmt="date"></span></dd></div>
      </dl>
    </section>
  </aside>
</div>

<x-admin.reason-modal id="rejectDealer" title="رد احراز هویت" submit="رد درخواست" label="دلیل رد (برای نمایشگاه‌دار ارسال می‌شود)"
  :chips="['تصویر مجوز ناخوانا است', 'مشخصات با مدارک مغایرت دارد', 'مدارک ناقص است']" />
<x-admin.reason-modal id="suspendDealer" title="تعلیق نمایشگاه" submit="تعلیق نمایشگاه" icon="lock" label="دلیل تعلیق (برای نمایشگاه‌دار ارسال می‌شود)"
  :chips="['شکایت مکرر خریداران', 'تخلف از قوانین سایت', 'اطلاعات نادرست در آگهی‌ها']" />
@endsection
