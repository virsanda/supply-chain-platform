<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegativeWordsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $words = [
            ['word'=>'inflation','weight'=>4,'category'=>'economic'],['word'=>'recession','weight'=>5,'category'=>'economic'],
            ['word'=>'deficit','weight'=>3,'category'=>'economic'],['word'=>'debt','weight'=>3,'category'=>'economic'],
            ['word'=>'bankruptcy','weight'=>5,'category'=>'economic'],['word'=>'default','weight'=>5,'category'=>'economic'],
            ['word'=>'downturn','weight'=>4,'category'=>'economic'],['word'=>'loss','weight'=>3,'category'=>'economic'],
            ['word'=>'decline','weight'=>3,'category'=>'economic'],['word'=>'decrease','weight'=>2,'category'=>'economic'],
            ['word'=>'drop','weight'=>2,'category'=>'economic'],['word'=>'collapse','weight'=>5,'category'=>'economic'],
            ['word'=>'crash','weight'=>5,'category'=>'economic'],['word'=>'crisis','weight'=>5,'category'=>'economic'],
            ['word'=>'instability','weight'=>4,'category'=>'economic'],['word'=>'stagnation','weight'=>3,'category'=>'economic'],
            ['word'=>'slump','weight'=>4,'category'=>'economic'],['word'=>'bearish','weight'=>3,'category'=>'economic'],
            ['word'=>'poverty','weight'=>4,'category'=>'economic'],['word'=>'unemployment','weight'=>3,'category'=>'economic'],
            ['word'=>'tariff','weight'=>2,'category'=>'economic'],['word'=>'sanction','weight'=>4,'category'=>'economic'],
            ['word'=>'embargo','weight'=>5,'category'=>'economic'],['word'=>'devaluation','weight'=>4,'category'=>'economic'],
            ['word'=>'hyperinflation','weight'=>5,'category'=>'economic'],['word'=>'fall','weight'=>2,'category'=>'economic'],
            ['word'=>'delay','weight'=>4,'category'=>'logistics'],['word'=>'disruption','weight'=>5,'category'=>'logistics'],
            ['word'=>'shortage','weight'=>4,'category'=>'logistics'],['word'=>'congestion','weight'=>4,'category'=>'logistics'],
            ['word'=>'backlog','weight'=>3,'category'=>'logistics'],['word'=>'bottleneck','weight'=>4,'category'=>'logistics'],
            ['word'=>'blocked','weight'=>3,'category'=>'logistics'],['word'=>'halt','weight'=>4,'category'=>'logistics'],
            ['word'=>'shutdown','weight'=>5,'category'=>'logistics'],['word'=>'closure','weight'=>4,'category'=>'logistics'],
            ['word'=>'strike','weight'=>4,'category'=>'logistics'],['word'=>'disrupted','weight'=>4,'category'=>'logistics'],
            ['word'=>'damage','weight'=>4,'category'=>'logistics'],['word'=>'accident','weight'=>3,'category'=>'logistics'],
            ['word'=>'grounded','weight'=>3,'category'=>'logistics'],['word'=>'cancel','weight'=>3,'category'=>'logistics'],
            ['word'=>'diverted','weight'=>2,'category'=>'logistics'],['word'=>'seized','weight'=>4,'category'=>'logistics'],
            ['word'=>'piracy','weight'=>5,'category'=>'logistics'],['word'=>'stranded','weight'=>3,'category'=>'logistics'],
            ['word'=>'war','weight'=>5,'category'=>'political'],['word'=>'conflict','weight'=>5,'category'=>'political'],
            ['word'=>'attack','weight'=>5,'category'=>'political'],['word'=>'terrorism','weight'=>5,'category'=>'political'],
            ['word'=>'coup','weight'=>5,'category'=>'political'],['word'=>'protest','weight'=>3,'category'=>'political'],
            ['word'=>'riot','weight'=>4,'category'=>'political'],['word'=>'tension','weight'=>3,'category'=>'political'],
            ['word'=>'dispute','weight'=>3,'category'=>'political'],['word'=>'threat','weight'=>4,'category'=>'political'],
            ['word'=>'invasion','weight'=>5,'category'=>'political'],['word'=>'blockade','weight'=>5,'category'=>'political'],
            ['word'=>'assassination','weight'=>5,'category'=>'political'],['word'=>'occupation','weight'=>4,'category'=>'political'],
            ['word'=>'disaster','weight'=>5,'category'=>'general'],['word'=>'storm','weight'=>4,'category'=>'general'],
            ['word'=>'hurricane','weight'=>5,'category'=>'general'],['word'=>'typhoon','weight'=>5,'category'=>'general'],
            ['word'=>'flood','weight'=>4,'category'=>'general'],['word'=>'earthquake','weight'=>5,'category'=>'general'],
            ['word'=>'drought','weight'=>4,'category'=>'general'],['word'=>'tsunami','weight'=>5,'category'=>'general'],
            ['word'=>'wildfire','weight'=>4,'category'=>'general'],['word'=>'pandemic','weight'=>5,'category'=>'general'],
            ['word'=>'epidemic','weight'=>4,'category'=>'general'],['word'=>'outbreak','weight'=>4,'category'=>'general'],
            ['word'=>'risk','weight'=>2,'category'=>'general'],['word'=>'danger','weight'=>4,'category'=>'general'],
            ['word'=>'warning','weight'=>3,'category'=>'general'],['word'=>'alert','weight'=>3,'category'=>'general'],
            ['word'=>'fear','weight'=>3,'category'=>'general'],['word'=>'concern','weight'=>2,'category'=>'general'],
            ['word'=>'uncertainty','weight'=>3,'category'=>'general'],['word'=>'volatile','weight'=>4,'category'=>'general'],
            ['word'=>'fragile','weight'=>3,'category'=>'general'],['word'=>'vulnerable','weight'=>3,'category'=>'general'],
            ['word'=>'deteriorate','weight'=>4,'category'=>'general'],['word'=>'worsen','weight'=>4,'category'=>'general'],
            ['word'=>'failed','weight'=>3,'category'=>'general'],['word'=>'broken','weight'=>3,'category'=>'general'],
        ];
        foreach ($words as $w) {
            DB::table('negative_words')->insertOrIgnore(array_merge($w,['created_at'=>$now,'updated_at'=>$now]));
        }
    }
}
