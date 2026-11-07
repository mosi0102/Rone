<footer class="site-footer">
  <div class="page-container">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="brand text-white mb-2">{{ config('app.name') }}</div>
        <p class="small mb-0">خرید، فروش، اجاره و مزایده‌ی ملک؛ قرارداد حضوری در آژانس‌های املاک تأییدشده.</p>
      </div>
      <div class="col-6 col-lg-3">
        <div class="fw-bold text-white mb-2">دسترسی سریع</div>
        <ul class="list-unstyled small d-grid gap-1 mb-0">
          <li><a href="{{ route('listings.index') }}">آگهی‌ها</a></li>
          <li><a href="{{ route('auctions.index') }}">مزایده‌ها</a></li>
          <li><a href="{{ route('radar.index') }}">رادار ملک</a></li>
          <li><a href="{{ route('agencies.index') }}">مشاوران املاک</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-4">
        <div class="fw-bold text-white mb-2">برای مشاوران املاک</div>
        <ul class="list-unstyled small d-grid gap-1 mb-0">
          <li><a href="{{ route('agency.join') }}">ثبت‌نام آژانس</a></li>
          <li><a href="#">تعرفه اشتراک</a></li>
        </ul>
      </div>
    </div>
    <div class="small mt-4 pt-3 border-top border-secondary border-opacity-25">© {{ config('app.name') }} — تمامی حقوق محفوظ است.</div>
  </div>
</footer>
