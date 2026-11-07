<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ListingData;

class FavoriteController extends Controller
{
    use ListingData;

    /** نشان‌شده‌ها (نمایشی: چند آگهی نمونه) */
    public function index()
    {
        $items = array_map(fn ($r) => array_merge($this->listingCardItem($r), ['city' => $r['city'], 'favorited' => true, 'featured' => false]), array_slice($this->listingStore(), 0, 6));

        return view('pages.account.favorites', ['items' => $items]);
    }
}
