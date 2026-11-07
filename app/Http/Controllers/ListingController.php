<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;
use Illuminate\Http\JsonResponse;

class ListingController extends Controller
{
    use ListingData;

    public function show($id)
    {
        $rows = collect($this->listingStore());
        $r = $rows->firstWhere('id', (int) $id) ?? $rows->first();
        $isRent = ! is_null($r['rent']);
        $mode = $this->propertyCategories()[$r['cat']]['price_mode'];
        $yesNo = fn (bool $v) => $v ? 'دارد' : 'ندارد';
        $floor = $r['floor'] === 0 ? 'همکف' : 'طبقه‌ی '.$this->fa($r['floor']);

        $specs = [
            ['icon' => 'ruler', 'label' => 'متراژ', 'value' => $this->fa(number_format($r['area'])).' متر'],
            ['icon' => 'bed', 'label' => 'اتاق', 'value' => $r['rooms'] ? $this->fa($r['rooms']) : 'بدون اتاق'],
            $mode === 'project'
                ? ['icon' => 'hammer', 'label' => 'مرحله‌ی ساخت', 'value' => $r['stage']]
                : ['icon' => 'calendar', 'label' => 'سال ساخت', 'value' => $this->fa($r['year'])],
            ['icon' => 'layers', 'label' => 'طبقه', 'value' => $floor],
            ['icon' => 'car', 'label' => 'پارکینگ', 'value' => $yesNo($r['parking'])],
            ['icon' => 'elevator', 'label' => 'آسانسور', 'value' => $yesNo($r['elevator'])],
        ];
        $ok = fn (bool $v) => $v ? 'ok' : 'bad';
        $details = array_values(array_filter([
            ['label' => 'نوع ملک', 'value' => $r['type']],
            $r['deed'] ? ['label' => 'سند', 'value' => $r['deed'], 'status' => 'ok'] : null,
            ['label' => 'انباری', 'value' => $yesNo($r['storage']), 'status' => $ok($r['storage'])],
            ['label' => 'جهت ساختمان', 'value' => 'شمالی'],
            $mode === 'short' ? ['label' => 'ظرفیت', 'value' => 'تا '.$this->fa($r['capacity']).' نفر'] : null,
            $isRent ? ['label' => 'قابل تبدیل رهن و اجاره', 'value' => 'بله', 'status' => 'ok'] : null,
        ]));
        $note = match ($mode) {
            'rent' => 'رهن و اجاره • قابل تبدیل',
            'short' => 'اجاره‌ی هر شب • حداقل ۲ شب',
            'project' => 'قیمت هر متر • پرداخت اقساطی',
            default => 'قیمت کل • قابل مذاکره',
        };

        return view('pages.listings.show', [
            'wallet' => 120000000, // موجودی کیف پول کاربر (برای تست حالت کمبود موجودی عدد را کم کنید)
            'property' => (object) [
                'id' => $r['id'], 'code' => $r['id'], 'title' => $r['title'], 'subtitle' => $r['type'].' • '.$this->fa($r['area']).' متر • '.$r['district'], 'sale_type' => $r['sale'], 'category' => $r['cat'],
                'price' => $r['price'], 'rent' => $r['rent'], 'price_label' => $this->priceLabel($r['cat']), 'price_note' => $note,
                'price_per_meter' => $mode === 'sale' ? (int) round($r['price'] / max(1, $r['area']) / 100000) * 100000 : null,
                'location' => $r['location'], 'address' => $r['address'], 'lat' => $r['lat'], 'lng' => $r['lng'], 'city' => $r['city'], 'views' => 326, 'favorited' => false,
                'images' => [
                    ['src' => $r['image'], 'thumb' => $r['image']],
                    ['src' => $this->img('detail-t1.jpg'), 'thumb' => $this->img('detail-t1.jpg')],
                    ['src' => $this->img('detail-t2.jpg'), 'thumb' => $this->img('detail-t2.jpg')],
                    ['src' => $this->img('detail-t3.jpg'), 'thumb' => $this->img('detail-t3.jpg')],
                ],
                'specs' => $specs,
                'details' => $details,
                'description' => 'ملک در موقعیت عالی و دسترسی آسان به مترو و مراکز خرید قرار دارد. نورگیر، کم‌واحد و با مشاعات تمیز؛ بازدید با هماهنگی قبلی. قرارداد در آژانس با کد رهگیری تنظیم می‌شود.',
                'agency' => ['id' => $r['agency_id'], 'name' => $r['agency'], 'area' => $r['location'], 'active_count' => 12, 'verified' => true, 'cover' => $this->img('detail-banner.jpg'), 'url' => route('agencies.show', $r['agency_id']), 'phone' => '۰۲۱-۲۲۳۴۵۶۷۸'],
            ],
            'similar' => $rows->where('cat', $r['cat'])->where('id', '!=', $r['id'])->take(3)->map(fn ($x) => $this->listingCardItem($x))->values()->all(),
        ]);
    }

