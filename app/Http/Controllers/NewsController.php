<?php

namespace App\Http\Controllers;

use App\Models\NewsCache;
use App\Services\GNewsService;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(private GNewsService $newsService, private SentimentAnalysisService $sentiment) {}

    public function index(Request $request)
    {
        $topic  = $request->input('topic','logistics');
        $this->newsService->getNewsByTopic($topic);
        $this->analyzeUnprocessed();

        $newsList        = NewsCache::byTopic($topic)->recent(72)->orderByDesc('published_at')->paginate(12);
        $sentimentSummary= $this->sentimentSummary($topic);
        $topics          = ['logistics','trade','shipping','economy','geopolitics'];

        return view('news.index', compact('newsList','topic','topics','sentimentSummary'));
    }

    public function byTopic(string $topic)
    {
        if (!in_array($topic,['logistics','trade','shipping','economy','geopolitics'])) abort(404);
        $this->newsService->getNewsByTopic($topic);
        $this->analyzeUnprocessed();

        $newsList        = NewsCache::byTopic($topic)->recent(72)->orderByDesc('published_at')->paginate(12);
        $sentimentSummary= $this->sentimentSummary($topic);
        $topics          = ['logistics','trade','shipping','economy','geopolitics'];

        if (request()->ajax()) return response()->json(['news'=>$newsList->items(),'sentiment'=>$sentimentSummary]);
        return view('news.index', compact('newsList','topic','topics','sentimentSummary'));
    }

    private function analyzeUnprocessed(): void
    {
        NewsCache::where('positive_count',0)->where('negative_count',0)->latest()->take(20)->get()->each(function ($news) {
            $r = $this->sentiment->analyze("{$news->title} {$news->description}");
            $news->update(['positive_count'=>$r['positive_count'],'negative_count'=>$r['negative_count'],'neutral_count'=>$r['neutral_count'],'sentiment'=>$r['sentiment'],'sentiment_score'=>$r['sentiment_score']]);
        });
    }

    private function sentimentSummary(string $topic): array
    {
        $news  = NewsCache::byTopic($topic)->recent(48)->get();
        if ($news->isEmpty()) return ['positive'=>0,'neutral'=>100,'negative'=>0,'total'=>0,'dominant'=>'neutral','avg_score'=>0];
        $total = $news->count();
        $pos   = $news->where('sentiment','positive')->count();
        $neg   = $news->where('sentiment','negative')->count();
        $neu   = $news->where('sentiment','neutral')->count();
        return [
            'positive'  => round(($pos/$total)*100,1),
            'negative'  => round(($neg/$total)*100,1),
            'neutral'   => round(($neu/$total)*100,1),
            'total'     => $total,
            'dominant'  => $pos>=$neg&&$pos>=$neu ? 'positive' : ($neg>=$neu ? 'negative' : 'neutral'),
            'avg_score' => round($news->avg('sentiment_score'),1),
        ];
    }
}
