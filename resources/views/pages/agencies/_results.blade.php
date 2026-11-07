{{-- نتیجه‌ی جست‌وجوی زنده‌ی آژانس‌ها (AJAX). $items: آرایه‌ی آژانس‌ها --}}
@if(count($items))
  <p class="so-count"><span data-fa>{{ count($items) }}</span> آژانس پیدا شد</p>
  <div class="lr-list">@foreach($items as $d)<x-agency-row :agency="$d" />@endforeach</div>
@else
  <x-empty-state icon="store" title="آژانسی پیدا نشد" text="عبارت دیگری را امتحان کنید یا شهر را تغییر دهید." />
@endif
