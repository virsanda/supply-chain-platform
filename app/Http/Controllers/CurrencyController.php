<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(private ExchangeRateService $currencyService) {}

    public function index(Request $request)
    {
        $base      = $request->input('base','USD');
        $ratesData = $this->currencyService->getRates($base);
        $currencyRates = CurrencyRate::forBase($base)->today()->get();
        $countries = Country::active()->whereNotNull('currency_code')->orderBy('name')->get(['code','name','currency_code','flag_emoji']);
        return view('currency.index', compact('base','ratesData','currencyRates','countries'));
    }

    public function show(string $code)
    {
        $code    = strtoupper($code);
        $country = Country::where('code',$code)->firstOrFail();
        if (!$country->currency_code) abort(404,'Currency not available');

        $base   = 'USD';
        $target = $country->currency_code;
        $currentRate = $this->currencyService->getRate($base,$target);
        $trendData   = $this->currencyService->getCurrencyTrend($base,$target,30);
        $rateRecord  = CurrencyRate::forPair($base,$target)->orderByDesc('rate_date')->first();

        return view('currency.show', compact('country','base','target','currentRate','trendData','rateRecord'));
    }
}
