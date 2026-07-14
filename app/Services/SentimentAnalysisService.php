<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;

/**
 * Lexicon-Based Sentiment Analysis (PHP)
 *
 * Cara kerja:
 * 1. Preprocess teks (lowercase, strip HTML/URLs, clean chars)
 * 2. Tokenize → array kata (min 3 char)
 * 3. Cocokkan dengan kamus positif/negatif (dari DB)
 * 4. Hitung weighted score
 * 5. sentiment_score = ((pos - neg) / (pos + neg)) × 100  → range -100..+100
 * 6. Klasifikasi: positive / negative / neutral (threshold 20%)
 *
 * Contoh:
 *   "Inflation increases while exports decrease due to war."
 *   Positive: increase(2) → pos=2
 *   Negative: inflation(4)+decrease(2)+war(5) → neg=11
 *   Score = (2-11)/(2+11)×100 = -69.23 → NEGATIVE
 */
class SentimentAnalysisService
{
    private array $positiveWords;
    private array $negativeWords;
    private array $positiveWeights;
    private array $negativeWeights;

    public function __construct()
    {
        $this->positiveWords   = Cache::remember('pw_list', 3600, fn()=>PositiveWord::getWordList());
        $this->negativeWords   = Cache::remember('nw_list', 3600, fn()=>NegativeWord::getWordList());
        $this->positiveWeights = Cache::remember('pw_map',  3600, fn()=>PositiveWord::getWordMap());
        $this->negativeWeights = Cache::remember('nw_map',  3600, fn()=>NegativeWord::getWordMap());
    }

    public function analyze(string $text): array
    {
        $words = $this->tokenize($this->preprocess($text));
        $posScore = 0; $negScore = 0; $posCount = 0; $negCount = 0;
        $matchedPos = []; $matchedNeg = [];

        foreach ($words as $word) {
            $w = strtolower($word);
            if (in_array($w, $this->positiveWords)) {
                $posScore += $this->positiveWeights[$w] ?? 1;
                $posCount++;
                $matchedPos[] = $w;
            }
            if (in_array($w, $this->negativeWords)) {
                $negScore += $this->negativeWeights[$w] ?? 1;
                $negCount++;
                $matchedNeg[] = $w;
            }
        }

        $sentiment      = $this->classify($posScore, $negScore);
        $sentimentScore = $this->score($posScore, $negScore);
        $totalWords     = count($words);
        $neutralCount   = max(0, $totalWords - $posCount - $negCount);

        return [
            'sentiment'        => $sentiment,
            'sentiment_score'  => $sentimentScore,
            'positive_count'   => $posCount,
            'negative_count'   => $negCount,
            'neutral_count'    => $neutralCount,
            'positive_score'   => $posScore,
            'negative_score'   => $negScore,
            'matched_positive' => array_unique($matchedPos),
            'matched_negative' => array_unique($matchedNeg),
        ];
    }

    private function preprocess(string $text): string
    {
        $text = strtolower(strip_tags($text));
        $text = preg_replace('#https?://[^\s]+#','',$text);
        $text = preg_replace('/[^a-z\s\-]/',' ',$text);
        return trim(preg_replace('/\s+/',' ',$text));
    }

    private function tokenize(string $text): array
    {
        return array_filter(explode(' ',$text), fn($w)=>strlen($w)>=3);
    }

    /**
     * Klasifikasi: positive / negative / neutral
     * Threshold: dominan jika selisih ≥ 20% dari total score
     */
    private function classify(int $pos, int $neg): string
    {
        $total = $pos + $neg;
        if ($total === 0) return 'neutral';
        $diff = abs($pos - $neg);
        $threshold = $total * 0.20;
        if ($pos>$neg && $diff>=$threshold) return 'positive';
        if ($neg>$pos && $diff>=$threshold) return 'negative';
        return 'neutral';
    }

    /**
     * Hitung sentiment score -100..+100
     * Formula: ((pos - neg) / (pos + neg)) × 100
     */
    private function score(int $pos, int $neg): float
    {
        $total = $pos + $neg;
        if ($total === 0) return 0.0;
        return round((($pos - $neg) / $total) * 100, 2);
    }

    public function analyzeBatch(array $texts): array
    {
        return array_map(fn($t) => $this->analyze($t), $texts);
    }

    public function aggregate(array $results): array
    {
        if (empty($results)) return ['average_score'=>0.0,'dominant'=>'neutral','positive_ratio'=>0.0,'negative_ratio'=>0.0,'neutral_ratio'=>0.0];
        $scores  = array_column($results,'sentiment_score');
        $avg     = array_sum($scores)/count($scores);
        $pos = count(array_filter($results,fn($r)=>$r['sentiment']==='positive'));
        $neg = count(array_filter($results,fn($r)=>$r['sentiment']==='negative'));
        $neu = count($results) - $pos - $neg;
        $total   = count($results);
        return ['average_score'=>round($avg,2),'dominant'=>$pos>=$neg&&$pos>=$neu?'positive':($neg>=$neu?'negative':'neutral'),'positive_ratio'=>round(($pos/$total)*100,2),'negative_ratio'=>round(($neg/$total)*100,2),'neutral_ratio'=>round(($neu/$total)*100,2),'total'=>$total];
    }

    public static function clearCache(): void
    {
        foreach (['pw_list','nw_list','pw_map','nw_map'] as $k) \Illuminate\Support\Facades\Cache::forget($k);
    }
}
