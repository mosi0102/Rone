<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  @include('partials.head')
  @stack('styles')
</head>
{{-- برای صفحاتی که ناوبری پایین نمی‌خواهند: @section('body_class', 'no-bottom-nav') --}}
<body class="@yield('body_class')">
  @include('partials.icons')
  <div class="net-banner" id="netBanner" role="alert">اتصال اینترنت قطع است</div>

  @include('partials.desk-header')
  @hasSection('no_topbar')@else @include('partials.topbar') @endif

  <main class="app-main" id="main">
    <div class="page-container">
      @yield('content')
    </div>
  </main>

  @include('partials.footer')
  @include('partials.bottom-nav')
  @include('partials.more-sheet')
  @include('partials.install-banner')
  <div class="toast-stack" id="toastStack" aria-live="polite"></div>

  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  {{-- پیام‌های فلش بک‌اند به توست تبدیل می‌شوند: session('success'), session('error') --}}
  @if(session('success'))<script>App.toast(@json(session('success')), 'success');</script>@endif
  @if(session('error'))<script>App.toast(@json(session('error')), 'error');</script>@endif
  @stack('scripts')
</body>
</html>
