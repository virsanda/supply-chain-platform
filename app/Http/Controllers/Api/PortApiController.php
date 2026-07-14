<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;

class PortApiController extends Controller
{
    public function index()
    {
        return response()->json(Port::active()->orderBy('country_name')->get()->map(fn($p)=>$p->toMapMarker()));
    }
    public function show(int $id)
    {
        return response()->json(Port::findOrFail($id));
    }
    public function byCountry(string $code)
    {
        return response()->json(Port::active()->byCountry(strtoupper($code))->get()->map(fn($p)=>$p->toMapMarker()));
    }
}
