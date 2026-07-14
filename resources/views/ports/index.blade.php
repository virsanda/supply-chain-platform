@extends('layouts.app')
@section('title','Port Location Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Port Locations</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-anchor me-2"></i>Port Location Dashboard</h4>
  <small class="text-muted">World Port Index — {{ $stats['total'] }} pelabuhan</small>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><i class="bi bi-anchor fs-3 text-primary"></i><div class="fw-bold fs-4 mt-1">{{ $stats['total'] }}</div><div class="text-muted small">Total Pelabuhan</div></div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><i class="bi bi-check-circle fs-3 text-success"></i><div class="fw-bold fs-4 mt-1">{{ $stats['low'] }}</div><div class="text-muted small">Low Congestion</div></div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><i class="bi bi-exclamation-triangle fs-3 text-warning"></i><div class="fw-bold fs-4 mt-1">{{ $stats['moderate'] }}</div><div class="text-muted small">Moderate</div></div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><i class="bi bi-x-circle fs-3 text-danger"></i><div class="fw-bold fs-4 mt-1">{{ $stats['high'] }}</div><div class="text-muted small">High/Critical</div></div></div></div>
</div>

<div class="card mb-3"><div class="card-body py-2">
  <div class="row g-2">
    <div class="col-md-5"><div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span><input type="text" id="psearch" class="form-control" placeholder="Cari pelabuhan..."></div></div>
    <div class="col-md-4"><select id="cfilter" class="form-select"><option value="">Semua Negara</option>@foreach($countries as $c)<option value="{{ $c->code }}" {{ request('country')===$c->code?'selected':'' }}>{{ $c->flag_emoji }} {{ $c->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><select id="congest" class="form-select"><option value="">Semua Kongesti</option><option value="low">Low</option><option value="moderate">Moderate</option><option value="high">High</option><option value="critical">Critical</option></select></div>
  </div>
</div></div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-map me-2"></i>Peta Pelabuhan Dunia <small class="text-muted fw-normal">— Klik marker untuk detail</small></div>
      <div class="card-body p-0"><div id="portMap" style="height:480px;border-radius:0 0 12px 12px"></div></div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between fw-semibold">
        <span><i class="bi bi-list me-2"></i>Daftar Pelabuhan</span>
        <span class="badge bg-secondary" id="pcnt">{{ $ports->count() }}</span>
      </div>
      <div class="card-body p-0" style="overflow-y:auto;max-height:440px">
        @foreach($ports as $p)
        <div class="port-item d-flex align-items-center justify-content-between px-3 py-2 border-bottom"
          data-country="{{ $p->country_code }}" data-name="{{ strtolower($p->port_name) }}"
          data-code="{{ strtolower($p->port_code) }}" data-cong="{{ $p->congestion_level }}"
          data-lat="{{ $p->latitude }}" data-lng="{{ $p->longitude }}"
          style="cursor:pointer" onclick="focusPort({{ $p->latitude??0 }},{{ $p->longitude??0 }},'{{ addslashes($p->port_name) }}')">
          <div>
            <div class="fw-medium small">{{ $p->port_name }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $p->country_name }} · {{ $p->port_code }}</div>
          </div>
          <span class="badge bg-{{ $p->congestion_badge_class }}" style="font-size:.62rem">{{ ucfirst($p->congestion_level) }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const pMap=L.map('portMap',{zoom:2,center:[20,0]});
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:18}).addTo(pMap);
const mks=@json($markers??[]);
const mMap={};
const cc={low:'#198754',moderate:'#ffc107',high:'#dc3545',critical:'#212529'};
mks.forEach(p=>{
  if(!p.lat||!p.lng)return;
  const col=cc[p.congestion]||'#6c757d';
  const m=L.circleMarker([p.lat,p.lng],{radius:8,color:'#fff',weight:2,fillColor:col,fillOpacity:.85}).addTo(pMap);
  m.bindPopup(`<b>${p.name}</b> (${p.code||'—'})<br>📍 ${p.country}<br>⚓ ${p.harbor_size}<br><span style="background:${col};color:#fff;padding:2px 8px;border-radius:10px;font-size:.75rem">Congestion: ${p.congestion} (${p.congestion_score.toFixed(0)})</span>`);
  mMap[p.name.toLowerCase()]=m;
});
function focusPort(lat,lng,name){pMap.setView([lat,lng],7);const m=mMap[name.toLowerCase()];if(m)m.openPopup();}
function doFilter(){
  const q=document.getElementById('psearch').value.toLowerCase();
  const ct=document.getElementById('cfilter').value;
  const cg=document.getElementById('congest').value;
  let cnt=0;
  document.querySelectorAll('.port-item').forEach(el=>{
    const ok=(!q||el.dataset.name.includes(q)||el.dataset.code.includes(q))&&(!ct||el.dataset.country===ct)&&(!cg||el.dataset.cong===cg);
    el.style.display=ok?'':'none';if(ok)cnt++;
  });
  document.getElementById('pcnt').textContent=cnt;
}
document.getElementById('psearch').addEventListener('input',doFilter);
document.getElementById('cfilter').addEventListener('change',doFilter);
document.getElementById('congest').addEventListener('change',doFilter);
@if(request('country'))document.getElementById('cfilter').value='{{ request('country') }}';doFilter();@endif
</script>
@endpush
