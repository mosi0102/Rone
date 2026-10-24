@extends('layouts.app')
@php $all = collect($deals); $held = $all->where('status', 'held')->count(); @endphp
@section('title', 'معاملات من')
@section('page_title', 'معاملات من')
@section('page_subtitle', 'بیعانه‌ها و وضعیت معاملات شما')

@push('styles')<link rel="stylesheet" href="{{ asset('css/deal.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/deal.js') }}"></script>@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="user" title="برای دیدن معاملات وارد شوید" action="ورود / ثبت‌نام" :href="route('login', ['next' => url()->current()])" />
@else
  @if($held)
    <section class="note-card mt-0 mb-3">
      <span class="nc-ic"><x-icon name="bell-ring" /></span>
      <div><h3><span data-fa>{{ $held }}</span> معامله منتظر تعیین وضعیت شماست</h3><p>پس از دیدار با نمایشگاه، «انجام شد» یا «انجام نشد» را ثبت کنید تا تکلیف بیعانه مشخص شود.</p></div>
    </section>
  @endif

  <div class="seg-tabs" id="dealTabs" role="tablist">
    <button type="button" class="on" data-status="all" role="tab">همه <span data-fa>{{ $all->count() }}</span></button>
    <button type="button" data-status="held" role="tab">در انتظار <span data-fa>{{ $held }}</span></button>
    <button type="button" data-status="done" role="tab">نتیجه‌دار <span data-fa>{{ $all->whereIn('status', ['completed', 'refunded', 'review'])->count() }}</span></button>
  </div>

  <div class="deal-list" id="dealList">
    @foreach($deals as $deal)<x-deal-row :deal="$deal" />@endforeach
  </div>
  <div class="d-none" id="dealEmpty"><x-empty-state icon="shield" title="معامله‌ای در این دسته نیست" text="بیعانه‌ای که پرداخت کنید اینجا نمایش داده می‌شود." /></div>
  @if($all->isEmpty())<x-empty-state icon="shield" title="هنوز بیعانه‌ای پرداخت نکرده‌اید" text="آگهی را انتخاب کنید و با بیعانهٔ امانی خودرو را رزرو کنید." action="مشاهده آگهی‌ها" :href="route('listings.index')" />@endif
@endif
@endsection
