{{-- دو مرحله‌ی مشترک ورود با کد یکبارمصرف: «شماره» و «کد». در صفحه‌ی ورود و ثبت‌نام نمایشگاه‌دار استفاده می‌شود.
     محتوای slot زیر دکمه‌ی «دریافت کد» نمایش داده می‌شود (لینک مهمان، ثبت‌نام نمایشگاه‌دار…).
     رفتار: js/auth.js ← Auth.flow() --}}
@props(['title' => 'ورود یا ثبت‌نام', 'sub' => 'شماره موبایل خود را وارد کنید تا کد تأیید ارسال شود.', 'password' => true])
<section class="auth-step" data-step="phone" data-title="{{ $title }}" data-sub="{{ $sub }}">
  <form data-form="phone" novalidate>
    <div class="fld">
      <label class="form-label" for="phone">شماره موبایل</label>
      <input class="form-control form-control-lg ltr" id="phone" name="phone" type="tel" inputmode="numeric" autocomplete="tel" maxlength="11" placeholder="09123456789" aria-describedby="phoneErr" autofocus>
      <div class="fld-err" id="phoneErr" role="alert"></div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">دریافت کد تأیید</button>
    @if($password)<button type="button" class="link-btn auth-link" data-goto-password>ورود با رمز عبور</button>@endif
    {{ $slot }}
  </form>
</section>

<section class="auth-step" data-step="code" data-title="کد تأیید را وارد کنید" data-sub="کد ۵ رقمی به شماره‌ی <b class='ltr d-inline-block' data-phone-show></b> ارسال شد." hidden>
  <form data-form="code" novalidate>
    <div class="otp-boxes" dir="ltr" role="group" aria-label="کد تأیید ۵ رقمی">
      @for($i = 0; $i < 5; $i++)
        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" aria-label="رقم {{ $i + 1 }}" @if($i === 0) autocomplete="one-time-code" @else autocomplete="off" @endif>
      @endfor
    </div>
    <div class="fld-err text-center" id="codeErr" role="alert"></div>
    <button type="submit" class="btn btn-primary btn-lg w-100 mt-2" data-verify>تأیید و ادامه</button>
    <div class="auth-meta">
      <button type="button" class="link-btn" data-edit-phone>ویرایش شماره</button>
      <button type="button" class="link-btn" data-resend disabled>ارسال مجدد (<span data-timer>۶۰</span>)</button>
    </div>
    @if($password)<button type="button" class="link-btn auth-link" data-goto-password>کد نرسید؟ ورود با رمز عبور</button>@endif
  </form>
</section>
