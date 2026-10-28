<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  @include('admin.partials.head')
  @stack('styles')
</head>
<body class="auth-body">
  @include('admin.partials.icons')
  <main class="auth-page" id="main">@yield('content')</main>
  <div class="toast-stack" id="toastStack" aria-live="polite"></div>
  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('admin-assets/js/admin.js') }}"></script>
  @stack('scripts')
</body>
</html>
