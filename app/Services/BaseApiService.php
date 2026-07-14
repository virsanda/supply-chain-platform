<?php

namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseApiService
{
    protected string $apiName = 'unknown';
    protected string $baseUrl = '';
    protected int $timeout    = 15;

    protected function get(string $endpoint, array $params=[]): ?array
    {
        $url   = $this->baseUrl.$endpoint;
        $start = microtime(true);
        try {
            $res     = Http::timeout($this->timeout)->get($url, $params);
            $elapsed = (int)((microtime(true) - $start) * 1000);
            ApiLog::record($this->apiName, $endpoint, $res->successful(), $res->status(), $elapsed, $params, $res->successful() ? null : $res->body());
            if ($res->successful()) return $res->json();
            Log::warning("[{$this->apiName}] HTTP {$res->status()} → {$url}");
            return null;
        } catch (\Throwable $e) {
            $elapsed = (int)((microtime(true) - $start) * 1000);
            ApiLog::record($this->apiName, $endpoint, false, 0, $elapsed, $params, $e->getMessage());
            Log::error("[{$this->apiName}] {$e->getMessage()}");
            return null;
        }
    }

    protected function cacheKey(string ...$parts): string
    {
        return $this->apiName.':'.implode(':',$parts);
    }
}
