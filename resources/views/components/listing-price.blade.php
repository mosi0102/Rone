{{--
  قیمت آگهی ملک بر اساس نوع معامله.
  $item: price (قیمت کل / رهن / هر شب / هر متر)، rent (اجاره‌ی ماهانه؛ فقط اجاره‌ها)، price_label (برچسب price)
  اجاره: «رهن ۶۰۰ میلیون • اجاره ۲۵ میلیون» — رهن کامل: rent = 0
--}}
@props(['item', 'compact' => false])
@php $i = (object) $item; $rent = $i->rent ?? null; $label = $i->price_label ?? null; @endphp
<span {{ $attributes->class('lp') }}>
  @if(!is_null($rent))
    <span class="lp-part"><small class="lp-l">رهن</small> <x-price :value="$i->price" :unit="$compact ? '' : 'تومان'" /></span>
    @if((int) $rent > 0)<span class="lp-part"><small class="lp-l">اجاره</small> <x-price :value="$rent" :unit="$compact ? '' : 'تومان'" /></span>@else<span class="lp-part"><small class="lp-l">رهن کامل</small></span>@endif
  @else
    @if($label && $label !== 'قیمت کل')<small class="lp-l">{{ $label }}</small> @endif<x-price :value="$i->price" />
  @endif
</span>
