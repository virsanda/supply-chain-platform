<?php $__env->startSection('title','Country Dashboard'); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
<li class="breadcrumb-item active">Countries</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-globe me-2 text-primary"></i>Global Country Dashboard</h4>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="<?php echo e(route('countries.index')); ?>" class="row g-3">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Cari negara..." value="<?php echo e(request('search')); ?>">
        </div>
      </div>
      <div class="col-md-4">
        <select name="region" class="form-select">
          <option value="">Semua Region</option>
          <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($region); ?>" <?php echo e(request('region')==$region?'selected':''); ?>><?php echo e($region); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
      </div>
    </form>
  </div>
</div>

<!-- Countries Grid -->
<div class="row g-3">
  <?php $__empty_1 = true; $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <div class="col-sm-6 col-md-4 col-xl-3">
    <div class="card h-100 country-card" style="cursor:pointer" onclick="window.location='<?php echo e(route('countries.show',$country->code)); ?>'">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <span style="font-size:2rem"><?php echo e($country->flag_emoji); ?></span>
          <div>
            <div class="fw-bold"><?php echo e($country->name); ?></div>
            <div class="text-muted small"><?php echo e($country->capital); ?> · <?php echo e($country->code); ?></div>
          </div>
        </div>
        <div class="small text-muted mb-2">
          <i class="bi bi-geo-alt me-1"></i><?php echo e($country->region); ?>

          <?php if($country->subregion): ?> · <?php echo e($country->subregion); ?><?php endif; ?>
        </div>
        <?php if($country->latestRiskScore): ?>
        <div class="mt-2">
          <span class="risk-<?php echo e($country->latestRiskScore->risk_level); ?>">
            <?php echo e($country->latestRiskScore->risk_label); ?>: <?php echo e(number_format($country->latestRiskScore->total_score,1)); ?>

          </span>
        </div>
        <?php endif; ?>
        <?php if($country->currency_code): ?>
        <div class="text-muted small mt-2"><i class="bi bi-currency-exchange me-1"></i><?php echo e($country->currency_code); ?></div>
        <?php endif; ?>
      </div>
      <div class="card-footer bg-transparent border-0 pt-0">
        <a href="<?php echo e(route('countries.show',$country->code)); ?>" class="btn btn-sm btn-outline-primary w-100">
          <i class="bi bi-eye me-1"></i>Lihat Detail
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="col-12 text-center py-5 text-muted">
    <i class="bi bi-search fs-1 mb-3 d-block opacity-25"></i>
    Tidak ada negara ditemukan.
  </div>
  <?php endif; ?>
</div>

<div class="mt-4"><?php echo e($countries->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
<style>.country-card:hover{transform:translateY(-3px);transition:.2s;box-shadow:0 6px 20px rgba(0,0,0,.1)}</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Global Supply\supply-chain-platform\resources\views/countries/index.blade.php ENDPATH**/ ?>