@extends('errors.layout')
@section('code', '404')
@section('icon', 'search')
@section('title', 'صفحه پیدا نشد')
@section('message', 'آدرس اشتباه است یا این صفحه دیگر وجود ندارد. می‌توانید در آگهی‌ها جست‌وجو کنید یا به صفحه‌ی اول بروید.')
@section('extra')
  <form class="err-search" action="{{ url('/listings') }}" role="search">
    <input type="search" name="q" class="form-control" placeholder="جست و جو در خودروها" aria-label="جست و جو در خودروها" enterkeyhint="search" autocomplete="off">
    <button type="submit" class="btn btn-primary" aria-label="جستجو"><x-icon name="search" /></button>
  </form>
@endsection
@section('actions')
  <a href="{{ url('/') }}" class="btn btn-primary">صفحه‌ی اول</a>
  <a href="{{ url('/listings') }}" class="btn btn-outline-primary">دیدن آگهی‌ها</a>
  <button type="button" class="btn btn-link" data-back>بازگشت</button>
@endsection
