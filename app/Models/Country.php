<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['code','code3','name','capital','region','subregion','latitude','longitude','population','currency_code','currency_name','flag_emoji','flag_url','languages','is_active'];
    protected $casts    = ['languages'=>'array','is_active'=>'boolean','latitude'=>'float','longitude'=>'float','population'=>'integer'];

    public function economicIndicators() { return $this->hasMany(EconomicIndicator::class,'country_code','code'); }
    public function latestEconomic()     { return $this->hasOne(EconomicIndicator::class,'country_code','code')->latestOfMany('year'); }
    public function riskScores()         { return $this->hasMany(RiskScore::class,'country_code','code'); }
    public function latestRiskScore()    { return $this->hasOne(RiskScore::class,'country_code','code')->latestOfMany('score_date'); }
    public function weatherCache()       { return $this->hasOne(WeatherCache::class,'country_code','code'); }
    public function ports()              { return $this->hasMany(Port::class,'country_code','code'); }
    public function newsCache()          { return $this->hasMany(NewsCache::class,'country_code','code'); }
    public function watchlists()         { return $this->hasMany(Watchlist::class,'country_code','code'); }
    public function riskHistory()        { return $this->hasMany(RiskHistory::class,'country_code','code'); }

    public function scopeActive($q)           { return $q->where('is_active',true); }
    public function scopeByRegion($q,$region) { return $q->where('region',$region); }
}
