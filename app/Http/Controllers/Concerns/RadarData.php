<?php

namespace App\Http\Controllers\Concerns;

/** داده و منطق تطابق رادار ملک (درخواست ملک ← آگهی‌های مشابه با درصد تطابق) */
trait RadarData
{
    use ListingData;

    /** کمتر از این درصد «مشابه» حساب نمی‌شود */
    protected function radarMin(): int
    {
        return 50;
    }

    /**
     * امتیاز تطابق یک آگهی با درخواست. فقط فیلدهای پرشده حساب می‌شوند؛ دسته‌ی نامطابق = ۰.
     * وزن‌ها: نوع ملک ۲۰ • شهر ۱۵ • محله ۱۵ • بودجه ۲۰ • اجاره ۱۰ • متراژ ۱۵ • اتاق ۱۰ • سال ساخت ۵ • هر امکان ۵
     *
     * @return array{0: int, 1: array<int, array{ok: bool, t: string}>}
     */
    protected function radarScore(array $q, array $row): array
    {
        if (! empty($q['category']) && $row['cat'] !== $q['category']) {
            return [0, []];
        }
        $earn = 0;
        $max = 0;
        $notes = [];
        $add = function (int $w, int $got, bool $ok, string $label) use (&$earn, &$max, &$notes) {
            $max += $w;
            $earn += $got;
            $notes[] = ['ok' => $ok, 't' => $label];
        };
        if (! empty($q['ptype'])) {
            $ok = $row['type'] === $q['ptype'];
            $add(20, $ok ? 20 : 0, $ok, $ok ? 'نوع: '.$q['ptype'] : 'نوع متفاوت ('.$row['type'].')');
        }
        if (! empty($q['city'])) {
            $ok = $row['city'] === $q['city'];
            $add(15, $ok ? 15 : 0, $ok, $ok ? 'شهر: '.$q['city'] : 'در شهر '.$row['city']);
        }
        if (! empty($q['district'])) {
            $ok = $row['district'] === $q['district'];
            $add(15, $ok ? 15 : 0, $ok, $ok ? 'محله: '.$q['district'] : 'محله‌ی '.$row['district']);
        }
        if (! empty($q['price_max'])) {
            $label = $this->priceLabel($row['cat']);
            $r = $row['price'] / max(1, (int) $q['price_max']);
            if ($r <= 1) {
                $add(20, 20, true, $label.' در بودجه‌ی شما');
            } elseif ($r <= 1.1) {
                $add(20, 14, false, $label.' '.$this->fa(round(($r - 1) * 100)).'٪ بالاتر از بودجه');
            } elseif ($r <= 1.25) {
                $add(20, 7, false, $label.' '.$this->fa(round(($r - 1) * 100)).'٪ بالاتر از بودجه');
            } else {
                $add(20, 0, false, $label.' خارج از بودجه');
            }
        }
        if (! empty($q['rent_max']) && ! is_null($row['rent'])) {
            $r = $row['rent'] / max(1, (int) $q['rent_max']);
            $add(10, $r <= 1 ? 10 : ($r <= 1.15 ? 5 : 0), $r <= 1, $r <= 1 ? 'اجاره در محدوده' : 'اجاره بیشتر از حد انتظار');
        }
        if (! empty($q['area_min'])) {
            $r = $row['area'] / max(1, (int) $q['area_min']);
            $add(15, $r >= 1 ? 15 : ($r >= 0.9 ? 9 : 0), $r >= 1, $r >= 1 ? 'متراژ '.$this->fa($row['area']).' متر' : 'متراژ کمتر ('.$this->fa($row['area']).' متر)');
        }
        if (! empty($q['rooms'])) {
            $ok = $this->roomsMatch($row['rooms'], $q['rooms']);
            $add(10, $ok ? 10 : 0, $ok, $ok ? 'تعداد اتاق مناسب' : 'تعداد اتاق متفاوت ('.$this->fa($row['rooms']).')');
        }
        if (! empty($q['year_from'])) {
            $d = max(0, (int) $q['year_from'] - $row['year']);
            $add(5, $d === 0 ? 5 : ($d <= 3 ? 3 : 0), $d === 0, $d === 0 ? 'ساخت '.$this->fa($row['year']) : 'ساخت '.$this->fa($row['year']).' (قدیمی‌تر)');
        }
        foreach (['parking' => 'پارکینگ', 'elevator' => 'آسانسور', 'storage' => 'انباری'] as $k => $label) {
            if (! empty($q[$k])) {
                $ok = (bool) $row[$k];
                $add(5, $ok ? 5 : 0, $ok, $ok ? $label.' دارد' : 'بدون '.$label);
            }
        }

        return [$max ? (int) round(100 * $earn / $max) : 60, $notes];
    }

