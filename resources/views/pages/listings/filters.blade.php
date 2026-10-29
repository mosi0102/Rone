@extends('layouts.app')
@section('title', 'فیلترها')
@section('page_title', 'فیلترها')
@section('page_subtitle', 'جست‌وجوی خود را دقیق‌تر کنید.')
@section('back', $back)
@section('body_class', 'no-bottom-nav has-sticky-cta')

@push('styles')<link rel="stylesheet" href="{{ asset('css/listings.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/filters.js') }}"></script>@endpush

@section('content')
<div class="ff-page">
  <x-filter-form :f="$f" :lf="$lf" :count="$count" :action="route(isset($f['q']) ? 'search' : 'listings.index')" />
</div>
@endsection
