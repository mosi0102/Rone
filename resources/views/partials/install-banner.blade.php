<div class="install-banner" id="installBanner" role="dialog" aria-label="نصب اپلیکیشن">
  <img src="{{ asset('icons/icon-192.png') }}" alt="">
  <div class="flex-fill">
    <div class="fw-bold">{{ config('app.name') }} را نصب کنید</div>
    <div class="small text-muted-s">دسترسی سریع‌تر، مثل یک اپلیکیشن واقعی</div>
  </div>
  <button class="btn btn-primary btn-sm" data-install>نصب</button>
  <button class="icon-btn" id="installDismiss" aria-label="بستن"><x-icon name="x" /></button>
</div>

{{-- راهنمای نصب در iOS (سافاری امکان نصب خودکار ندارد) --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="iosInstallSheet" aria-labelledby="iosInstallTitle">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="iosInstallTitle">نصب روی آیفون</h5>
    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button>
  </div>
  <div class="offcanvas-body pt-0">
    <ol class="ps-3 d-grid gap-2 mb-3">
      <li>در سافاری روی دکمه <x-icon name="share" class="icon-sm" /> «اشتراک‌گذاری» بزنید.</li>
      <li>گزینه «Add to Home Screen» را انتخاب کنید.</li>
      <li>روی «Add» بزنید.</li>
    </ol>
    <button class="btn btn-primary w-100" data-bs-dismiss="offcanvas">متوجه شدم</button>
  </div>
</div>
