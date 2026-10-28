@extends('admin.layouts.app')
@section('title', $title)
@section('content')
  <x-admin.page-head :title="$title" />
  <div class="card-a rise">
    <x-admin.empty icon="sparkles" title="این بخش به‌زودی ساخته می‌شود" :text="'صفحه‌ی «' . $title . '» در مرحله‌ی بعد اضافه می‌شود.'" />
  </div>
@endsection
