<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExchangeRateService;

class CurrencyApiController extends Controller
{
    public function __construct(private ExchangeRateService $currency) {}

    public function index()
    {
        return response()->json($this->currency->getRates('USD'));
    }

    public function show(string $code)
    {
        $rate  = $this->currency->getRate('USD', strtoupper($code));
        $trend = $this->currency->getCurrencyTrend('USD', strtoupper($code), 30);
        return response()->json(['base'=>'USD','target'=>strtoupper($code),'rate'=>$rate,'trend'=>$trend]);
    }
}
