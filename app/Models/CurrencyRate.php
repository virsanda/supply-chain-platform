<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    protected $fillable = ['base_currency','target_currency','rate','rate_previous','change_percent','rate_date'];
    protected $casts    = ['rate'=>'float','rate_previous'=>'float','change_percent'=>'float','rate_date'=>'date'];

    public function scopeForBase($q,$b)         { return $q->where('base_currency',strtoupper($b)); }
    public function scopeForPair($q,$b,$t)      { return $q->where('base_currency',strtoupper($b))->where('target_currency',strtoupper($t)); }
    public function scopeToday($q)              { return $q->whereDate('rate_date',today()); }

    public function getCurrencyRiskScoreAttribute(): float
    {
        $c = abs($this->change_percent ?? 0);
        if ($c<=1) return 5.0; if ($c<=3) return 20.0; if ($c<=5) return 40.0; if ($c<=10) return 65.0; if ($c<=15) return 80.0; return 100.0;
    }
    public function getTrendIconAttribute(): string  { return !$this->change_percent ? '→' : ($this->change_percent>0 ? '↑' : '↓'); }
    public function getTrendClassAttribute(): string { return !$this->change_percent ? 'text-secondary' : ($this->change_percent>0 ? 'text-success' : 'text-danger'); }
}
