<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonSnapshot extends Model
{
    protected $table = 'comparison_snapshots';

    protected $fillable = ['user_id','country_a','country_b','country_a_data','country_b_data','winner_gdp','winner_risk','winner_inflation','recommendation','recommendation_reason'];
    protected $casts    = ['country_a_data'=>'array','country_b_data'=>'array'];

    public function user()     { return $this->belongsTo(User::class); }
    public function countryA() { return $this->belongsTo(Country::class,'country_a','code'); }
    public function countryB() { return $this->belongsTo(Country::class,'country_b','code'); }
}
