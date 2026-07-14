<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = ['title','description','content','url','image_url','source_name','source_url','language','country_code','topic','published_at','positive_count','negative_count','neutral_count','sentiment','sentiment_score','fetched_at'];
    protected $casts    = ['published_at'=>'datetime','fetched_at'=>'datetime','positive_count'=>'integer','negative_count'=>'integer','neutral_count'=>'integer','sentiment_score'=>'float'];

    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function scopeByTopic($q,$t)     { return $q->where('topic',$t); }
    public function scopeForCountry($q,$c)  { return $q->where('country_code',$c); }
    public function scopeRecent($q,$h=24)   { return $q->where('published_at','>=',now()->subHours($h)); }

    public function getSentimentBadgeClassAttribute(): string { return match($this->sentiment){'positive'=>'success','negative'=>'danger',default=>'secondary'}; }
    public function getSentimentIconAttribute(): string       { return match($this->sentiment){'positive'=>'😊','negative'=>'😟',default=>'😐'}; }
    public function getNewsRiskScoreAttribute(): float        { return max(0, min(100, (($this->sentiment_score * -1) + 100) / 2)); }
    public function getTimeAgoAttribute(): string             { return $this->published_at?->diffForHumans() ?? 'Unknown'; }
}
