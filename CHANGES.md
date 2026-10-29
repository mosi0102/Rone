# مرحله ۱۲ — کلید نقش + جست‌وجو، فیلتر و لیست آگهی‌ها

## ⚠️ حذف فایل‌های قدیمی
- `resources/views/pages/search.blade.php` و `public/css/search.css` (صفحه جست‌وجوی قدیمی؛ جایگزین شد)

## ۱) ورود به صفحه‌های نمایشگاه‌دار (کلید نقش)
بک‌اند هنوز وصل نیست، پس نقش با `$auth` نمایشی تعیین می‌شود. حالا در حالت `APP_DEBUG=true` یک دکمه شناور «نقش: …» پایین صفحه هست:
مهمان · کاربر عادی · نمایشگاه‌دار (حالت خریدار) · **نمایشگاه‌دار (حالت نمایشگاه)** ← پنل، ثبت آگهی، مزایده‌های من، معاملات.
همچنین در صفحه ورود (OTP) دو دکمه «ورود نمایشی» اضافه شد. (معادل دستی: `?as=dealer&mode=dealer` که در session می‌ماند.)
بک‌اندکار: `partials/dev-switcher`، دکمه‌های ورود نمایشی و بلوک `View::composer` در routes را حذف و `$auth` واقعی را جایگزین کند.

## ۲) صفحه نتایج (جست‌وجو و همه آگهی‌ها)
| مسیر | نام | توضیح |
|---|---|---|
| /search?q= | search | «جست‌وجوی خودرو» (scope=dealers به لیست نمایشگاه‌ها می‌رود) |
| /listings | listings.index | «آگهی‌ها» — همان صفحه، بدون عبارت جست‌وجو |
| /listings/filters | listings.filters | صفحه «فیلترها» (موبایل)؛ در دسکتاپ ستون کناری ثابت |
| /listings/count | listings.count | JSON شمارش زنده برای دکمه «نمایش N آگهی» |
- تب دسته (سواری/سنگین/موتورسیکلت؛ لمس دوباره = برداشتن)، کادر جست‌وجو با پیشنهاد (جست‌وجوهای اخیر در localStorage + پرطرفدار)، دکمه فیلترها با تعداد، مرتب‌سازی، چیپ‌های فیلتر فعال (× = حذف)، «حذف فیلترها»، «نمایش آگهی‌های بیشتر».
- آگهی ویژه = کارت بزرگ (`x-listing-feature`)، بقیه ردیف (`x-listing-row`).
- لینک‌های قبلی کار می‌کنند: `?category=heavy`، `?type=instant` (فروش فوری)، `?dealer=ID`.
## ۳) فیلتر (`x-filter-form`)
نوع ← برند ← مدل، شهر، بازه قیمت با اسلایدر دوسر (همگام با ورودی‌ها)، **مشخصات فنی بر اساس دسته** (سنگین: آرایش محور، وزن ناخالص…؛ سواری: رنگ، وضعیت بدنه…؛ موتور: حجم، نوع)، فقط دارای عکس، نوع آگهی (همه/فروش فوری/مزایده). فیلدهای خالی در URL نمی‌آیند.

## سمت نمایشگاه‌دار (مقابل)
در حالت نمایشگاه: فیلتر «فقط آگهی‌های نمایشگاه من» و دکمه «ویرایش» روی آگهی‌های خودش در نتایج.

## قرارداد بک‌اند
پارامترهای GET: category brand model city price_min price_max year_from year_to km_max gearbox fuel axle weight color body cc type with_photo sale_type(instant|auction) mine dealer q sort(new|cheap|expensive|low_km|new_model) page
View نتایج: items[{id,title,url,image,price,old_price,year,km,extra,dealer,verified,location,posted,favorited,sale_type,featured,dealer_id,category}], total, chips[{label,url}], tabs[{title,on,url}], sorts, sort, filter_count, filters_url, clear_url, has_more, next_url, lf (پیکربندی فیلترها: categories, brands{دسته:{برند:[مدل]}}, cities, max_price, specs[]).
«نمایش بیشتر»: همان URL با page+1 (HTML کامل؛ فقط آیتم‌های جدید اضافه می‌شود).
رویدادهای JS: app:search-submit(q) · filters:change(params)
