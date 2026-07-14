<?php

namespace App\Services;

use App\Models\NewsCache;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

/**
 * GNews API — Free tier: 100 req/day
 * https://gnews.io
 */
class GNewsService extends BaseApiService
{
    protected string $apiName = 'gnews';
    protected string $baseUrl = 'https://gnews.io/api/v4';

    private const QUERIES = [
        'logistics'  => 'logistics OR supply chain OR shipping OR freight',
        'trade'      => 'trade OR export OR import OR tariff',
        'shipping'   => 'shipping OR port OR cargo OR maritime',
        'economy'    => 'economy OR GDP OR inflation OR recession',
        'geopolitics'=> 'war OR conflict OR sanction OR embargo',
    ];

    public function __construct(private string $apiKey='')
    {
        $this->apiKey = config('services.gnews.key','');
    }

    public function getNewsByTopic(string $topic, int $max=10): array
    {
        $min = SystemSetting::get('cache_news_minutes', 120);
        return Cache::remember($this->cacheKey('topic',$topic), $min*60, function () use ($topic,$max) {
            if (empty($this->apiKey)) return $this->fromDb($topic,$max);
            $data = $this->get('/search', ['q'=>self::QUERIES[$topic]??$topic,'lang'=>'en','max'=>$max,'apikey'=>$this->apiKey,'sortby'=>'publishedAt']);
            if (!$data || !isset($data['articles'])) return $this->fromDb($topic,$max);
            foreach ($data['articles'] as $a) $this->saveToDb($this->normalize($a,$topic));
            return $data['articles'];
        });
    }

    public function getNewsByCountry(string $code, int $max=5): array
    {
        return Cache::remember($this->cacheKey('country',$code), 7200, function () use ($code,$max) {
            if (empty($this->apiKey)) return $this->fromDb(null,$max,$code);
            $data = $this->get('/search', ['q'=>'supply chain OR logistics OR trade','country'=>strtolower($code),'max'=>$max,'apikey'=>$this->apiKey,'sortby'=>'publishedAt']);
            if (!$data || !isset($data['articles'])) return $this->fromDb(null,$max,$code);
            foreach ($data['articles'] as $a) $this->saveToDb($this->normalize($a,'general',$code));
            return $data['articles'];
        });
    }

    private function normalize(array $raw, string $topic='general', string $code=null): array
    {
        return ['title'=>$raw['title']??'','description'=>$raw['description']??'','content'=>$raw['content']??'','url'=>$raw['url']??'','image_url'=>$raw['image']??null,'source_name'=>$raw['source']['name']??'Unknown','source_url'=>$raw['source']['url']??null,'language'=>'en','country_code'=>$code,'topic'=>$topic,'published_at'=>$raw['publishedAt']??now()->toDateTimeString()];
    }

    private function saveToDb(array $a): void
    {
        if (empty($a['url']) || NewsCache::where('url',$a['url'])->exists()) return;
        NewsCache::create(array_merge($a,['fetched_at'=>now(),'sentiment'=>'neutral']));
    }

    private function fromDb(?string $topic, int $max, ?string $code=null): array
    {
        $q = NewsCache::recent(72)->orderByDesc('published_at')->limit($max);
        if ($topic) $q->byTopic($topic);
        if ($code)  $q->forCountry($code);
        $results = $q->get()->toArray();

        // Jika tidak ada data sama sekali, seed dummy news agar tampilan tidak kosong
        if (empty($results)) {
            $this->seedDummyNews();
            $q2 = NewsCache::recent(72)->orderByDesc('published_at')->limit($max);
            if ($topic) $q2->byTopic($topic);
            return $q2->get()->toArray();
        }

        return $results;
    }

