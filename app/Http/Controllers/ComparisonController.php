<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ComparisonSnapshot;
use App\Services\RiskScoringEngine;
use App\Services\WorldBankService;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComparisonController extends Controller
{
    public function __construct(
        private RiskScoringEngine   $riskEngine,
        private WorldBankService    $worldBank,
        private ExchangeRateService $currency
    ) {}

    public function index()
    {
        $countries = Country::active()->orderBy('name')->get(['code','name','flag_emoji','region']);
        $recent    = ComparisonSnapshot::where('user_id',Auth::id())->with(['countryA','countryB'])->latest()->take(5)->get();
        return view('comparison.index', compact('countries','recent'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'country_a' => 'required|string|size:2',
            'country_b' => 'required|string|size:2|different:country_a',
        ]);

        $codeA = strtoupper($request->country_a);
        $codeB = strtoupper($request->country_b);

        $comparison = $this->riskEngine->compare($codeA, $codeB);

        // Tambah data GDP
        $econA = $this->worldBank->getEconomicIndicators($codeA);
        $econB = $this->worldBank->getEconomicIndicators($codeB);

        $comparison['country_a']['economic'] = $econA;
        $comparison['country_b']['economic'] = $econB;

        // GDP winner
        $gdpA = $econA['gdp'] ?? 0;
        $gdpB = $econB['gdp'] ?? 0;
        $comparison['winner_gdp'] = $gdpA >= $gdpB ? $codeA : $codeB;

        // Currency rates
        $countryA = Country::where('code',$codeA)->first();
        $countryB = Country::where('code',$codeB)->first();
        $comparison['country_a']['currency_rate'] = ($countryA && $countryA->currency_code && $countryA->currency_code!=='USD')
            ? $this->currency->getRate('USD',$countryA->currency_code) : null;
        $comparison['country_b']['currency_rate'] = ($countryB && $countryB->currency_code && $countryB->currency_code!=='USD')
            ? $this->currency->getRate('USD',$countryB->currency_code) : null;

        // Simpan snapshot
        ComparisonSnapshot::create([
            'user_id'               => Auth::id(),
            'country_a'             => $codeA,
            'country_b'             => $codeB,
            'country_a_data'        => $comparison['country_a'],
            'country_b_data'        => $comparison['country_b'],
            'winner_gdp'            => $comparison['winner_gdp'],
            'winner_risk'           => $comparison['winner_risk'],
            'winner_inflation'      => $comparison['winner_inflation'],
            'recommendation'        => $comparison['recommendation'],
            'recommendation_reason' => $comparison['recommendation_reason'],
        ]);

        if ($request->ajax()) return response()->json($comparison);
        return view('comparison.result', compact('comparison','countryA','countryB'));
    }
}
