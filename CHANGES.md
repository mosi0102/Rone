# مرحله ۷ — مزایده‌ها (دید کاربر + دید نمایشگاه‌دار)

## صفحه‌ها
| مسیر | نام | توضیح |
|---|---|---|
| /auctions | auctions.index | فهرست مزایده‌ها: فیلتر دسته و وضعیت (سمت مرورگر)، شمارنده، دکمه پرداخت ورودی |
| /auctions/{id} | auctions.show | جزئیات: قبل از شروع · در حال برگزاری · پایان‌یافته؛ برای کاربر، مهمان و صاحب مزایده |
| /auctions/{id}/entry | auctions.entry | پرداخت ورودی همین مزایده + شرایط + حالت موفق |
| /panel/auctions/create (?edit=ID) | auctions.create | ساخت/ویرایش مزایده (نمایشگاه‌دار) |

## قوانین پیاده‌شده
- شمارش معکوس فقط در ۲۴ ساعت آخر قبل از شروع فعال است؛ زودتر از آن «تاریخ شروع» نشان داده می‌شود (`<x-countdown :window="86400">`).
- مزایده زنده بدون پرداخت ورودی: بخش «پیشنهاد» و «آخرین پیشنهادها» قفل است. **داده واقعی پیشنهادها اصلاً در HTML نمی‌آید** (فقط placeholder محو)؛ بک‌اند هم باید پیشنهادها را فقط برای پرداخت‌کننده برگرداند.
- ثبت پیشنهاد پیش از شروع ممکن نیست (حتی بعد از پرداخت ورودی).
- ثبت پیشنهاد: حداقل = بالاترین + گام؛ جلوی اشتباه تایپی (بیش از ۱.۵ برابر) گرفته می‌شود؛ قبل از ثبت تأیید می‌گیرد.
- صاحب مزایده نمی‌تواند پیشنهاد بدهد؛ پنل مدیریت دارد (شرکت‌کننده، پیشنهاد، درآمد ورودی، ویرایش/لغو قبل از شروع).
- پایان: برنده → مهلت پرداخت بیعانه با شمارنده (و هشدار محرومیت)؛ بقیه پیام پایان.

## اتصال بک‌اند / Real-time (public/js/auction.js)
- ارسال پیشنهاد: `$(document).on('app:bid-submit', (e, auctionId, amount) => …)` ← POST بزنید، در پاسخ موفق `Auction.addBid({amount, mine:true, time})`
- پیشنهاد دیگران از WebSocket/Echo: `Auction.addBid({amount, user:'کاربر ۴۲۰۰۰', time:'۱۹:۱۰'})`
- شبیه‌ساز نمایشی را با حذف `data-demo` از `#bidBox` / `#bidFeed` خاموش کنید.
- رویدادها: `app:auction-pay(id,fee)` · `app:auction-create({edit,listing})` · `app:auction-cancel(id)` · `auction:bid`
- پایان/شروع شمارنده → یک رفرش (حداکثر هر ۳۰ ثانیه).

## داده‌ها
auction: id,status(scheduled|live|ended),category(car|heavy|bike),title,short_title,subtitle,year,km,extra,image,images[],listing_id,listing_url,dealer{id,name,area,active_count,verified,cover,url},start_price,original_price,entry_fee,min_step,top_bid,bids_count,participants,starts_at,ends_at,paid(کاربر ورودی را پرداخته؟),my_last_bid,bids[{amount,user,time,mine}],won,deposit_deadline
لیست: `dealer` رشته نام + `dealer_id` (برای تشخیص «مزایده شما»).
پیش‌نمایش حالت‌ها: `/auctions/11?state=live&paid=1` · `?state=ended&won=1` · `?paid=1`

## سایر
- `x-countdown`: پراپ‌های `big` و `window`؛ `x-countdown-card`، `x-auction-card`، `x-bid-list` جدید.
- `App.jfmt` / `data-jfmt="date|time|datetime"` برای «۱۴۰۵/۰۷/۲۰ • ۱۸:۰۰».
- تب «مزایده‌ها»ی پنل نمایشگاه به صفحه جزئیات هر مزایده لینک شد.
