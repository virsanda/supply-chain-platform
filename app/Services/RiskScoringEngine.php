public function calculate(string $code): array
{
    $key = "risk_score:{$code}:".today()->format('Y-m-d');

    return Cache::remember($key, 3600, function () use ($code) {

        $country = Country::where('code', $code)->first();

        if (!$country) {
            return $this->defaultScore($code, 'Country not found');
        }

        $weights = SystemSetting::getRiskWeights();

        // ===========================
        // SEMENTARA TANPA API EKSTERNAL
        // ===========================

        $weatherData = [];

        $weatherScore = 20;
        $inflationScore = 20;
        $currencyScore = 20;
        $newsScore = 20;

        // ===========================

        $total = round(
            ($weatherScore * $weights['weather'] / 100) +
            ($inflationScore * $weights['inflation'] / 100) +
            ($currencyScore * $weights['currency'] / 100) +
            ($newsScore * $weights['news'] / 100),
            2
        );

        $level = $this->level($total);

        $result = [
            'country_code' => $code,
            'country_name' => $country->name,
            'flag_emoji' => $country->flag_emoji,
            'score_date' => today()->toDateString(),

            'weather_score' => $weatherScore,
            'inflation_score' => $inflationScore,
            'currency_score' => $currencyScore,
            'news_sentiment_score' => $newsScore,

            'weather_weight' => $weights['weather'],
            'inflation_weight' => $weights['inflation'],
            'currency_weight' => $weights['currency'],
            'news_weight' => $weights['news'],

            'total_score' => $total,
            'risk_level' => $level,
            'risk_label' => $this->label($level),
            'risk_badge_class' => $this->badge($level),
            'marker_color' => $this->color($level),

            'raw_weather' => [],
            'raw_economic' => [],
        ];

        $this->persist($result);

        return $result;
    });
}