{{-- <x-admin.pagination :page="1" :pages="5" /> — ظاهر صفحه‌بندی. بک‌اندکار با $paginator->links() جایگزین کند --}}
@props(['page' => 1, 'pages' => 1])
@if($pages > 1)
<nav class="pager" aria-label="صفحه‌بندی">
  <a class="pg {{ $page <= 1 ? 'disabled' : '' }}" href="#" aria-label="قبلی"><x-admin.icon name="chevron-right" class="icon-sm" /></a>
  @for($i = 1; $i <= $pages; $i++)
    <a class="pg {{ $i === $page ? 'active' : '' }}" href="#" @if($i === $page) aria-current="page" @endif data-fa>{{ $i }}</a>
  @endfor
  <a class="pg {{ $page >= $pages ? 'disabled' : '' }}" href="#" aria-label="بعدی"><x-admin.icon name="chevron-left" class="icon-sm" /></a>
</nav>
@endif
