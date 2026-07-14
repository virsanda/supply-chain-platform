<?php

namespace App\Services;

use App\Models\EconomicIndicator;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

/**
 * World Bank API — Gratis, tanpa API Key
 * https://api.worldbank.org/v2
 */
class WorldBankService extends BaseApiService
{
    protected string $apiName = 'worldbank';
    protected string $baseUrl = 'https://api.worldbank.org/v2';

    private const INDICATORS = [
        'gdp'         => 'NY.GDP.MKTP.CD',
        'gdp_per_cap' => 'NY.GDP.PCAP.CD',
        'inflation'   => 'FP.CPI.TOTL.ZG',
        'unemployment'=> 'SL.UEM.TOTL.ZS',
        'population'  => 'SP.POP.TOTL',
        'exports'     => 'NE.EXP.GNFS.CD',
        'imports'     => 'NE.IMP.GNFS.CD',
    ];

    public function getEconomicIndicators(string $code): ?array
    {
        $hours = SystemSetting::get('cache_worldbank_hours', 24);
        return Cache::remember($this->cacheKey('indicators',$code), $hours*3600, function () use ($code) {
            $year = (int)date('Y') - 1;
            $data = [];
            foreach (self::INDICATORS as $key => $indicator) {
                $data[$key] = $this->fetchIndicator($code, $indicator, $year);
            }
            if (!empty(array_filter($data))) {
                $tb = ($data['exports'] && $data['imports']) ? $data['exports'] - $data['imports'] : null;
                EconomicIndicator::updateOrCreate(
                    ['country_code'=>strtoupper($code),'year'=>$year],
                    ['gdp'=>$data['gdp'],'gdp_per_capita'=>$data['gdp_per_cap'],'inflation_rate'=>$data['inflation'],'unemployment_rate'=>$data['unemployment'],'population'=>$data['population']??(null),'exports_usd'=>$data['exports'],'imports_usd'=>$data['imports'],'trade_balance'=>$tb,'fetched_at'=>now()]
                );
            }
            return $data;
        });
    }

    private function fetchIndicator(string $code, string $indicator, int $year): ?float
    {
        $resp = $this->get("/country/{$code}/indicator/{$indicator}", ['format'=>'json','date'=>"{$year}:{$year}",'per_page'=>1]);
        if (!$resp || count($resp)<2 || !is_array($resp[1]) || empty($resp[1])) return null;
        return isset($resp[1][0]['value']) && $resp[1][0]['value'] !== null ? (float)$resp[1][0]['value'] : null;
    }

    public function getGdpTrend(string $code, int $years=5): array
    {
        return Cache::remember($this->cacheKey('gdp-trend',$code), 86400, function () use ($code,$years) {
            $cur = (int)date('Y')-1; $from = $cur-$years+1;
            $resp = $this->get("/country/{$code}/indicator/".self::INDICATORS['gdp'], ['format'=>'json','date'=>"{$from}:{$cur}",'per_page'=>$years]);
            if (!$resp || count($resp)<2) return [];
            return collect($resp[1])->map(fn($i)=>['year'=>$i['date']??null,'value'=>$i['value']??null])->filter(fn($i)=>$i['value']!==null)->sortBy('year')->values()->toArray();
        });
    }

    public function getInflationTrend(string $code, int $years=5): array
    {
        return Cache::remember($this->cacheKey('inflation-trend',$code), 86400, function () use ($code,$years) {
            $cur = (int)date('Y')-1; $from = $cur-$years+1;
            $resp = $this->get("/country/{$code}/indicator/".self::INDICATORS['inflation'], ['format'=>'json','date'=>"{$from}:{$cur}",'per_page'=>$years]);
            if (!$resp || count($resp)<2) return [];
            return collect($resp[1])->map(fn($i)=>['year'=>$i['date']??null,'value'=>$i['value']??null])->filter(fn($i)=>$i['value']!==null)->sortBy('year')->values()->toArray();
        });
    }
}
