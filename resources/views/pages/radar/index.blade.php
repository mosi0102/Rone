@extends('layouts.app')
@section('title', 'رادار خودرو')
@section('page_title', 'رادار خودرو')
@section('page_subtitle', 'تا وقتی حذف نکنید، برایتان پیدا می‌کند')
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/deal.css') }}"><link rel="stylesheet" href="{{ asset('css/radar.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/radar.js') }}"></script>@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="sparkles" title="برای استفاده از رادار وارد شوید" text="خودروی دلخواه را ثبت کنید؛ هر آگهی تازه‌ای که شبیهش باشد، با درصد تطابق به شما اطلاع می‌دهیم." action="ورود / ثبت‌نام" :href="route('login', ['next' => url()->current()])" />
@else
  <section class="rd-hero">
    <span class="rd-hero-ic"><x-icon name="sparkles" /></span>
    <div>
      <h2>بگویید چه می‌خواهید</h2>
      <p>مشخصات خودروی دلخواه را ثبت کنید. مشابه‌ها و نزدیک‌ترین‌ها را با درصد تطابق نشان می‌دهیم و اگر آگهی مناسبی بعداً ثبت شود، خبرتان می‌کنیم.</p>
    </div>
    <a href="{{ route('radar.create') }}" class="btn btn-accent btn-lg"><x-icon name="plus" /> ثبت رادار جدید</a>
  </section>

  @if($newTotal)
    <section class="note-card mt-3 mb-0">
      <span class="nc-ic"><x-icon name="bell-ring" /></span>
      <div><h3><span data-fa>{{ $newTotal }}</span> آگهی تازه با رادار شما مطابقت دارد</h3><p>روی «مشاهده» بزنید تا موارد جدید را ببینید.</p></div>
    </section>
  @endif

  <div class="rd-list" id="rdList">
    @foreach($cards as $c)
      @php $r = $c['r']; $off = $r['status'] !== 'active'; @endphp
      <article class="rd-card {{ $off ? 'is-off' : '' }}" data-radar="{{ $r['id'] }}" data-delete-url="{{ route('radar.destroy', $r['id']) }}">
        <header class="rd-head">
          <span class="rd-cat"><x-icon :name="['car' => 'car', 'heavy' => 'truck', 'bike' => 'bike', 'bicycle' => 'bicycle'][$r['category']] ?? 'car'" /></span>
          <div class="rd-ttl"><h3>{{ $c['title'] }}</h3><small>ثبت شده {{ $r['created'] }}</small></div>
          @if($off)<span class="badge-s badge-warn">متوقف توسط مدیریت</span>@else<span class="badge-s badge-success"><span class="rd-pulse" aria-hidden="true"></span>فعال</span>@endif
        </header>
        <div class="rd-chips">@foreach($c['chips'] as $ch)<span>{{ $ch }}</span>@endforeach</div>

        @if($c['count'])
          <div class="rd-found">
            <div><b data-fa>{{ $c['count'] }}</b> مورد مشابه @if($c['new'])<span class="rd-new"><span data-fa>{{ $c['new'] }}</span> جدید</span>@endif</div>
            <div class="rd-best">بهترین تطابق <x-radar-pct :value="$c['best']" /></div>
          </div>
          <div class="rd-thumbs">
            @foreach($c['top'] as $t)
              <a href="{{ $t['url'] }}" class="rd-thumb"><img src="{{ $t['image'] }}" alt="" loading="lazy"><x-radar-pct :value="$t['percent']" /><span>{{ $t['title'] }}</span></a>
            @endforeach
          </div>
        @else
          <p class="rd-none"><x-icon name="clock" class="icon-sm" /> هنوز مورد مشابهی نیست. رادار فعال می‌ماند و به‌محض ثبت آگهی مناسب خبرتان می‌کنیم.</p>
        @endif

        <footer class="rd-act">
          <a href="{{ route('radar.show', $r['id']) }}" class="btn btn-primary">مشاهده @if($c['count'])<span data-fa>{{ $c['count'] }}</span> مورد@endif</a>
          <button type="button" class="btn btn-soft rd-del" data-del>حذف</button>
        </footer>
      </article>
    @endforeach
  </div>
  <div class="{{ count($cards) ? 'd-none' : '' }}" id="rdEmpty"><x-empty-state icon="sparkles" title="هنوز رادار فعالی ندارید" text="خودروی دلخواه را ثبت کنید تا برایتان پیدا کنیم." action="ثبت رادار جدید" :href="route('radar.create')" /></div>
@endif

{{-- تأیید حذف --}}
<div class="modal fade" id="rdConfirm" tabindex="-1" aria-labelledby="rdConfirmT" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-3">
    <h5 id="rdConfirmT" class="mb-2">حذف این رادار؟</h5>
    <p class="text-muted-s mb-3">بعد از حذف، دیگر برای این مشخصات آگهی پیدا نمی‌کنیم و اعلانی نمی‌گیرید.</p>
    <div class="d-flex gap-2"><button type="button" class="btn btn-soft flex-fill" data-bs-dismiss="modal">انصراف</button><button type="button" class="btn btn-danger flex-fill" id="rdConfirmYes">حذف رادار</button></div>
  </div></div>
</div>
@endsection
