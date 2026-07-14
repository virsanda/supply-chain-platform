<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin','user'])->default('user');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. countries
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('code3', 3)->nullable();
            $table->string('name');
            $table->string('capital')->nullable();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->bigInteger('population')->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->string('currency_name')->nullable();
            $table->string('flag_emoji', 10)->nullable();
            $table->string('flag_url')->nullable();
            $table->json('languages')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. economic_indicators
        Schema::create('economic_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->integer('year');
            $table->decimal('gdp', 20, 2)->nullable();
            $table->decimal('gdp_per_capita', 15, 2)->nullable();
            $table->decimal('inflation_rate', 8, 4)->nullable();
            $table->decimal('unemployment_rate', 8, 4)->nullable();
            $table->bigInteger('population')->nullable();
            $table->decimal('exports_usd', 20, 2)->nullable();
            $table->decimal('imports_usd', 20, 2)->nullable();
            $table->decimal('trade_balance', 20, 2)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
            $table->unique(['country_code','year']);
            $table->index('country_code');
        });

        // 4. risk_scores
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->date('score_date');
            $table->decimal('weather_score', 5, 2)->default(0);
            $table->decimal('inflation_score', 5, 2)->default(0);
            $table->decimal('currency_score', 5, 2)->default(0);
            $table->decimal('news_sentiment_score', 5, 2)->default(0);
            $table->decimal('weather_weight', 4, 2)->default(30);
            $table->decimal('inflation_weight', 4, 2)->default(20);
            $table->decimal('currency_weight', 4, 2)->default(10);
            $table->decimal('news_weight', 4, 2)->default(40);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->enum('risk_level', ['low','medium','high','critical'])->default('low');
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->unique(['country_code','score_date']);
            $table->index(['country_code','score_date']);
            $table->index('risk_level');
        });

        // 5. weather_cache
        Schema::create('weather_cache', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('temperature_2m', 6, 2)->nullable();
            $table->decimal('apparent_temperature', 6, 2)->nullable();
            $table->decimal('precipitation', 8, 2)->nullable();
            $table->decimal('windspeed_10m', 8, 2)->nullable();
            $table->decimal('windgusts_10m', 8, 2)->nullable();
            $table->integer('weathercode')->nullable();
            $table->string('weather_description')->nullable();
            $table->integer('cloudcover')->nullable();
            $table->integer('humidity')->nullable();
            $table->boolean('is_storm')->default(false);
            $table->boolean('is_heavy_rain')->default(false);
            $table->boolean('is_strong_wind')->default(false);
            $table->decimal('weather_risk_score', 5, 2)->default(0);
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });

        // 6. currency_rates
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 10);
            $table->string('target_currency', 10);
            $table->decimal('rate', 20, 8);
            $table->decimal('rate_previous', 20, 8)->nullable();
            $table->decimal('change_percent', 8, 4)->nullable();
            $table->date('rate_date');
            $table->timestamps();
            $table->index(['base_currency','target_currency']);
            $table->index('rate_date');
        });

        // 7. news_cache
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('url')->unique();
            $table->string('image_url')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->string('language', 10)->default('en');
            $table->string('country_code', 3)->nullable();
            $table->enum('topic', ['logistics','trade','shipping','economy','geopolitics','general'])->default('general');
            $table->timestamp('published_at')->nullable();
            $table->integer('positive_count')->default(0);
            $table->integer('negative_count')->default(0);
            $table->integer('neutral_count')->default(0);
            $table->enum('sentiment', ['positive','negative','neutral'])->default('neutral');
            $table->decimal('sentiment_score', 5, 2)->default(0);
            $table->timestamp('fetched_at')->nullable()->useCurrent();
            $table->timestamps();
            $table->index('country_code');
            $table->index('topic');
            $table->index('sentiment');
            $table->index('published_at');
        });

        // 8. ports
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('port_name');
            $table->string('port_code')->nullable();
            $table->string('country_code', 3)->nullable();
            $table->string('country_name')->nullable();
            $table->string('province_region')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('harbor_size', ['very_small','small','medium','large'])->nullable();
            $table->enum('harbor_type', ['coastal','river','lake','open_roadstead','canal','other'])->nullable();
            $table->boolean('shelter_afforded')->default(false);
            $table->boolean('entrance_tide')->default(false);
            $table->boolean('max_vessel_size_ocean')->default(false);
            $table->integer('max_draft_ft')->nullable();
            $table->boolean('good_holding_ground')->default(false);
            $table->boolean('turning_area')->default(false);
            $table->boolean('first_port_of_entry')->default(false);
            $table->enum('congestion_level', ['low','moderate','high','critical'])->default('low');
            $table->decimal('congestion_score', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('country_code');
            $table->index('congestion_level');
        });

        // 9. watchlists
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('country_code', 3);
            $table->string('country_name');
            $table->string('notes')->nullable();
            $table->boolean('notify_risk_change')->default(true);
            $table->timestamps();
            $table->unique(['user_id','country_code']);
        });

        // 10. articles
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->string('country_code', 3)->nullable();
            $table->enum('category', ['risk_analysis','market_update','logistics','geopolitics','economy'])->default('risk_analysis');
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->index('slug');
            $table->index('status');
        });

        // 11. positive_words
        Schema::create('positive_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->integer('weight')->default(1);
            $table->string('category')->nullable();
            $table->timestamps();
        });

        // 12. negative_words
        Schema::create('negative_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->integer('weight')->default(1);
            $table->string('category')->nullable();
            $table->timestamps();
        });

        // 13. risk_history
        Schema::create('risk_history', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->date('recorded_date');
            $table->decimal('total_score', 5, 2);
            $table->decimal('weather_score', 5, 2)->default(0);
            $table->decimal('inflation_score', 5, 2)->default(0);
            $table->decimal('currency_score', 5, 2)->default(0);
            $table->decimal('news_score', 5, 2)->default(0);
            $table->enum('risk_level', ['low','medium','high','critical']);
            $table->timestamps();
            $table->index(['country_code','recorded_date']);
        });

        // 14. api_logs
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_name');
            $table->string('endpoint');
            $table->string('method', 10)->default('GET');
            $table->json('parameters')->nullable();
            $table->integer('response_code')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->timestamp('called_at');
            $table->timestamps();
            $table->index('api_name');
            $table->index('called_at');
        });

        // 15. user_activity_logs
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('subject')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('action');
        });

        // 16. comparison_snapshots
        Schema::create('comparison_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('country_a', 3);
            $table->string('country_b', 3);
            $table->json('country_a_data');
            $table->json('country_b_data');
            $table->string('winner_gdp', 3)->nullable();
            $table->string('winner_risk', 3)->nullable();
            $table->string('winner_inflation', 3)->nullable();
            $table->string('recommendation', 3)->nullable();
            $table->text('recommendation_reason')->nullable();
            $table->timestamps();
            $table->index(['country_a','country_b']);
        });

        // 17. system_settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->string('description')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
            $table->index('key');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('comparison_snapshots');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('risk_history');
        Schema::dropIfExists('negative_words');
        Schema::dropIfExists('positive_words');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('ports');
        Schema::dropIfExists('news_cache');
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('weather_cache');
        Schema::dropIfExists('risk_scores');
        Schema::dropIfExists('economic_indicators');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('users');
    }
};
