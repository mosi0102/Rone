@extends('admin.layouts.app')
@section('title', 'گفتگوها')

@push('scripts')<script src="{{ asset('admin-assets/js/chats.js') }}"></script>@endpush

@section('content')
@php
  $counts = array_count_values(array_column($chats, 'status'));
  $hitLabel = ['phone' => 'شماره تماس', 'link' => 'لینک/آیدی بیرونی'];
@endphp

<x-admin.page-head title="گفتگوها" subtitle="مشاهده‌ی گفتگوی خریدار و نمایشگاه‌دار (فقط خواندنی) و پیگیری تخلف‌ها" />

<div class="chat-shell card-a rise" data-filterable data-chats>
  {{-- ===== فهرست گفتگوها ===== --}}
  <div class="chat-list-pane">
    <div class="chat-tools">
      <div class="search-pill">
        <x-admin.icon name="search" />
        <input type="search" data-filter-search placeholder="جستجوی کاربر، نمایشگاه یا خودرو…" aria-label="جستجو در گفتگوها" autocomplete="off">
      </div>
      <div class="seg" role="group" aria-label="وضعیت گفتگو">
        <button type="button" class="seg-btn active" data-filter="status" data-value="all" aria-pressed="true">همه <span class="n" data-count-for="status:all" data-fa>{{ count($chats) }}</span></button>
        <button type="button" class="seg-btn" data-filter="status" data-value="flagged" aria-pressed="false">پرچم‌شده <span class="n" data-count-for="status:flagged" data-fa>{{ $counts['flagged'] ?? 0 }}</span></button>
      </div>
    </div>
    <ul class="chat-list adm-scroll">
      @foreach($chats as $c)
        @php $last = end($c['messages']); @endphp
        <li data-row data-status="{{ $c['status'] }}" data-chat="{{ $c['id'] }}" data-search="{{ $c['user'] }} {{ $c['dealer'] }} {{ $c['title'] }}">
          <button type="button" class="chat-item" data-open="{{ $c['id'] }}">
            <span class="avatar">{{ mb_substr($c['user'], 0, 1) }}</span>
            <span class="ci-body">
              <b>{{ $c['user'] }} <small>با {{ $c['dealer'] }}</small></b>
              <span class="ci-title">{{ $c['title'] }}</span>
              <span class="ci-last">{{ $last['text'] }}</span>
            </span>
            <span class="ci-end"><span data-status-cell>@if($c['status'] !== 'normal')<x-admin.status :status="$c['status']" />@endif</span><small class="text-muted" data-jdate="{{ $last['at'] }}" data-jfmt="time"></small></span>
          </button>
        </li>
      @endforeach
    </ul>
    <div data-empty hidden><x-admin.empty icon="search" title="گفتگویی پیدا نشد" text="جستجو یا فیلتر را تغییر دهید." /></div>
  </div>

  {{-- ===== نمای گفتگو ===== --}}
  <div class="chat-thread-pane">
    <div class="chat-placeholder" data-placeholder><x-admin.empty icon="message" title="یک گفتگو را انتخاب کنید" text="پیام‌های طرفین اینجا نمایش داده می‌شود. ادمین فقط می‌تواند مشاهده و پرچم‌گذاری کند." /></div>
    @foreach($chats as $c)
      <section class="thread" data-thread="{{ $c['id'] }}" data-status="{{ $c['status'] }}" hidden>
        <header class="thread-head">
          <button type="button" class="icon-btn thread-back" data-back aria-label="بازگشت به فهرست"><x-admin.icon name="arrow-left" style="transform:scaleX(-1)" /></button>
          <div class="th-who"><b>{{ $c['user'] }} <small>با {{ $c['dealer'] }}</small></b><span>درباره‌ی {{ $c['title'] }}</span></div>
          <span data-d="status">@if($c['status'] !== 'normal')<x-admin.status :status="$c['status']" />@endif</span>
          <div class="th-actions">
            <button type="button" class="btn btn-bad btn-sm" data-show="normal reviewed" data-set="flagged" data-reason-modal="flagChat" data-title="{{ $c['user'] }} و {{ $c['dealer'] }}" data-done="گفتگو پرچم‌گذاری شد"><x-admin.icon name="flag" class="icon-sm" /> پرچم‌گذاری</button>
            <button type="button" class="btn btn-ok btn-sm" data-show="flagged" data-set="reviewed" data-confirm="این گفتگو بررسی‌شده و بدون مشکل علامت بخورد؟" data-confirm-ok="بررسی شد" data-done="گفتگو بررسی‌شده شد"><x-admin.icon name="check" class="icon-sm" /> بررسی شد</button>
          </div>
        </header>
        <div class="note-box bad thread-note" data-show="flagged" data-note-box><x-admin.icon name="flag" /><div><b>گفتگوی پرچم‌شده</b><p data-note>{{ $c['note'] }}</p></div></div>
        <ul class="msgs adm-scroll">
          @foreach($c['messages'] as $m)
            <li class="msg {{ $m['from'] }} {{ $m['hit'] ? 'hit' : '' }}">
              <small>{{ $m['from'] === 'user' ? $c['user'] : $c['dealer'] }} • <span data-jdate="{{ $m['at'] }}" data-jfmt="time"></span></small>
              {{ $m['text'] }}
              @if($m['hit'])<span class="hit-tag"><x-admin.icon name="alert-triangle" class="icon-sm" /> {{ $hitLabel[$m['hit']] }} (تشخیص خودکار)</span>@endif
            </li>
          @endforeach
        </ul>
        <footer class="thread-foot"><x-admin.icon name="eye" class="icon-sm" /> نمای ادمین: فقط خواندنی</footer>
      </section>
    @endforeach
  </div>
</div>

<x-admin.reason-modal id="flagChat" title="پرچم‌گذاری گفتگو" submit="پرچم‌گذاری" icon="flag" label="دلیل پرچم‌گذاری (فقط برای تیم مدیریت)"
  :chips="['تلاش برای خروج از سایت', 'درخواست پرداخت خارج از سایت', 'محتوای توهین‌آمیز', 'احتمال کلاهبرداری']" />
@endsection
