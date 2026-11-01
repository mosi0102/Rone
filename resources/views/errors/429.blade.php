@extends('errors.layout')
@section('code', '429')
@section('icon', 'clock')
@section('title', 'درخواست‌های زیاد')
@section('message', 'در مدت کوتاه تعداد درخواست‌های شما از حد مجاز گذشت. چند لحظه صبر کنید و دوباره تلاش کنید.')
@section('actions')
  <button type="button" class="btn btn-primary" data-reload>تلاش دوباره</button>
  <a href="{{ url('/') }}" class="btn btn-outline-primary">صفحه‌ی اول</a>
@endsection
