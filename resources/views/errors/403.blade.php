@extends('errors.layout')
@section('code', '403')
@section('icon', 'lock')
@section('title', 'دسترسی ندارید')
@section('message', 'برای دیدن این صفحه مجوز لازم را ندارید. اگر وارد حساب دیگری شده‌اید، خارج و دوباره وارد شوید.')
@section('actions')
  <a href="{{ url('/login') }}" class="btn btn-primary">ورود به حساب</a>
  <a href="{{ url('/') }}" class="btn btn-outline-primary">صفحه‌ی اول</a>
@endsection