    protected function radarMatches(array $q)
    {
        $out = [];
        foreach ($this->listingStore() as $row) {
            [$p, $notes] = $this->radarScore($q, $row);
            if ($p >= $this->radarMin()) {
                $out[] = ['row' => $row, 'percent' => $p, 'notes' => $notes];
            }
        }
        usort($out, fn ($a, $b) => $b['percent'] <=> $a['percent'] ?: $a['row']['price'] <=> $b['row']['price']);

        return $out;
    }

    protected function radarItem(array $m)
    {
        return array_merge($this->listingCardItem($m['row']), ['percent' => $m['percent'], 'notes' => $m['notes'], 'city' => $m['row']['city']]);
    }

    protected function radarTitle(array $r)
    {
        $lf = $this->lf();
        $type = $r['ptype'] ?? '';
        $where = ($r['district'] ?? '') ?: ($r['city'] ?? '');
        $name = trim($type.($where ? ' در '.$where : ''));

        return $name !== '' ? $name : ($lf['categories'][$r['category']] ?? 'ملک').' — هر نوع';
    }

    protected function radarChips(array $r)
    {
        $lf = $this->lf();
        $c = [];
        $c[] = $lf['categories'][$r['category']] ?? '';
        if (! empty($r['price_max'])) {
            $c[] = ($lf['price_labels'][$r['category']] ?? 'قیمت').' تا '.$this->human((int) $r['price_max']);
        }
        if (! empty($r['rent_max'])) {
            $c[] = 'اجاره تا '.$this->human((int) $r['rent_max']);
        }
        if (! empty($r['area_min'])) {
            $c[] = 'حداقل '.$this->fa($r['area_min']).' متر';
        }
        if (! empty($r['rooms'])) {
            $c[] = $r['rooms'] === 'بدون اتاق' ? 'بدون اتاق' : $r['rooms'].' اتاق';
        }
        if (! empty($r['year_from'])) {
            $c[] = 'ساخت از '.$this->fa($r['year_from']);
        }
        if (! empty($r['city'])) {
            $c[] = $r['city'];
        }
        foreach (['parking' => 'پارکینگ', 'elevator' => 'آسانسور', 'storage' => 'انباری'] as $k => $t) {
            if (! empty($r[$k])) {
                $c[] = $t;
            }
        }

        return array_values(array_filter($c));
    }

    protected function radarStore()
    {
        $base = [
            701 => ['id' => 701, 'category' => 'sale_res', 'ptype' => 'آپارتمان', 'city' => 'تهران', 'district' => 'سعادت‌آباد', 'price_max' => 20000000000, 'area_min' => 100, 'rooms' => '۲', 'year_from' => '1398', 'parking' => '1', 'elevator' => '1', 'note' => '', 'status' => 'active', 'created' => '۲ روز پیش', 'new' => 2],
            702 => ['id' => 702, 'category' => 'rent_res', 'ptype' => 'آپارتمان', 'city' => 'تهران', 'district' => '', 'price_max' => 700000000, 'rent_max' => 30000000, 'area_min' => 80, 'rooms' => '۲', 'note' => '', 'status' => 'active', 'created' => '۵ روز پیش', 'new' => 0],
            703 => ['id' => 703, 'category' => 'short', 'ptype' => 'ویلا و باغ', 'city' => 'رشت', 'district' => '', 'price_max' => 7000000, 'note' => 'برای تعطیلات نوروز', 'status' => 'active', 'created' => 'هفته‌ی پیش', 'new' => 1],
            704 => ['id' => 704, 'category' => 'sale_com', 'ptype' => 'مغازه و غرفه', 'city' => 'تهران', 'district' => 'ونک', 'price_max' => 10000000000, 'area_min' => 30, 'note' => '', 'status' => 'suspended', 'created' => 'ماه پیش', 'new' => 0],
        ];
        $deleted = (array) session('demo_radar_deleted', []);
        $mine = (array) session('demo_radars', []);

        return collect(array_diff_key($base + $mine, array_flip($deleted)))->all();
    }

