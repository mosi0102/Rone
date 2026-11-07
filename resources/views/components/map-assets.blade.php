{{-- فایل‌های نقشه (Leaflet + public/js/map.js). در هر صفحه‌ای که نقشه دارد یک‌بار: <x-map-assets /> --}}
@once
  @push('styles')<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}"><link rel="stylesheet" href="{{ asset('css/map.css') }}">@endpush
  @push('scripts')
    <script>window.MAP_CFG = @json(config('services.map'));</script>
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('js/map.js') }}"></script>
  @endpush
@endonce
