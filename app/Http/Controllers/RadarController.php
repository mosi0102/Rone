<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RadarData;

class RadarController extends Controller
{
    use RadarData;

    public function index()
    {
        $cards = array_map([$this, 'radarCard'], array_values($this->radarStore()));

        return view('pages.radar.index', ['cards' => $cards, 'newTotal' => array_sum(array_column($cards, 'new')), 'icons' => $this->lf()['icons']]);
    }

    public function create()
    {
        $lf = $this->lf();

        return view('pages.radar.create', ['lf' => $lf]);
    }

    public function preview()
    {
        $q = array_filter(request()->query(), fn ($v) => $v !== null && $v !== '');
        $m = $this->radarMatches($q);

        return response()->json(['count' => count($m), 'best' => $m ? $m[0]['percent'] : 0,
            'top' => array_map(fn ($x) => ['title' => $x['row']['title'], 'percent' => $x['percent']], array_slice($m, 0, 3))]);
    }

    public function store()
    {
        $in = array_filter(request()->all(), fn ($v) => $v !== null && $v !== '');
        $id = 800 + count((array) session('demo_radars', [])) + 1;
        $r = array_merge(['category' => 'sale_res', 'status' => 'active', 'created' => 'همین الان', 'new' => 0], $in, ['id' => $id]);
        session(['demo_radars' => ((array) session('demo_radars', [])) + [$id => $r]]);

        return response()->json(['id' => $id, 'url' => route('radar.show', $id)], 201);
    }

    public function show($id)
    {
        $r = $this->radarStore()[(int) $id] ?? abort(404);
        $items = array_map([$this, 'radarItem'], $this->radarMatches($r));

        return view('pages.radar.show', ['r' => $r, 'title' => $this->radarTitle($r), 'chips' => $this->radarChips($r), 'items' => $items,
            'high' => count(array_filter($items, fn ($i) => $i['percent'] >= 80))]);
    }

    public function destroy($id)
    {
        session(['demo_radar_deleted' => array_merge((array) session('demo_radar_deleted', []), [(int) $id])]);

        return response()->json(['ok' => true]);
    }

    public function panel()
    {
        $lf = $this->lf();
        $rows = $this->radarBuyerRequests();

        return view('pages.panel.radar', ['requests' => $rows, 'cities' => $lf['cities'], 'cats' => $lf['categories'], 'icons' => $lf['icons'], 'related' => count(array_filter($rows, fn ($x) => $x['mine']))]);
    }

    public function offer($id)
    {
        return response()->json(['ok' => true, 'id' => (int) $id]);
    }
}
