<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AdminDepositData;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{
    use AdminDepositData;

    public function index()
    {
        return view('admin.pages.deposits.index', ['deposits' => $this->depositData()]);
    }

    public function show($id)
    {
        $all = $this->depositData();
        $d = array_values(array_filter($all, fn($x) => $x['id'] === (int)$id))[0] ?? $all[0];
        return view('admin.pages.deposits.show', ['d' => $d]);

    }
}
