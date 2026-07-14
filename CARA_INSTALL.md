# 🚀 Cara Install & Jalankan Project

## Prasyarat
- PHP 8.1+
- Composer
- MySQL 8.0+
- Git

---

## Langkah Instalasi

### 1. Buka terminal di folder project
```
cd "d:\Global Supply\supply-chain-platform"
```

### 2. Install dependencies PHP
```
composer install
```

### 3. Buat APP_KEY
```
php artisan key:generate
```

### 4. Buat database MySQL
```sql
CREATE DATABASE supply_chain_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan migrasi + seeder
```
php artisan migrate
php artisan db:seed
```

### 6. Jalankan server
```
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## Login Default

| Role  | Email                    | Password    |
|-------|--------------------------|-------------|
| Admin | admin@supplychain.com    | Admin@1234  |
| User  | user@supplychain.com     | User@1234   |

---

## API Keys (opsional)

Isi di file `.env`:

```
EXCHANGERATE_API_KEY=  ← daftar gratis di exchangerate-api.com
GNEWS_API_KEY=         ← daftar gratis di gnews.io
```

Tanpa API key, sistem tetap berjalan menggunakan data cache dari database.

---

## Struktur File Lengkap (95 files)

```
supply-chain-platform/
├── app/
│   ├── Http/
│   │   ├── Controllers/          (11 web + 6 API controllers)
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/                   (17 models)
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/                 (8 services)
│       ├── BaseApiService.php
│       ├── OpenMeteoService.php    ← cuaca (no API key)
│       ├── WorldBankService.php    ← GDP, inflasi (no API key)
│       ├── RestCountriesService.php← data negara (no API key)
│       ├── ExchangeRateService.php ← kurs (optional key)
│       ├── GNewsService.php        ← berita (optional key)
│       ├── SentimentAnalysisService.php ← LEXICON-BASED (PHP murni)
│       └── RiskScoringEngine.php   ← WEIGHTED RISK MODEL
│
├── bootstrap/
│   └── app.php
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── services.php
│   └── session.php
│
├── database/
│   ├── migrations/
│   │   └── 2024_01_01_000001_create_all_tables.php  (17 tabel)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php
│       ├── CountriesSeeder.php      (25 negara)
│       ├── PortsSeeder.php          (55+ pelabuhan)
│       ├── PositiveWordsSeeder.php  (70+ kata)
│       ├── NegativeWordsSeeder.php  (85+ kata)
│       └── SystemSettingsSeeder.php
│
├── public/
│   ├── index.php
│   └── .htaccess
│
├── resources/views/
│   ├── layouts/app.blade.php       ← Bootstrap 5 + Chart.js + Leaflet.js
│   ├── auth/login + register
│   ├── dashboard/index
│   ├── countries/index + show      ← GDP, Inflasi, Risk, Cuaca, Currency
│   ├── weather/index + show        ← Peta Leaflet.js + forecast 7 hari
│   ├── currency/index + show       ← Kurs real-time + Chart.js trends
│   ├── news/index                  ← Sentiment Analysis + berita
│   ├── ports/index                 ← 55+ pelabuhan + Leaflet.js map
│   ├── comparison/index + result   ← Radar chart + bar chart
│   ├── visualization/index + show  ← 4 trend charts
│   ├── watchlist/index             ← Favorite monitoring
│   └── admin/                      ← dashboard, users, ports, articles, settings
│
├── routes/
│   ├── web.php    (30+ web routes)
│   ├── api.php    (17 API routes)
│   └── console.php
│
├── .env
├── .env.example
├── artisan
├── composer.json
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## 10 Fitur Utama

| # | Fitur | Route |
|---|-------|-------|
| 1 | Global Country Dashboard | /countries |
| 2 | Risk Scoring Engine | (otomatis di dashboard) |
| 3 | Global Weather Monitoring | /weather |
| 4 | Currency Impact Dashboard | /currency |
| 5 | News Intelligence | /news |
| 6 | Port Location Dashboard | /ports |
| 7 | Data Visualization Dashboard | /visualization |
| 8 | Country Comparison Engine | /comparison |
| 9 | Favorite Monitoring List | /watchlist |
| 10 | Admin Dashboard | /admin |

---

## REST API Endpoints

```
GET /api/v1/countries
GET /api/v1/countries/{code}
GET /api/v1/risk
GET /api/v1/risk/{code}
POST /api/v1/risk/calculate
GET /api/v1/ports
GET /api/v1/ports/country/{code}
GET /api/v1/news
GET /api/v1/news/sentiment
GET /api/v1/news/topic/{topic}
GET /api/v1/currency
GET /api/v1/currency/{code}
GET /api/v1/weather/{code}
GET /api/v1/weather/risk/{code}
```

---

## Algoritma Risk Scoring (Buatan Sendiri)

```
Risk Score = (Weather × 30%) + (Inflation × 20%) + (Currency × 10%) + (News × 40%)
```

Setiap komponen dihitung 0-100:
- **Weather**: WMO code + wind speed + precipitation
- **Inflation**: Skala bertingkat (≤2% → 5pts, >20% → 100pts)
- **Currency**: Volatilitas kurs terhadap USD (% change)
- **News**: Rata-rata news_risk_score dari sentimen berita

Threshold level:
- Low: 0-30 (hijau)
- Medium: 31-60 (kuning)
- High: 61-80 (merah)
- Critical: 81-100 (hitam)

---

## Sentiment Analysis (Lexicon-Based PHP)

Formula: `sentiment_score = ((positive - negative) / (positive + negative)) × 100`

- positive_score > negative_score + 20% → **positive**
- negative_score > positive_score + 20% → **negative**
- Selainnya → **neutral**

Dictionary: 70+ kata positif, 85+ kata negatif (tersimpan di database)

---

**Selamat mengerjakan! 🎓**
