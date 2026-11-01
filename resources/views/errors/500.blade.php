@extends('errors.layout')
@section('code', '500')
@section('icon', 'alert')
@section('title', 'مشکلی از سمت ما پیش آمد')
@section('message', 'خطایی غیرمنتظره رخ داد و ثبت شد. لطفاً چند دقیقه‌ی دیگر دوباره تلاش کنید.')
@section('actions')
  <button type="button" class="btn btn-primary" data-reload>تلاش دوباره</button>
  <a href="{{ url('/') }}" class="btn btn-outline-primary">صفحه‌ی اول</a>
@endsection
