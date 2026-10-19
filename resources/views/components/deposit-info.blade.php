{{-- توضیح بیعانه امانی؛ در پروفایل نمایشگاه و صفحه آگهی. با $icon هدر آیکن‌دار (پروفایل) --}}
@props(['title' => 'بیعانه امانی و ادامه فرایند معامله', 'text' => 'برای ادامه معامله، ابتدا بیعانه را از طریق پلتفرم پرداخت کنید. مبلغ بیعانه نزد پلتفرم امانت می‌ماند و تا زمان انجام یا عدم انجام معامله بلوکه می‌شود.', 'icon' => null])
<section {{ $attributes->class('card-s deposit-info') }}>
  <div class="di-head">
    @if($icon)<span class="di-ic"><x-icon :name="$icon" /></span>@endif
    <div>
      <h2>{{ $title }}</h2>
      <p>{{ $text }}</p>
    </div>
  </div>
  <div class="di-grid">
    <div class="di-box ok">
      <h3><span class="di-i"><x-icon name="shield" class="icon-sm" /></span> انجام معامله</h3>
      <p>پس از بازدید و توافق، مبلغ بیعانه به نمایشگاه‌دار یا صاحب آگهی واریز می‌شود.</p>
    </div>
    <div class="di-box no">
      <h3><span class="di-i"><x-icon name="x-circle" class="icon-sm" /></span> عدم انجام معامله</h3>
      <p>اگر معامله انجام نشود، مبلغ بیعانه به‌صورت کامل به شما بازگردانده می‌شود.</p>
    </div>
  </div>
  <div class="di-note"><x-icon name="info" class="icon-sm" /> تأیید نمایشگاه، جایگزین کارشناسی خودرو نیست. قبل از پرداخت بیعانه، مدارک و شرایط خودرو را بررسی کنید.</div>
</section>
