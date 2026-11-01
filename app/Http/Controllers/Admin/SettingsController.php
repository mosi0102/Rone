<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.pages.settings.index', ['s' => $this->settingsData()]);
    }

    protected function settingsData()
    {
        return [
            'deposit' => ['car' => 2, 'heavy' => 1.5, 'bike' => 3, 'bicycle' => 5, 'service_fee' => 200000, 'hold_hours' => 72, 'min_amount' => 500000],
            'auction' => ['entry_fee' => 200000, 'min_step_percent' => 0.5, 'pay_hours' => 24, 'ban_days' => 30, 'extend_minutes' => 3, 'max_duration_hours' => 72],
            'pricing' => ['sub_30' => 1500000, 'sub_90' => 4000000, 'sub_365' => 14000000, 'instant_fee' => 300000, 'auction_listing_fee' => 500000],
            'matching' => ['min_percent' => 60, 'max_requests' => 5, 'w' => ['model' => 30, 'price' => 25, 'year' => 15, 'mileage' => 15, 'city' => 10, 'extra' => 5]],
            'payout' => ['min_withdraw' => 5000000, 'process_hours' => 48],
        ];
    }
}
