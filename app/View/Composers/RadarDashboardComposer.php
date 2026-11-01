<?php

namespace App\View\Composers;

use App\Http\Controllers\Concerns\RadarData;
use Illuminate\View\View;

/** درخواست‌های رادار مرتبط برای داشبورد نمایشگاه‌دار */
class RadarDashboardComposer
{
    use RadarData;

    public function compose(View $view): void
    {
        $view->with('radarRelated', count(array_filter($this->radarBuyerRequests(), fn($x) => $x['mine'] && !$x['offered'])));
    }
}
