@extends('layouts.app')
@section('title','Country Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Countries</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-globe me-2 text-primary"></i>Global Country Dashboard</h4>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('countries.index') }}" class="row g-3">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Cari negara..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-4">
        <select name="region" class="form-select">
          <option value="">Semua Region</option>
          @foreach($regions as $region)
          <option value="{{ $region }}" {{ request('region')==$region?'selected':'' }}>{{ $region }}</option>
          @endforeach
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
  @forelse($countries as $country)
  <div class="col-sm-6 col-md-4 col-xl-3">
    <div class="card h-100 country-card" style="cursor:pointer" onclick="window.location='{{ route('countries.show',$country->code) }}'">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <span style="font-size:2rem">{{ $country->flag_emoji }}</span>
          <div>
            <div class="fw-bold">{{ $country->name }}</div>
            <div class="text-muted small">{{ $country->capital }} · {{ $country->code }}</div>
          </div>
        </div>
        <div class="small text-muted mb-2">
          <i class="bi bi-geo-alt me-1"></i>{{ $country->region }}
          @if($country->subregion) · {{ $country->subregion }}@endif
        </div>
        @if($country->latestRiskScore)
        <div class="mt-2">
          <span class="risk-{{ $country->latestRiskScore->risk_level }}">
            {{ $country->latestRiskScore->risk_label }}: {{ number_format($country->latestRiskScore->total_score,1) }}
          </span>
        </div>
        @endif
        @if($country->currency_code)
        <div class="text-muted small mt-2"><i class="bi bi-currency-exchange me-1"></i>{{ $country->currency_code }}</div>
        @endif
      </div>
      <div class="card-footer bg-transparent border-0 pt-0">
        <a href="{{ route('countries.show',$country->code) }}" class="btn btn-sm btn-outline-primary w-100">
          <i class="bi bi-eye me-1"></i>Lihat Detail
        </a>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12 text-center py-5 text-muted">
    <i class="bi bi-search fs-1 mb-3 d-block opacity-25"></i>
    Tidak ada negara ditemukan.
  </div>
  @endforelse
</div>

<div class="mt-4">{{ $countries->withQueryString()->links() }}</div>
@endsection
@push('styles')
<style>.country-card:hover{transform:translateY(-3px);transition:.2s;box-shadow:0 6px 20px rgba(0,0,0,.1)}</style>
@endpush
