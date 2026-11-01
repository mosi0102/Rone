<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AdminDepositData;
use App\Http\Controllers\Controller;

class DisputeController extends Controller
{
    use AdminDepositData;

    public function index()
    {
        return view('admin.pages.disputes.index', ['disputes' => $this->disputeData()]);
    }

    public function show($id)
    {
        $all = $this->disputeData();
        $x = array_values(array_filter($all, fn($r) => $r['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.disputes.show', ['x' => $x]);

    }

    protected function disputeData()
    {
        $min = fn($m) => now()->addMinutes($m)->toIso8601String();
        $deps = [];
        foreach ($this->depositData() as $d) $deps[$d['id']] = $d;
        // [id، کد بیعانه، بازکننده(buyer|dealer)، موضوع، شرح، وضعیت، ثبت(دقیقه‌ی قبل)، پاسخ طرف مقابل]
        $seed = [
            [901, 304, 'buyer', 'مغایرت خودرو با آگهی', 'موتور هنگام بازدید دچار خرابی قطعه‌ی ترمز بود و نمایشگاه‌دار آن را در آگهی ذکر نکرده بود. خواستار برگشت بیعانه هستم.', 'open', 1300, null],
            [902, 305, 'dealer', 'انصراف خریدار پس از رزرو', 'خریدار پس از دو روز هماهنگی بدون دلیل از معامله منصرف شد و خودرو را از فروش خارج کرده بودیم. درخواست آزادسازی بیعانه.', 'review', 2900, 'من با کارشناس مستقل بررسی کردم و رنگ‌شدگی دو در مخفی شده بود؛ به همین دلیل منصرف شدم.'],
            [903, 306, 'buyer', 'عدم تحویل خودرو در زمان مقرر', 'نمایشگاه‌دار سه بار قرار تحویل را به تعویق انداخت.', 'resolved', 7000, 'تحویل به دلیل مشکل مدارک بود و در نهایت انجام شد.'],
        ];
        return array_map(function ($r) use ($deps, $min) {
            [$id, $depId, $by, $subject, $text, $st, $agoM, $reply] = $r;
            $d = $deps[$depId] ?? array_values($deps)[0];
            return [
                'id' => $id, 'code' => 'DS-' . (700 + $id), 'deposit' => $d, 'by' => $by, 'subject' => $subject, 'text' => $text, 'reply' => $reply,
                'status' => $st, 'opened_at' => $min(-$agoM), 'age_hours' => (int)round($agoM / 60),
                'resolution' => $st === 'resolved' ? ['type' => 'release', 'note' => 'پس از بررسی مدارک، تحویل انجام شده بود؛ بیعانه به نمایشگاه‌دار آزاد شد.', 'at' => $min(-5000)] : null,
                'evidence' => $id === 901 ? ['detail-t1.jpg', 'detail-t2.jpg'] : ($id === 902 ? ['detail-t3.jpg'] : []),
                'messages' => [
                    ['from' => 'buyer', 'text' => 'سلام، موتور رو از نزدیک دیدم، ترمز جلو مشکل داره.', 'at' => $min(-$agoM - 120)],
                    ['from' => 'dealer', 'text' => 'در زمان بازدید مشکلی نبود، احتمالاً بعدش پیش اومده.', 'at' => $min(-$agoM - 100)],
                    ['from' => 'buyer', 'text' => 'تو آگهی چیزی از این موضوع ننوشته بودید.', 'at' => $min(-$agoM - 90)],
                ],
            ];
        }, $seed);
    }
}
