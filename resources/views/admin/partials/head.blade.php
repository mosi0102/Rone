<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'پنل مدیریت') | {{ config('app.name') }}</title>
<meta name="theme-color" content="#f6f2ea">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.rtl.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-assets/css/admin.css') }}">
{{-- جلوگیری از پرش چیدمان: وضعیت سایدبار قبل از رندر اعمال می‌شود --}}
<script>try { if (localStorage.getItem('adm-sb') === '1') document.documentElement.classList.add('sb-collapsed'); } catch (e) {}</script>
