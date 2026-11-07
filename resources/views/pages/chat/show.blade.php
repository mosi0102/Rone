@extends('layouts.app')
@section('title', 'گفتگو با ' . ($role === 'agency' ? $t['buyer'] : $t['agency']['name']))
@section('page_title', $role === 'agency' ? $t['buyer'] : $t['agency']['name'])
@section('page_subtitle', $t['listing']['title'])
@section('back', route('chat.index'))
@section('body_class', 'no-bottom-nav chat-page')

@push('styles')<link rel="stylesheet" href="{{ asset('css/chat.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/chat.js') }}"></script>@endpush

@section('content')
@php $paid = true; // شماره‌ی آژانس عمومی است؛ گفتگو همیشه باز است @endphp
<div class="chat" id="chat" data-id="{{ $t['id'] }}" data-role="{{ $role }}" data-unlocked="{{ $paid ? 1 : 0 }}"
     data-send-url="{{ route('chat.send', $t['id']) }}" data-report-url="{{ route('chat.report', $t['id']) }}">

  <a href="{{ $t['listing']['url'] }}" class="ch-car-card">
    <img src="{{ $t['listing']['image'] }}" alt="">
    <div><b>{{ $t['listing']['title'] }}</b><span><x-price :value="$t['listing']['price']" /></span></div>
    <x-icon name="chevron-left" />
  </a>

  <div class="ch-banner"><x-icon name="shield" /><p><b>گفتگو تحت نظارت سامانه است.</b> ارسال لینک و آی‌دی شبکه‌های اجتماعی مجاز نیست. پیش از بازدید و قرارداد در آژانس، هیچ پولی واریز نکنید.</p></div>

  <div class="ch-msgs" id="chMsgs" role="log" aria-live="polite" aria-label="پیام‌ها">
    @foreach($t['messages'] as $m)
      @php $mine = $m['from'] === $role; @endphp
      <div class="ch-msg {{ $mine ? 'mine' : 'theirs' }} {{ $m['hidden'] ? 'is-hidden' : '' }}" data-mid="{{ $m['id'] }}">
        <div class="ch-bubble">@if($m['hidden'])<span class="ch-hid"><x-icon name="alert" class="icon-sm" /> {{ $mine ? 'این پیام به‌دلیل نقض قوانین ارسال نشد.' : 'این پیام حاوی لینک، آی‌دی یا درخواست واریز بود و نمایش داده نمی‌شود.' }}</span>@else{{ $m['text'] }}@endif</div>
        <div class="ch-meta"><time data-jfmt="time" data-jdate="{{ $m['at'] }}"></time>
          @if(!$mine)<button type="button" class="ch-rep" data-report="{{ $m['id'] }}" {{ $m['reported'] ? 'disabled' : '' }}>{{ $m['reported'] ? 'گزارش شد' : 'گزارش' }}</button>@endif</div>
      </div>
    @endforeach
  </div>

  <div class="ch-warn" id="chWarn" role="alert" hidden><x-icon name="alert" /><div><b id="chWarnT"></b><p id="chWarnS"></p></div></div>

  <div class="ch-quick" id="chQuick">@foreach($quick as $q)<button type="button" data-quick>{{ $q }}</button>@endforeach</div>
  <form class="ch-form" id="chForm" novalidate>
    <textarea id="chText" rows="1" maxlength="1000" placeholder="پیام خود را بنویسید…" aria-label="متن پیام"></textarea>
    <button type="submit" class="ch-send" id="chSend" aria-label="ارسال"><x-icon name="arrow-left" /></button>
  </form>
</div>

{{-- گزارش پیام --}}
<div class="offcanvas offcanvas-bottom sheet" tabindex="-1" id="repSheet" aria-labelledby="repT">
  <div class="sheet-handle"></div>
  <div class="offcanvas-header"><h5 class="offcanvas-title" id="repT">گزارش پیام</h5><button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="offcanvas" aria-label="بستن"></button></div>
  <div class="offcanvas-body pt-0">
    <p class="text-muted-s">دلیل گزارش را انتخاب کنید؛ مدیریت بررسی می‌کند.</p>
    <div class="ch-reasons">
      @foreach(['contact' => 'درخواست یا ارسال شماره/آی‌دی', 'offsite' => 'پیشنهاد معامله یا پرداخت خارج از سامانه', 'abuse' => 'توهین یا رفتار نامناسب', 'fraud' => 'مشکوک به کلاهبرداری'] as $k => $l)
        <label><input type="radio" name="reason" value="{{ $k }}"><span>{{ $l }}</span></label>
      @endforeach
    </div>
    <button type="button" class="btn btn-primary btn-lg w-100 mt-3" id="repGo" disabled>ثبت گزارش</button>
  </div>
</div>
@endsection
