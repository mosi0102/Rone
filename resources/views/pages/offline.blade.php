@extends('layouts.app')
@section('title', 'آفلاین')
@section('body_class', 'no-bottom-nav')
@section('content')
  <x-empty-state icon="alert" title="اتصال اینترنت برقرار نیست" text="اینترنت خود را بررسی کنید و دوباره تلاش کنید." />
  <div class="text-center"><button class="btn btn-primary" onclick="location.reload()">تلاش دوباره</button></div>
@endsection
