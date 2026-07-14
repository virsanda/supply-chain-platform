@extends('layouts.app')
@section('title','Kelola Ports')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
<li class="breadcrumb-item active">Ports</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-anchor me-2"></i>Kelola Dataset Pelabuhan</h4>
  <span class="badge bg-secondary">{{ $ports->total() }} pelabuhan</span>
</div>
<div class="card mb-3"><div class="card-body py-2">
  <input type="text" id="psearch" class="form-control" placeholder="Cari nama atau kode pelabuhan...">
</div></div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive" style="max-height:580px;overflow-y:auto">
      <table class="table table-sm table-hover mb-0">
        <thead class="table-light sticky-top">
          <tr><th>Pelabuhan</th><th>Kode</th><th>Negara</th><th>Region</th><th>Ukuran</th><th>Kongesti</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody id="pTbody">
          @forelse($ports as $p)
          <tr data-name="{{ strtolower($p->port_name) }}" data-code="{{ strtolower($p->port_code) }}">
            <td class="fw-medium small">{{ $p->port_name }}</td>
            <td><span class="badge bg-light text-dark border small">{{ $p->port_code }}</span></td>
            <td class="small">{{ $p->country_name }}</td>
            <td class="small text-muted">{{ $p->province_region }}</td>
            <td><span class="badge bg-secondary small">{{ $p->harbor_size_label }}</span></td>
            <td><span class="badge bg-{{ $p->congestion_badge_class }}" style="font-size:.67rem">{{ ucfirst($p->congestion_level) }} ({{ round($p->congestion_score) }})</span></td>
            <td class="text-center">
              <form method="POST" action="{{ route('admin.ports.delete',$p->id) }}" onsubmit="return confirm('Hapus pelabuhan ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger" style="font-size:.7rem;padding:2px 8px"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">{{ $ports->links() }}</div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('psearch').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#pTbody tr').forEach(r=>{
    r.style.display=!q||r.dataset.name?.includes(q)||r.dataset.code?.includes(q)?'':'none';
  });
});
</script>
@endpush
