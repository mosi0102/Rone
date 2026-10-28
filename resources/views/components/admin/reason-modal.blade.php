{{-- مودال «اقدام با دلیل». استفاده:
     <x-admin.reason-modal id="cancelModal" title="لغو مزایده" submit="لغو مزایده" :chips="['دلیل ۱', 'دلیل ۲']"> فیلدهای اضافه (اختیاری) </x-admin.reason-modal>
     در JS: Admin.askReason('cancelModal', 'متن زیرعنوان').then(function (reason) { ... }) --}}
@props(['id', 'title', 'submit' => 'تأیید', 'chips' => [], 'icon' => 'x-circle', 'tone' => 'bad', 'label' => 'دلیل (برای طرف مقابل ارسال می‌شود)', 'placeholder' => 'توضیح بیشتر (اختیاری)…', 'required' => true])
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-head-a tone-{{ $tone }}">
        <span class="c-ic"><x-admin.icon :name="$icon" class="icon-lg" /></span>
        <div><h3 id="{{ $id }}Title">{{ $title }}</h3><p data-r="title"></p></div>
      </div>
      <div class="modal-body-a" data-required="{{ $required ? '1' : '0' }}">
        {{ $slot }}
        <label class="form-label" for="{{ $id }}Reason">{{ $label }}</label>
        @if(count($chips))
          <div class="reason-chips">@foreach($chips as $c)<button type="button" class="reason-chip">{{ $c }}</button>@endforeach</div>
        @endif
        <textarea class="form-control" id="{{ $id }}Reason" rows="3" maxlength="300" placeholder="{{ $placeholder }}"></textarea>
      </div>
      <div class="modal-foot-a">
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">انصراف</button>
        <button type="button" class="btn btn-danger" data-r="submit" disabled>{{ $submit }}</button>
      </div>
    </div>
  </div>
</div>
