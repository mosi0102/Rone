{{--
  کارت رسید/خلاصه. <x-receipt title="رسید بیعانهٔ امانی" :rows="[...]" note="..." />
  هر ردیف: ['k' => 'برچسب', 'money' => 20000000 (قیمت آبی)] | ['k'=>..., 'v' => 'متن'] | ['k'=>..., 'date' => ISO, 'fmt' => 'datetime'] | ['sep' => true] | ['note' => 'متن توضیح']
--}}
@props(['title', 'rows', 'note' => null, 'total' => null])
<section {{ $attributes->class('card-s dl-card rcpt') }}>
  <h2>{{ $title }}</h2>
  @foreach($rows as $r)
    @if(!empty($r['sep']))<hr class="rc-sep">
    @elseif(!empty($r['note']))<p class="rc-inline-note">{{ $r['note'] }}</p>
    @else
      <div class="rc-row {{ !empty($r['big']) ? 'big' : '' }}">
        <span class="rc-k">{{ $r['k'] }}</span>
        @if(isset($r['money']))<b class="rc-v blue"><x-price :value="$r['money']" /></b>
        @elseif(isset($r['date']))<b class="rc-v" data-jfmt="{{ $r['fmt'] ?? 'datetime' }}" data-jdate="{{ \Illuminate\Support\Carbon::parse($r['date'])->toIso8601String() }}"></b>
        @else<b class="rc-v">{{ $r['v'] ?? '' }}</b>@endif
      </div>
    @endif
  @endforeach
  @if($note)<p class="rc-note">{{ $note }}</p>@endif
</section>
