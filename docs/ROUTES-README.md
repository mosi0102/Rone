# ساختار مسیرها و کنترلرها

فایل‌های `routes/*.php` فقط آدرس هستند (`Route::get(url, [Controller::class, 'متد'])->name(...)`). نام همه‌ی route ها بدون تغییر است.

| فایل route | کنترلر |
|---|---|
| `web.php` | Home, Dealer, Listing, Auth, Panel, Auction, Deal, Page |
| `auth.php` | AuthController |
| `radar.php` | RadarController |
| `chat.php` | ChatController |
| `admin.php` | `Admin\*` (Dashboard, Listing, Auction, Request, Deposit, Dispute, Dealer, User, Finance, Chat, Settings, Content, Auth) |

- داده‌ی نمایشی مشترک: `app/Http/Controllers/Concerns/` (`ListingData`, `DealData`, `RadarData`, `AdminDepositData`, `MockAsset`).
- `$auth` نمایشی، داده‌ی ادمین و رادار داشبورد: `app/View/Composers/` (ثبت در `AppServiceProvider::boot`).
- بک‌اندکار: بدنه‌ی هر متد کنترلر را با Model/Service واقعی جایگزین کند؛ نام متدها و کلیدهای خروجی view را نگه دارد.
- `routes/admin.php` قبلی (نسخه‌ی قدیمی) با این نسخه جایگزین می‌شود و در انتهای `web.php` require شده است.
