{{--
  بنر آژانس بالای صفحه جزئیات آگهی (مشخص می‌کند آگهی مال کدام آژانس است). لمس = رفتن به پروفایل آژانس.
  $agency: id, name, area, active_count, cover, url
--}}
@props(['agency'])
@php $d = (object) $agency; $map = array_combine(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹']); @endphp
<a href="{{ $d->url ?? '#' }}" class="agency-banner" aria-label="مشاهده آژانس {{ $d->name }}">
  @if(!empty($d->cover))<img src="{{ $d->cover }}" alt="" fetchpriority="high">@endif
  <span class="db-shade" aria-hidden="true"></span>
  <span class="db-text">
    <b>{{ $d->name }}</b>
    <span>{{ $d->area }} • {{ strtr((string) $d->active_count, $map) }} آگهی فعال</span>
  </span>
</a>
