<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $ports     = Port::active()->orderBy('country_name')->orderBy('port_name')->get();
        $markers   = $ports->map(fn($p) => $p->toMapMarker())->toArray();
        $countries = Country::active()->whereIn('code', $ports->pluck('country_code')->unique())->orderBy('name')->get(['code','name','flag_emoji']);
        $stats     = [
            'total'    => $ports->count(),
            'low'      => $ports->where('congestion_level','low')->count(),
            'moderate' => $ports->where('congestion_level','moderate')->count(),
            'high'     => $ports->whereIn('congestion_level',['high','critical'])->count(),
        ];
        return view('ports.index', compact('ports','markers','countries','stats'));
    }

    public function search(Request $request)
    {
        $term      = $request->input('q','');
        $country   = $request->input('country','');
        $query     = Port::active();
        if ($term)    $query->search($term);
        if ($country) $query->byCountry($country);
        $ports   = $query->orderBy('port_name')->get();
        $markers = $ports->map(fn($p) => $p->toMapMarker())->toArray();
        return response()->json(['ports'=>$ports,'markers'=>$markers]);
    }
}