    /**
     * Seed dummy/sample news ke database saat tidak ada API key.
     * Data ini mensimulasikan berita supply chain global.
     */
    private function seedDummyNews(): void
    {
        // Cegah double seeding
        if (NewsCache::count() > 0) return;

        $dummies = [
            // Logistics
            ['title'=>'Global Shipping Routes Face New Disruptions in Red Sea','description'=>'Major shipping companies are rerouting vessels away from the Red Sea due to ongoing security concerns, adding days to transit times and increasing costs for global supply chains.','url'=>'https://example.com/news/shipping-red-sea-1','source_name'=>'Supply Chain Digest','topic'=>'logistics','published_at'=>now()->subHours(2)],
            ['title'=>'Port of Singapore Reports Record Container Throughput','description'=>'Singapore\'s port authority announced record container handling volumes this quarter, demonstrating the port\'s resilience amid global logistics challenges.','url'=>'https://example.com/news/singapore-port-record','source_name'=>'Maritime Executive','topic'=>'logistics','published_at'=>now()->subHours(5)],
            ['title'=>'Supply Chain Delays Continue to Impact Manufacturing Sector','description'=>'Manufacturers worldwide report ongoing delays in receiving critical components, with semiconductor shortages still affecting production schedules across multiple industries.','url'=>'https://example.com/news/supply-chain-delays','source_name'=>'Reuters','topic'=>'logistics','published_at'=>now()->subHours(8)],
            ['title'=>'New Freight Rail Corridor Opens Between Europe and Asia','description'=>'A new rail freight corridor connecting Central Europe to East Asia has been inaugurated, offering faster and more reliable alternatives to sea freight for certain cargo types.','url'=>'https://example.com/news/rail-corridor-europe-asia','source_name'=>'Logistics World','topic'=>'logistics','published_at'=>now()->subHours(12)],
            ['title'=>'Air Freight Demand Surges as E-commerce Growth Accelerates','description'=>'Air cargo volumes have increased significantly as e-commerce retailers seek faster delivery options, putting pressure on airport capacity at major hubs globally.','url'=>'https://example.com/news/air-freight-ecommerce','source_name'=>'Air Cargo World','topic'=>'logistics','published_at'=>now()->subHours(18)],

            // Trade
            ['title'=>'US and EU Reach New Trade Agreement on Technology Exports','description'=>'The United States and European Union have finalized a bilateral agreement on technology export controls, easing restrictions on semiconductor and AI hardware trade between the two blocs.','url'=>'https://example.com/news/us-eu-trade-tech','source_name'=>'Financial Times','topic'=>'trade','published_at'=>now()->subHours(3)],
            ['title'=>'China Export Growth Slows Amid Weakening Global Demand','description'=>'China\'s export growth has decelerated for the third consecutive month as demand from major trading partners weakens, raising concerns about economic momentum.','url'=>'https://example.com/news/china-exports-slow','source_name'=>'Bloomberg','topic'=>'trade','published_at'=>now()->subHours(6)],
            ['title'=>'Indonesia Boosts Palm Oil Exports to European Markets','description'=>'Indonesia has increased palm oil exports following negotiations that resolved earlier trade disputes with European regulators over sustainability certifications.','url'=>'https://example.com/news/indonesia-palm-oil','source_name'=>'Jakarta Post','topic'=>'trade','published_at'=>now()->subHours(10)],
            ['title'=>'WTO Reports Global Trade Volume Growth of 3.2% This Year','description'=>'The World Trade Organization has revised upward its forecast for global merchandise trade growth, citing stronger-than-expected performance in services trade.','url'=>'https://example.com/news/wto-trade-growth','source_name'=>'WTO News','topic'=>'trade','published_at'=>now()->subHours(24)],

            // Shipping
            ['title'=>'Container Shipping Rates Rise 15% on Asia-Europe Routes','description'=>'Spot freight rates on major Asia-Europe shipping lanes have increased sharply as carriers reduce capacity and demand from European importers remains strong.','url'=>'https://example.com/news/container-rates-asia-europe','source_name'=>'Drewry','topic'=>'shipping','published_at'=>now()->subHours(4)],
            ['title'=>'Maersk Announces Fleet Expansion with New LNG-Powered Vessels','description'=>'A.P. Moller-Maersk has ordered ten new LNG-powered container ships as part of its decarbonization strategy, with deliveries expected over the next three years.','url'=>'https://example.com/news/maersk-lng-fleet','source_name'=>'Hellenic Shipping News','topic'=>'shipping','published_at'=>now()->subHours(7)],
            ['title'=>'Panama Canal Restricts Draft Depth Due to Low Water Levels','description'=>'The Panama Canal Authority has implemented draft restrictions as water levels in Gatun Lake remain below seasonal averages, forcing larger vessels to reduce cargo loads.','url'=>'https://example.com/news/panama-canal-draft','source_name'=>'Lloyd\'s List','topic'=>'shipping','published_at'=>now()->subHours(14)],

            // Economy
            ['title'=>'IMF Upgrades Global Growth Forecast to 3.1% for Current Year','description'=>'The International Monetary Fund has raised its global economic growth projection, citing resilient consumer spending in advanced economies and strong performance in Asian markets.','url'=>'https://example.com/news/imf-growth-forecast','source_name'=>'IMF','topic'=>'economy','published_at'=>now()->subHours(1)],
            ['title'=>'Inflation Eases in Major Economies as Central Banks Hold Rates','description'=>'Consumer price inflation has moderated across most G7 economies, with central banks signaling they may begin cutting interest rates in the coming months as price pressures stabilize.','url'=>'https://example.com/news/inflation-eases-g7','source_name'=>'The Economist','topic'=>'economy','published_at'=>now()->subHours(9)],
            ['title'=>'Southeast Asia Emerges as Manufacturing Hub Amid China+1 Strategies','description'=>'Vietnam, Indonesia, and Thailand are attracting record foreign direct investment as multinational companies diversify their manufacturing base beyond China.','url'=>'https://example.com/news/sea-manufacturing-hub','source_name'=>'Nikkei Asia','topic'=>'economy','published_at'=>now()->subHours(16)],
            ['title'=>'Germany Economy Contracts for Second Quarter, Recession Risk Rises','description'=>'Germany\'s GDP contracted in the second quarter, raising the possibility of a technical recession as the country\'s industrial sector continues to struggle with high energy costs.','url'=>'https://example.com/news/germany-recession-risk','source_name'=>'Deutsche Welle','topic'=>'economy','published_at'=>now()->subHours(20)],
            ['title'=>'India GDP Growth Accelerates to 7.8%, Outpacing Major Economies','description'=>'India\'s economy grew at an annual rate of 7.8% in the latest quarter, making it the fastest-growing major economy and attracting increased investor attention.','url'=>'https://example.com/news/india-gdp-growth','source_name'=>'Economic Times','topic'=>'economy','published_at'=>now()->subHours(28)],

            // Geopolitics
            ['title'=>'G7 Nations Agree on New Framework for Critical Mineral Supply Chains','description'=>'Leaders of the G7 countries have endorsed a comprehensive framework to secure supply chains for critical minerals essential for clean energy technologies and semiconductor manufacturing.','url'=>'https://example.com/news/g7-critical-minerals','source_name'=>'Reuters','topic'=>'geopolitics','published_at'=>now()->subHours(2)],
            ['title'=>'Tensions in Taiwan Strait Raise Concerns Over Semiconductor Supply','description'=>'Analysts warn that rising tensions in the Taiwan Strait could disrupt global semiconductor supply chains, as Taiwan produces over 90% of the world\'s most advanced chips.','url'=>'https://example.com/news/taiwan-semiconductor-risk','source_name'=>'CNBC','topic'=>'geopolitics','published_at'=>now()->subHours(11)],
            ['title'=>'New Sanctions Package Targets Russian Energy Exports','description'=>'Western nations have announced a new round of sanctions targeting Russian energy exports, potentially affecting oil and gas flows to global markets and impacting energy prices.','url'=>'https://example.com/news/russia-sanctions-energy','source_name'=>'BBC News','topic'=>'geopolitics','published_at'=>now()->subHours(15)],
            ['title'=>'ASEAN Summit Focuses on Regional Trade Integration','description'=>'Southeast Asian leaders meeting at the ASEAN summit prioritized deeper regional economic integration, with discussions on reducing non-tariff barriers and improving connectivity.','url'=>'https://example.com/news/asean-trade-integration','source_name'=>'Channel News Asia','topic'=>'geopolitics','published_at'=>now()->subHours(22)],
        ];

        foreach ($dummies as $d) {
            if (NewsCache::where('url', $d['url'])->exists()) continue;
            NewsCache::create([
                'title'          => $d['title'],
                'description'    => $d['description'],
                'content'        => $d['description'],
                'url'            => $d['url'],
                'image_url'      => null,
                'source_name'    => $d['source_name'],
                'source_url'     => null,
                'language'       => 'en',
                'country_code'   => null,
                'topic'          => $d['topic'],
                'published_at'   => $d['published_at'],
                'positive_count' => 0,
                'negative_count' => 0,
                'neutral_count'  => 0,
                'sentiment'      => 'neutral',
                'sentiment_score'=> 0,
                'fetched_at'     => now(),
            ]);
        }
    }

    public function getCountrySentimentScore(string $code): float
    {
        $news = NewsCache::forCountry($code)->recent(48)->get();
        if ($news->isEmpty()) return 50.0;
        return $news->avg(fn($n)=>$n->news_risk_score) ?? 50.0;
    }
}
