<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = ['author_id','title','slug','excerpt','body','cover_image','country_code','category','status','published_at','views'];
    protected $casts    = ['published_at'=>'datetime','views'=>'integer'];

    public function author()  { return $this->belongsTo(User::class,'author_id'); }
    public function country() { return $this->belongsTo(Country::class,'country_code','code'); }

    public function scopePublished($q) { return $q->where('status','published')->whereNotNull('published_at')->where('published_at','<=',now()); }
    public function scopeByCategory($q,$c) { return $q->where('category',$c); }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Article $a) {
            if (empty($a->slug)) $a->slug = Str::slug($a->title).'-'.Str::random(5);
            if (empty($a->excerpt) && $a->body) $a->excerpt = Str::limit(strip_tags($a->body),200);
        });
    }

    public function getCategoryLabelAttribute(): string { return match($this->category){'risk_analysis'=>'Risk Analysis','market_update'=>'Market Update','logistics'=>'Logistics','geopolitics'=>'Geopolitics','economy'=>'Economy',default=>$this->category}; }
    public function incrementViews(): void { $this->increment('views'); }
}
