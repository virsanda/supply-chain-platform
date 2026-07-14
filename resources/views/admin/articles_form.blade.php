@extends('layouts.app')
@section('title', $article ? 'Edit Artikel' : 'Tulis Artikel')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.articles') }}">Artikel</a></li>
<li class="breadcrumb-item active">{{ $article ? 'Edit' : 'Baru' }}</li>
@endsection
@section('content')
<div class="card" style="max-width:800px">
  <div class="card-header fw-semibold">
    <i class="bi bi-{{ $article?'pencil':'plus' }} me-2 text-primary"></i>
    {{ $article ? 'Edit Artikel' : 'Tulis Artikel Baru' }}
  </div>
  <div class="card-body">
    <form method="POST" action="{{ $action }}">
      @csrf
      @if($article) @method('PUT') @endif

      <div class="mb-3">
        <label class="form-label fw-medium">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
          value="{{ old('title', $article?->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
          <select name="category" class="form-select" required>
            @foreach(['risk_analysis'=>'Risk Analysis','market_update'=>'Market Update','logistics'=>'Logistics','geopolitics'=>'Geopolitics','economy'=>'Economy'] as $val=>$lbl)
            <option value="{{ $val }}" {{ old('category',$article?->category)===$val?'selected':'' }}>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-select" required>
            <option value="draft"     {{ old('status',$article?->status)==='draft'?'selected':'' }}>Draft</option>
            <option value="published" {{ old('status',$article?->status)==='published'?'selected':'' }}>Published</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-medium">Terkait Negara</label>
          <select name="country_code" class="form-select">
            <option value="">-- Semua Negara --</option>
            @foreach(App\Models\Country::active()->orderBy('name')->get() as $c)
            <option value="{{ $c->code }}" {{ old('country_code',$article?->country_code)===$c->code?'selected':'' }}>
              {{ $c->flag_emoji }} {{ $c->name }}
            </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-medium">Ringkasan</label>
        <textarea name="excerpt" class="form-control" rows="2" placeholder="Ringkasan singkat...">{{ old('excerpt', $article?->excerpt) }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label fw-medium">Isi Artikel <span class="text-danger">*</span></label>
        <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="14" required>{{ old('body', $article?->body) }}</textarea>
        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary fw-semibold">
          <i class="bi bi-save me-2"></i>{{ $article ? 'Simpan Perubahan' : 'Publikasikan' }}
        </button>
        <a href="{{ route('admin.articles') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
