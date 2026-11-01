<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function wallet()
    {
        return view('pages.soon', ['title' => 'کیف پول']);
    }

    public function favorites()
    {
        return view('pages.soon', ['title' => 'نشان‌شده‌ها']);
    }

    public function help()
    {
        return view('pages.soon', ['title' => 'راهنما']);
    }

    public function notifications()
    {
        return view('pages.soon', ['title' => 'اعلان‌ها']);
    }
}