    public function search()
    {
        if (request('scope') === 'agencies') {
            return redirect(route('agencies.index', array_filter(['q' => request('q'), 'city' => request('city')])));
        }

        return $this->listingPage('search');
    }

    public function index()
    {
        return $this->listingPage('list');
    }

    public function filters()
    {
        $lf = $this->lf();
        $q = array_filter(request()->query(), fn ($v) => $v !== null && $v !== '');

        return view('pages.listings.filters', ['f' => $q, 'lf' => $lf, 'count' => $this->listingFilter($this->listingStore(), $q)->count(), 'back' => route(isset($q['q']) ? 'search' : 'listings.index', $q)]);
    }

    public function count()
    {
        // شمارش زنده برای دکمه «نمایش N آگهی»
        $q = array_filter(request()->query(), fn ($v) => $v !== null && $v !== '');

        return response()->json(['count' => $this->listingFilter($this->listingStore(), $q)->count()]);
    }

    /** مشاهده‌ی آگهی‌ها روی نقشه (فیلتر: category، city، sale_type و همه‌ی فیلترهای فهرست) */
    public function map()
    {
        $q = array_filter(request()->only(['category', 'city', 'sale_type', 'ptype', 'district', 'q', 'agency']), fn ($v) => $v !== null && $v !== '');

        return view('pages.listings.map', ['lf' => $this->lf(), 'query' => $q]);
    }

    /**
     * JSON نشانگرهای نقشه. بک‌اند: با داده‌ی زیاد، فیلتر محدوده‌ی نقشه (bounds) و خوشه‌بندی سمت سرور اضافه شود.
     *
     * @return JsonResponse {total, items[{id,title,url,image,lat,lng,price_short,price_text,meta,location,sale_type,category}]}
     */
    public function mapData()
    {
        $q = array_filter(request()->query(), fn ($v) => $v !== null && $v !== '');
        $rows = $this->listingFilter($this->listingStore(), $q);
        $items = $rows->map(function ($r) {
            $mode = $this->propertyCategories()[$r['cat']]['price_mode'];
            $short = match ($mode) {
                'rent' => 'رهن '.$this->human($r['price']),
                'short' => $this->human($r['price']).' / شب',
                'project' => $this->human($r['price']).' / متر',
                default => $this->human($r['price']),
            };
            $text = match ($mode) {
                'rent' => 'رهن '.$this->human($r['price']).' تومان'.($r['rent'] ? ' • اجاره '.$this->human($r['rent']) : ' • رهن کامل'),
                default => $this->priceLabel($r['cat']).': '.$this->human($r['price']).' تومان',
            };
            $meta = array_filter([$this->fa($r['area']).' متر', $r['rooms'] ? $this->fa($r['rooms']).' اتاق' : null, $r['cat'] === 'project' ? $r['stage'] : 'ساخت '.$this->fa($r['year'])]);

            return [
                'id' => $r['id'], 'title' => $r['title'], 'url' => route('listings.show', $r['id']), 'image' => $r['image'], 'lat' => $r['lat'], 'lng' => $r['lng'],
                'price_short' => $short, 'price_text' => $text, 'meta' => implode(' • ', $meta), 'location' => $r['location'].' • '.$r['agency'],
                'sale_type' => $r['sale'], 'category' => $r['cat'],
            ];
        })->values();

        return response()->json(['total' => $items->count(), 'items' => $items]);
    }
}
