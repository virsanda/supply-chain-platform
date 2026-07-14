# 🌍 Global Supply Chain Risk Intelligence Platform

Platform monitoring risiko rantai pasok global berbasis Multi-API dan Analitik Data.

## 📦 Instalasi Cepat

```bash
cd "d:\Global Supply\supply-chain-platform"

# 1. Install dependencies
composer install

# 2. Setup environment
copy .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env
# DB_DATABASE=supply_chain_db
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Create database
mysql -u root -e "CREATE DATABASE supply_chain_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations & seeders
php artisan migrate
php artisan db:seed

# 6. Start server
php artisan serve
```

**Login:**
- Admin: `admin@supplychain.com` / `Admin@1234`
- User: `user@supplychain.com` / `User@1234`

---

## 🎯 10 Fitur Utama

### 1. **Global Country Dashboard**
User pilih negara → tampilkan GDP, Inflasi, Populasi, Mata uang, Cuaca

### 2. **Risk Scoring Engine** ⚡
```
Risk Score = (Weather×30%) + (Inflation×20%) + (Currency×10%) + (News×40%)
```
- Output: Low (0-30), Medium (31-60), High (61-80), Critical (81-100)
- Bobot dikonfigurasi Admin

### 3. **Global Weather Monitoring** 🌤️
Peta Leaflet.js → temperatur, curah hujan, angin, badai (Open-Meteo API)

### 4. **Currency Impact Dashboard** 💱
Nilai tukar real-time + grafik Chart.js (ExchangeRate API)

### 5. **News Intelligence** 📰
Berita + **Lexicon-Based Sentiment Analysis** (PHP, 150+ kata dictionary)

### 6. **Port Location Dashboard** 🚢
55+ pelabuhan dunia → search, filter, Leaflet markers, congestion level

### 7. **Data Visualization Dashboard** 📊
GDP Trend, Inflation Trend, Currency Trend, Risk Trend (Chart.js)

### 8. **Country Comparison Engine** ⚖️
Bandingkan 2 negara → GDP, Inflation, Risk, Weather, Currency + AI recommendation

### 9. **Favorite Monitoring List** ⭐
User simpan negara ke watchlist → notifikasi risk change

### 10. **Admin Dashboard** 👨‍💼
Kelola users, ports, articles, konfigurasi risk weights

---

## 🛠️ Tech Stack

**Backend:** PHP 8.1+ | Laravel 10 | MySQL 8  
**Frontend:** Bootstrap 5 | JavaScript ES6 (AJAX) | Chart.js | Leaflet.js  
**APIs (6 gratis):**
1. Open-Meteo (cuaca, no key)
2. World Bank (GDP/inflasi, no key)
3. REST Countries (negara, no key)
4. ExchangeRate (kurs, free 1500/mo)
5. GNews (berita, free 100/day)
6. World Port Index (dataset publik)

---

## 📊 Database (17 tabel)

users | countries | economic_indicators | risk_scores | weather_cache | currency_rates | news_cache | ports | watchlists | articles | positive_words | negative_words | risk_history | api_logs | user_activity_logs | comparison_snapshots | system_settings

---

## 🧠 Algoritma Khusus

### Risk Scoring Engine
**File:** `app/Services/RiskScoringEngine.php`

```php
$totalScore = (
    ($weatherScore   × 30%) +
    ($inflationScore × 20%) +
    ($currencyScore  × 10%) +
    ($newsScore      × 40%)
);
```

**Weather Risk** (0-100): WMO code + wind + rain  
**Inflation Risk** (0-100): ≤2%→5pts, ≤5%→20pts, >20%→100pts  
**Currency Risk** (0-100): Volatilitas kurs vs USD  
**News Risk** (0-100): Sentimen negatif = score tinggi

### Sentiment Analysis
**File:** `app/Services/SentimentAnalysisService.php`

**Algoritma Lexicon-Based:**
1. Preprocessing: lowercase, remove HTML/URLs, clean special chars
2. Tokenization: split words (length ≥ 3)
3. Matching: cocokkan dengan 70+ positive words, 85+ negative words
4. Scoring: `sentiment_score = ((pos - neg) / (pos + neg)) × 100`
5. Classification: positive / negative / neutral (threshold 20%)

**Contoh:**
```
Text: "Inflation increases while exports decrease due to war."

Match:
- Positive: increase (weight 2)
- Negative: inflation(4), decrease(2), war(5) = 11

Result:
- Score: -69.23 (NEGATIVE)
- Sentiment: negative
```

---

## 📡 API Endpoints

```
GET /api/v1/countries              - Semua negara
GET /api/v1/countries/{code}       - Detail negara
GET /api/v1/risk                   - Risk scores
GET /api/v1/risk/{code}            - Risk detail
POST /api/v1/risk/calculate        - Hitung risk
GET /api/v1/ports                  - Semua pelabuhan
GET /api/v1/ports/country/{code}   - Pelabuhan per negara
GET /api/v1/news                   - Berita
GET /api/v1/news/sentiment         - Aggregate sentiment
GET /api/v1/currency               - Kurs
GET /api/v1/weather/{code}         - Cuaca
```

---

## 🔧 Troubleshooting

**Migration fails:**
```bash
php artisan migrate:fresh
php artisan db:seed --force
```

**Class not found:**
```bash
composer dump-autoload
php artisan optimize:clear
```

**API not working:**
- Sistem fallback ke cached data jika API fail
- Check logs: `storage/logs/laravel.log`

---

## ⚠️ Important Notes

**API Keys:**
- ExchangeRate: daftar di https://exchangerate-api.com
- GNews: daftar di https://gnews.io
- Tanpa key → sistem pakai data cache dari DB

**Originality:**
- Setiap mahasiswa HARUS buat algoritma risk scoring sendiri
- HARUS kustomisasi UI/UX berbeda
- Hasil sama/serupa = PLAGIASI

---

## 📁 Project Structure

```
supply-chain-platform/
├── app/
│   ├── Http/Controllers/     # Auth,Dashboard,Country,Weather,Currency,News,Port,Comparison,Watchlist,DataViz,Admin,API
│   ├── Http/Middleware/      # AdminMiddleware
│   ├── Models/               # 17 Eloquent models
│   └── Services/             # BaseApi,OpenMeteo,WorldBank,RestCountries,ExchangeRate,GNews,SentimentAnalysis,RiskScoring
├── database/
│   ├── migrations/           # 1 file → 17 tables
│   └── seeders/              # 6 seeders (Admin,Countries,Ports,+Words,Settings)
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # REST API
├── .env.example
├── composer.json
└── README.md
```

**Total:** ~15,000 LOC | 25 negara | 55 pelabuhan | 150+ sentiment words | 6 API

---

## 🎓 Final Project by Mahasiswa

**Spesifikasi:**
- Full Stack Development
- API Integration
- Data Engineering
- Dashboard Analytics
- Geospatial Visualization
- Business Intelligence
- Decision Support System

**Deliverables:**
✅ Working application  
✅ Source code (GitHub)  
✅ README dokumentasi  
✅ Algoritma risk scoring sendiri  
✅ Presentasi + demo

---

**⚡ Good Luck!**
