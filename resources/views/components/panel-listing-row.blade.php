{{--
  ردیف آگهی در پنل نمایشگاه‌دار (با آمار و منوی عملیات).
  $item: id,title,image,price,status,sale_type,views,saves,reserves,expires
--}}
@props(['item'])
@php $i = (object) $item; @endphp
<article class="pl-row" data-status="{{ $i->status }}">
  <a href="{{ route('listings.show', $i->id) }}" class="pl-media">
    @if(!empty($i->image))<img src="{{ $i->image }}" alt="" loading="lazy">@else<x-icon name="car" />@endif
  </a>
  <div class="pl-body">
    <div class="pl-top">
      <h3><a href="{{ route('listings.show', $i->id) }}" class="text-reset">{{ $i->title }}</a></h3>
      <div class="dropdown">
        <button class="icon-btn pl-menu" type="button" data-bs-toggle="dropdown" aria-label="عملیات" aria-expanded="false"><x-icon name="dots" /></button>
        <ul class="dropdown-menu dropdown-menu-end user-menu">
          <li><a class="dropdown-item" href="{{ route('listings.edit', $i->id) }}">ویرایش آگهی</a></li>
          <li><a class="dropdown-item" href="{{ route('listings.show', $i->id) }}">مشاهده آگهی</a></li>
          @if($i->status === 'active')
            <li><a class="dropdown-item" href="{{ route('listings.show', $i->id) }}#instant">فروش فوری</a></li>
            <li><a class="dropdown-item" href="{{ route('auctions.create', ['listing' => $i->id]) }}">ساخت مزایده</a></li>
          @endif
          @if($i->status === 'expired')<li><a class="dropdown-item" href="#" data-renew="{{ $i->id }}">تمدید آگهی</a></li>@endif
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#" data-delete="{{ $i->id }}">حذف</a></li>
        </ul>
      </div>
    </div>
    <div class="pl-price"><x-price :value="$i->price" /></div>
    <div class="pl-meta">
      <x-status-badge :status="$i->status" />
      @if(($i->sale_type ?? null) === 'instant')<span class="badge-s badge-instant"><x-icon name="bolt" /> فروش فوری</span>@endif
      <span class="pl-exp">{{ $i->expires }}</span>
    </div>
    <div class="pl-stats">
      <span title="بازدید"><x-icon name="eye" class="icon-sm" /> <span data-fa>{{ $i->views }}</span></span>
      <span title="نشان‌شده"><x-icon name="bookmark" class="icon-sm" /> <span data-fa>{{ $i->saves }}</span></span>
      <span title="بیعانه"><x-icon name="shield" class="icon-sm" /> <span data-fa>{{ $i->reserves }}</span></span>
    </div>
  </div>
</article>
