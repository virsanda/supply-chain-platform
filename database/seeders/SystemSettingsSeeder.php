<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $settings = [
            ['key'=>'risk_weight_weather','value'=>'30','type'=>'decimal','description'=>'Weather risk weight (%)','group'=>'risk_weights'],
            ['key'=>'risk_weight_inflation','value'=>'20','type'=>'decimal','description'=>'Inflation risk weight (%)','group'=>'risk_weights'],
            ['key'=>'risk_weight_currency','value'=>'10','type'=>'decimal','description'=>'Currency risk weight (%)','group'=>'risk_weights'],
            ['key'=>'risk_weight_news','value'=>'40','type'=>'decimal','description'=>'News sentiment risk weight (%)','group'=>'risk_weights'],
            ['key'=>'risk_threshold_low','value'=>'30','type'=>'integer','description'=>'Max score for Low Risk','group'=>'risk_thresholds'],
            ['key'=>'risk_threshold_medium','value'=>'60','type'=>'integer','description'=>'Max score for Medium Risk','group'=>'risk_thresholds'],
            ['key'=>'risk_threshold_high','value'=>'80','type'=>'integer','description'=>'Max score for High Risk','group'=>'risk_thresholds'],
            ['key'=>'weather_wind_storm_kmh','value'=>'60','type'=>'integer','description'=>'Wind speed (km/h) = storm','group'=>'weather_config'],
            ['key'=>'weather_rain_heavy_mm','value'=>'20','type'=>'integer','description'=>'Precipitation (mm) = heavy rain','group'=>'weather_config'],
            ['key'=>'cache_weather_minutes','value'=>'60','type'=>'integer','description'=>'Weather cache (min)','group'=>'api_config'],
            ['key'=>'cache_worldbank_hours','value'=>'24','type'=>'integer','description'=>'World Bank cache (hours)','group'=>'api_config'],
            ['key'=>'cache_currency_minutes','value'=>'30','type'=>'integer','description'=>'Currency cache (min)','group'=>'api_config'],
            ['key'=>'cache_news_minutes','value'=>'120','type'=>'integer','description'=>'News cache (min)','group'=>'api_config'],
            ['key'=>'platform_name','value'=>'Supply Chain Risk Intelligence Platform','type'=>'string','description'=>'Platform title','group'=>'display'],
            ['key'=>'default_base_currency','value'=>'USD','type'=>'string','description'=>'Default base currency','group'=>'display'],
            ['key'=>'map_default_zoom','value'=>'2','type'=>'integer','description'=>'Leaflet default zoom','group'=>'display'],
            ['key'=>'dashboard_default_countries','value'=>'["ID","CN","DE","AU","US","JP"]','type'=>'json','description'=>'Default dashboard countries','group'=>'display'],
        ];
        foreach ($settings as $s) {
            DB::table('system_settings')->updateOrInsert(['key'=>$s['key']],array_merge($s,['created_at'=>$now,'updated_at'=>$now]));
        }
    }
}
