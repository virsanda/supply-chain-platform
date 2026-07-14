<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $table = 'weather_cache';

    protected $fillable = ['country_code','latitude','longitude','temperature_2m','apparent_temperature','precipitation','windspeed_10m','windgusts_10m','weathercode','weather_description','cloudcover','humidity','is_storm','is_heavy_rain','is_strong_wind','weather_risk_score','fetched_at'];
    protected $casts    = ['temperature_2m'=>'float','apparent_temperature'=>'float','precipitation'=>'float','windspeed_10m'=>'float','windgusts_10m'=>'float','weather_risk_score'=>'float','is_storm'=>'boolean','is_heavy_rain'=>'boolean','is_strong_wind'=>'boolean','fetched_at'=>'datetime'];

    public static array $wmoCodes = [0=>'Clear sky',1=>'Mainly clear',2=>'Partly cloudy',3=>'Overcast',45=>'Foggy',48=>'Icy fog',51=>'Light drizzle',61=>'Slight rain',63=>'Moderate rain',65=>'Heavy rain',80=>'Slight showers',81=>'Moderate showers',82=>'Violent showers',95=>'Thunderstorm',96=>'Thunderstorm w/ hail',99=>'Thunderstorm w/ heavy hail'];
    public static function describeCode(int $c): string { return self::$wmoCodes[$c] ?? "Code {$c}"; }

    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function getWeatherIconAttribute(): string
    {
        $c = $this->weathercode ?? 0;
        if (in_array($c,[0,1])) return '☀️'; if (in_array($c,[2,3])) return '⛅';
        if (in_array($c,[45,48])) return '🌫️'; if (in_array($c,[51,61])) return '🌦️';
        if (in_array($c,[63,65,80,81])) return '🌧️'; if (in_array($c,[82,95,96,99])) return '⛈️';
        if (in_array($c,[71,73,75])) return '🌨️'; return '🌡️';
    }

    public function isFresh(int $minutes=60): bool { return $this->fetched_at && $this->fetched_at->diffInMinutes(now()) < $minutes; }
}
