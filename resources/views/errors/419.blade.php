@extends('errors.layout')
@section('code', '419')
@section('icon', 'clock')
@section('title', 'نشست شما منقضی شده')
@section('message', 'مدتی بی‌فعالیت بودید و امنیت حساب شما حفظ شد. صفحه را دوباره بارگذاری کنید و عملیات را تکرار کنید.')
@section('actions')
  <button type="button" class="btn btn-primary" data-reload>بارگذاری دوباره</button>
  <a href="{{ url('/') }}" class="btn btn-outline-primary">صفحه‌ی اول</a>
@endsection
