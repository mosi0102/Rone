@extends('admin.layouts.app')
@section('title', 'داده‌های پایه')

@push('scripts')<script src="{{ asset('admin-assets/js/catalog.js') }}"></script>@endpush

@section('content')
@php $first = array_key_first($sets); @endphp
<x-admin.page-head title="داده‌های پایه" subtitle="برند، مدل، شهر و ویژگی‌هایی که در فرم ثبت آگهی و فیلترها استفاده می‌شود">
  <button type="button" class="btn btn-ink btn-sm" data-add><x-admin.icon name="plus" class="icon-sm" /> افزودن مورد</button>
</x-admin.page-head>

<div data-catalog>
  <section class="card-a filters rise" style="--d:1">
    <div class="filters-row">
      <div class="seg" role="tablist" aria-label="نوع داده">
        @foreach($sets as $key => $set)
          <button type="button" class="seg-btn {{ $key === $first ? 'active' : '' }}" role="tab" data-tab="{{ $key }}" aria-selected="{{ $key === $first ? 'true' : 'false' }}">{{ $set['title'] }} <span class="n" data-fa>{{ count($set['items']) }}</span></button>
        @endforeach
      </div>
      <div class="search-pill"><x-admin.icon name="search" /><input type="search" data-q placeholder="جستجو در این فهرست…" aria-label="جستجو" autocomplete="off"></div>
    </div>
  </section>

  @foreach($sets as $key => $set)
    <section class="card-a rise" style="--d:2" data-pane="{{ $key }}" data-parent-label="{{ $set['parent'] }}" @if($key !== $first) hidden @endif>
      <div class="tbl-wrap adm-scroll">
        <table class="tbl tbl-cards">
          <thead><tr><th>نام</th><th>{{ $set['parent'] }}</th><th>آگهی‌ها</th><th>فعال</th><th></th></tr></thead>
          <tbody>
            @foreach($set['items'] as [$name, $parent, $ads, $on])
              <tr data-item data-search="{{ $name }} {{ $parent }}" data-ads="{{ $ads }}">
                <td class="td-main"><b data-name>{{ $name }}</b></td>
                <td data-label="{{ $set['parent'] }}" data-parent>{{ $parent }}</td>
                <td data-label="آگهی‌ها" data-fa>{{ $ads }}</td>
                <td data-label="فعال"><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" role="switch" data-toggle aria-label="فعال بودن {{ $name }}" {{ $on ? "checked" : "" }}></div></td>
                <td class="td-act"><div class="row-actions">
                  <button type="button" class="btn btn-soft btn-sm" data-edit><x-admin.icon name="pencil" class="icon-sm" /> ویرایش</button>
                  <button type="button" class="btn btn-bad btn-sm" data-del><x-admin.icon name="trash" class="icon-sm" /> حذف</button>
                </div></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div data-none hidden><x-admin.empty icon="search" title="موردی پیدا نشد" text="عبارت جستجو را تغییر دهید." /></div>
    </section>
  @endforeach
</div>

<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-head-a tone-info"><span class="c-ic"><x-admin.icon name="pencil" class="icon-lg" /></span><div><h3 id="itemModalTitle" data-m="title">افزودن مورد</h3><p data-m="sub"></p></div></div>
    <div class="modal-body-a">
      <label class="form-label" for="itemName">نام</label>
      <input class="form-control mb-3" id="itemName" maxlength="60" autocomplete="off">
      <label class="form-label" for="itemParent" data-m="parentLabel">گروه</label>
      <input class="form-control" id="itemParent" maxlength="60" autocomplete="off">
    </div>
    <div class="modal-foot-a"><button type="button" class="btn btn-soft" data-bs-dismiss="modal">انصراف</button><button type="button" class="btn btn-ink" data-m="save" disabled>ذخیره</button></div>
  </div></div>
</div>
@endsection
