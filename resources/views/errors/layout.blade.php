{{--
  قالب مشترک صفحات خطا — مستقل از layouts.app (بدون composer/دیتابیس/نشست)، تا حتی وقتی خود برنامه خطا می‌دهد (۵۰۰) درست نمایش داده شود.
  استفاده: @extends('errors.layout') + @section('code') @section('icon') @section('title') @section('message') + (اختیاری) @section('actions') / @section('extra')
--}}
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="robots" content="noindex">
  <meta name="theme-color" content="#1b4d9e">
  <title>@yield('title') | {{ config('app.name') }}</title>
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
  <link rel="preload" href="{{ asset('fonts/Vazirmatn-Variable.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body class="err-body">
  @include('partials.icons')
  <main class="err" id="main">
    <div class="err-card">
      <a href="{{ url('/') }}" class="err-brand" aria-label="{{ config('app.name') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" width="84" height="44"></a>
      <div class="err-art" aria-hidden="true">
        <span class="err-code">@yield('code')</span>
        <span class="err-ic"><svg class="icon" aria-hidden="true"><use href="#i-@yield('icon', 'alert')"/></svg></span>
      </div>
      <h1>@yield('title')</h1>
      <p class="err-msg">@yield('message')</p>
      @yield('extra')
      <div class="err-actions">
        @hasSection('actions')@yield('actions')@else<a href="{{ url('/') }}" class="btn btn-primary">بازگشت به صفحه اول</a>@endif
      </div>
      <p class="err-help">اگر مشکل ادامه داشت، از بخش <a href="{{ url('/help') }}">راهنما و پشتیبانی</a> با ما در تماس باشید.</p>
    </div>
  </main>
  <script src="{{ asset('js/errors.js') }}" defer></script>
</body>
</html>
