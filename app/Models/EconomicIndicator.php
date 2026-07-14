<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicIndicator extends Model
{
    protected $table = 'economic_indicators';

    protected $fillable = ['country_code','year','gdp','gdp_per_capita','inflation_rate','unemployment_rate','population','exports_usd','imports_usd','trade_balance','fetched_at'];
    protected $casts    = ['gdp'=>'float','gdp_per_capita'=>'float','inflation_rate'=>'float','unemployment_rate'=>'float','exports_usd'=>'float','imports_usd'=>'float','trade_balance'=>'float','population'=>'integer','fetched_at'=>'datetime'];

    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function scopeForCountry($q,$code) { return $q->where('country_code',$code); }
    public function scopeLatestYear($q)        { return $q->orderByDesc('year'); }

    public function getInflationScoreAttribute(): float
    {
        $v = abs($this->inflation_rate ?? 0);
        if ($v<=2) return 5.0; if ($v<=5) return 20.0; if ($v<=10) return 45.0;
        if ($v<=15) return 65.0; if ($v<=20) return 80.0; return 100.0;
    }

    public function getGdpFormattedAttribute(): string
    {
        if (!$this->gdp) return 'N/A';
        if ($this->gdp>=1e12) return '$'.round($this->gdp/1e12,2).'T';
        if ($this->gdp>=1e9)  return '$'.round($this->gdp/1e9,2).'B';
        return '$'.number_format($this->gdp);
    }
}
