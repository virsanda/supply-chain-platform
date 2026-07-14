@extends('layouts.app')
@section('title','Kelola Users')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
<li class="breadcrumb-item active">Users</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>Kelola Users</h4>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Terdaftar</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($users as $u)
          <tr>
            <td class="text-muted small">{{ $u->id }}</td>
            <td><div class="d-flex align-items-center gap-2">
              <img src="{{ $u->avatar_url }}" width="32" height="32" class="rounded-circle">
              <div class="fw-medium small">{{ $u->name }}</div>
            </div></td>
            <td class="small text-muted">{{ $u->email }}</td>
            <td><span class="badge bg-{{ $u->role==='admin'?'danger':'primary' }}">{{ ucfirst($u->role) }}</span></td>
            <td><span class="badge bg-{{ $u->is_active?'success':'secondary' }}">{{ $u->is_active?'Aktif':'Nonaktif' }}</span></td>
            <td class="text-muted small">{{ $u->created_at->format('d M Y') }}</td>
            <td class="text-center">
              @if($u->id!==auth()->id())
              <div class="d-flex gap-1 justify-content-center">
                <select class="form-select form-select-sm" style="width:85px" onchange="chRole({{ $u->id }},this.value)">
                  <option value="user" {{ $u->role==='user'?'selected':'' }}>User</option>
                  <option value="admin" {{ $u->role==='admin'?'selected':'' }}>Admin</option>
                </select>
                <form method="POST" action="{{ route('admin.users.delete',$u->id) }}" onsubmit="return confirm('Hapus user ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
              </div>
              @else<span class="badge bg-info small">You</span>@endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada user</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">{{ $users->links() }}</div>
</div>
@endsection
@push('scripts')
<script>
function chRole(id,role){
  if(!confirm(`Ubah role menjadi ${role}?`))return location.reload();
  fetch(`/admin/users/${id}/role`,{method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:JSON.stringify({role})})
  .then(r=>r.json()).then(d=>{d.success?location.reload():alert(d.message||'Gagal');});
}
</script>
@endpush
