<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Supply Chain Risk Platform</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:linear-gradient(135deg,#1a1d23 0%,#0d6efd20 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-card{width:420px;max-width:95vw;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.login-header{background:linear-gradient(135deg,#0d6efd,#0099ff);padding:32px 32px 24px;color:#fff}
.login-body{background:#fff;padding:32px}
.form-control:focus{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.2)}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-header">
    <div class="d-flex align-items-center gap-3 mb-3">
      <i class="bi bi-globe2" style="font-size:2.5rem;opacity:.9"></i>
      <div>
        <div class="fw-bold fs-5">Supply Chain Risk</div>
        <div style="font-size:.85rem;opacity:.8">Intelligence Platform</div>
      </div>
    </div>
    <p class="mb-0 small opacity-75">Monitor risiko rantai pasok global secara real-time</p>
  </div>
  <div class="login-body">
    <h5 class="mb-4 fw-semibold text-dark">Masuk ke Akun</h5>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-sm py-2">
      <?php echo e($errors->first()); ?>

    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login.post')); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label fw-medium small">Email</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
          <input type="email" name="email" class="form-control border-start-0 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="your@email.com" value="<?php echo e(old('email')); ?>" required autofocus>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-medium small">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
          <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
        </div>
      </div>
      <div class="mb-4 d-flex justify-content-between align-items-center">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label small" for="remember">Ingat saya</label>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
      </button>
    </form>

    <hr class="my-4">
    <p class="text-center text-muted small mb-0">
      Belum punya akun? <a href="<?php echo e(route('register')); ?>" class="text-primary fw-medium">Daftar sekarang</a>
    </p>

    <div class="mt-3 p-3 bg-light rounded small">
      <div class="fw-medium text-muted mb-1">Demo Credentials:</div>
      <div>Admin: <code>admin@supplychain.com</code> / <code>Admin@1234</code></div>
      <div>User: <code>user@supplychain.com</code> / <code>User@1234</code></div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH D:\Global Supply\supply-chain-platform\resources\views/auth/login.blade.php ENDPATH**/ ?>