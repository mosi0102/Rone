{{--
  بنر نمایشگاه بالای صفحه جزئیات آگهی (مشخص می‌کند آگهی مال کدام نمایشگاه است). لمس = رفتن به پروفایل نمایشگاه.
  $dealer: id, name, area, active_count, cover, url
--}}
@props(['dealer'])
@php $d = (object) $dealer; $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
<a href="{{ $d->url ?? '#' }}" class="dealer-banner" aria-label="مشاهده نمایشگاه {{ $d->name }}">
  @if(!empty($d->cover))<img src="{{ $d->cover }}" alt="" fetchpriority="high">@endif
  <span class="db-shade" aria-hidden="true"></span>
  <span class="db-text">
    <b>{{ $d->name }}</b>
    <span>{{ $d->area }} • {{ strtr((string) $d->active_count, $map) }} آگهی فعال</span>
  </span>
</a>
