{{-- فیلد رمز عبور با نمایش/پنهان‌سازی و (اختیاری) سنجش قدرت.
     <x-auth.password id="regPw" name="password" label="رمز عبور" :meter="true" autocomplete="new-password" /> --}}
@props(['id', 'name' => 'password', 'label' => 'رمز عبور', 'meter' => false, 'autocomplete' => 'current-password'])
<div class="fld pw-field" data-pw @if($meter) data-meter @endif>
  <label class="form-label" for="{{ $id }}">{{ $label }}</label>
  <div class="pw-wrap">
    <input class="form-control form-control-lg ltr" type="password" id="{{ $id }}" name="{{ $name }}" autocomplete="{{ $autocomplete }}" aria-describedby="{{ $id }}Err" placeholder="••••••••">
    <button type="button" class="pw-eye" data-pw-toggle aria-label="نمایش رمز عبور" aria-pressed="false"><x-icon name="eye" /></button>
  </div>
  @if($meter)
    <div class="pw-meter" data-pw-meter aria-live="polite"><span class="bars"><i></i><i></i><i></i></span><b data-pw-label>—</b></div>
    <ul class="pw-rules">
      <li data-rule="len"><x-icon name="check-circle" /> حداقل ۸ کاراکتر</li>
      <li data-rule="mix"><x-icon name="check-circle" /> ترکیب حروف و عدد</li>
      <li data-rule="common"><x-icon name="check-circle" /> نه رمز رایج یا قابل‌حدس (مثل ۱۲۳۴۵۶۷۸)</li>
    </ul>
  @endif
  <div class="fld-err" id="{{ $id }}Err" role="alert"></div>
</div>