    protected function radarCard(array $r)
    {
        $m = $this->radarMatches($r);

        return ['r' => $r, 'title' => $this->radarTitle($r), 'chips' => $this->radarChips($r), 'count' => count($m), 'best' => $m ? $m[0]['percent'] : 0,
            'top' => array_map([$this, 'radarItem'], array_slice($m, 0, 3)), 'new' => (int) ($r['new'] ?? 0)];
    }

    /** درخواست‌های فعال مشتریان (ناشناس) برای پنل آژانس + بهترین آگهی مطابق از آژانس خودش */
    protected function radarBuyerRequests()
    {
        // id, دسته، نوع، شهر، محله، بودجه (قیمت/رهن/هر شب)، اجاره، حداقل متراژ، اتاق، ساعت قبل
        $seed = [
            [901, 'sale_res', 'آپارتمان', 'تهران', 'سعادت‌آباد', 20000000000, null, 110, '۲', 2],
            [902, 'rent_res', 'آپارتمان', 'تهران', 'پونک', 700000000, 28000000, 85, '۲', 5],
            [903, 'rent_res', '', 'کرج', '', 1600000000, null, 60, '', 8],
            [904, 'sale_com', 'مغازه و غرفه', 'تهران', 'ونک', 10000000000, null, 30, '', 12],
            [905, 'short', 'آپارتمان و سوئیت', 'مشهد', '', 2000000, null, null, '۱', 20],
            [906, 'rent_com', 'دفتر کار، اتاق اداری و مطب', 'تهران', '', 400000000, 45000000, 60, '', 30],
            [907, 'sale_res', 'خانه و ویلا', 'تهران', 'نیاوران', 45000000000, null, 300, '۴ و بیشتر', 40],
            [908, 'project', 'پیش‌فروش', 'تهران', '', 160000000, null, 100, '', 55],
        ];
        $mine = array_values(array_filter($this->listingStore(), fn ($r) => (int) $r['agency_id'] === 8));
        $out = [];
        foreach ($seed as [$id, $cat, $type, $city, $district, $pm, $rent, $area, $rooms, $hours]) {
            $r = array_filter(['id' => $id, 'category' => $cat, 'ptype' => $type, 'city' => $city, 'district' => $district, 'price_max' => $pm, 'rent_max' => $rent, 'area_min' => $area, 'rooms' => $rooms], fn ($v) => $v !== null && $v !== '');
            $best = null;
            foreach ($mine as $row) {
                [$p, $notes] = $this->radarScore($r, $row);
                if ($p >= $this->radarMin() && (! $best || $p > $best['percent'])) {
                    $best = ['row' => $row, 'percent' => $p, 'notes' => $notes];
                }
            }
            $out[] = ['id' => $id, 'cat' => $cat, 'title' => $this->radarTitle($r), 'chips' => $this->radarChips($r), 'city' => $city, 'budget' => $pm, 'hours' => $hours,
                'posted' => $hours < 24 ? $this->fa($hours).' ساعت پیش' : $this->fa(intdiv($hours, 24)).' روز پیش', 'buyer' => 'مشتری '.$this->fa(40000 + $id * 7), 'mine' => $best ? $this->radarItem($best) : null, 'offered' => $id === 905];
        }

        return $out;
    }
}
