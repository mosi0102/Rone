<?php

namespace App\Http\Controllers\Concerns;

/** آدرس تصاویر نمایشی */
trait MockAsset
{
    protected function img(string $file): string
    {
        return asset('img/mock/' . $file);
    }
}
