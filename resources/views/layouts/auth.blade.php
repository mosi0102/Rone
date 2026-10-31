<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  @include('partials.head')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ @filemtime(public_path('css/auth.css')) ?: 1 }}">
  @stack('styles')
</head>
{{-- لایه‌ی مستقل صفحات ورود/ثبت‌نام/خوش‌آمدگویی: بدون هدر و ناوبری پایین، حس اپلیکیشن --}}
<body class="auth-body @yield('body_class')">
  @include('partials.icons')
  {{-- آیکن‌های مخصوص این جریان که در اسپرایت سایت نیست --}}
  <svg width="0" height="0" style="position:absolute" aria-hidden="true"><symbol id="i-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.2A10.6 10.6 0 0 1 12 4c6 0 10 8 10 8a17 17 0 0 1-3.2 4.2M6.6 6.6A16.5 16.5 0 0 0 2 12s4 8 10 8a10 10 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/><path d="m2 2 20 20"/></symbol></svg>
  <div class="net-banner" id="netBanner" role="alert">اتصال اینترنت قطع است</div>
  <main class="auth-main" id="main">@yield('content')</main>
  <div class="toast-stack" id="toastStack" aria-live="polite"></div>

  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/auth.js') }}?v={{ @filemtime(public_path('js/auth.js')) ?: 1 }}"></script>
  @stack('scripts')
</body>
</html>
