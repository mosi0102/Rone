<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class FavoriteController extends Controller
{
    use ListingData;

    /** نشان‌شده‌ها (نمایشی: چند آگهی نمونه) */
    public function index()
    {
        $items = array_map(fn($r) => [
            'id' => $r['id'], 'title' => $r['title'], 'url' => route('listings.show', 24817), 'image' => $r['image'], 'price' => $r['price'], 'old_price' => $r['old'],
            'year' => $r['year'], 'km' => $r['km'], 'extra' => $r['cat'] === 'heavy' ? $r['fuel'] : $r['color'], 'dealer' => $r['dealer'], 'verified' => true,
            'location' => $r['location'], 'posted' => $r['posted'], 'favorited' => true, 'sale_type' => $r['sale'], 'featured' => false, 'dealer_id' => $r['dealer_id'], 'category' => $r['cat'],
        ], array_slice($this->listingStore(), 0, 6));
        return view('pages.account.favorites', ['items' => $items]);
    }
}
