<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\OpenMeteoService;

class WeatherApiController extends Controller
{
    public function __construct(private OpenMeteoService $weather) {}

    public function show(string $code)
    {
        $country = Country::where('code',strtoupper($code))->firstOrFail();
        $data    = $this->weather->getCurrentWeather($country->latitude??0,$country->longitude??0,strtoupper($code));
        return response()->json($data);
    }

    public function riskScore(string $code)
    {
        $country = Country::where('code',strtoupper($code))->firstOrFail();
        $data    = $this->weather->getCurrentWeather($country->latitude??0,$country->longitude??0,strtoupper($code));
        return response()->json(['country_code'=>strtoupper($code),'weather_risk_score'=>$data['weather_risk_score']??0,'is_storm'=>$data['is_storm']??false,'is_heavy_rain'=>$data['is_heavy_rain']??false,'is_strong_wind'=>$data['is_strong_wind']??false]);
    }
}
