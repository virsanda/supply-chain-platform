<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCache;
use App\Services\SentimentAnalysisService;

class NewsApiController extends Controller
{
    public function __construct(private SentimentAnalysisService $sentiment) {}

    public function index()
    {
        return response()->json(NewsCache::recent(24)->orderByDesc('published_at')->take(20)->get());
    }

    public function byTopic(string $topic)
    {
        return response()->json(NewsCache::byTopic($topic)->recent(72)->orderByDesc('published_at')->take(20)->get());
    }

    public function sentiment()
    {
        $news     = NewsCache::recent(48)->get();
        $results  = $news->map(fn($n)=>['sentiment'=>$n->sentiment,'sentiment_score'=>$n->sentiment_score])->toArray();
        $aggregate= $this->sentiment->aggregate($results);
        return response()->json($aggregate);
    }
}
