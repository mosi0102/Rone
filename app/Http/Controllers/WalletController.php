<?php

namespace App\Http\Controllers;

use App\Support\DemoAuth;

class WalletController extends Controller
{
    private const MIN_CHARGE = 100000;
    private const MIN_WITHDRAW = 1000000;

    /** کیف پول: موجودی، شارژ، برداشت و تراکنش‌ها */
    public function index()
    {
        $auth = DemoAuth::get();
        return view('pages.account.wallet', [
            'tab' => in_array(request('tab'), ['charge', 'withdraw', 'tx'], true) ? request('tab') : 'tx',
            'balance' => $auth->wallet,
            'held' => 49500000,                      // بیعانه‌های در انتظار (قفل‌شده در امانت)
            'min_charge' => self::MIN_CHARGE,
            'min_withdraw' => self::MIN_WITHDRAW,
            'quick' => [5000000, 10000000, 20000000, 50000000],
            // حساب مقصد برداشت (بک‌اندکار: از اطلاعات بانکی تأییدشده؛ شبا ماسک‌شده)
            'bank' => (object)['holder' => $auth->name, 'iban' => 'IR12 **** **** **** **** **52 14', 'bank' => 'بانک ملی'],
            'done' => request('done'),               // charged | withdrawn
            'amount' => (int)request('amount'),
            'tx' => [
                ['id' => 1, 'title' => 'شارژ کیف پول (درگاه پرداخت)', 'amount' => 50000000, 'in' => true, 'kind' => 'charge', 'date' => now()->subHours(3)],
                ['id' => 2, 'title' => 'بیعانه «پژو ۲۰۷ اتوماتیک»', 'amount' => 20200000, 'in' => false, 'kind' => 'deposit', 'date' => now()->subDays(1)],
                ['id' => 3, 'title' => 'ورودیه‌ی مزایده «ولوو FH500»', 'amount' => 50000, 'in' => false, 'kind' => 'auction', 'date' => now()->subDays(2)],
                ['id' => 4, 'title' => 'بازگشت بیعانه «سمند LX»', 'amount' => 15000000, 'in' => true, 'kind' => 'refund', 'date' => now()->subDays(4)],
                ['id' => 5, 'title' => 'برداشت به حساب بانکی', 'amount' => 30000000, 'in' => false, 'kind' => 'withdraw', 'date' => now()->subDays(6)],
                ['id' => 6, 'title' => 'شارژ کیف پول (درگاه پرداخت)', 'amount' => 100000000, 'in' => true, 'kind' => 'charge', 'date' => now()->subDays(9)],
            ],
        ]);
    }

    /** شارژ (نمایشی). بک‌اندکار: ساخت تراکنش و هدایت به درگاه؛ فقط کاربر احراز‌شده. */
    public function charge()
    {
        if ($r = $this->requireVerified()) return $r;
        $amount = (int)preg_replace('/\D/', '', (string)request()->input('amount'));
        if ($amount < self::MIN_CHARGE) return redirect()->route('account.wallet', ['tab' => 'charge', 'error' => 'min']);
        return redirect()->route('account.wallet', ['done' => 'charged', 'amount' => $amount]);
    }

    /** برداشت (نمایشی). بک‌اندکار: بررسی موجودی قابل برداشت و ثبت درخواست تسویه؛ فقط کاربر احراز‌شده. */
    public function withdraw()
    {
        if ($r = $this->requireVerified()) return $r;
        $amount = (int)preg_replace('/\D/', '', (string)request()->input('amount'));
        if ($amount < self::MIN_WITHDRAW || $amount > DemoAuth::get()->wallet) return redirect()->route('account.wallet', ['tab' => 'withdraw', 'error' => 'amount']);
        return redirect()->route('account.wallet', ['done' => 'withdrawn', 'amount' => $amount]);
    }
}
