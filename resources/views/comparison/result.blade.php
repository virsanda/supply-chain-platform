@extends('layouts.app')
@section('title','Comparison Result')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('comparison.index') }}">Comparison</a></li>
<li class="breadcrumb-item active">Result</li>
@endsection
@section('content')
@php $A=$comparison['country_a'];$B=$comparison['country_b'];$W=$comparison['winner_risk']; @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Hasil Perbandingan</h4>
  <a href="{{ route('comparison.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Bandingkan Lagi</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-5">
    <div class="card text-center" style="border:3px solid {{ $W===$A['country_code']?'#198754':'#dee2e6' }}">
      <div class="card-body py-3">
        <div style="font-size:2.5rem">{{ $A['flag_emoji']??'🏳️' }}</div>
        <h5 class="fw-bold mt-1">{{ $A['country_name'] }}</h5>
        <div style="font-size:1.8rem;font-weight:700;color:{{ $A['marker_color']??'#666' }}">{{ number_format($A['total_score'],1) }}</div>
        <span class="badge bg-{{ $A['risk_badge_class']??'secondary' }}">{{ $A['risk_label']??'' }}</span>
        @if($W===$A['country_code'])<div class="badge bg-success d-block mt-1">🏆 Direkomendasikan</div>@endif
      </div>
    </div>
  </div>
  <div class="col-md-2 d-flex align-items-center justify-content-center"><div style="font-size:2.5rem">⚖️</div></div>
  <div class="col-md-5">
    <div class="card text-center" style="border:3px solid {{ $W===$B['country_code']?'#198754':'#dee2e6' }}">
      <div class="card-body py-3">
        <div style="font-size:2.5rem">{{ $B['flag_emoji']??'🏳️' }}</div>
        <h5 class="fw-bold mt-1">{{ $B['country_name'] }}</h5>
        <div style="font-size:1.8rem;font-weight:700;color:{{ $B['marker_color']??'#666' }}">{{ number_format($B['total_score'],1) }}</div>
        <span class="badge bg-{{ $B['risk_badge_class']??'secondary' }}">{{ $B['risk_label']??'' }}</span>
        @if($W===$B['country_code'])<div class="badge bg-success d-block mt-1">🏆 Direkomendasikan</div>@endif
      </div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header fw-semibold"><i class="bi bi-table me-2 text-primary"></i>Perbandingan Detail</div>
  <div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>Indikator</th><th class="text-center">{{ $A['country_name'] }}</th><th class="text-center">{{ $B['country_name'] }}</th><th class="text-center">Lebih Baik</th></tr></thead>
      <tbody>
        @foreach([
          ['l'=>'🛡️ Risk Score','a'=>number_format($A['total_score'],1),'b'=>number_format($B['total_score'],1),'wa'=>$A['total_score']<=$B['total_score'],'wn'=>$A['total_score']<=$B['total_score']?$A['country_name']:$B['country_name']],
          ['l'=>'🌤️ Weather Risk','a'=>number_format($A['weather_score'],1),'b'=>number_format($B['weather_score'],1),'wa'=>$A['weather_score']<=$B['weather_score'],'wn'=>$A['weather_score']<=$B['weather_score']?$A['country_name']:$B['country_name']],
          ['l'=>'📈 Inflation Risk','a'=>number_format($A['inflation_score'],1),'b'=>number_format($B['inflation_score'],1),'wa'=>$A['inflation_score']<=$B['inflation_score'],'wn'=>$A['inflation_score']<=$B['inflation_score']?$A['country_name']:$B['country_name']],
          ['l'=>'💱 Currency Risk','a'=>number_format($A['currency_score'],1),'b'=>number_format($B['currency_score'],1),'wa'=>$A['currency_score']<=$B['currency_score'],'wn'=>$A['currency_score']<=$B['currency_score']?$A['country_name']:$B['country_name']],
          ['l'=>'📰 News Risk','a'=>number_format($A['news_sentiment_score'],1),'b'=>number_format($B['news_sentiment_score'],1),'wa'=>$A['news_sentiment_score']<=$B['news_sentiment_score'],'wn'=>$A['news_sentiment_score']<=$B['news_sentiment_score']?$A['country_name']:$B['country_name']],
        ] as $r)
        <tr>
          <td class="fw-medium small">{{ $r['l'] }}</td>
          <td class="text-center {{ $r['wa']?'table-success fw-bold':'' }}">{{ $r['a'] }}</td>
          <td class="text-center {{ !$r['wa']?'table-success fw-bold':'' }}">{{ $r['b'] }}</td>
          <td class="text-center"><span class="badge bg-success small">{{ $r['wn'] }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div></div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-6"><div class="card"><div class="card-header fw-semibold small">Radar Chart</div><div class="card-body"><canvas id="rC" height="280"></canvas></div></div></div>
  <div class="col-md-6"><div class="card"><div class="card-header fw-semibold small">Risk Bar Chart</div><div class="card-body"><canvas id="bC" height="280"></canvas></div></div></div>
</div>

<div class="card border-success border-2">
  <div class="card-header bg-success text-white fw-semibold"><i class="bi bi-trophy me-2"></i>Rekomendasi Supply Chain</div>
  <div class="card-body">
    @php $rec=$comparison['recommendation']===$A['country_code']?$A:$B; @endphp
    <div class="row align-items-center">
      <div class="col-auto"><span style="font-size:3rem">{{ $rec['flag_emoji']??'🏳️' }}</span></div>
      <div class="col"><h4 class="fw-bold mb-1">{{ $rec['country_name'] }}</h4><p class="text-muted mb-0 small">{{ $comparison['recommendation_reason'] }}</p></div>
      <div class="col-auto text-center"><div style="font-size:1.5rem;font-weight:700;color:#198754">{{ number_format($rec['total_score'],1) }}</div><div class="small text-muted">Risk Score</div></div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const A=@json($comparison['country_a']),B=@json($comparison['country_b']);
const lbls=['Risk','Weather','Inflation','Currency','News'];
const dA=[A.total_score,A.weather_score,A.inflation_score,A.currency_score,A.news_sentiment_score];
const dB=[B.total_score,B.weather_score,B.inflation_score,B.currency_score,B.news_sentiment_score];
new Chart(document.getElementById('rC').getContext('2d'),{type:'radar',data:{labels:lbls,datasets:[{label:A.country_name,data:dA,borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,.15)'},{label:B.country_name,data:dB,borderColor:'#dc3545',backgroundColor:'rgba(220,53,69,.15)'}]},options:{responsive:true,scales:{r:{min:0,max:100}},plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('bC').getContext('2d'),{type:'bar',data:{labels:lbls,datasets:[{label:A.country_name,data:dA,backgroundColor:'rgba(13,110,253,.75)',borderRadius:4},{label:B.country_name,data:dB,backgroundColor:'rgba(220,53,69,.75)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{max:100,min:0}}}});
</script>
@endpush
