<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = ['port_name','port_code','country_code','country_name','province_region','latitude','longitude','harbor_size','harbor_type','shelter_afforded','entrance_tide','max_vessel_size_ocean','max_draft_ft','good_holding_ground','turning_area','first_port_of_entry','congestion_level','congestion_score','is_active'];
    protected $casts    = ['latitude'=>'float','longitude'=>'float','shelter_afforded'=>'boolean','entrance_tide'=>'boolean','max_vessel_size_ocean'=>'boolean','good_holding_ground'=>'boolean','turning_area'=>'boolean','first_port_of_entry'=>'boolean','congestion_score'=>'float','is_active'=>'boolean'];

    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function scopeActive($q)           { return $q->where('is_active',true); }
    public function scopeByCountry($q,$c)     { return $q->where('country_code',$c); }
    public function scopeHighCongestion($q)   { return $q->whereIn('congestion_level',['high','critical']); }
    public function scopeSearch($q,$t)        { return $q->where(fn($x)=>$x->where('port_name','like',"%{$t}%")->orWhere('country_name','like',"%{$t}%")->orWhere('port_code','like',"%{$t}%")); }

    public function getCongestionBadgeClassAttribute(): string { return match($this->congestion_level){'low'=>'success','moderate'=>'warning','high'=>'danger','critical'=>'dark',default=>'secondary'}; }
    public function getHarborSizeLabelAttribute(): string      { return match($this->harbor_size){'very_small'=>'Very Small','small'=>'Small','medium'=>'Medium','large'=>'Large',default=>'Unknown'}; }

    public function toMapMarker(): array
    {
        return ['id'=>$this->id,'name'=>$this->port_name,'code'=>$this->port_code,'country'=>$this->country_name,'country_code'=>$this->country_code,'lat'=>$this->latitude,'lng'=>$this->longitude,'harbor_size'=>$this->harbor_size_label,'congestion'=>$this->congestion_level,'congestion_score'=>$this->congestion_score,'badge_class'=>$this->congestion_badge_class];
    }
}
