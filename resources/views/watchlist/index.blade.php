@extends('layouts.app')
@section('title','Favorite Monitoring List')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Watchlist</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-star me-2 text-warning"></i>Favorite Monitoring List</h4>
  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus me-1"></i>Tambah Negara
  </button>
</div>

@if($watchlist->isEmpty())
<div class="text-center py-5 text-muted">
  <i class="bi bi-star display-3 d-block mb-3 opacity-25"></i>
  <h5>Watchlist Kosong</h5>
  <p class="small">Tambahkan negara yang ingin Anda pantau secara rutin.</p>
  <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus me-2"></i>Tambah Negara Pertama
  </button>
</div>
@else
<div class="row g-3">
  @foreach($watchlist as $item)
  @php $rs=$riskScores[$item->country_code]??null; @endphp
  <div class="col-12 col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <span style="font-size:2rem">{{ $item->country->flag_emoji??'🏳️' }}</span>
            <div class="fw-bold mt-1">{{ $item->country_name }}</div>
            @if($item->country)<div class="text-muted small">{{ $item->country->region }} · {{ $item->country->capital }}</div>@endif
          </div>
          @if($rs)
          <div class="text-end">
            <div style="font-size:1.6rem;font-weight:700;color:{{ $rs['marker_color']??'#666' }}">{{ number_format($rs['total_score'],1) }}</div>
            <span class="badge bg-{{ $rs['risk_badge_class']??'secondary' }}" style="font-size:.72rem">{{ $rs['risk_label']??'' }}</span>
          </div>
          @endif
        </div>
        @if($rs)
        <div class="mb-2">
          @foreach([['n'=>'Weather','s'=>$rs['weather_score'],'c'=>'info'],['n'=>'Inflation','s'=>$rs['inflation_score'],'c'=>'warning'],['n'=>'Currency','s'=>$rs['currency_score'],'c'=>'secondary'],['n'=>'News','s'=>$rs['news_sentiment_score'],'c'=>'danger']] as $comp)
          <div class="d-flex align-items-center mb-1">
            <div class="text-muted" style="width:62px;font-size:.7rem">{{ $comp['n'] }}</div>
            <div class="progress flex-fill" style="height:5px;border-radius:3px"><div class="progress-bar bg-{{ $comp['c'] }}" style="width:{{ $comp['s'] }}%"></div></div>
            <div class="ms-2 text-muted" style="width:26px;font-size:.7rem;text-align:right">{{ round($comp['s']) }}</div>
          </div>
          @endforeach
        </div>
        @endif
        @if($item->notes)<div class="mt-2 p-2 bg-light rounded small text-muted">📝 {{ $item->notes }}</div>@endif
      </div>
      <div class="card-footer bg-transparent d-flex gap-2">
        <a href="{{ route('countries.show',$item->country_code) }}" class="btn btn-outline-primary btn-sm flex-fill"><i class="bi bi-eye me-1"></i>Detail</a>
        <a href="{{ route('visualization.show',$item->country_code) }}" class="btn btn-outline-secondary btn-sm flex-fill"><i class="bi bi-graph-up me-1"></i>Chart</a>
        <form method="POST" action="{{ route('watchlist.remove',$item->id) }}" onsubmit="return confirm('Hapus?')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-star me-2 text-warning"></i>Tambah ke Watchlist</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <select id="wlSel" class="form-select">
        <option value="">-- Pilih Negara --</option>
        @foreach($countries as $c)
        <option value="{{ $c->code }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      <button type="button" class="btn btn-warning fw-semibold" onclick="addToWl()">
        <i class="bi bi-star-fill me-2"></i>Tambahkan
      </button>
    </div>
  </div></div>
</div>
@endsection
@push('scripts')
<script>
function addToWl(){
  const code=document.getElementById('wlSel').value;
  if(!code){alert('Pilih negara!');return;}
  ajaxPost('{{ route('watchlist.add') }}',{country_code:code},d=>{
    if(d.success){bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();location.reload();}
    else alert(d.message||'Gagal.');
  });
}
</script>
@endpush
