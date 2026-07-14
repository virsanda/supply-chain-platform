<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositiveWordsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $words = [
            ['word'=>'growth','weight'=>4,'category'=>'economic'],['word'=>'profit','weight'=>4,'category'=>'economic'],
            ['word'=>'surplus','weight'=>3,'category'=>'economic'],['word'=>'stable','weight'=>3,'category'=>'economic'],
            ['word'=>'recovery','weight'=>4,'category'=>'economic'],['word'=>'boost','weight'=>3,'category'=>'economic'],
            ['word'=>'expansion','weight'=>3,'category'=>'economic'],['word'=>'investment','weight'=>3,'category'=>'economic'],
            ['word'=>'prosperity','weight'=>5,'category'=>'economic'],['word'=>'revenue','weight'=>2,'category'=>'economic'],
            ['word'=>'gain','weight'=>3,'category'=>'economic'],['word'=>'increase','weight'=>2,'category'=>'economic'],
            ['word'=>'improve','weight'=>3,'category'=>'economic'],['word'=>'rise','weight'=>2,'category'=>'economic'],
            ['word'=>'strengthen','weight'=>3,'category'=>'economic'],['word'=>'accelerate','weight'=>3,'category'=>'economic'],
            ['word'=>'positive','weight'=>2,'category'=>'economic'],['word'=>'upward','weight'=>2,'category'=>'economic'],
            ['word'=>'bullish','weight'=>3,'category'=>'economic'],['word'=>'reform','weight'=>2,'category'=>'economic'],
            ['word'=>'development','weight'=>3,'category'=>'economic'],['word'=>'productivity','weight'=>3,'category'=>'economic'],
            ['word'=>'efficiency','weight'=>3,'category'=>'economic'],['word'=>'advance','weight'=>2,'category'=>'economic'],
            ['word'=>'income','weight'=>2,'category'=>'economic'],['word'=>'profitable','weight'=>3,'category'=>'economic'],
            ['word'=>'record','weight'=>2,'category'=>'economic'],['word'=>'milestone','weight'=>3,'category'=>'economic'],
            ['word'=>'delivery','weight'=>2,'category'=>'logistics'],['word'=>'smooth','weight'=>3,'category'=>'logistics'],
            ['word'=>'efficient','weight'=>3,'category'=>'logistics'],['word'=>'clearance','weight'=>2,'category'=>'logistics'],
            ['word'=>'agreement','weight'=>3,'category'=>'logistics'],['word'=>'partnership','weight'=>3,'category'=>'logistics'],
            ['word'=>'deal','weight'=>3,'category'=>'logistics'],['word'=>'cooperation','weight'=>3,'category'=>'logistics'],
            ['word'=>'alliance','weight'=>3,'category'=>'logistics'],['word'=>'open','weight'=>2,'category'=>'logistics'],
            ['word'=>'liberalize','weight'=>3,'category'=>'logistics'],['word'=>'contract','weight'=>2,'category'=>'logistics'],
            ['word'=>'supply','weight'=>1,'category'=>'logistics'],['word'=>'infrastructure','weight'=>2,'category'=>'logistics'],
            ['word'=>'capacity','weight'=>2,'category'=>'logistics'],['word'=>'export','weight'=>2,'category'=>'logistics'],
            ['word'=>'negotiation','weight'=>2,'category'=>'logistics'],['word'=>'approved','weight'=>2,'category'=>'logistics'],
            ['word'=>'peace','weight'=>5,'category'=>'political'],['word'=>'stability','weight'=>5,'category'=>'political'],
            ['word'=>'ceasefire','weight'=>4,'category'=>'political'],['word'=>'diplomatic','weight'=>3,'category'=>'political'],
            ['word'=>'resolution','weight'=>4,'category'=>'political'],['word'=>'treaty','weight'=>4,'category'=>'political'],
            ['word'=>'secure','weight'=>3,'category'=>'political'],['word'=>'safe','weight'=>3,'category'=>'political'],
            ['word'=>'democratic','weight'=>2,'category'=>'political'],['word'=>'transparent','weight'=>2,'category'=>'political'],
            ['word'=>'success','weight'=>4,'category'=>'general'],['word'=>'benefit','weight'=>3,'category'=>'general'],
            ['word'=>'opportunity','weight'=>3,'category'=>'general'],['word'=>'innovation','weight'=>3,'category'=>'general'],
            ['word'=>'solution','weight'=>3,'category'=>'general'],['word'=>'reliable','weight'=>3,'category'=>'general'],
            ['word'=>'strong','weight'=>2,'category'=>'general'],['word'=>'robust','weight'=>3,'category'=>'general'],
            ['word'=>'resilient','weight'=>4,'category'=>'general'],['word'=>'optimistic','weight'=>3,'category'=>'general'],
            ['word'=>'confidence','weight'=>3,'category'=>'general'],['word'=>'favorable','weight'=>3,'category'=>'general'],
            ['word'=>'excellent','weight'=>4,'category'=>'general'],['word'=>'outstanding','weight'=>4,'category'=>'general'],
            ['word'=>'signed','weight'=>2,'category'=>'general'],['word'=>'launch','weight'=>2,'category'=>'general'],
            ['word'=>'highest','weight'=>2,'category'=>'general'],['word'=>'booming','weight'=>3,'category'=>'general'],
        ];
        foreach ($words as $w) {
            DB::table('positive_words')->insertOrIgnore(array_merge($w,['created_at'=>$now,'updated_at'=>$now]));
        }
    }
}
