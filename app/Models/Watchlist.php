<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = ['user_id','country_code','country_name','notes','notify_risk_change'];
    protected $casts    = ['notify_risk_change'=>'boolean'];

    public function user()    { return $this->belongsTo(User::class); }
    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }
}
