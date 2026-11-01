@extends('errors.layout')
@section('code', '503')
@section('icon', 'shield')
@section('title', 'در حال به‌روزرسانی هستیم')
@section('message', 'سرویس برای مدتی کوتاه در دسترس نیست. به‌زودی برمی‌گردیم؛ لطفاً کمی بعد سر بزنید.')
@section('actions')
  <button type="button" class="btn btn-primary" data-reload>بررسی دوباره</button>
@endsection
