<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\RiskHistory;
use App\Models\NewsCache;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Supply Chain Risk Scoring Engine
 *
 * Algoritma: Weighted Risk Model
 * Risk Score = (Weather×30%) + (Inflation×20%) + (Currency×10%) + (NewsS×40%)
 *
 * Bobot konfigurabel via Admin → system_settings
 *
 * Output:
 *   Germany : 22 (Low Risk)
 *   China   : 47 (Medium Risk)
 */
class RiskScoringEngine
{
    public function __construct(
        private OpenMeteoService        $weather,
        private WorldBankService        $worldBank,
        private ExchangeRateService     $currency,
        private GNewsService            $news,
        private SentimentAnalysisService $sentiment
    ) {}

    public function calculate(string $code): array
    {
        $key = "risk_score:{$code}:".today()->format('Y-m-d');
        return Cache::remember($key, 3600, function () use ($code) {
            $country = Country::where('code',$code)->first();
            if (!$country) return $this->defaultScore($code,'Country not found');

            $weights = SystemSetting::getRiskWeights();

            // ── Komponen 1: Weather Risk ──────────────────────────
            $weatherData  = $this->weather->getCurrentWeather($country->latitude??0, $country->longitude??0, $code);
            $weatherScore = (float)($weatherData['weather_risk_score'] ?? 20.0);

            // ── Komponen 2: Inflation Risk ────────────────────────
            $econData      = $this->worldBank->getEconomicIndicators($code);
            $inflationScore = $this->inflationScore($econData);

            // ── Komponen 3: Currency Risk ─────────────────────────
            $currencyScore = $country->currency_code && $country->currency_code!=='USD'
                ? $this->currency->getCurrencyRiskScore($country->currency_code)
                : 0.0;

            // ── Komponen 4: News Sentiment Risk ───────────────────
            $newsScore = $this->newsScore($code, $country->name);

            // ── Total Weighted Score ──────────────────────────────
            $total = round(min(100, max(0,
                ($weatherScore   * $weights['weather']   / 100) +
                ($inflationScore * $weights['inflation']  / 100) +
                ($currencyScore  * $weights['currency']   / 100) +
                ($newsScore      * $weights['news']       / 100)
            )), 2);

            $level = $this->level($total);

            $result = [
                'country_code'         => $code,
                'country_name'         => $country->name,
                'flag_emoji'           => $country->flag_emoji,
                'score_date'           => today()->toDateString(),
                'weather_score'        => round($weatherScore,2),
                'inflation_score'      => round($inflationScore,2),
                'currency_score'       => round($currencyScore,2),
                'news_sentiment_score' => round($newsScore,2),
                'weather_weight'       => $weights['weather'],
                'inflation_weight'     => $weights['inflation'],
                'currency_weight'      => $weights['currency'],
                'news_weight'          => $weights['news'],
                'total_score'          => $total,
                'risk_level'           => $level,
                'risk_label'           => $this->label($level),
                'risk_badge_class'     => $this->badge($level),
                'marker_color'         => $this->color($level),
                'raw_weather'          => $weatherData,
                'raw_economic'         => $econData,
            ];

            $this->persist($result);
            return $result;
        });
    }

    public function calculateMultiple(array $codes): array
    {
        $out = [];
        foreach ($codes as $c) $out[$c] = $this->calculate($c);
        uasort($out, fn($a,$b) => $b['total_score'] <=> $a['total_score']);
        return $out;
    }

    public function compare(string $codeA, string $codeB): array
    {
        $a = $this->calculate($codeA);
        $b = $this->calculate($codeB);
        $winner = $a['total_score'] <= $b['total_score'] ? $codeA : $codeB;
        $winnerName = $winner===$codeA ? $a['country_name'] : $b['country_name'];
        $loserName  = $winner===$codeA ? $b['country_name'] : $a['country_name'];
        $wScore     = $winner===$codeA ? $a['total_score'] : $b['total_score'];
        $lScore     = $winner===$codeA ? $b['total_score'] : $a['total_score'];
        $reason     = "{$winnerName} direkomendasikan untuk supply chain dengan risk score ".number_format($wScore,1)." vs {$loserName}: ".number_format($lScore,1).". Selisih ".round(abs($wScore-$lScore),1)." poin menunjukkan kondisi logistik, cuaca, sentimen berita, dan ekonomi yang lebih stabil.";
        return ['country_a'=>$a,'country_b'=>$b,'winner_risk'=>$winner,'winner_inflation'=>$a['inflation_score']<=$b['inflation_score']?$codeA:$codeB,'recommendation'=>$winner,'recommendation_reason'=>$reason];
    }

