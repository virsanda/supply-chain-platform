<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use App\Services\WorldBankService;
use App\Services\OpenMeteoService;
use App\Services\ExchangeRateService;
use App\Services\RiskScoringEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    public function __construct(
        private WorldBankService    $worldBank,
        private OpenMeteoService    $weather,
        private ExchangeRateService $currency,
        private RiskScoringEngine   $riskEngine
    ) {}

    public function index(Request $request)
    {
        $query = Country::active();
        if ($request->filled('search')) {
            $t = $request->search;
            $query->where(fn($q)=>$q->where('name','like',"%{$t}%")->orWhere('code','like',"%{$t}%")->orWhere('region','like',"%{$t}%"));
        }
        if ($request->filled('region')) $query->where('region',$request->region);

        $countries = $query->with('latestRiskScore')->orderBy('name')->paginate(20);
        $regions   = Country::active()->distinct()->orderBy('region')->pluck('region');

        return view('countries.index', compact('countries','regions'));
    }

    public function show(string $code)
    {
        $code    = strtoupper($code);
        $country = Country::where('code',$code)->firstOrFail();

        $economic     = $this->worldBank->getEconomicIndicators($code);
        $weather      = $this->weather->getCurrentWeather($country->latitude??0, $country->longitude??0, $code);
        $riskScore    = $this->riskEngine->calculate($code);
        $gdpTrend     = $this->worldBank->getGdpTrend($code, 5);
        $inflatTrend  = $this->worldBank->getInflationTrend($code, 5);

        $currencyRate = null;
        if ($country->currency_code && $country->currency_code!=='USD') {
            $currencyRate = $this->currency->getRate('USD', $country->currency_code);
        }

        $isWatchlisted = Auth::check()
            ? Watchlist::where('user_id',Auth::id())->where('country_code',$code)->exists()
            : false;

        return view('countries.show', compact('country','economic','weather','riskScore','gdpTrend','inflatTrend','currencyRate','isWatchlisted'));
    }

    public function search(Request $request)
    {
        $term = $request->input('q','');
        $countries = Country::active()
            ->where(fn($q)=>$q->where('name','like',"%{$term}%")->orWhere('code','like',"%{$term}%"))
            ->limit(15)->get(['id','code','name','flag_emoji','region','currency_code']);
        return response()->json($countries);
    }
}
