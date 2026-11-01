<?php

namespace App\Http\Controllers\Concerns;

/** داده‌ی نمایشی بیعانه/معامله‌ها */
trait DealData
{
    use MockAsset;

    protected function dealStore()
    {
        $hw = ['id' => 8, 'name' => 'اتوگالری هالیوود', 'area' => 'تهران، سعادت‌آباد', 'verified' => true, 'url' => route('dealers.show', 8),
            'address' => 'تهران، سعادت‌آباد، بین سعادتی ۲۰ و ۲۲، جنب بانک شهر', 'hours' => 'شنبه تا پنجشنبه ۹ تا ۲۰', 'phone' => '۰۹۱۲۴۵۶۸۹۲۳', 'map_url' => 'https://maps.google.com/?q=35.78,51.37'];
        $base = ['listing_id' => 24817, 'title' => 'پژو ۲۰۷ اتوماتیک', 'listing_url' => route('listings.show', 24817), 'image' => $this->img('dealer-stock-1.jpg'), 'year' => 1402, 'km' => 24000, 'extra' => 'سفید', 'price' => 985000000,
            'amount' => 20000000, 'fee' => 200000, 'tx' => '۸۶۴۲۱۹۷۳', 'dealer' => $hw, 'buyer' => ['name' => 'علی محمدی', 'phone' => '۰۹۱۲۳۴۵۶۷۸۹'],
            'paid_at' => now()->subHours(26), 'decide_by' => now()->addHours(46), 'decided_at' => null, 'reason' => null, 'reason_note' => null];
        return [
            101 => array_merge($base, ['id' => 101, 'status' => 'held']),
            102 => array_merge($base, ['id' => 102, 'status' => 'completed', 'listing_id' => 24819, 'title' => 'یاماها NMAX 155', 'image' => $this->img('dealer-stock-3.jpg'), 'year' => 1403, 'km' => 4500, 'extra' => 'آبی', 'price' => 385000000, 'amount' => 10000000, 'tx' => '۷۳۱۵۴۰۶۲', 'dealer' => array_merge($hw, ['id' => 21, 'name' => 'آریا موتور']), 'paid_at' => now()->subDays(6), 'decided_at' => now()->subDays(3)]),
            103 => array_merge($base, ['id' => 103, 'status' => 'refunded', 'listing_id' => 24818, 'title' => 'ولوو FH500 کشنده', 'image' => $this->img('dealer-stock-2.jpg'), 'year' => 1399, 'km' => 320000, 'extra' => 'دیزل', 'price' => 9800000000, 'amount' => 100000000, 'tx' => '۵۵۲۰۹۱۱۸', 'dealer' => array_merge($hw, ['id' => 22, 'name' => 'آریا خودروهای تجاری']), 'paid_at' => now()->subDays(9), 'decided_at' => now()->subDays(6), 'reason' => 'خودرو با توضیحات آگهی تفاوت داشت']),
            104 => array_merge($base, ['id' => 104, 'status' => 'review', 'listing_id' => 24820, 'title' => 'پژو ۲۰۶ تیپ ۲', 'image' => $this->img('dealer-stock-1.jpg'), 'year' => 1400, 'km' => 61000, 'price' => 540000000, 'amount' => 12000000, 'tx' => '۹۹۱۲۰۳۴۵', 'paid_at' => now()->subDays(3), 'decided_at' => now()->subHours(5), 'reason' => 'نمایشگاه پاسخ نداد / در دسترس نبود', 'reason_note' => 'دو روز تماس گرفتم و کسی پاسخ نداد.']),
            105 => array_merge($base, ['id' => 105, 'status' => 'completed', 'listing_id' => 24821, 'title' => 'سمند سورن', 'image' => $this->img('dealer-stock-1.jpg'), 'year' => 1401, 'km' => 38000, 'extra' => 'نقره‌ای', 'price' => 610000000, 'amount' => 15000000, 'tx' => '۴۴۱۸۷۲۰۶', 'paid_at' => now()->subDays(8), 'decided_at' => now()->subDays(5)]),
        ];
    }
}
