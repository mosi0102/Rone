/* Service Worker — کش استاتیک + صفحه آفلاین. با هر تغییر فایل‌های استاتیک، VERSION را بالا ببرید. */
const VERSION = 'v9';
const STATIC = 'static-' + VERSION;
const PRECACHE = [
  '/offline',
  '/css/app.css', '/js/app.js',
  '/vendor/bootstrap/bootstrap.rtl.min.css', '/vendor/bootstrap/bootstrap.bundle.min.js', '/vendor/jquery/jquery.min.js',
  '/fonts/Vazirmatn-Variable.woff2', '/icons/icon-192.png'
];

self.addEventListener('install', e => {
  e.waitUntil(caches.open(STATIC).then(c => c.addAll(PRECACHE)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k !== STATIC).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
  const req = e.request, url = new URL(req.url);
  if (req.method !== 'GET' || url.origin !== location.origin) return;
  // هرگز API، پرداخت و ... کش نشود
  if (/^\/(api|payment|broadcasting|livewire)/.test(url.pathname)) return;

  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).catch(() => caches.match('/offline')));
    return;
  }
  // CSS/JS: اول شبکه (همیشه آخرین نسخه)، اگر آفلاین بود از کش
  if (/\.(css|js)$/.test(url.pathname)) {
    e.respondWith(fetch(req).then(res => { if (res.ok) { const c = res.clone(); caches.open(STATIC).then(x => x.put(req, c)); } return res; }).catch(() => caches.match(req)));
    return;
  }
  // فونت و تصویر: اول کش، همزمان به‌روزرسانی پس‌زمینه
  if (/\.(woff2|png|svg|jpg|jpeg|webp)$/.test(url.pathname)) {
    e.respondWith(caches.match(req).then(hit => {
      const net = fetch(req).then(res => { if (res.ok) { const c = res.clone(); caches.open(STATIC).then(x => x.put(req, c)); } return res; }).catch(() => hit);
      return hit || net;
    }));
  }
});
