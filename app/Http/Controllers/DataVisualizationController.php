<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskHistory;
use App\Services\WorldBankService;
use App\Services\ExchangeRateService;
use App\Services\RiskScoringEngine;
use Illuminate\Http\Request;

class DataVisualizationController extends Controller
{
    public function __construct(
        private WorldBankService    $worldBank,
        private ExchangeRateService $currency,
        private RiskScoringEngine   $riskEngine
    ) {}

    public function index()
    {
        $countries = Country::active()->orderBy('name')->get(['code','name','flag_emoji','currency_code']);
        return view('visualization.index', compact('countries'));
    }

    public function show(string $code)
    {
        $code    = strtoupper($code);
        $country = Country::where('code',$code)->firstOrFail();

        // GDP Trend
        $gdpTrend       = $this->worldBank->getGdpTrend($code, 5);
        $inflationTrend = $this->worldBank->getInflationTrend($code, 5);

        // Currency Trend
        $currencyTrend  = null;
        if ($country->currency_code && $country->currency_code!=='USD') {
            $currencyTrend = $this->currency->getCurrencyTrend('USD', $country->currency_code, 30);
        }

        // Risk Trend (30 hari)
        $riskTrend = RiskHistory::getChartData($code, 30);

        // Current risk score
        $riskScore = $this->riskEngine->calculate($code);

        if (request()->ajax()) {
            return response()->json(compact('gdpTrend','inflationTrend','currencyTrend','riskTrend','riskScore'));
        }

        return view('visualization.show', compact('country','gdpTrend','inflationTrend','currencyTrend','riskTrend','riskScore'));
    }
}
