<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;

/**
 * REST Countries API — Gratis, tanpa API Key
 * https://restcountries.com/v3.1
 */
class RestCountriesService extends BaseApiService
{
    protected string $apiName = 'restcountries';
    protected string $baseUrl = 'https://restcountries.com/v3.1';

    public function getCountry(string $code): ?array
    {
        return Cache::remember($this->cacheKey('country',strtolower($code)), 86400, function () use ($code) {
            $data = $this->get("/alpha/{$code}", ['fields'=>'name,capital,region,subregion,latlng,population,currencies,languages,flags,cca2,cca3']);
            if (!$data || !is_array($data)) return null;
            $raw    = is_array($data[0] ?? null) ? $data[0] : $data;
            $result = $this->normalize($raw);
            if ($result) $this->saveToDb($result);
            return $result;
        });
    }

    private function normalize(array $raw): ?array
    {
        $code = $raw['cca2'] ?? null;
        if (!$code) return null;
        $latlng       = $raw['latlng'] ?? [0,0];
        $currCode     = !empty($raw['currencies']) ? array_key_first($raw['currencies']) : null;
        $currName     = $currCode ? ($raw['currencies'][$currCode]['name'] ?? null) : null;
        $capital      = !empty($raw['capital']) ? $raw['capital'][0] : null;
        $languages    = !empty($raw['languages']) ? array_values($raw['languages']) : [];
        return ['code'=>strtoupper($code),'code3'=>strtoupper($raw['cca3']??''),'name'=>$raw['name']['common']??'Unknown','capital'=>$capital,'region'=>$raw['region']??null,'subregion'=>$raw['subregion']??null,'latitude'=>$latlng[0]??0,'longitude'=>$latlng[1]??0,'population'=>$raw['population']??null,'currency_code'=>$currCode,'currency_name'=>$currName,'flag_url'=>$raw['flags']['png']??null,'languages'=>$languages];
    }

    private function saveToDb(array $c): void
    {
        if (empty($c['code'])) return;
        Country::updateOrCreate(['code'=>$c['code']], ['code3'=>$c['code3']??null,'name'=>$c['name'],'capital'=>$c['capital']??null,'region'=>$c['region']??null,'subregion'=>$c['subregion']??null,'latitude'=>$c['latitude']??null,'longitude'=>$c['longitude']??null,'population'=>$c['population']??null,'currency_code'=>$c['currency_code']??null,'currency_name'=>$c['currency_name']??null,'flag_url'=>$c['flag_url']??null,'languages'=>json_encode($c['languages']??[]),'is_active'=>true]);
    }
}
