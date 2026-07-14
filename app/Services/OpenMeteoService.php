<?php

namespace App\Services;

use App\Models\WeatherCache;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Open-Meteo API — Gratis, tanpa API Key
 * https://api.open-meteo.com
 */
class OpenMeteoService extends BaseApiService
{
    protected string $apiName = 'openmeteo';
    protected string $baseUrl = 'https://api.open-meteo.com/v1';

    public function getCurrentWeather(float $lat, float $lng, string $code): ?array
    {
        $minutes = SystemSetting::get('cache_weather_minutes', 60);
        return Cache::remember($this->cacheKey('weather',$code), $minutes*60, function () use ($lat,$lng,$code) {
            $data = $this->get('/forecast', [
                'latitude'  => $lat, 'longitude' => $lng,
                'current'   => 'temperature_2m,apparent_temperature,precipitation,windspeed_10m,windgusts_10m,weathercode,cloudcover,relativehumidity_2m',
                'timezone'  => 'auto', 'forecast_days' => 1,
            ]);
            if (!$data || !isset($data['current'])) return null;
            $c     = $data['current'];
            $wind  = (float)($c['windspeed_10m'] ?? 0);
            $gust  = (float)($c['windgusts_10m'] ?? 0);
            $rain  = (float)($c['precipitation'] ?? 0);
            $wmo   = (int)($c['weathercode'] ?? 0);
            $stormWind = SystemSetting::get('weather_wind_storm_kmh', 60);
            $heavyRain = SystemSetting::get('weather_rain_heavy_mm', 20);
            $isStorm   = ($wmo>=95) || ($gust>=$stormWind);
            $isHeavy   = ($rain>=$heavyRain) || $wmo===65 || $wmo===82;
            $isWind    = $wind>=40;
            $score     = $this->calcRiskScore($wmo,$wind,$rain);
            $result = [
                'country_code'=>$code,'latitude'=>$lat,'longitude'=>$lng,
                'temperature_2m'=>$c['temperature_2m']??null,'apparent_temperature'=>$c['apparent_temperature']??null,
                'precipitation'=>$rain,'windspeed_10m'=>$wind,'windgusts_10m'=>$gust,
                'weathercode'=>$wmo,'weather_description'=>WeatherCache::describeCode($wmo),
                'cloudcover'=>$c['cloudcover']??null,'humidity'=>$c['relativehumidity_2m']??null,
                'is_storm'=>$isStorm,'is_heavy_rain'=>$isHeavy,'is_strong_wind'=>$isWind,
                'weather_risk_score'=>$score,'fetched_at'=>now(),
            ];
            WeatherCache::updateOrCreate(['country_code'=>$code], $result);
            return $result;
        });
    }

    public function getWeatherForecast(float $lat, float $lng, string $code): ?array
    {
        return Cache::remember($this->cacheKey('forecast',$code), 3600, function () use ($lat,$lng) {
            return $this->get('/forecast', [
                'latitude'=>$lat,'longitude'=>$lng,
                'daily'=>'weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max',
                'timezone'=>'auto','forecast_days'=>7,
            ]);
        });
    }

    /**
     * Algoritma Weather Risk Score (0-100)
     * Komponen: WMO code (maks 40) + angin (maks 30) + hujan (maks 30)
     */
    private function calcRiskScore(int $wmo, float $wind, float $rain): float
    {
        $s = 0.0;
        // Komponen 1: WMO Code
        $s += match(true) {
            in_array($wmo,[0,1])          => 0,
            in_array($wmo,[2,3])          => 5,
            in_array($wmo,[45,48])        => 15,
            in_array($wmo,[51,53,55])     => 15,
            in_array($wmo,[61,63])        => 20,
            $wmo===65                     => 35,
            in_array($wmo,[80,81])        => 25,
            $wmo===82                     => 40,
            in_array($wmo,[71,73,75])     => 30,
            in_array($wmo,[95,96,99])     => 40,
            default                       => 10,
        };
        // Komponen 2: Wind
        $s += match(true) { $wind<20=>0, $wind<40=>10, $wind<60=>20, default=>30 };
        // Komponen 3: Rain
        $s += match(true) { $rain<5=>0, $rain<10=>10, $rain<20=>20, default=>30 };
        return min(100.0, $s);
    }
}
