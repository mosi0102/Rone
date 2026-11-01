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
            'badges' => [
                'admin.listings.index' => 12,
                'admin.disputes.index' => 3,
                'admin.dealers.index' => 4,
                'admin.settlements.index' => 5,
            ],
        ]);
    }
}
