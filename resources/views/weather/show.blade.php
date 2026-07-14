@extends('layouts.app')
@section('title','Cuaca — '.$country->name)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('weather.index') }}">Weather</a></li>
<li class="breadcrumb-item active">{{ $country->name }}</li>
@endsection
@section('content')
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card mb-4" style="background:linear-gradient(135deg,#1a3a5c,#1a6ba8);color:#fff;border:none">
      <div class="card-body text-center p-4">
        <div style="font-size:2.5rem">{{ $country->flag_emoji??'🏳️' }}</div>
        <h4 class="text-white mt-1 mb-3">{{ $country->name }}</h4>
        @if($weather)
        @php
        $wc=$weather['weathercode']??0;
        $icon=match(true){
          in_array($wc,[0,1])  =>'☀️',
          in_array($wc,[2,3])  =>'⛅',
          in_array($wc,[45,48])=>'🌫️',
          in_array($wc,[51,61,63])=>'🌦️',
          in_array($wc,[65,80,81,82])=>'🌧️',
          in_array($wc,[95,96,99])=>'⛈️',
          default=>'🌡️'
        };
        @endphp
        <div style="font-size:4rem;line-height:1">{{ $icon }}</div>
        <div style="font-size:3rem;font-weight:700">{{ $weather['temperature_2m']??'--' }}°C</div>
        <div class="opacity-80 mb-3">{{ $weather['weather_description']??'' }}</div>
        <div class="row g-2">
          <div class="col-4"><div style="font-size:.65rem;opacity:.7">Hujan</div><div class="fw-semibold small">{{ $weather['precipitation']??0 }}mm</div></div>
          <div class="col-4"><div style="font-size:.65rem;opacity:.7">Angin</div><div class="fw-semibold small">{{ $weather['windspeed_10m']??0 }}km/h</div></div>
          <div class="col-4"><div style="font-size:.65rem;opacity:.7">Lembab</div><div class="fw-semibold small">{{ $weather['humidity']??'--' }}%</div></div>
        </div>
        @if($weather['is_storm']||$weather['is_heavy_rain']||$weather['is_strong_wind'])
        <div class="mt-2 d-flex flex-wrap gap-1 justify-content-center">
          @if($weather['is_storm'])<span class="badge bg-danger">⛈️ STORM</span>@endif
          @if($weather['is_heavy_rain'])<span class="badge bg-primary">🌧️ HEAVY RAIN</span>@endif
          @if($weather['is_strong_wind'])<span class="badge bg-warning text-dark">💨 STRONG WIND</span>@endif
        </div>
        @endif
        <div class="mt-3 p-2 rounded" style="background:rgba(255,255,255,.15)">
          <div style="font-size:.75rem;opacity:.8">Weather Risk Score</div>
          <div style="font-size:1.8rem;font-weight:700">{{ number_format($weather['weather_risk_score']??0,1) }}/100</div>
          @php $wr=$weather['weather_risk_score']??0; @endphp
          <small>{{ $wr<=30?'✅ Low Risk':($wr<=60?'⚠️ Medium':($wr<=80?'🔴 High':'🚨 Critical')) }}</small>
        </div>
        @else
        <div class="py-4 opacity-70">Data cuaca tidak tersedia</div>
        @endif
      </div>
    </div>
    <div class="card"><div class="card-body d-grid gap-2">
      <a href="{{ route('countries.show',$country->code) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-globe me-2"></i>Country Dashboard
      </a>
      <a href="{{ route('weather.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Peta
      </a>
    </div></div>
  </div>

  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header fw-semibold"><i class="bi bi-geo-alt me-2 text-info"></i>Lokasi — {{ $country->name }}</div>
      <div class="card-body p-0"><div id="cMap" style="height:260px"></div></div>
    </div>

    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-calendar-week me-2 text-info"></i>Prakiraan 7 Hari</div>
      <div class="card-body">
        @if($forecast && isset($forecast['daily']))
        @php
        $fd  = $forecast['daily'];
        $wicons = [0=>'☀️',1=>'🌤️',2=>'⛅',3=>'☁️',45=>'🌫️',51=>'🌦️',61=>'🌦️',63=>'🌧️',65=>'🌧️',80=>'🌧️',82=>'⛈️',95=>'⛈️',99=>'⛈️'];
        @endphp
        <div class="row g-2 mb-4">
          @foreach($fd['time']??[] as $i=>$date)
          <div class="col">
            <div class="text-center p-2 rounded" style="background:#f8f9fa">
              <div class="text-muted" style="font-size:.65rem">{{ \Carbon\Carbon::parse($date)->format('D') }}</div>
              <div style="font-size:1.3rem">{{ $wicons[$fd['weathercode'][$i]??0]??'🌡️' }}</div>
              <div class="fw-semibold small">{{ round($fd['temperature_2m_max'][$i]??0) }}°</div>
              <div class="text-muted" style="font-size:.72rem">{{ round($fd['temperature_2m_min'][$i]??0) }}°</div>
              @if(($fd['precipitation_sum'][$i]??0)>0)
              <div style="font-size:.65rem;color:#0d6efd">💧{{ round($fd['precipitation_sum'][$i],1) }}mm</div>
              @endif
            </div>
          </div>
          @endforeach
        </div>
        <canvas id="fChart" height="120"></canvas>
        @else
        <div class="text-center text-muted py-3">
          <i class="bi bi-cloud-slash d-block fs-3 mb-2 opacity-25"></i>Data prakiraan tidak tersedia
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const cm=L.map('cMap',{zoom:4,center:[{{ $country->latitude??0 }},{{ $country->longitude??0 }}],zoomControl:false});
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(cm);
L.marker([{{ $country->latitude??0 }},{{ $country->longitude??0 }}]).addTo(cm).bindPopup('<b>{{ $country->name }}</b>').openPopup();
@if($forecast && isset($forecast['daily']))
@php $fd=$forecast['daily']; @endphp
new Chart(document.getElementById('fChart').getContext('2d'),{
  type:'line',
  data:{
    labels:@json(array_map(fn($x)=>\Carbon\Carbon::parse($x)->format('D'),$fd['time']??[])),
    datasets:[
      {label:'Max°C',data:@json($fd['temperature_2m_max']??[]),borderColor:'#dc3545',tension:.4,fill:false,pointRadius:4},
      {label:'Min°C',data:@json($fd['temperature_2m_min']??[]),borderColor:'#0d6efd',tension:.4,fill:false,pointRadius:4}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'top',labels:{font:{size:11}}}},scales:{y:{ticks:{callback:v=>v+'°C'}}}}
});
@endif
</script>
@endpush
