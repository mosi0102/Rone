@extends('layouts.app')
@section('title', $title)
@section('page_title', $title)
@section('back', route('home'))
@section('content')
  <x-empty-state icon="clock" :title="$title . ' — به‌زودی'" text="این صفحه در مرحله بعد ساخته می‌شود." action="بازگشت به خانه" :href="route('home')" />
@endsection
