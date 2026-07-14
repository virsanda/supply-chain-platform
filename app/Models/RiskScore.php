<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $table = 'risk_scores';

    protected $fillable = ['country_code','score_date','weather_score','inflation_score','currency_score','news_sentiment_score','weather_weight','inflation_weight','currency_weight','news_weight','total_score','risk_level','raw_data'];
    protected $casts    = ['score_date'=>'date','weather_score'=>'float','inflation_score'=>'float','currency_score'=>'float','news_sentiment_score'=>'float','weather_weight'=>'float','inflation_weight'=>'float','currency_weight'=>'float','news_weight'=>'float','total_score'=>'float','raw_data'=>'array'];

    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function scopeForCountry($q,$code) { return $q->where('country_code',$code); }
    public function scopeToday($q)             { return $q->whereDate('score_date',today()); }
    public function scopeHighRisk($q)          { return $q->whereIn('risk_level',['high','critical']); }

    public function getRiskBadgeClassAttribute(): string { return match($this->risk_level){'low'=>'success','medium'=>'warning','high'=>'danger','critical'=>'dark',default=>'secondary'}; }
    public function getRiskLabelAttribute(): string      { return match($this->risk_level){'low'=>'Low Risk','medium'=>'Medium Risk','high'=>'High Risk','critical'=>'Critical Risk',default=>'Unknown'}; }
    public function getMarkerColorAttribute(): string    { return match($this->risk_level){'low'=>'#198754','medium'=>'#ffc107','high'=>'#dc3545','critical'=>'#212529',default=>'#6c757d'}; }
}
