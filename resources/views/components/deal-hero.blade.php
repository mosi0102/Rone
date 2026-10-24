{{-- کارت وضعیت بالای صفحه معامله. tone: info (آبی) | green | amber --}}
@props(['tone' => 'info', 'icon' => 'shield', 'title', 'text' => null, 'amount' => null])
<section class="dl-hero {{ $tone }}">
  <span class="dl-ic"><x-icon :name="$icon" /></span>
  <h1>{{ $title }}</h1>
  @if($text)<p>{{ $text }}</p>@endif
  @if(!is_null($amount))<div class="dl-amt"><x-price :value="$amount" /></div>@endif
</section>
