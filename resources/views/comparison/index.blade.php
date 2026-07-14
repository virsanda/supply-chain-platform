@extends('layouts.app')
@section('title','Country Comparison Engine')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Comparison</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Country Comparison Engine</h4>
  <small class="text-muted">Bandingkan 2 negara: GDP · Inflation · Risk · Weather · Currency</small>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form id="cmpForm">
      @csrf
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-medium">Negara A</label>
          <select name="country_a" id="cA" class="form-select" required>
            <option value="">Pilih Negara A...</option>
            @foreach($countries as $c)
            <option value="{{ $c->code }}" {{ request('a')===$c->code?'selected':'' }}>{{ $c->flag_emoji }} {{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1 text-center pb-1"><span style="font-size:1.5rem">⚖️</span></div>
        <div class="col-md-4">
          <label class="form-label fw-medium">Negara B</label>
          <select name="country_b" id="cB" class="form-select" required>
            <option value="">Pilih Negara B...</option>
            @foreach($countries as $c)
            <option value="{{ $c->code }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-primary w-100 fw-semibold">
            <i class="bi bi-search me-2"></i>Bandingkan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div id="cmpResult" style="display:none">
  <div class="row g-3 mb-4">
    <div class="col-md-5">
      <div class="card text-center" id="cardA">
        <div class="card-body py-3">
          <div style="font-size:2.5rem" id="flagA">🏳️</div>
          <h5 class="fw-bold mt-1 mb-0" id="nameA">—</h5>
          <div id="scoreA" class="mt-2"></div>
        </div>
      </div>
    </div>
    <div class="col-md-2 d-flex align-items-center justify-content-center">
      <div style="font-size:2rem">🆚</div>
    </div>
    <div class="col-md-5">
      <div class="card text-center" id="cardB">
        <div class="card-body py-3">
          <div style="font-size:2.5rem" id="flagB">🏳️</div>
          <h5 class="fw-bold mt-1 mb-0" id="nameB">—</h5>
          <div id="scoreB" class="mt-2"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-semibold"><i class="bi bi-table me-2 text-primary"></i>Perbandingan Detail</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Indikator</th><th class="text-center" id="thA">Negara A</th><th class="text-center" id="thB">Negara B</th><th class="text-center">Lebih Baik</th></tr>
          </thead>
          <tbody id="cmpBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header fw-semibold small">Radar Chart</div>
        <div class="card-body"><canvas id="cmpRadar" height="280"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header fw-semibold small">Risk Components</div>
        <div class="card-body"><canvas id="cmpBar" height="280"></canvas></div>
      </div>
    </div>
  </div>

  <div class="card border-success border-2" id="recCard">
    <div class="card-header bg-success text-white fw-semibold">
      <i class="bi bi-trophy me-2"></i>Rekomendasi Supply Chain
    </div>
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-auto"><div style="font-size:3rem" id="recFlag">🏳️</div></div>
        <div class="col">
          <h4 class="fw-bold mb-1" id="recName">—</h4>
          <p class="text-muted mb-0 small" id="recReason">—</p>
        </div>
        <div class="col-auto text-center">
          <div style="font-size:1.5rem;font-weight:700;color:#198754" id="recScore">—</div>
          <div class="small text-muted">Risk Score</div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($recent->isNotEmpty())
<div class="card mt-4">
  <div class="card-header fw-semibold small"><i class="bi bi-clock-history me-2 text-muted"></i>Perbandingan Terakhir</div>
  <div class="card-body p-0">
    <table class="table table-sm table-hover mb-0">
      <thead class="table-light"><tr><th>Negara A</th><th></th><th>Negara B</th><th>Rekomendasi</th><th>Waktu</th></tr></thead>
      <tbody>
        @foreach($recent as $snap)
        <tr>
          <td class="small">{{ $snap->countryA->flag_emoji??'' }} {{ $snap->countryA->name??$snap->country_a }}</td>
          <td class="text-center small">⚖️</td>
          <td class="small">{{ $snap->countryB->flag_emoji??'' }} {{ $snap->countryB->name??$snap->country_b }}</td>
          <td><button class="btn btn-xs btn-outline-success" style="font-size:.7rem;padding:2px 8px"
            onclick="replay('{{ $snap->country_a }}','{{ $snap->country_b }}')">{{ $snap->recommendation }}</button></td>
          <td class="text-muted small">{{ $snap->created_at->diffForHumans() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
@push('scripts')
<script>
let rI=null,bI=null;
document.getElementById('cmpForm').addEventListener('submit',e=>{
  e.preventDefault();
  const a=document.getElementById('cA').value,b=document.getElementById('cB').value;
  if(!a||!b||a===b){alert('Pilih dua negara berbeda!');return;}
  runCmp(a,b);
});
function replay(a,b){document.getElementById('cA').value=a;document.getElementById('cB').value=b;runCmp(a,b);}
function runCmp(a,b){
  showSpinner();
  fetch('{{ route('comparison.compare') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:JSON.stringify({country_a:a,country_b:b})})
  .then(r=>r.json()).then(d=>{hideSpinner();renderCmp(d);}).catch(e=>{hideSpinner();console.error(e);alert('Error. Coba lagi.');});
}
function renderCmp(d){
  const A=d.country_a,B=d.country_b,W=d.winner_risk;
  document.getElementById('flagA').textContent=A.flag_emoji||'🏳️';
  document.getElementById('nameA').textContent=A.country_name;
  document.getElementById('flagB').textContent=B.flag_emoji||'🏳️';
  document.getElementById('nameB').textContent=B.country_name;
  document.getElementById('thA').textContent=A.country_name;
  document.getElementById('thB').textContent=B.country_name;
  document.getElementById('cardA').style.border=`3px solid ${W===A.country_code?'#198754':'transparent'}`;
  document.getElementById('cardB').style.border=`3px solid ${W===B.country_code?'#198754':'transparent'}`;
  const mkScore=(x)=>`<span class="badge bg-${x.risk_badge_class||'secondary'}">${x.risk_label||''}: ${(x.total_score||0).toFixed(1)}</span>${W===x.country_code?'<div class="badge bg-success mt-1">🏆 Direkomendasikan</div>':''}`;
  document.getElementById('scoreA').innerHTML=mkScore(A);
  document.getElementById('scoreB').innerHTML=mkScore(B);

  const econA=A.economic||{},econB=B.economic||{};
  const fmtGdp=v=>!v?'N/A':v>=1e12?'$'+(v/1e12).toFixed(2)+'T':v>=1e9?'$'+(v/1e9).toFixed(2)+'B':'$'+v.toLocaleString();
  const rows=[
    {l:'🛡️ Risk Score',a:(A.total_score||0).toFixed(1),b:(B.total_score||0).toFixed(1),wa:A.total_score<=B.total_score,wn:A.total_score<=B.total_score?A.country_name:B.country_name},
    {l:'🌤️ Weather Risk',a:(A.weather_score||0).toFixed(1),b:(B.weather_score||0).toFixed(1),wa:A.weather_score<=B.weather_score,wn:A.weather_score<=B.weather_score?A.country_name:B.country_name},
    {l:'📈 Inflation Risk',a:(A.inflation_score||0).toFixed(1),b:(B.inflation_score||0).toFixed(1),wa:A.inflation_score<=B.inflation_score,wn:A.inflation_score<=B.inflation_score?A.country_name:B.country_name},
    {l:'💱 Currency Risk',a:(A.currency_score||0).toFixed(1),b:(B.currency_score||0).toFixed(1),wa:A.currency_score<=B.currency_score,wn:A.currency_score<=B.currency_score?A.country_name:B.country_name},
    {l:'📰 News Risk',a:(A.news_sentiment_score||0).toFixed(1),b:(B.news_sentiment_score||0).toFixed(1),wa:A.news_sentiment_score<=B.news_sentiment_score,wn:A.news_sentiment_score<=B.news_sentiment_score?A.country_name:B.country_name},
    {l:'💰 GDP',a:fmtGdp(econA.gdp),b:fmtGdp(econB.gdp),wa:(econA.gdp||0)>=(econB.gdp||0),wn:(econA.gdp||0)>=(econB.gdp||0)?A.country_name:B.country_name},
    {l:'📊 Inflasi (%)',a:econA.inflation?econA.inflation.toFixed(2)+'%':'N/A',b:econB.inflation?econB.inflation.toFixed(2)+'%':'N/A',wa:(econA.inflation||99)<=(econB.inflation||99),wn:(econA.inflation||99)<=(econB.inflation||99)?A.country_name:B.country_name},
  ];
  const tb=document.getElementById('cmpBody');tb.innerHTML='';
  rows.forEach(r=>tb.innerHTML+=`<tr><td class="fw-medium small">${r.l}</td><td class="text-center ${r.wa?'table-success fw-bold':''}">${r.a}</td><td class="text-center ${!r.wa?'table-success fw-bold':''}">${r.b}</td><td class="text-center"><span class="badge bg-success small">${r.wn}</span></td></tr>`);

  const rec=d.recommendation===A.country_code?A:B;
  document.getElementById('recFlag').textContent=rec.flag_emoji||'🏳️';
  document.getElementById('recName').textContent=rec.country_name;
  document.getElementById('recScore').textContent=(rec.total_score||0).toFixed(1);
  document.getElementById('recReason').textContent=d.recommendation_reason;

  if(rI)rI.destroy();
  rI=new Chart(document.getElementById('cmpRadar').getContext('2d'),{type:'radar',data:{labels:['Risk','Weather','Inflation','Currency','News'],datasets:[{label:A.country_name,data:[A.total_score,A.weather_score,A.inflation_score,A.currency_score,A.news_sentiment_score],borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,.15)',pointBackgroundColor:'#0d6efd'},{label:B.country_name,data:[B.total_score,B.weather_score,B.inflation_score,B.currency_score,B.news_sentiment_score],borderColor:'#dc3545',backgroundColor:'rgba(220,53,69,.15)',pointBackgroundColor:'#dc3545'}]},options:{responsive:true,scales:{r:{min:0,max:100,ticks:{stepSize:20}}},plugins:{legend:{position:'bottom'}}}});

  if(bI)bI.destroy();
  bI=new Chart(document.getElementById('cmpBar').getContext('2d'),{type:'bar',data:{labels:['Total','Weather','Inflation','Currency','News'],datasets:[{label:A.country_name,data:[A.total_score,A.weather_score,A.inflation_score,A.currency_score,A.news_sentiment_score],backgroundColor:'rgba(13,110,253,.75)',borderRadius:4},{label:B.country_name,data:[B.total_score,B.weather_score,B.inflation_score,B.currency_score,B.news_sentiment_score],backgroundColor:'rgba(220,53,69,.75)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{max:100,min:0}}}});

  document.getElementById('cmpResult').style.display='';
  document.getElementById('cmpResult').scrollIntoView({behavior:'smooth'});
}
</script>
@endpush
