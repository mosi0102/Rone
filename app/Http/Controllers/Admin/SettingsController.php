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
            // مزایده: سایت پول ملک را نمی‌گیرد؛ برنده تا contract_hours برای قرارداد به آژانس مراجعه می‌کند
            'auction' => ['entry_fee' => 500000, 'min_step_percent' => 0.5, 'contract_hours' => 48, 'ban_days' => 30, 'extend_minutes' => 3, 'max_duration_hours' => 72],
            'pricing' => ['sub_30' => 1500000, 'sub_90' => 4000000, 'sub_365' => 14000000, 'instant_fee' => 450000, 'auction_create_fee' => 2000000],
            'matching' => ['min_percent' => 60, 'max_requests' => 5, 'w' => ['type' => 20, 'location' => 30, 'price' => 25, 'area' => 15, 'rooms' => 5, 'extra' => 5]],
            'payout' => ['min_withdraw' => 5000000, 'process_hours' => 48],
        ];
    }
}
