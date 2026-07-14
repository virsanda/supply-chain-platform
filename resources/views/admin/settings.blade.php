@extends('layouts.app')
@section('title','Pengaturan Sistem')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
<li class="breadcrumb-item active">Pengaturan</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-gear me-2 text-secondary"></i>Pengaturan Sistem</h4>
</div>

<form method="POST" action="{{ route('admin.settings.save') }}">
  @csrf
  @foreach($settings as $group => $groupSettings)
  <div class="card mb-4">
    <div class="card-header fw-semibold text-capitalize">
      <i class="bi bi-sliders me-2 text-primary"></i>
      {{ str_replace('_',' ', ucwords($group,'_')) }}
      @if($group==='risk_weights')
      <small class="text-muted ms-2 fw-normal">(Total harus = 100%)</small>
      @endif
    </div>
    <div class="card-body">
      <div class="row g-3">
        @foreach($groupSettings as $setting)
        <div class="col-md-6">
          <label class="form-label fw-medium small">
            {{ $setting->key }}
            @if($setting->description)<i class="bi bi-question-circle text-muted ms-1" title="{{ $setting->description }}"></i>@endif
          </label>
          @if($setting->type==='json')
          <textarea name="{{ $setting->key }}" class="form-control form-control-sm font-monospace" rows="2">{{ $setting->value }}</textarea>
          @elseif($setting->type==='boolean')
          <select name="{{ $setting->key }}" class="form-select form-select-sm">
            <option value="true"  {{ $setting->value==='true'?'selected':'' }}>Ya</option>
            <option value="false" {{ $setting->value!=='true'?'selected':'' }}>Tidak</option>
          </select>
          @else
          <input type="{{ in_array($setting->type,['integer','decimal'])?'number':'text' }}"
            name="{{ $setting->key }}" class="form-control form-control-sm"
            value="{{ $setting->value }}"
            step="{{ $setting->type==='decimal'?'0.01':'1' }}">
          @endif
          @if($setting->description)
          <div class="text-muted" style="font-size:.7rem;margin-top:2px">{{ $setting->description }}</div>
          @endif
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endforeach

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary fw-semibold">
      <i class="bi bi-save me-2"></i>Simpan Semua Pengaturan
    </button>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
@endsection
