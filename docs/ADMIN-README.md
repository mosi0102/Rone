# پنل مدیریت — راهنمای بک‌اندکار

فرانت‌اند پنل کامل است؛ داده‌ها نمایشی‌اند (در `routes/admin.php`). هر مسیر را با کنترلر واقعی جایگزین کنید و **نام مسیرها (`admin.*`) را تغییر ندهید**.

## نصب
1. فایل‌ها را روی پروژه باز کنید (مسیرها همان ساختار پروژه است).
2. در انتهای `routes/web.php`: `require __DIR__ . '/admin.php';`
3. `php artisan optimize:clear`
4. پوشه‌ی استاتیک: `public/admin-assets` (نه `public/admin`، چون با آدرس `/admin` تداخل دارد).
5. وابستگی‌ها از پوشه‌ی سایت: `vendor/bootstrap` (RTL)، `vendor/jquery`، `fonts/Vazirmatn-Variable.woff2`.

## امنیت (بک‌اندکار)
- روی گروه مسیرها (به‌جز `login`) میدل‌ور احراز هویت + نقش ادمین بگذارید.
- همه‌ی اقدام‌های تغییردهنده (تأیید، رد، بن، آزادسازی بیعانه…) باید سمت سرور اعتبارسنجی و در «گزارش فعالیت» ثبت شوند.
- فرم‌ها `@csrf` دارند؛ درخواست‌های AJAX از `meta[name=csrf-token]` استفاده کنند.

## مسیرها
| صفحه | مسیر | نام |
|---|---|---|
| ورود / خروج | `/admin/login`، `/admin/logout` | `admin.login`، `admin.logout` |
| داشبورد | `/admin` | `admin.dashboard` |
| آگهی‌ها | `/admin/listings` | `admin.listings.index` |
| مزایده‌ها | `/admin/auctions`، `/admin/auctions/{id}` | `admin.auctions.index/show` |
| درخواست‌های خرید | `/admin/requests`، `/{id}` | `admin.requests.index/show` |
| بیعانه‌ها | `/admin/deposits`، `/{id}` | `admin.deposits.index/show` |
| اختلاف‌ها | `/admin/disputes`، `/{id}` | `admin.disputes.index/show` |
| نمایشگاه‌داران | `/admin/dealers`، `/{id}` | `admin.dealers.index/show` |
| کاربران | `/admin/users`، `/{id}` | `admin.users.index/show` |
| مالی | `/admin/finance` | `admin.finance.index` |
| تسویه‌ها | `/admin/settlements` | `admin.settlements.index` |
| گفتگوها | `/admin/chats` | `admin.chats.index` |
| تنظیمات تجاری | `/admin/settings` | `admin.settings.index` |
| داده‌های پایه | `/admin/catalog` | `admin.catalog.index` |
| صفحات و محتوا | `/admin/content` | `admin.content.index` |
| گزارش فعالیت | `/admin/logs` | `admin.logs.index` |

## ساختار داده‌ی هر صفحه
شکل آرایه‌ها را از closure مربوط در `routes/admin.php` بخوانید: `$auctionData`، `$depositData`، `$disputeData`، `$dealerData`، `$userData`، `$requestData`، `$txData`، `$settleData`، `$chatData`، `$settingsData`، `$catalogData`، `$pageData`، `$logData`. کلیدها همان چیزی است که Blade می‌خواند؛ می‌توانید Eloquent را در همین شکل به Blade بدهید.
- مبلغ‌ها: عدد صحیح تومان. تاریخ‌ها: ISO‌8601؛ نمایش شمسی را `admin.js` انجام می‌دهد (`data-jdate`).
- لیست‌ها فعلاً کامل رندر و در مرورگر فیلتر می‌شوند (`[data-filterable]`). با داده‌ی زیاد، فیلتر/جستجو را سمت سرور انجام دهید و `<x-admin.pagination>` را با `->links()` عوض کنید (این نقطه‌ها در Blade کامنت شده‌اند).

## وضعیت‌ها (باید با enum بک‌اند هم‌نام شوند)
`x-admin.status` و `STATUS` در `admin.js` را همزمان تغییر دهید.
آگهی: `active pending rejected suspended expired sold` • نوع فروش: `normal instant auction` • مزایده: `live scheduled ended awaiting_pay defaulted cancelled` • بیعانه: `held released refunded dispute` • اختلاف: `open review resolved` • نمایشگاه: `pending verified suspended rejected` • کاربر: `active banned` • تسویه: `unpaid paid rejected` • گفتگو: `normal flagged reviewed`.

## اتصال اقدام‌ها (نقطه‌های اصلی)
اکنون اقدام‌ها فقط DOM را عوض می‌کنند و toast نشان می‌دهند. برای واقعی‌شدن:
- دکمه‌های `data-confirm`: پس از تأیید، رویداد `admin:confirmed` روی همان دکمه فایر می‌شود → اینجا `fetch/$.ajax` بزنید و در موفقیت UI را تغییر دهید.
- اقدام‌های دارای دلیل: `Admin.askReason('modalId', 'عنوان').then(function (reason) {...})`.
- فایل‌ها: `listings.js`، `auctions.js`، `auction-show.js`، `deposits.js`، `disputes.js`، `people.js`، `chats.js`، `catalog.js`، `content.js`، `settings.js`، `requests.js` — هر کدام در بالا توضیح «بک‌اندکار» دارند.
- مزایده‌ی زنده: در `auction-show.js` بخش «شبیه‌ساز» را حذف و پیام WebSocket/SSE را به `addBid(user, amount)` بدهید.
- تنظیمات: نام فیلدها (`deposit[car]`، `matching[w][price]`…) ثابت است؛ `action` فرم را وصل کنید.
- تطابق درخواست‌های خرید و تشخیص شماره/لینک در گفتگو سمت بک‌اند محاسبه شود (قالب فقط نمایش می‌دهد).

## API جاوااسکریپت (`window.Admin`)
`toast(msg, type)` • `confirm({title,text,ok,danger})` • `askReason(modalId, subtitle)` • `statusHtml(status)` • `money(n)` • `toFa(v)` • `jfmt(date, 'date|time')` • `refreshFilterable($root)`.

## رنگ و ظاهر
همه‌ی رنگ‌ها توکن‌های بالای `admin.css` هستند (`--primary: #1b4d9e` هم‌رنگ اپلیکیشن). فقط همان‌جا تغییر دهید.

## تست‌شده
Chromium (دسکتاپ ۱۴۴۰، تبلت ۱۰۲۴، موبایل ۳۹۰): بدون خطای JS، بدون اسکرول افقی، کنتراست متن ≥ ۴٫۵:۱، برچسب برای همه‌ی فیلدها و دکمه‌ها. روی Laravel واقعی و Firefox/Safari اجرا نشده؛ پس از اتصال، یک بار روی آن‌ها بررسی کنید.
