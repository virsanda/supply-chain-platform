<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskHistory extends Model
{
    protected $table = 'risk_history';

    protected $fillable = ['country_code','recorded_date','total_score','weather_score','inflation_score','currency_score','news_score','risk_level'];
    protected $casts    = ['recorded_date'=>'date','total_score'=>'float','weather_score'=>'float','inflation_score'=>'float','currency_score'=>'float','news_score'=>'float'];

    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function scopeForCountry($q,$c)    { return $q->where('country_code',$c); }
    public function scopeLastDays($q,$days=30){ return $q->where('recorded_date','>=',now()->subDays($days)); }

    public static function getChartData(string $code, int $days=30): array
    {
        $records = static::forCountry($code)->lastDays($days)->orderBy('recorded_date')->get(['recorded_date','total_score','risk_level']);
        return [
            'labels' => $records->pluck('recorded_date')->map(fn($d)=>$d->format('M d'))->toArray(),
            'data'   => $records->pluck('total_score')->toArray(),
            'levels' => $records->pluck('risk_level')->toArray(),
        ];
    }
}
