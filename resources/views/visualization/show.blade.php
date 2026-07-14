@extends('layouts.app')
@section('title','Visualisasi — '.$country->name)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('visualization.index') }}">Visualization</a></li>
<li class="breadcrumb-item active">{{ $country->name }}</li>
@endsection
@section('content')
<div class="card mb-4" style="background:linear-gradient(135deg,#1a1d23,#0d3880);color:#fff;border:none">
  <div class="card-body py-3 d-flex align-items-center gap-3">
    <span style="font-size:2.5rem">{{ $country->flag_emoji??'🏳️' }}</span>
    <div class="flex-fill"><h5 class="text-white mb-0 fw-bold">{{ $country->name }}</h5><div class="opacity-75 small">{{ $country->region }} · {{ $country->subregion }}</div></div>
    @if($riskScore)<div class="text-end"><div style="font-size:1.8rem;font-weight:700;color:{{ $riskScore['marker_color']??'#fff' }}">{{ number_format($riskScore['total_score'],1) }}</div><span class="badge bg-{{ $riskScore['risk_badge_class']??'secondary' }}">{{ $riskScore['risk_label']??'' }}</span></div>@endif
  </div>
</div>
<div class="row g-4 mb-4">
  <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-bar-chart text-success me-2"></i>GDP Trend (5 Tahun)</div><div class="card-body"><canvas id="gdpC" height="220"></canvas></div></div></div>
  <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-graph-down-arrow text-danger me-2"></i>Inflation Trend (5 Tahun)</div><div class="card-body"><canvas id="inflC" height="220"></canvas></div></div></div>
</div>
<div class="row g-4">
  <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-currency-exchange text-warning me-2"></i>Currency Trend (30 Hari)</div><div class="card-body"><canvas id="currC" height="220"></canvas></div></div></div>
  <div class="col-md-6"><div class="card h-100"><div class="card-header fw-semibold small"><i class="bi bi-shield-exclamation text-primary me-2"></i>Risk Trend (30 Hari)</div><div class="card-body"><canvas id="riskC" height="220"></canvas></div></div></div>
</div>
@endsection
@push('scripts')
<script>
@php $g=$gdpTrend??[];$inf=$inflationTrend??[];$rt=$riskTrend??[]; @endphp
@if(count($g))
new Chart(document.getElementById('gdpC').getContext('2d'),{type:'bar',data:{labels:@json(array_column($g,'year')),datasets:[{data:@json(array_column($g,'value')),backgroundColor:'rgba(25,135,84,.75)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v>=1e12?'$'+(v/1e12).toFixed(1)+'T':v>=1e9?'$'+(v/1e9).toFixed(1)+'B':'$'+v}}}}});
@else
document.getElementById('gdpC').parentElement.innerHTML='<div class="text-center text-muted py-5 small">Data tidak tersedia</div>';
@endif
@if(count($inf))
new Chart(document.getElementById('inflC').getContext('2d'),{type:'line',data:{labels:@json(array_column($inf,'year')),datasets:[{data:@json(array_column($inf,'value')),borderColor:'#dc3545',backgroundColor:'rgba(220,53,69,.1)',tension:.4,fill:true,pointRadius:5}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v+'%'}}}}});
@else
document.getElementById('inflC').parentElement.innerHTML='<div class="text-center text-muted py-5 small">Data tidak tersedia</div>';
@endif
@if($currencyTrend && !empty($currencyTrend['rates']))
new Chart(document.getElementById('currC').getContext('2d'),{type:'line',data:{labels:@json($currencyTrend['labels']),datasets:[{data:@json($currencyTrend['rates']),borderColor:'#ffc107',backgroundColor:'rgba(255,193,7,.1)',tension:.4,fill:true,pointRadius:0}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{maxTicksLimit:6}}}}});
@else
document.getElementById('currC').parentElement.innerHTML='<div class="text-center text-muted py-5 small">{{ ($country->currency_code??'')==='USD'?'Base currency USD':'Data kurs belum tersedia' }}</div>';
@endif
@if(!empty($rt['labels']))
new Chart(document.getElementById('riskC').getContext('2d'),{type:'line',data:{labels:@json($rt['labels']),datasets:[{data:@json($rt['data']),borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,.1)',tension:.4,fill:true,pointRadius:4,pointBackgroundColor:@json(array_map(fn($l)=>match($l){'low'=>'#198754','medium'=>'#ffc107','high'=>'#dc3545','critical'=>'#212529',default=>'#6c757d'},$rt['levels']??[]))}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{min:0,max:100}}}});
@else
document.getElementById('riskC').parentElement.innerHTML='<div class="text-center text-muted py-5 small">Belum ada histori risk score</div>';
@endif
</script>
@endpush
