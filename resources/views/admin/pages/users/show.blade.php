@extends('admin.layouts.app')
@section('title', $u['name'])

@push('scripts')<script src="{{ asset('admin-assets/js/people.js') }}"></script>@endpush

@section('content')
@php $st = $u['status']; @endphp

<x-admin.page-head :title="$u['name']" :subtitle="$u['city']">
  <a href="{{ route('admin.users.index') }}" class="btn btn-soft btn-sm"><x-admin.icon name="arrow-left" class="icon-sm" style="transform:scaleX(-1)" /> بازگشت به لیست</a>
  <span data-d="status"><x-admin.status :status="$st" /></span>
</x-admin.page-head>

<div class="dash" data-entity data-status="{{ $st }}">
  <div class="dash-main">
    <section class="row g-3" aria-label="آمار کاربر">
      <div class="col-6 rise" style="--d:1"><x-admin.kpi icon="wallet" tone="sky" label="موجودی کیف پول" :value="$u['wallet']" unit="تومان" :money="true" /></div>
      <div class="col-6 rise" style="--d:2"><x-admin.kpi icon="shield-check" tone="sage" label="بیعانه‌ی در امانت" :value="$u['held']" unit="تومان" :money="true" /></div>
      <div class="col-6 rise" style="--d:3"><x-admin.kpi icon="gavel" tone="peach" label="مزایده‌ی شرکت‌کرده" :value="$u['auctions']" /></div>
      <div class="col-6 rise" style="--d:4"><x-admin.kpi icon="target" tone="sand" label="درخواست خرید فعال" :value="$u['requests']" /></div>
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>دسترسی سریع</h2></div>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-soft btn-sm" href="{{ route('admin.deposits.index') }}"><x-admin.icon name="shield-check" class="icon-sm" /> بیعانه‌های کاربر</a>
        <a class="btn btn-soft btn-sm" href="{{ route('admin.requests.index') }}"><x-admin.icon name="target" class="icon-sm" /> درخواست‌های خرید</a>
        <a class="btn btn-soft btn-sm" href="{{ route('admin.chats.index') }}"><x-admin.icon name="message" class="icon-sm" /> گفتگوها</a>
        <a class="btn btn-soft btn-sm" href="{{ route('admin.finance.index') }}"><x-admin.icon name="receipt" class="icon-sm" /> تراکنش‌ها</a>
      </div>
    </section>
  </div>

  <aside class="dash-aside">
    <section class="card-a rise" style="--d:2">
      <div class="card-head"><h2>وضعیت حساب</h2></div>
      <div class="note-box bad mb-3" data-show="banned"><x-admin.icon name="lock" /><div><b>حساب مسدود است</b><p data-reason-note>{{ $u['reason'] }}</p></div></div>
      @if($u['auction_ban_days'])
        <div class="note-box warn mb-3"><x-admin.icon name="gavel" /><div><b>محروم از مزایده</b><p><span data-fa>{{ $u['auction_ban_days'] }}</span> روز دیگر؛ به‌دلیل عدم پرداخت بیعانه‌ی مزایده.</p></div></div>
        <button type="button" class="btn btn-soft w-100 mb-2" data-confirm="محرومیت این کاربر از مزایده برداشته شود؟" data-confirm-ok="رفع محرومیت" data-done="محرومیت از مزایده برداشته شد" id="unbanAuc"><x-admin.icon name="undo" class="icon-sm" /> رفع محرومیت از مزایده</button>
      @endif
      <button type="button" class="btn btn-bad w-100" data-show="active" data-set="banned" data-reason-modal="banUser" data-title="{{ $u['name'] }}" data-done="کاربر مسدود شد"><x-admin.icon name="lock" class="icon-sm" /> مسدود کردن کاربر</button>
      <button type="button" class="btn btn-ok w-100" data-show="banned" data-set="active" data-confirm="مسدودیت این کاربر برداشته شود؟" data-confirm-ok="رفع مسدودیت" data-done="مسدودیت کاربر برداشته شد"><x-admin.icon name="undo" class="icon-sm" /> رفع مسدودیت</button>
    </section>

    <section class="card-a rise" style="--d:3">
      <div class="card-head"><h2>مشخصات</h2></div>
      <dl class="info-list mb-0">
        <div><dt>موبایل</dt><dd class="tnum" data-fa>{{ $u['phone'] }}</dd></div>
        <div><dt>شهر</dt><dd>{{ $u['city'] }}</dd></div>
        <div><dt>عضویت</dt><dd><span data-jdate="{{ $u['created_at'] }}" data-jfmt="date"></span></dd></div>
      </dl>
    </section>
  </aside>
</div>

<x-admin.reason-modal id="banUser" title="مسدودسازی کاربر" submit="مسدود کردن" icon="lock" label="دلیل مسدودسازی (برای کاربر ارسال می‌شود)"
  :chips="['انتشار اطلاعات تماس در گفتگو', 'کلاهبرداری یا تلاش برای آن', 'تخلف مکرر از قوانین']" />
@endsection
