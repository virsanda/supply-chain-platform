<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\NewsCache;
use App\Models\Port;
use App\Models\Watchlist;
use App\Models\SystemSetting;
use App\Services\RiskScoringEngine;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private RiskScoringEngine $riskEngine) {}

    public function index()
    {
        $defaultCodes = SystemSetting::get('dashboard_default_countries', ['ID','CN','DE','AU','US','JP']);

        // Hitung risk scores untuk negara default
        $riskScores = [];
        foreach ($defaultCodes as $code) {
            $riskScores[$code] = $this->riskEngine->calculate($code);
        }

        // Statistik ringkasan
        $stats = [
            'total_countries' => Country::active()->count(),
            'total_ports'     => Port::active()->count(),
            'high_risk_count' => RiskScore::whereDate('score_date', today())->whereIn('risk_level', ['high','critical'])->count(),
            'news_count'      => NewsCache::recent(24)->count(),
        ];

        // Watchlist user
        $watchlist = Auth::check()
            ? Watchlist::where('user_id', Auth::id())->with('country')->latest()->take(6)->get()
            : collect();

        // Berita terbaru
        $latestNews = NewsCache::recent(24)->orderByDesc('published_at')->take(5)->get();

        // Risk distribution untuk pie chart
        $riskDist = RiskScore::whereDate('score_date', today())
            ->selectRaw('risk_level, COUNT(*) as count')
            ->groupBy('risk_level')
            ->pluck('count','risk_level')
            ->toArray();

        // Semua countries for map markers
        $allRisks = RiskScore::whereDate('score_date', today())
            ->with('country')
            ->get()
            ->map(fn($r) => [
                'code'  => $r->country_code,
                'name'  => $r->country->name ?? $r->country_code,
                'lat'   => $r->country->latitude ?? 0,
                'lng'   => $r->country->longitude ?? 0,
                'score' => $r->total_score,
                'level' => $r->risk_level,
                'label' => $r->risk_label,
                'color' => $r->marker_color,
            ])
            ->toArray();

        return view('dashboard.index', compact('riskScores','stats','watchlist','latestNews','riskDist','allRisks','defaultCodes'));
    }
}
