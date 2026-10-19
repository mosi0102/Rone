<footer class="site-footer">
  <div class="page-container">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="brand text-white mb-2">{{ config('app.name') }}</div>
        <p class="small mb-0">رزرو آنلاین خودرو با بیعانه امن؛ معامله حضوری در نمایشگاه‌های تأییدشده.</p>
      </div>
      <div class="col-6 col-lg-3">
        <div class="fw-bold text-white mb-2">دسترسی سریع</div>
        <ul class="list-unstyled small d-grid gap-1 mb-0">
          <li><a href="{{ route('listings.index') }}">آگهی‌ها</a></li>
          <li><a href="{{ route('auctions.index') }}">مزایده‌ها</a></li>
          <li><a href="{{ route('dealers.index') }}">نمایشگاه‌ها</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-4">
        <div class="fw-bold text-white mb-2">برای نمایشگاه‌داران</div>
        <ul class="list-unstyled small d-grid gap-1 mb-0">
          <li><a href="#">ثبت‌نام نمایشگاه</a></li>
          <li><a href="#">تعرفه اشتراک</a></li>
        </ul>
      </div>
    </div>
    <div class="small mt-4 pt-3 border-top border-secondary border-opacity-25">© {{ config('app.name') }} — تمامی حقوق محفوظ است.</div>
  </div>
</footer>
