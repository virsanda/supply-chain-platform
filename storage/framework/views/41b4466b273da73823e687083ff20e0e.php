<?php $__env->startSection('title','Kurs — '.$country->name); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('currency.index')); ?>">Currency</a></li>
<li class="breadcrumb-item active"><?php echo e($country->name); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card mb-4" style="background:linear-gradient(135deg,#856404,#ffc107);color:#fff;border:none">
      <div class="card-body text-center p-4">
        <div style="font-size:3rem"><?php echo e($country->flag_emoji??'🏳️'); ?></div>
        <h4 class="text-white mt-2 mb-1"><?php echo e($country->name); ?></h4>
        <div style="font-size:.85rem;opacity:.85">1 <?php echo e($base); ?> = ? <?php echo e($target); ?></div>
        <div style="font-size:2.8rem;font-weight:700;margin:10px 0">
          <?php echo e($currentRate?number_format($currentRate,$currentRate<1?6:2):'—'); ?>

        </div>
        <div style="font-size:.85rem;opacity:.85"><?php echo e($target); ?> — <?php echo e($country->currency_name); ?></div>
        <?php if($rateRecord): ?>
        <div class="mt-3 p-2 rounded" style="background:rgba(255,255,255,.2)">
          <div class="row g-2 text-center">
            <div class="col-6"><div style="font-size:.68rem;opacity:.8">Kemarin</div><div class="fw-semibold"><?php echo e($rateRecord->rate_previous?number_format($rateRecord->rate_previous,2):'—'); ?></div></div>
            <div class="col-6"><div style="font-size:.68rem;opacity:.8">Perubahan</div>
              <div class="fw-semibold"><?php echo e($rateRecord->trend_icon); ?> <?php echo e($rateRecord->change_percent!==null?number_format(abs($rateRecord->change_percent),2).'%':'—'); ?></div>
            </div>
          </div>
          <div class="mt-2 small">Currency Risk: <b><?php echo e(number_format($rateRecord->currency_risk_score,1)); ?>/100</b></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <table class="table table-sm table-borderless mb-3 small">
          <tr><td class="text-muted">Negara</td><td class="fw-medium"><?php echo e($country->name); ?></td></tr>
          <tr><td class="text-muted">Kode</td><td class="fw-medium"><?php echo e($target); ?></td></tr>
          <tr><td class="text-muted">Nama</td><td class="fw-medium"><?php echo e($country->currency_name); ?></td></tr>
          <tr><td class="text-muted">Base</td><td class="fw-medium"><?php echo e($base); ?></td></tr>
        </table>
        <a href="<?php echo e(route('countries.show',$country->code)); ?>" class="btn btn-outline-primary btn-sm w-100">
          <i class="bi bi-globe me-2"></i>Country Dashboard
        </a>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header fw-semibold"><i class="bi bi-graph-up me-2 text-warning"></i>Tren Kurs 30 Hari — <?php echo e($base); ?>/<?php echo e($target); ?></div>
      <div class="card-body"><canvas id="trendChart" height="240"></canvas></div>
    </div>
    <?php if(!empty($trendData['rates'])): ?>
    <?php $r=$trendData['rates'];$mn=min($r);$mx=max($r);$avg=array_sum($r)/count($r);$lt=end($r);$ft=reset($r);$chg=$ft>0?(($lt-$ft)/$ft)*100:0; ?>
    <div class="row g-3">
      <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="text-muted small">Tertinggi</div><div class="fw-bold"><?php echo e(number_format($mx,2)); ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="text-muted small">Terendah</div><div class="fw-bold"><?php echo e(number_format($mn,2)); ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="text-muted small">Rata-rata</div><div class="fw-bold"><?php echo e(number_format($avg,2)); ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="text-muted small">Perubahan 30h</div>
        <div class="fw-bold <?php echo e($chg>0?'text-danger':($chg<0?'text-success':'text-secondary')); ?>"><?php echo e($chg>0?'+':''); ?><?php echo e(number_format($chg,2)); ?>%</div>
      </div></div></div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
const td=<?php echo json_encode($trendData??[], 15, 512) ?>,rt=td.rates||[],lb=td.labels||[];
if(rt.length){
  new Chart(document.getElementById('trendChart').getContext('2d'),{
    type:'line',
    data:{labels:lb,datasets:[{label:'<?php echo e($base); ?>/<?php echo e($target); ?>',data:rt,borderColor:'#ffc107',backgroundColor:'rgba(255,193,7,.15)',tension:.4,fill:true,pointRadius:0,borderWidth:2}]},
    options:{responsive:true,interaction:{intersect:false,mode:'index'},plugins:{legend:{display:false}},scales:{y:{min:Math.min(...rt)*.995,max:Math.max(...rt)*1.005,ticks:{callback:v=>v.toFixed(2)}}}}
  });
} else {
  document.getElementById('trendChart').parentElement.innerHTML='<div class="text-center text-muted py-5"><i class="bi bi-graph-up fs-2 d-block mb-2 opacity-25"></i>Data tren belum tersedia</div>';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Global Supply\supply-chain-platform\resources\views/currency/show.blade.php ENDPATH**/ ?>