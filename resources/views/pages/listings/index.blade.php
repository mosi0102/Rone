@extends('layouts.app')
@php
  $isSearch = $mode === 'search';
  $fa = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']);
  $myDealer = $auth->is_dealer ? $auth->dealer->id : null;     // نمایشگاه‌دار: دکمه ویرایش روی آگهی‌های خودش
  $popular = ['پژو ۲۰۷', 'ولوو FH500', 'یاماها NMAX', 'سمند', 'تیبا', 'کامیون کشنده', 'هیوندای النترا'];   // جست‌وجوهای پرطرفدار (از بک‌اند)
@endphp
@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', $subtitle)
@section('back', route('home'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/search.css') }}"><link rel="stylesheet" href="{{ asset('css/listings.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/filters.js') }}"></script><script src="{{ asset('js/listing-browser.js') }}"></script><script src="{{ asset('js/listings.js') }}"></script>@endpush

@section('content')
@include('pages.listings._browser')
@endsection
