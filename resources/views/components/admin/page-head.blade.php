{{-- <x-admin.page-head title="آگهی‌ها" subtitle="...">  دکمه‌ها (اختیاری) در slot  </x-admin.page-head> --}}
@props(['title', 'subtitle' => null])
<div class="page-head rise">
  <div><h1>{{ $title }}</h1>@if($subtitle)<p>{{ $subtitle }}</p>@endif</div>
  @if(trim($slot))<div class="ph-actions">{{ $slot }}</div>@endif
</div>
