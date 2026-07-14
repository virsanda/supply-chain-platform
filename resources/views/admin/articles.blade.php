@extends('layouts.app')
@section('title','Kelola Artikel')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
<li class="breadcrumb-item active">Artikel</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-newspaper me-2 text-info"></i>Kelola Artikel Analisis</h4>
  <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus me-1"></i>Tulis Artikel
  </a>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Penulis</th><th>Views</th><th>Dibuat</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($articles as $art)
          <tr>
            <td>
              <div class="fw-medium small">{{ Str::limit($art->title,50) }}</div>
              @if($art->country_code)<span class="badge bg-light text-dark border" style="font-size:.62rem">{{ $art->country_code }}</span>@endif
            </td>
            <td><span class="badge bg-info-subtle text-info small">{{ $art->category_label }}</span></td>
            <td><span class="badge bg-{{ $art->status==='published'?'success':($art->status==='draft'?'warning':'secondary') }}">{{ ucfirst($art->status) }}</span></td>
            <td class="small text-muted">{{ $art->author?->name??'—' }}</td>
            <td class="text-muted small">{{ $art->views }}</td>
            <td class="text-muted small">{{ $art->created_at->format('d M Y') }}</td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('admin.articles.edit',$art->id) }}" class="btn btn-xs btn-outline-primary" style="font-size:.7rem;padding:2px 8px">Edit</a>
                <form method="POST" action="{{ route('admin.articles.delete',$art->id) }}" onsubmit="return confirm('Hapus artikel?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-outline-danger" style="font-size:.7rem;padding:2px 8px"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-4">
            Belum ada artikel. <a href="{{ route('admin.articles.create') }}">Tulis artikel pertama</a>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">{{ $articles->links() }}</div>
</div>
@endsection
