<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherCache;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(private OpenMeteoService $weatherService) {}

    public function index()
    {
        $weatherData = WeatherCache::with('country')->orderByDesc('fetched_at')->get()->unique('country_code')->values();

        $mapMarkers = $weatherData->map(fn($w) => [
            'country_code'=>$w->country_code,'country_name'=>$w->country->name??$w->country_code,
            'lat'=>$w->country->latitude??0,'lng'=>$w->country->longitude??0,
            'temperature'=>$w->temperature_2m,'precipitation'=>$w->precipitation,
            'windspeed'=>$w->windspeed_10m,'weathercode'=>$w->weathercode,
            'description'=>$w->weather_description,'icon'=>$w->weather_icon,
            'is_storm'=>$w->is_storm,'is_heavy_rain'=>$w->is_heavy_rain,'is_strong_wind'=>$w->is_strong_wind,
            'risk_score'=>$w->weather_risk_score,
        ]);

        $countries = Country::active()->orderBy('name')->get(['code','name','flag_emoji']);
        return view('weather.index', compact('mapMarkers','countries'));
    }

    public function show(string $code)
    {
        $code    = strtoupper($code);
        $country = Country::where('code',$code)->firstOrFail();
        $weather  = $this->weatherService->getCurrentWeather($country->latitude??0, $country->longitude??0, $code);
        $forecast = $this->weatherService->getWeatherForecast($country->latitude??0, $country->longitude??0, $code);

        if (request()->ajax()) {
            return response()->json(['weather'=>$weather,'forecast'=>$forecast,'country'=>$country]);
        }
        return view('weather.show', compact('country','weather','forecast'));
    }
}
