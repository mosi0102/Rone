<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ListingData;
use App\Http\Controllers\Controller;

class ListingController extends Controller
{
    use ListingData;

    public function index()
    {
        $desc = 'ملک نورگیر و کم‌واحد، دسترسی عالی به مترو و مراکز خرید؛ بازدید با هماهنگی قبلی در آژانس.';
        // وضعیت‌ها برای نمایش همه‌ی حالت‌های پنل (از بک‌اند: status و reason)
        $statuses = ['pending', 'pending', 'pending', 'active', 'active', 'active', 'active', 'rejected', 'suspended', 'sold', 'pending', 'expired', 'active', 'active', 'pending', 'active'];
        $reasons = [7 => 'تصاویر نامناسب یا ناکافی'];
        $listings = [];
        foreach (array_slice($this->listingStore(), 0, 16) as $i => $r) {
            $specs = [['نوع ملک', $r['type']], ['متراژ', number_format($r['area']).' متر'], ['اتاق', $r['rooms'] ?: 'بدون اتاق']];
            $specs[] = $r['cat'] === 'project' ? ['مرحله‌ی ساخت', $r['stage']] : ['سال ساخت', $r['year']];
            $specs[] = ['آدرس', $r['address']];
            $specs[] = ['مختصات', $r['lat'].', '.$r['lng']];
            $specs[] = ! is_null($r['rent']) ? ['اجاره‌ی ماهانه', $r['rent'] ? number_format($r['rent']).' تومان' : 'رهن کامل'] : ['سند', $r['deed'] ?? '—'];
            $listings[] = [
                'id' => $r['id'], 'code' => $r['id'], 'title' => $r['title'], 'cat' => $r['cat'], 'price' => $r['price'], 'sale' => $r['sale'] ?? 'normal', 'status' => $statuses[$i],
                'agency' => $r['agency'], 'city' => $r['city'], 'posted' => $r['posted'], 'reason' => $reasons[$i] ?? '', 'desc' => $desc,
                'map_url' => 'https://www.openstreetmap.org/?mlat='.$r['lat'].'&mlon='.$r['lng'].'#map=17/'.$r['lat'].'/'.$r['lng'],
                'images' => [$r['image'], $this->img('detail-t1.jpg'), $this->img('detail-t2.jpg'), $this->img('detail-t3.jpg')],
                'specs' => $specs,
            ];
        }

        return view('admin.pages.listings.index', ['listings' => $listings]);
    }
}
