{{--
  کلید نقش (فقط حالت نمایشی/توسعه: APP_DEBUG=true). چون بک‌اند هنوز وصل نیست، با این کلید بین نقش‌ها جابه‌جا می‌شوید:
  مهمان · کاربر عادی · نمایشگاه‌دار (حالت خریدار) · نمایشگاه‌دار (حالت نمایشگاه).
  بک‌اندکار: این partial و بلوک View::composer نمایشی در routes/web.php را حذف کند.
--}}
@php
  $roleLabel = !$auth->check ? 'مهمان' : (!$auth->is_dealer ? 'کاربر عادی' : ($auth->dealer_mode ? 'نمایشگاه‌دار' : 'نمایشگاه‌دار (خریدار)'));
@endphp
<div class="dev-fab" id="devFab">
  <button type="button" class="dev-btn" aria-expanded="false" aria-controls="devPanel" aria-label="تغییر نقش (حالت نمایشی). نقش فعلی: {{ $roleLabel }}" title="نقش فعلی: {{ $roleLabel }}"><x-icon name="users" /></button>
  <div class="dev-panel" id="devPanel" hidden>
    <strong>نقش فعلی: <span class="dev-now">{{ $roleLabel }}</span></strong>
    <button type="button" data-as="guest" class="{{ !$auth->check ? 'on' : '' }}">مهمان (وارد نشده)</button>
    <button type="button" data-as="user" class="{{ $auth->check && !$auth->is_dealer ? 'on' : '' }}">کاربر عادی</button>
    <button type="button" data-as="dealer" data-mode="buyer" class="{{ $auth->is_dealer && !$auth->dealer_mode ? 'on' : '' }}">نمایشگاه‌دار • حالت خریدار</button>
    <button type="button" data-as="dealer" data-mode="dealer" class="{{ $auth->dealer_mode ? 'on' : '' }}">نمایشگاه‌دار • حالت نمایشگاه (پنل، ثبت آگهی)</button>
    <small>فقط وقتی APP_DEBUG روشن است دیده می‌شود.</small>
  </div>
</div>
