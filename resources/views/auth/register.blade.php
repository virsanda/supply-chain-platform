<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Supply Chain Risk Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:linear-gradient(135deg,#1a1d23 0%,#0d6efd20 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.register-card{width:440px;max-width:95vw;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.card-header-custom{background:linear-gradient(135deg,#0d6efd,#0099ff);padding:24px 32px;color:#fff}
.card-body-custom{background:#fff;padding:32px}
</style>
</head>
<body>
<div class="register-card">
  <div class="card-header-custom">
    <div class="d-flex align-items-center gap-3">
      <i class="bi bi-globe2 fs-2 opacity-90"></i>
      <div>
        <div class="fw-bold fs-5">Daftar Akun Baru</div>
        <div class="small opacity-75">Supply Chain Risk Platform</div>
      </div>
    </div>
  </div>
  <div class="card-body-custom">
    @if($errors->any())
    <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register.post') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-medium small">Nama Lengkap</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
          placeholder="Nama Anda" value="{{ old('name') }}" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label fw-medium small">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
          placeholder="email@example.com" value="{{ old('email') }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label fw-medium small">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
      </div>
      <div class="mb-4">
        <label class="form-label fw-medium small">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
        <i class="bi bi-person-plus me-2"></i>Buat Akun
      </button>
    </form>
    <p class="text-center text-muted small mt-3 mb-0">
      Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-medium">Masuk di sini</a>
    </p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
