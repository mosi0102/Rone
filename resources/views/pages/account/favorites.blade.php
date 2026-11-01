@extends('layouts.app')
@section('title', 'نشان‌شده‌ها')
@section('page_title', 'نشان‌شده‌ها')
@section('page_subtitle', 'آگهی‌هایی که ذخیره کرده‌اید')
@section('back', route('account'))

@push('scripts')<script src="{{ asset('js/favorites.js') }}"></script>@endpush

@section('content')
@if(!$auth->check)
  <x-empty-state icon="bookmark" title="وارد حساب خود شوید" text="برای ذخیره و دیدن آگهی‌های مورد علاقه ابتدا وارد شوید." action="ورود / ثبت‌نام" :href="route('login')" />
@else
  <p class="auc-sum"><span data-fa id="favCount">{{ count($items) }}</span> آگهی نشان‌شده</p>
  <div class="listing-grid" id="favGrid">
    @foreach($items as $it)<x-listing-card :item="$it" />@endforeach
  </div>
  <div class="{{ count($items) ? 'd-none' : '' }}" id="favEmpty"><x-empty-state icon="bookmark" title="هنوز آگهی‌ای نشان نکرده‌اید" text="روی نشان قلب/نشان آگهی‌ها بزنید تا اینجا جمع شوند." action="دیدن آگهی‌ها" :href="route('listings.index')" /></div>
@endif
@endsection
