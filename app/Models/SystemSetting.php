<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = ['key','value','type','description','group'];

    public static function get(string $key, mixed $default=null): mixed
    {
        return Cache::remember("setting.{$key}", 600, function () use ($key, $default) {
            $s = static::where('key',$key)->first();
            if (!$s) return $default;
            return match($s->type){'integer'=>(int)$s->value,'decimal'=>(float)$s->value,'boolean'=>filter_var($s->value,FILTER_VALIDATE_BOOLEAN),'json'=>json_decode($s->value,true),default=>$s->value};
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key'=>$key],['value'=>is_array($value)?json_encode($value):(string)$value]);
        Cache::forget("setting.{$key}");
    }

    public static function getRiskWeights(): array
    {
        return ['weather'=>(float)static::get('risk_weight_weather',30),'inflation'=>(float)static::get('risk_weight_inflation',20),'currency'=>(float)static::get('risk_weight_currency',10),'news'=>(float)static::get('risk_weight_news',40)];
    }

    public static function getRiskThresholds(): array
    {
        return ['low'=>(int)static::get('risk_threshold_low',30),'medium'=>(int)static::get('risk_threshold_medium',60),'high'=>(int)static::get('risk_threshold_high',80)];
    }
}