    // ── Helpers ───────────────────────────────────────────────

    private function inflationScore(?array $data): float
    {
        if (!$data || !isset($data['inflation'])) return 30.0;
        $v = abs((float)$data['inflation']);
        if ($v<=2) return 5.0; if ($v<=5) return 20.0; if ($v<=10) return 45.0;
        if ($v<=15) return 65.0; if ($v<=20) return 80.0; return 100.0;
    }

    private function newsScore(string $code, string $name): float
    {
        $topics = ['logistics','trade','shipping','economy','geopolitics'];
        $weights= ['logistics'=>0.25,'trade'=>0.20,'shipping'=>0.20,'economy'=>0.20,'geopolitics'=>0.15];
        $weighted = 0.0; $totalW = 0.0;

        foreach ($topics as $topic) {
            $recentNews = NewsCache::byTopic($topic)->recent(48)->get();
            if ($recentNews->isEmpty()) continue;

            // Analisis berita yang belum diproses
            foreach ($recentNews->where('positive_count',0)->where('negative_count',0) as $news) {
                $r = $this->sentiment->analyze("{$news->title} {$news->description}");
                $news->update(['positive_count'=>$r['positive_count'],'negative_count'=>$r['negative_count'],'neutral_count'=>$r['neutral_count'],'sentiment'=>$r['sentiment'],'sentiment_score'=>$r['sentiment_score']]);
            }

            $topicScore = $recentNews->avg(fn($n) => $n->news_risk_score) ?? 50.0;
            $w = $weights[$topic] ?? 0.2;
            $weighted += $topicScore * $w;
            $totalW   += $w;
        }

        return $totalW > 0 ? round($weighted / $totalW, 2) : 40.0;
    }

    private function level(float $score): string
    {
        $t = SystemSetting::getRiskThresholds();
        if ($score<=$t['low']) return 'low';
        if ($score<=$t['medium']) return 'medium';
        if ($score<=$t['high']) return 'high';
        return 'critical';
    }

    private function label(string $l): string  { return match($l){'low'=>'Low Risk','medium'=>'Medium Risk','high'=>'High Risk','critical'=>'Critical Risk',default=>'Unknown'}; }
    private function badge(string $l): string  { return match($l){'low'=>'success','medium'=>'warning','high'=>'danger','critical'=>'dark',default=>'secondary'}; }
    private function color(string $l): string  { return match($l){'low'=>'#198754','medium'=>'#ffc107','high'=>'#dc3545','critical'=>'#212529',default=>'#6c757d'}; }

    private function defaultScore(string $code, string $reason=''): array
    {
        return ['country_code'=>$code,'country_name'=>$code,'flag_emoji'=>'🏳️','score_date'=>today()->toDateString(),'weather_score'=>0.0,'inflation_score'=>0.0,'currency_score'=>0.0,'news_sentiment_score'=>0.0,'total_score'=>0.0,'risk_level'=>'low','risk_label'=>'Low Risk','risk_badge_class'=>'success','marker_color'=>'#198754','error'=>$reason];
    }

    private function persist(array $r): void
    {
        RiskScore::updateOrCreate(
            ['country_code'=>$r['country_code'],'score_date'=>$r['score_date']],
            ['weather_score'=>$r['weather_score'],'inflation_score'=>$r['inflation_score'],'currency_score'=>$r['currency_score'],'news_sentiment_score'=>$r['news_sentiment_score'],'weather_weight'=>$r['weather_weight'],'inflation_weight'=>$r['inflation_weight'],'currency_weight'=>$r['currency_weight'],'news_weight'=>$r['news_weight'],'total_score'=>$r['total_score'],'risk_level'=>$r['risk_level'],'raw_data'=>json_encode(['weather'=>$r['raw_weather'],'economic'=>$r['raw_economic']])]
        );
        RiskHistory::firstOrCreate(
            ['country_code'=>$r['country_code'],'recorded_date'=>$r['score_date']],
            ['total_score'=>$r['total_score'],'weather_score'=>$r['weather_score'],'inflation_score'=>$r['inflation_score'],'currency_score'=>$r['currency_score'],'news_score'=>$r['news_sentiment_score'],'risk_level'=>$r['risk_level']]
        );
    }
}
