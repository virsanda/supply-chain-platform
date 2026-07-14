<?php $__env->startSection('title','Currency Impact Dashboard'); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
<li class="breadcrumb-item active">Currency Impact</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-currency-exchange me-2 text-warning"></i>Currency Impact Dashboard</h4>
  <form method="GET" class="d-flex gap-2 align-items-center">
    <label class="small text-muted mb-0">Base:</label>
    <select name="base" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
      <?php $__currentLoopData = ['USD','EUR','JPY','GBP','CNY','SGD']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($b); ?>" <?php echo e($base===$b?'selected':''); ?>><?php echo e($b); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </form>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3">
    <i class="bi bi-currency-dollar fs-3 text-success"></i>
    <div class="fw-bold fs-5 mt-1"><?php echo e($base); ?></div><div class="text-muted small">Base Currency</div>
  </div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3">
    <i class="bi bi-bar-chart fs-3 text-primary"></i>
    <div class="fw-bold fs-5 mt-1"><?php echo e(count($ratesData['rates']??[])); ?></div><div class="text-muted small">Mata Uang Dipantau</div>
  </div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3">
    <i class="bi bi-arrow-up fs-3 text-danger"></i>
    <div class="fw-bold fs-5 mt-1"><?php echo e($currencyRates->where('change_percent','>',0)->count()); ?></div><div class="text-muted small">Menguat vs Kemarin</div>
  </div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3">
    <i class="bi bi-arrow-down fs-3 text-success"></i>
    <div class="fw-bold fs-5 mt-1"><?php echo e($currencyRates->where('change_percent','<',0)->count()); ?></div><div class="text-muted small">Melemah vs Kemarin</div>
  </div></div></div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-table me-2 text-warning"></i>Kurs Real-Time (1 <?php echo e($base); ?>)</span>
        <small class="text-muted"><?php echo e($ratesData['last_updated']??now()->format('d M Y H:i')); ?>

          <?php if(($ratesData['source']??'')==='database'): ?><span class="badge bg-warning ms-1 text-dark" style="font-size:.62rem">Cache</span><?php endif; ?>
        </small>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive" style="max-height:480px;overflow-y:auto">
          <table class="table table-hover mb-0">
            <thead class="table-light sticky-top">
              <tr><th>Mata Uang</th><th>Negara</th><th class="text-end">Kurs</th><th class="text-end">Perubahan</th><th></th></tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $ratesData['rates']??[]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur=>$rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
              $rec=$currencyRates->where('target_currency',$cur)->first();
              $ch=$rec?->change_percent;
              $ti=$ch>0?'↑':($ch<0?'↓':'→');
              $tc=$ch>0?'text-danger':($ch<0?'text-success':'text-secondary');
              $cnt=$countries->firstWhere('currency_code',$cur);
              ?>
              <tr>
                <td><span class="badge bg-light text-dark border fw-bold"><?php echo e($cur); ?></span></td>
                <td class="small"><?php echo e($cnt?$cnt->flag_emoji.' '.$cnt->name:'—'); ?></td>
                <td class="text-end fw-semibold"><?php echo e(number_format($rate,$rate<1?6:2)); ?></td>
                <td class="text-end <?php echo e($tc); ?> small"><?php echo e($ti); ?> <?php echo e($ch!==null?number_format(abs($ch),2).'%':'—'); ?></td>
                <td><?php if($cnt): ?><a href="<?php echo e(route('currency.show',$cnt->code)); ?>" class="btn btn-xs btn-outline-primary" style="font-size:.7rem;padding:2px 8px">Grafik</a><?php endif; ?></td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header fw-semibold small"><i class="bi bi-bar-chart-fill me-2 text-warning"></i>Perubahan Kurs Terbesar</div>
      <div class="card-body"><canvas id="changeChart" height="220"></canvas></div>
    </div>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span class="small fw-semibold"><i class="bi bi-graph-up me-2 text-warning"></i>Tren Kurs</span>
        <select id="qCur" class="form-select form-select-sm" style="width:90px" onchange="loadQ()">
          <?php $__currentLoopData = $countries->whereNotNull('currency_code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($c->currency_code!==$base): ?><option value="<?php echo e($c->code); ?>"><?php echo e($c->currency_code); ?></option><?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="card-body"><canvas id="quickChart" height="150"></canvas></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
const cr=<?php echo json_encode($currencyRates??[], 15, 512) ?>;
const sorted=cr.filter(r=>r.change_percent!==null).sort((a,b)=>Math.abs(b.change_percent)-Math.abs(a.change_percent)).slice(0,10);
new Chart(document.getElementById('changeChart').getContext('2d'),{
  type:'bar',
  data:{labels:sorted.map(r=>r.target_currency),datasets:[{data:sorted.map(r=>r.change_percent),backgroundColor:sorted.map(r=>r.change_percent>0?'rgba(220,53,69,.7)':'rgba(25,135,84,.7)'),borderRadius:4}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v.toFixed(2)+'%'}}}}
});
let qC=null;
function loadQ(){
  const code=document.getElementById('qCur').value;
  if(!code)return;
  showSpinner();
  fetch('/api/v1/currency/'+code).then(r=>r.json()).then(data=>{
    hideSpinner();
    const t=data.trend||{};
    if(qC)qC.destroy();
    qC=new Chart(document.getElementById('quickChart').getContext('2d'),{
      type:'line',
      data:{labels:t.labels||[],datasets:[{label:(data.target||'')+'/'+data.base,data:t.rates||[],borderColor:'#ffc107',backgroundColor:'rgba(255,193,7,.1)',tension:.4,fill:true,pointRadius:0}]},
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{maxTicksLimit:5}}}}
    });
  }).catch(()=>hideSpinner());
}
if(document.getElementById('qCur').options.length>0)loadQ();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Global Supply\supply-chain-platform\resources\views/currency/index.blade.php ENDPATH**/ ?>