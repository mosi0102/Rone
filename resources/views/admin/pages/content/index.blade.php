@extends('admin.layouts.app')
@section('title', 'صفحات و محتوا')

@push('scripts')<script src="{{ asset('admin-assets/js/content.js') }}"></script>@endpush

@section('content')
<x-admin.page-head title="صفحات و محتوا" subtitle="صفحه‌های ثابت سایت مثل قوانین، حریم خصوصی و راهنماها" />

<section class="card-a rise" style="--d:1" data-content>
  <div class="tbl-wrap adm-scroll">
    <table class="tbl tbl-cards">
      <thead><tr><th>صفحه</th><th>آدرس</th><th>آخرین ویرایش</th><th>وضعیت</th><th></th></tr></thead>
      <tbody>
        @foreach($pages as $p)
          <tr data-row data-id="{{ $p['id'] }}" data-title="{{ $p['title'] }}" data-slug="{{ $p['slug'] }}" data-body="{{ $p['body'] }}" data-status="{{ $p['status'] }}">
            <td class="td-main"><div class="cell-main"><span class="lr-ic t-sky"><x-admin.icon name="file" /></span><b data-t>{{ $p['title'] }}</b></div></td>
            <td data-label="آدرس"><span class="tnum" dir="ltr">/{{ $p['slug'] }}</span></td>
            <td data-label="آخرین ویرایش" class="text-muted">{{ $p['ago'] }}</td>
            <td data-label="وضعیت"><span data-status-cell>{!! $p['status'] === 'active' ? '<span class="badge-s ok">منتشرشده</span>' : '<span class="badge-s warn">پیش‌نویس</span>' !!}</span></td>
            <td class="td-act"><div class="row-actions"><button type="button" class="btn btn-soft btn-sm" data-edit><x-admin.icon name="pencil" class="icon-sm" /> ویرایش</button></div></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

<div class="offcanvas offcanvas-end detail" tabindex="-1" id="pageEditor" aria-labelledby="pageEditorTitle">
  <div class="offcanvas-header"><h2 class="offcanvas-title" id="pageEditorTitle">ویرایش صفحه</h2><button type="button" class="icon-btn" data-bs-dismiss="offcanvas" aria-label="بستن"><x-admin.icon name="x" /></button></div>
  <div class="offcanvas-body">
    <label class="form-label" for="pgTitle">عنوان</label><input class="form-control mb-3" id="pgTitle" maxlength="80">
    <label class="form-label" for="pgSlug">آدرس صفحه</label><input class="form-control mb-3 text-start" dir="ltr" id="pgSlug" maxlength="60">
    <label class="form-label" for="pgBody">متن صفحه</label><textarea class="form-control mb-3" id="pgBody" rows="12"></textarea>
    <div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" role="switch" id="pgPublish"><label class="form-check-label" for="pgPublish">منتشر شود</label></div>
    <button type="button" class="btn btn-ink w-100" id="pgSave" disabled>ذخیره‌ی صفحه</button>
  </div>
</div>
@endsection
