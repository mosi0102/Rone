# ورود، ثبت‌نام و خوش‌آمدگویی (سمت کاربر)

## فایل‌ها
| فایل | کار |
|---|---|
| `routes/auth.php` | مسیرهای `welcome` ، `login` (جایگزین قبلی) ، `dealer.register` |
| `layouts/auth.blade.php` | لایه‌ی مستقل (بدون هدر/ناوبری) |
| `pages/auth/welcome.blade.php` + `js/welcome.js` | ۴ اسلاید، رد کردن، ورود/مهمان/ثبت‌نام نمایشگاه |
| `pages/auth/login.blade.php` + `js/auth-login.js` | شماره ← کد ← (ورود \| تکمیل ثبت‌نام)، ورود با رمز، فراموشی رمز |
| `pages/auth/dealer.blade.php` + `js/dealer-register.js` | ثبت‌نام نمایشگاه‌دار (۴ مرحله + در انتظار تأیید) |
| `components/auth/otp-flow`, `components/auth/password` | بخش‌های مشترک |
| `js/auth.js` | هسته‌ی مشترک + **`Auth.api` نمایشی** |
| `css/auth.css` | همه‌ی استایل‌ها |

## اتصال (سه تغییر کوچک در فایل‌های مشترک)
1. `routes/web.php`: خط قدیمی `/login` را حذف و در انتها `require __DIR__.'/auth.php';` بگذارید.
2. اولین ورود → اسلاید: در `partials/head.blade.php` (فقط روی صفحه‌ی اصلی):
```html
<script>try{if(location.pathname==='/'&&!localStorage.getItem('rone_welcome')&&!/[?&]as=/.test(location.search))location.replace('{{ route('welcome') }}')}catch(e){}</script>
```
3. در `pages/dealers/join.blade.php` دکمه‌ی «ثبت‌نام» را به `route('dealer.register')` لینک کنید (ویزارد قبلی داخل همان صفحه حذف می‌شود).

## قرارداد API (بک‌اندکار در `Auth.api` وصل کند)
- `sendOtp(phone)` → `{}`
- `verifyOtp(phone, code)` → `{ isNew: bool }`
- `loginPassword(phone, password)` → `{}`
- `register({phone, first_name, last_name, password})`
- `resetPassword({phone, password})`
- `registerDealer(FormData)` → `{status:'pending'}` (فیلدها: phone, first_name, last_name, national_code, password(فقط کاربر جدید), showroom_name, city, address, types[], license, id_card)

خطا: reject با `{message}`. قوانین رمز در سرور هم بررسی شود (حداقل ۸ کاراکتر، حرف+عدد، نه رمز رایج).
دمو: کد `00000` غلط است؛ شماره‌ای که رقم آخرش فرد باشد کاربر جدید است؛ رمز `Demo1234` برای حساب‌های موجود درست است.

## رفتار
- کد ۵ رقمی، ارسال مجدد بعد از ۶۰ ثانیه، پر شدن خودکار از پیامک (WebOTP)، چسباندن کد.
- اگر کد نرسید: «ورود با رمز عبور» (در مرحله‌ی شماره و کد). رمز ضعیف در ثبت‌نام پذیرفته نمی‌شود.
- پیش‌نویس فرم نمایشگاه‌دار در `sessionStorage` می‌ماند؛ فلگ اولین ورود: `localStorage.rone_welcome`.
- تست‌شده: Chromium (موبایل ۳۹۰ و دسکتاپ)، بدون خطای JS و اسکرول افقی؛ Firefox/Safari و لاراول واقعی تست نشده.
