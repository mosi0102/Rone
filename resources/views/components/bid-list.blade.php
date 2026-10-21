{{--
  لیست آخرین پیشنهادها (زنده). $bids: [{amount,user,time,mine}] از بالاترین به پایین.
  فقط برای کسی که ورودی را پرداخت کرده یا صاحب مزایده رندر شود (برای بقیه از lock استفاده کنید).
--}}
@props(['bids'])
<section class="card-s bid-card" aria-live="polite">
  <h2 class="bc-t">آخرین پیشنهادها</h2>
  <div class="bid-list" id="bidList">
    @forelse($bids as $b)
      @php $b = (object) $b; @endphp
      <div class="bid-row {{ !empty($b->mine) ? 'mine' : '' }} {{ $loop->first ? 'top' : '' }}">
        <div class="br-main"><b><x-price :value="$b->amount" /></b><span>{{ !empty($b->mine) ? 'شما' : $b->user }}</span></div>
        <time class="br-time">{{ $b->time }}</time>
      </div>
    @empty
      <p class="bid-empty">هنوز پیشنهادی ثبت نشده؛ اولین نفر باشید.</p>
    @endforelse
  </div>
</section>
