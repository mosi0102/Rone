<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  @include('admin.partials.head')
  @stack('styles')
</head>
<body class="@yield('body_class')">
  <a class="skip-link" href="#main">پرش به محتوا</a>
  @include('admin.partials.icons')
  <div class="adm-shell">
    @include('admin.partials.sidebar')
    <div class="adm-main">
      @include('admin.partials.topbar')
      <main class="adm-content" id="main">
        @yield('content')
      </main>
    </div>
  </div>
  <div class="adm-overlay" data-sb-toggle aria-hidden="true"></div>

  {{-- مودال تأیید عمومی: Admin.confirm() یا data-confirm --}}
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-body confirm">
          <div class="c-ic" data-c="icon"><svg class="icon icon-lg"><use href="#a-help"/></svg></div>
          <h3 data-c="title"></h3><p data-c="text"></p>
        </div>
        <div class="modal-footer confirm-foot">
          <button type="button" class="btn btn-soft" data-bs-dismiss="modal" data-c="cancel">انصراف</button>
          <button type="button" class="btn btn-ink" data-c="ok">تأیید</button>
        </div>
      </div>
    </div>
  </div>
  {{-- پنجره‌ی دلیل: Admin.reason() یا data-reason --}}
  <div class="modal fade reason-modal" id="reasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <h3 data-r="title"></h3><p class="sub" data-r="text"></p>
          <label class="form-label" for="rsReason">دلیل</label>
          <select class="form-select mb-3" id="rsReason" data-r="reason"></select>
          <label class="form-label" for="rsNote">توضیح برای ارسال به کاربر (اختیاری)</label>
          <textarea class="form-control" id="rsNote" data-r="note" rows="3" maxlength="300" placeholder="توضیح کوتاه…"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-soft" data-bs-dismiss="modal">انصراف</button>
          <button type="button" class="btn btn-danger" data-r="ok">ثبت</button>
        </div>
      </div>
    </div>
  </div>
  <div class="toast-stack" id="toastStack" aria-live="polite"></div>

  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('admin-assets/js/admin.js') }}"></script>
  <script>
    // خروج: پس از تأیید، فرم POST ارسال می‌شود
    $('#logoutBtn').on('admin:confirmed', function () { $('#logoutForm').trigger('submit'); });
  </script>
  @if(session('success'))<script>Admin.toast(@json(session('success')), 'success');</script>@endif
  @if(session('error'))<script>Admin.toast(@json(session('error')), 'error');</script>@endif
  @stack('scripts')
</body>
</html>
