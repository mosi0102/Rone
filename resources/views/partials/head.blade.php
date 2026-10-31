<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', config('app.name'))@hasSection('title') | {{ config('app.name') }}@endif</title>
<meta name="description" content="@yield('description', 'خرید و فروش خودرو سواری، سنگین، موتور و دوچرخه با رزرو آنلاین')">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- PWA --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#1b4d9e">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">

<link rel="preload" href="{{ asset('fonts/Vazirmatn-Variable.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.rtl.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/blocks.css') }}">
{{-- اولین ورود: صفحه‌ی اول → اسپلش (فلگ در localStorage؛ با ?as=… در حالت نمایشی رد می‌شود) --}}
@if(request()->routeIs('home'))
<script>try{if(!localStorage.getItem('rone_welcome')&&!/[?&]as=/.test(location.search))location.replace('{{ route('welcome') }}')}catch(e){}</script>
@endif
