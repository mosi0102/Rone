<?php

namespace App\View\Composers;

use Illuminate\View\View;

/** داده‌ی مشترک صفحه‌های ادمین (تعداد موارد منتظر در سایدبار و اطلاعات مدیر) */
class AdminComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'admin' => ['name' => 'مدیر سایت', 'first_name' => 'مدیر'],
            // دسته‌های ملک (کلید ← [عنوان، آیکن]) — هم‌نام با app/Http/Controllers/Concerns/ListingData.php
            'propertyCats' => [
                'sale_res' => ['فروش مسکونی', 'home'], 'rent_res' => ['اجاره مسکونی', 'key'],
                'sale_com' => ['فروش تجاری', 'briefcase'], 'rent_com' => ['اجاره تجاری', 'store'],
                'project' => ['پروژه‌ها', 'hammer'], 'short' => ['کوتاه‌مدت', 'calendar'],
            ],
            'badges' => [
                'admin.listings.index' => 12,
                'admin.auctions.index' => 2,
                'admin.agencies.index' => 4,
                'admin.settlements.index' => 5,
            ],
        ]);
    }
}
