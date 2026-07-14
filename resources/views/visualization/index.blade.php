@extends('layouts.app')
@section('title','Data Visualization Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Visualization</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Data Visualization Dashboard</h4>
  <small class="text-muted">GDP · Inflation · Currency · Risk Trend</small>
</div>

<div class="card mb-4">
  <div class="card-body py-3">
    <div class="row g-2 align-items-center">
      <div class="col-md-4">
        <select id="vizSel" class="form-select">
          @foreach($countries as $c)
          <option value="{{ $c->code }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100" onclick="loadViz()">
          <i class="bi bi-bar-chart me-1"></i>Tampilkan
        </button>
      </div>
      <div class="col-md-6 d-flex gap-2 flex-wrap">
        @foreach($countries->take(6) as $c)
        <button class="btn btn-sm btn-outline-secondary" onclick="qViz('{{ $c->code }}')">
          {{ $c->flag_emoji }} {{ $c->code }}
        </button>
        @endforeach
      </div>
    </div>
  </div>
</div>

<div id="vizBanner" class="card mb-4" style="display:none;background:linear-gradient(135deg,#1a1d23,#0d3880);color:#fff;border:none">
  <div class="card-body py-3 d-flex align-items-center gap-3">
    <span id="vFlag" style="font-size:2.5rem"></span>
    <div class="flex-fill"><h5 class="text-white mb-0 fw-bold" id="vName"></h5></div>
    <span id="vRisk" class="badge fs-6"></span>
  </div>
</div>

<div id="vizCharts" style="display:none">
  <div class="row g-4 mb-4">
    <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-bar-chart text-success me-2"></i>GDP Trend (5 Tahun)</div><div class="card-body"><canvas id="vGdp" height="220"></canvas></div></div></div>
    <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-graph-down-arrow text-danger me-2"></i>Inflation Trend (5 Tahun)</div><div class="card-body"><canvas id="vInfl" height="220"></canvas></div></div></div>
  </div>
  <div class="row g-4">
    <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-currency-exchange text-warning me-2"></i>Currency Trend (30 Hari)</div><div class="card-body"><canvas id="vCurr" height="220"></canvas></div></div></div>
    <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-shield-exclamation text-primary me-2"></i>Risk Trend (30 Hari)</div><div class="card-body"><canvas id="vRisk" height="220"></canvas></div></div></div>
  </div>
</div>

<div id="vizPH" class="text-center py-5 text-muted">
  <i class="bi bi-bar-chart-line display-3 d-block mb-3 opacity-25"></i>
  <p>Pilih negara untuk menampilkan visualisasi data</p>
</div>
@endsection
@push('scripts')
<script>
let cG=null,cI=null,cC=null,cR=null;
function qViz(code){document.getElementById('vizSel').value=code;loadViz();}
function loadViz(){
  const code=document.getElementById('vizSel').value;
  if(!code)return;
  showSpinner();
  fetch('/visualization/'+code,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
  .then(r=>r.json()).then(data=>{
    hideSpinner();
    document.getElementById('vizPH').style.display='none';
    document.getElementById('vizCharts').style.display='';
    const rs=data.riskScore||{};
    document.getElementById('vizBanner').style.display='';
    document.getElementById('vFlag').textContent=rs.flag_emoji||'🏳️';
    document.getElementById('vName').textContent=rs.country_name||code;
    document.getElementById('vRisk').textContent=(rs.risk_label||'')+': '+((rs.total_score||0).toFixed(1));
    document.getElementById('vRisk').className='badge fs-6 bg-'+(rs.risk_badge_class||'secondary');
    buildG(data.gdpTrend||[]);buildI(data.inflationTrend||[]);buildC(data.currencyTrend||null);buildR(data.riskTrend||{});
  }).catch(e=>{hideSpinner();console.error(e);});
}
function buildG(d){
  if(cG)cG.destroy();
  const ctx=document.getElementById('vGdp').getContext('2d');
  if(!d.length){ctx.canvas.parentElement.innerHTML='<div class="text-center text-muted py-4 small">Data tidak tersedia</div>';return;}
  cG=new Chart(ctx,{type:'bar',data:{labels:d.map(x=>x.year),datasets:[{data:d.map(x=>x.value),backgroundColor:'rgba(25,135,84,.75)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v>=1e12?'$'+(v/1e12).toFixed(1)+'T':v>=1e9?'$'+(v/1e9).toFixed(1)+'B':'$'+v}}}}});
}
function buildI(d){
  if(cI)cI.destroy();
  const ctx=document.getElementById('vInfl').getContext('2d');
  if(!d.length){ctx.canvas.parentElement.innerHTML='<div class="text-center text-muted py-4 small">Data tidak tersedia</div>';return;}
  cI=new Chart(ctx,{type:'line',data:{labels:d.map(x=>x.year),datasets:[{data:d.map(x=>x.value),borderColor:'#dc3545',backgroundColor:'rgba(220,53,69,.1)',tension:.4,fill:true,pointRadius:5,pointBackgroundColor:'#dc3545'}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v+'%'}}}}});
}
function buildC(d){
  if(cC)cC.destroy();
  const ctx=document.getElementById('vCurr').getContext('2d');
  if(!d||!d.rates?.length){ctx.canvas.parentElement.innerHTML='<div class="text-center text-muted py-4 small">Data kurs tidak tersedia</div>';return;}
  cC=new Chart(ctx,{type:'line',data:{labels:d.labels,datasets:[{data:d.rates,borderColor:'#ffc107',backgroundColor:'rgba(255,193,7,.1)',tension:.4,fill:true,pointRadius:0}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{maxTicksLimit:6}}}}});
}
function buildR(d){
  if(cR)cR.destroy();
  const ctx=document.getElementById('vRisk').getContext('2d');
  if(!d.labels?.length){ctx.canvas.parentElement.innerHTML='<div class="text-center text-muted py-4 small">Belum ada histori risk score</div>';return;}
  const cols=(d.levels||[]).map(l=>({'low':'#198754','medium':'#ffc107','high':'#dc3545','critical':'#212529'}[l]||'#6c757d'));
  cR=new Chart(ctx,{type:'line',data:{labels:d.labels,datasets:[{data:d.data,borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,.1)',tension:.4,fill:true,pointRadius:4,pointBackgroundColor:cols}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{min:0,max:100}}}});
}
document.addEventListener('DOMContentLoaded',()=>{if(document.getElementById('vizSel').options.length>0)loadViz();});
</script>
@endpush
