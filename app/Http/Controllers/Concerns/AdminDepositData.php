<?php

namespace App\Http\Controllers\Concerns;

/** داده‌ی نمایشی بیعانه‌ها (ادمین) */
trait AdminDepositData
{
    use MockAsset;

    protected function depositData()
    {
        $min = fn($m) => now()->addMinutes($m)->toIso8601String();
        $buyers = [
            ['name' => 'علی رضایی', 'phone' => '09121110011'], ['name' => 'مریم احمدی', 'phone' => '09122220022'],
            ['name' => 'حسین کریمی', 'phone' => '09123330033'], ['name' => 'سارا موسوی', 'phone' => '09124440044'],
            ['name' => 'رضا نوری', 'phone' => '09125550055'], ['name' => 'نیلوفر صادقی', 'phone' => '09126660066'],
        ];
        // [id، عنوان، دسته، نمایشگاه، تصویر، قیمت خودرو، درصد بیعانه، منبع(instant|auction)، وضعیت، ثبت(دقیقه‌ی قبل)، مهلت(دقیقه‌ی بعد)، خریدار]
        $seed = [
            [301, 'پژو ۲۰۷ اتوماتیک', 'car', 'اتوگالری هالیوود', 'car-207.jpg', 985000000, 2, 'instant', 'held', 95, 2800, 0],
            [302, 'ولوو FH500 کشنده', 'heavy', 'آریا خودروهای تجاری', 'truck-fh.jpg', 9800000000, 1.5, 'instant', 'held', 600, 1200, 2],
            [303, 'کامیون بنز ۲۶۳۱', 'heavy', 'سنگین‌خودرو البرز', 'truck-fh-a.jpg', 5800000000, 1.5, 'auction', 'held', 180, 4200, 4],
            [304, 'یاماها NMAX 155', 'bike', 'آریا موتور', 'yamaha.jpg', 385000000, 3, 'instant', 'dispute', 3100, null, 1],
            [305, 'سمند LX EF7', 'car', 'نمایشگاه پارس', 'dealer-stock-1.jpg', 480000000, 2, 'instant', 'dispute', 4200, null, 3],
            [306, 'تیبا ۲ صفر', 'car', 'اتوگالری هالیوود', 'detail-t1.jpg', 370000000, 2, 'auction', 'released', 6000, null, 5],
            [307, 'رنو تندر ۹۰', 'car', 'نمایشگاه پارس', 'detail-t3.jpg', 560000000, 2, 'instant', 'released', 9000, null, 0],
            [308, 'موتور هوندا CBR 250', 'bike', 'موتور سیتی', 'dealer-stock-3.jpg', 360000000, 3, 'instant', 'refunded', 7000, null, 2],
            [309, 'هیوندای النترا', 'car', 'نمایشگاه نگین', 'detail-main.jpg', 1700000000, 2, 'auction', 'refunded', 11000, null, 4],
            [310, 'پژو ۲۰۷ اتوماتیک', 'car', 'اتوگالری هالیوود', 'car-207-a.jpg', 990000000, 2, 'instant', 'held', 30, 2900, 5],
        ];
        return array_map(function ($r) use ($buyers, $min) {
            [$id, $title, $cat, $dealer, $image, $price, $pct, $src, $st, $agoM, $leftM, $b] = $r;
            $amount = (int)round($price * $pct / 100);
            $events = [['t' => $min(-$agoM), 'label' => 'بیعانه از کیف پول خریدار کسر و در امانت قرار گرفت', 'tone' => 'info']];
            if ($st === 'released') $events[] = ['t' => $min(-($agoM - 2400)), 'label' => 'معامله تأیید شد و بیعانه به کیف پول نمایشگاه‌دار منتقل شد', 'tone' => 'ok'];
            if ($st === 'refunded') $events[] = ['t' => $min(-($agoM - 1500)), 'label' => 'معامله انجام نشد و بیعانه به کیف پول خریدار برگشت', 'tone' => 'warn'];
            if ($st === 'dispute') $events[] = ['t' => $min(-($agoM - 1300)), 'label' => 'اختلاف ثبت شد؛ بیعانه تا تعیین تکلیف مسدود است', 'tone' => 'bad'];
            return [
                'id' => $id, 'code' => 'DP-' . (5200 + $id), 'title' => $title, 'cat' => $cat, 'dealer' => $dealer, 'image' => $this->img($image),
                'price' => $price, 'percent' => $pct, 'amount' => $amount, 'source' => $src, 'status' => $st,
                'created_at' => $min(-$agoM), 'hold_until' => $leftM !== null ? $min($leftM) : null,
                'buyer' => $buyers[$b], 'events' => array_reverse($events), 'listing_code' => 31000 + $id,
            ];
        }, $seed);
    }
}
