<?php $__env->startSection('title','Dashboard'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active">Dashboard</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Row -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0099ff)">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-value"><?php echo e($stats['total_countries']); ?></div><div class="stat-label">Negara Dipantau</div></div>
        <i class="bi bi-globe2 stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#198754,#20c997)">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-value"><?php echo e($stats['total_ports']); ?></div><div class="stat-label">Pelabuhan Dunia</div></div>
        <i class="bi bi-anchor stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#ff6b6b)">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-value"><?php echo e($stats['high_risk_count']); ?></div><div class="stat-label">High Risk Today</div></div>
        <i class="bi bi-exclamation-triangle stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#6f42c1,#a855f7)">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-value"><?php echo e($stats['news_count']); ?></div><div class="stat-label">Berita 24 Jam</div></div>
        <i class="bi bi-newspaper stat-icon"></i>
      </div>
    </div>
  </div>
</div>

<!-- Risk Scores Row -->
<div class="row g-3 mb-4">
  <div class="col-12 col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-exclamation me-2 text-primary"></i>Risk Scoring — <?php echo e(today()->format('d M Y')); ?></span>
        <a href="<?php echo e(route('countries.index')); ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr><th>Negara</th><th>Weather</th><th>Inflasi</th><th>Kurs</th><th>Berita</th><th class="text-center">Risk Score</th></tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $riskScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $risk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td>
                  <a href="<?php echo e(route('countries.show',$code)); ?>" class="text-decoration-none text-dark fw-medium">
                    <?php echo e($risk['flag_emoji'] ?? ''); ?> <?php echo e($risk['country_name']); ?>

                  </a>
                </td>
                <td><small><?php echo e(number_format($risk['weather_score'],1)); ?></small></td>
                <td><small><?php echo e(number_format($risk['inflation_score'],1)); ?></small></td>
                <td><small><?php echo e(number_format($risk['currency_score'],1)); ?></small></td>
                <td><small><?php echo e(number_format($risk['news_sentiment_score'],1)); ?></small></td>
                <td class="text-center">
                  <span class="risk-<?php echo e($risk['risk_level']); ?>">
                    <?php echo e(number_format($risk['total_score'],1)); ?> — <?php echo e($risk['risk_label']); ?>

                  </span>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <!-- Risk Distribution Pie -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-pie-chart me-2 text-primary"></i>Risk Distribution</div>
      <div class="card-body">
        <canvas id="riskPieChart" height="160"></canvas>
      </div>
    </div>

    <!-- Watchlist -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-star me-2 text-warning"></i>Watchlist</span>
        <a href="<?php echo e(route('watchlist.index')); ?>" class="btn btn-sm btn-outline-warning">Kelola</a>
      </div>
      <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $watchlist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
          <div>
            <span class="fw-medium"><?php echo e($item->country->flag_emoji ?? ''); ?> <?php echo e($item->country_name); ?></span>
          </div>
          <a href="<?php echo e(route('countries.show',$item->country_code)); ?>" class="btn btn-xs btn-outline-secondary" style="font-size:.75rem;padding:2px 8px">Detail</a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted py-3 small">Belum ada negara di watchlist</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- World Map + Latest News Row -->
<div class="row g-3">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header"><i class="bi bi-map me-2 text-primary"></i>Global Risk Map</div>
      <div class="card-body p-0">
        <div id="worldMap" style="height:380px"></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-newspaper me-2 text-primary"></i>Berita Terbaru</span>
        <a href="<?php echo e(route('news.index')); ?>" class="btn btn-sm btn-outline-primary">Semua</a>
      </div>
      <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $latestNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="px-3 py-2 border-bottom">
          <div class="d-flex justify-content-between mb-1">
            <span class="badge bg-<?php echo e($news->sentiment_badge_class); ?> badge-sm" style="font-size:.65rem"><?php echo e(ucfirst($news->sentiment)); ?></span>
            <small class="text-muted"><?php echo e($news->time_ago); ?></small>
          </div>
          <a href="<?php echo e($news->url); ?>" target="_blank" class="text-decoration-none text-dark small fw-medium" style="line-height:1.3">
            <?php echo e(Str::limit($news->title, 80)); ?>

          </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted py-3 small">Tidak ada berita</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Risk Pie Chart ──────────────────────────────────────────
const riskDist = <?php echo json_encode($riskDist ?? [], 15, 512) ?>;
const pieLabels = Object.keys(riskDist).map(l=>l.charAt(0).toUpperCase()+l.slice(1));
const pieData   = Object.values(riskDist);
const pieColors = Object.keys(riskDist).map(l=>riskColor(l));

new Chart(document.getElementById('riskPieChart').getContext('2d'),{
  type:'doughnut',
  data:{labels:pieLabels,datasets:[{data:pieData,backgroundColor:pieColors,borderWidth:2,borderColor:'#fff'}]},
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{padding:10,font:{size:11}}}}}
});

// ── World Risk Map (Leaflet.js) ────────────────────────────
const worldMap = L.map('worldMap',{zoom:2,center:[20,0],zoomControl:true});
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
  attribution:'© <a href="https://openstreetmap.org">OpenStreetMap</a>',maxZoom:18
}).addTo(worldMap);

const allRisks = <?php echo json_encode($allRisks ?? [], 15, 512) ?>;
allRisks.forEach(r => {
  if (!r.lat || !r.lng) return;
  const marker = L.circleMarker([r.lat, r.lng], {
    radius:10, color:'#fff', weight:2,
    fillColor:r.color, fillOpacity:.85
  }).addTo(worldMap);
  marker.bindPopup(`
    <b>${r.name}</b><br>
    <span style="background:${riskBg(r.level)};padding:2px 8px;border-radius:10px;font-size:.8rem">${r.label}: ${r.score.toFixed(1)}</span>
  `);
});

// Default risk scores dari dashboard
const defaultRisks = <?php echo json_encode($riskScores ?? [], 15, 512) ?>;
Object.values(defaultRisks).forEach(r => {
  const weather = r.raw_weather;
  if (!weather || !weather.latitude) return;
  const existing = allRisks.find(x=>x.code===r.country_code);
  if (existing) return;
  L.circleMarker([weather.latitude, weather.longitude], {
    radius:10, color:'#fff', weight:2,
    fillColor:r.marker_color, fillOpacity:.85
  }).addTo(worldMap)
   .bindPopup(`<b>${r.country_name}</b><br><span>${r.risk_label}: ${r.total_score.toFixed(1)}</span>`);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Global Supply\supply-chain-platform\resources\views/dashboard/index.blade.php ENDPATH**/ ?>