# نقشه‌ی API برای نسخه‌ی اندروید (Kotlin) — مرحله به مرحله

رابط اندروید برای کاربر و مشاور املاک (یک اپ، سه نوع کاربر: مهمان / کاربر / مشاور املاک). هر مرحله پس از دریافت API آن ساخته و تست می‌شود.

## قواعد مشترک (قبل از مرحله‌ی ۱ نهایی شود)
- مسیر پایه `/api/v1`، پاسخ JSON، توکن `Authorization: Bearer` (Sanctum یا مشابه).
- پاسخ موفق `{ "data": … , "meta": { "page", "per_page", "total", "has_more" } }`؛ خطا `{ "message", "errors": { "field": ["…"] }, "code" }` با کدهای HTTP استاندارد (401 ورود لازم، 403 احراز هویت تأییدنشده با `code: "profile_required"`، 422 اعتبارسنجی).
- پول: عدد صحیح به تومان. تاریخ: ISO 8601 با منطقه‌ی زمانی (نمایش شمسی و ارقام فارسی سمت اپ). تصاویر: آدرس کامل. وضعیت‌ها: رشته‌های ثابت انگلیسی (`pending|approved|rejected` …).
- دسته‌ها: `car | heavy | bike | bicycle`. نوع فروش: `instant | auction | null`.
- نسخه‌بندی و حداقل نسخه‌ی پشتیبانی‌شده‌ی اپ (`GET /app/config`: حداقل نسخه، درصد بیعانه هر دسته، حداقل شارژ/برداشت، شهرها).

## مرحله ۱ — ورود و کاربر
- `POST /auth/otp/send` (mobile) · `POST /auth/otp/verify` (mobile, code) → `{ token, is_new }`
- `POST /auth/register` (first_name, last_name, password) · `POST /auth/login` (mobile, password) · `POST /auth/logout`
- `GET /me` → `name, phone, wallet, is_agency, agency{id,name,…}, profile_status (none|pending|approved|rejected), verified`
- `POST /devices` (fcm_token, platform) برای اعلان

## مرحله ۲ — کاتالوگ عمومی (مهمان هم می‌بیند)
- `GET /home` (مزایده‌های نزدیک، فروش فوری، تازه‌ها، دسته‌ها، بنر)
- `GET /listings` (فیلترها: category, brand, model, city, price_min/max, year_from/to, km_max, gearbox, fuel, btype, wheel …؛ sort؛ page) · `GET /listings/count` · `GET /listings/filters-meta?category=` · `GET /search/suggest?q=`
- `GET /listings/{id}` (مشخصات، تصاویر، آژانس، مشابه، `deposit_amount`, `service_fee`)
- `GET /agencies` · `GET /agencies/{id}` (+ `?tab=` و آگهی‌ها)

## مرحله ۳ — مزایده
- `GET /auctions` · `GET /auctions/{id}` (پیشنهادها، زمان‌ها، وضعیت) · `POST /auctions/{id}/entry` (پرداخت ورودیه از کیف پول)
- `POST /auctions/{id}/bids` (amount)
- زنده‌بودن: WebSocket/SSE (`auction.{id}`: bid جدید، تغییر وضعیت، زمان پایان) یا حداقل polling هر ۲ ثانیه.

## مرحله ۴ — بیعانه و معامله (نیازمند verified)
- `POST /listings/{id}/deposit` · `GET /deals` · `GET /deals/{id}` · `POST /deals/{id}/confirm` · `POST /deals/{id}/dispute` (reason, note)

## مرحله ۵ — پروفایل و کیف پول
- `GET /profile` · `POST /profile` (multipart: first_name, last_name, national_code, birth_date, national_card, selfie_with_card, iban, card_number) → `profile_status`
- `GET /wallet` (balance, held, tx با صفحه‌بندی) · `POST /wallet/charge` → آدرس درگاه (مرورگر داخلی/Custom Tab) و بازگشت با deep link · `POST /wallet/withdraw`

## مرحله ۶ — گفتگو
- `GET /chats` · `GET /chats/{id}/messages` · `POST /chats/{id}/messages` (422 با `hit: phone|link|offsite` طبق قوانین) · `POST /chats/{id}/report` · `POST /chats/start` (listing_id)
- پیام لحظه‌ای: WebSocket/FCM.

## مرحله ۷ — رادار، نشان‌شده‌ها، اعلان، راهنما
- رادار: `GET/POST /radar` · `GET /radar/{id}` (تطابق‌ها با درصد) · `DELETE /radar/{id}` · `POST /radar/preview`
- `GET /favorites` · `PUT/DELETE /favorites/{listing_id}`
- `GET /notifications` · `POST /notifications/read` (ids | all)
- `GET /help` (پرسش‌ها) — می‌تواند ثابت داخل اپ باشد.

## مرحله ۸ — مشاور املاک
- ثبت‌نام و وضعیت: `POST /agency/register` (multipart) · `GET /agency/status`
- پنل: `GET /agency/dashboard` · آگهی‌ها: `GET/POST/PUT/DELETE /agency/listings` (آپلود چندتصویر)
- مزایده‌ی جدید: `POST /agency/auctions` · مزایده‌های من
- درخواست مشتریان (رادار): `GET /agency/radar` (فیلتر) · `POST /agency/radar/{id}/offer`
- مالی: `GET /agency/finance` · `POST /agency/settlements` · `GET /agency/settlements`
- اشتراک: `GET /agency/plans` · `POST /agency/subscription`
