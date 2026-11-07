{{--
  کلید نقش (فقط حالت نمایشی/توسعه: APP_DEBUG=true). چون بک‌اند هنوز وصل نیست، با این کلید بین نقش‌ها جابه‌جا می‌شوید:
  مهمان · کاربر · مشاور املاک؛ و وضعیت احراز هویت (برای دیدن محدودیت عملیات پرداختی).
  بک‌اندکار: این partial و بلوک View::composer نمایشی (app/View/Composers/DemoAuthComposer.php) را حذف کند.
--}}
@php
  $roleLabel = !$auth->check ? 'مهمان' : ($auth->is_agency ? 'مشاور املاک' : 'کاربر');
  $pl = ['none' => 'تکمیل‌نشده', 'pending' => 'در بررسی', 'approved' => 'تأییدشده', 'rejected' => 'ردشده'];
@endphp
<div class="dev-fab" id="devFab">
  <button type="button" class="dev-btn" aria-expanded="false" aria-controls="devPanel" aria-label="تغییر نقش (حالت نمایشی). نقش فعلی: {{ $roleLabel }}" title="نقش فعلی: {{ $roleLabel }}"><x-icon name="users" /></button>
  <div class="dev-panel" id="devPanel" hidden>
    <strong>نقش فعلی: <span class="dev-now">{{ $roleLabel }}</span></strong>
    <button type="button" data-as="guest" class="{{ !$auth->check ? 'on' : '' }}">مهمان (وارد نشده)</button>
    <button type="button" data-as="user" class="{{ $auth->check && !$auth->is_agency ? 'on' : '' }}">کاربر (وارد شده)</button>
    <button type="button" data-as="agency" class="{{ $auth->is_agency ? 'on' : '' }}">مشاور املاک (امکانات کاربر + پنل)</button>
    @if($auth->check)
      <strong>احراز هویت: <span class="dev-now">{{ $pl[$auth->profile_status] }}</span></strong>
      @foreach($pl as $k => $lbl)<button type="button" data-profile="{{ $k }}" class="{{ $auth->profile_status === $k ? 'on' : '' }}">{{ $lbl }}</button>@endforeach
    @endif
    <small>فقط وقتی APP_DEBUG روشن است دیده می‌شود.</small>
  </div>
</div>
