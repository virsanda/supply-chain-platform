<?php

namespace App\Services;

use App\Models\CurrencyRate;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

/**
 * ExchangeRate API — Free tier: 1500 req/month
 * https://v6.exchangerate-api.com
 */
class ExchangeRateService extends BaseApiService
{
    protected string $apiName = 'exchangerate';
    protected string $baseUrl = 'https://v6.exchangerate-api.com/v6';

    private const TRACKED = ['IDR','CNY','EUR','GBP','JPY','KRW','INR','SGD','MYR','THB','VND','AED','SAR','BRL','AUD','CAD','ZAR','TRY','RUB','MXN','PHP'];

    public function __construct(private string $apiKey='')
    {
        $this->apiKey = config('services.exchangerate.key','');
    }

    public function getRates(string $base='USD'): ?array
    {
        $min = SystemSetting::get('cache_currency_minutes', 30);
        return Cache::remember($this->cacheKey('rates',$base), $min*60, function () use ($base) {
            if (empty($this->apiKey)) return $this->fromDb($base);
            $data = $this->get("/{$this->apiKey}/latest/{$base}");
            if (!$data || ($data['result']??'')!=='success') return $this->fromDb($base);
            $rates = collect($data['conversion_rates']??[])->only(self::TRACKED)->toArray();
            $this->saveToDb($base, $rates);
            return ['base'=>$base,'rates'=>$rates,'last_updated'=>$data['time_last_update_utc']??now()->toDateTimeString(),'source'=>'api'];
        });
    }

    public function getRate(string $base, string $target): ?float
    {
        return $this->getRates($base)['rates'][$target] ?? null;
    }

    public function getCurrencyTrend(string $base, string $target, int $days=30): array
    {
        $history = CurrencyRate::forPair($base,$target)->where('rate_date','>=',now()->subDays($days))->orderBy('rate_date')->get(['rate_date','rate','change_percent']);
        if ($history->isEmpty()) return $this->simulateTrend($base,$target,$days);
        return ['labels'=>$history->pluck('rate_date')->map(fn($d)=>$d->format('M d'))->toArray(),'rates'=>$history->pluck('rate')->toArray(),'change_percents'=>$history->pluck('change_percent')->toArray()];
    }

    public function getCurrencyRiskScore(string $currency, string $base='USD'): float
    {
        $rate = CurrencyRate::forPair($base,$currency)->today()->latest()->first();
        return $rate ? $rate->currency_risk_score : 30.0;
    }

    private function fromDb(string $base): ?array
    {
        $rates = CurrencyRate::forBase($base)->orderByDesc('rate_date')->get()->groupBy('target_currency')->map(fn($g)=>$g->first()->rate)->toArray();
        return empty($rates) ? null : ['base'=>$base,'rates'=>$rates,'last_updated'=>now()->toDateTimeString(),'source'=>'database'];
    }

    private function saveToDb(string $base, array $rates): void
    {
        $today = today();
        foreach ($rates as $target => $rate) {
            $prev   = CurrencyRate::forPair($base,$target)->where('rate_date','<',$today)->orderByDesc('rate_date')->value('rate');
            $change = ($prev && $prev>0) ? (($rate-$prev)/$prev)*100 : null;
            CurrencyRate::updateOrCreate(['base_currency'=>$base,'target_currency'=>$target,'rate_date'=>$today],['rate'=>$rate,'rate_previous'=>$prev,'change_percent'=>$change]);
        }
    }

    private function simulateTrend(string $base, string $target, int $days): array
    {
        $cur = $this->getRate($base,$target) ?? 1.0;
        $labels=[]; $rates=[];
        for ($i=$days-1; $i>=0; $i--) { $labels[]=now()->subDays($i)->format('M d'); $rates[]=round($cur*(1+(rand(-200,200)/10000)),4); }
        return ['labels'=>$labels,'rates'=>$rates,'change_percents'=>[]];
    }
}
