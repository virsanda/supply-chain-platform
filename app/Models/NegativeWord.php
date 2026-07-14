<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegativeWord extends Model
{
    protected $table = 'negative_words';

    protected $fillable = ['word','weight','category'];
    protected $casts    = ['weight'=>'integer'];

    public static function getWordList(): array { return static::pluck('word')->map(fn($w)=>strtolower($w))->toArray(); }
    public static function getWordMap(): array  { return static::pluck('weight','word')->mapWithKeys(fn($w,$k)=>[strtolower($k)=>$w])->toArray(); }
}
