<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\WorldBankService;

class CountryApiController extends Controller
{
    public function __construct(private WorldBankService $wb) {}

    public function index()
    {
        return response()->json(Country::active()->with('latestRiskScore')->orderBy('name')->get());
    }

    public function show(string $code)
    {
        $country = Country::where('code',strtoupper($code))->with(['latestRiskScore','weatherCache','latestEconomic'])->firstOrFail();
        return response()->json($country);
    }

    public function gdp(string $code)
    {
        return response()->json(['country_code'=>strtoupper($code),'gdp_trend'=>$this->wb->getGdpTrend(strtoupper($code),5),'inflation_trend'=>$this->wb->getInflationTrend(strtoupper($code),5)]);
    }
}
