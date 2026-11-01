{{-- نتیجه‌ی جست‌وجوی زنده‌ی نمایشگاه‌ها (AJAX). $items: آرایه‌ی نمایشگاه‌ها --}}
@if(count($items))
  <p class="so-count"><span data-fa>{{ count($items) }}</span> نمایشگاه پیدا شد</p>
  <div class="lr-list">@foreach($items as $d)<x-dealer-row :dealer="$d" />@endforeach</div>
@else
  <x-empty-state icon="store" title="نمایشگاهی پیدا نشد" text="عبارت دیگری را امتحان کنید یا شهر را تغییر دهید." />
@endif
