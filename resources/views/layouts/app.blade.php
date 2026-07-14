<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') — Supply Chain Risk Platform</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Leaflet.js CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
:root{--sidebar-width:260px;--topbar-height:60px;--primary:#0d6efd;--dark:#1a1d23}
body{font-family:'Segoe UI',sans-serif;background:#f0f2f5;color:#333}
/* Sidebar */
.sidebar{position:fixed;top:0;left:0;height:100vh;width:var(--sidebar-width);background:var(--dark);color:#fff;z-index:1030;transition:transform .3s;overflow-y:auto}
.sidebar-brand{padding:18px 20px;font-size:1rem;font-weight:700;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;gap:10px}
.sidebar-brand span{color:#0d6efd}
.sidebar .nav-link{color:rgba(255,255,255,.75);padding:10px 20px;border-radius:0;display:flex;align-items:center;gap:10px;font-size:.9rem;transition:all .2s}
.sidebar .nav-link:hover,.sidebar .nav-link.active{color:#fff;background:rgba(13,110,253,.25)}
.sidebar .nav-link i{font-size:1rem;width:20px;text-align:center}
.sidebar .nav-section{padding:8px 20px 4px;font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);font-weight:600}
/* Main content */
.main-content{margin-left:var(--sidebar-width);min-height:100vh}
.topbar{position:sticky;top:0;z-index:1020;background:#fff;border-bottom:1px solid #e9ecef;height:var(--topbar-height);padding:0 24px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.page-content{padding:24px}
/* Cards */
.card{border:none;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.08)}
.card-header{background:transparent;border-bottom:1px solid #f0f0f0;font-weight:600}
/* Risk badges */
.risk-low{background:#d1f5e0;color:#0a5c35;border-radius:20px;padding:3px 10px;font-size:.78rem;font-weight:600}
.risk-medium{background:#fff3cd;color:#7d5a00;border-radius:20px;padding:3px 10px;font-size:.78rem;font-weight:600}
.risk-high{background:#fde8e8;color:#8b1a1a;border-radius:20px;padding:3px 10px;font-size:.78rem;font-weight:600}
.risk-critical{background:#2d2d2d;color:#fff;border-radius:20px;padding:3px 10px;font-size:.78rem;font-weight:600}
/* Stat card */
.stat-card{border-radius:12px;padding:20px;color:#fff}
.stat-card .stat-value{font-size:2rem;font-weight:700;line-height:1}
.stat-card .stat-label{font-size:.85rem;opacity:.85;margin-top:4px}
.stat-card .stat-icon{font-size:2.5rem;opacity:.3}
/* Map container */
#worldMap,#portMap,#weatherMap{border-radius:12px;overflow:hidden}
/* Loading */
.spinner-overlay{position:fixed;inset:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;z-index:9999;display:none}
/* Responsive */
@media(max-width:768px){.sidebar{transform:translateX(-100%)}.sidebar.show{transform:translateX(0)}.main-content{margin-left:0}}
</style>
@stack('styles')
</head>
<body>

<div class="spinner-overlay" id="globalSpinner">
  <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <i class="bi bi-globe2 text-primary fs-4"></i>
    <div><div class="text-white" style="font-size:.85rem;font-weight:700">Supply Chain</div><span style="font-size:.7rem;color:#6c9fff">Risk Platform</span></div>
  </div>

  <div class="sidebar-section mt-2">
    <div class="nav-section">Main</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
  </div>

  <div class="nav-section mt-2">Analytics</div>
  <a href="{{ route('countries.index') }}" class="nav-link {{ request()->routeIs('countries.*') ? 'active' : '' }}">
    <i class="bi bi-globe"></i> Country Dashboard
  </a>
  <a href="{{ route('weather.index') }}" class="nav-link {{ request()->routeIs('weather.*') ? 'active' : '' }}">
    <i class="bi bi-cloud-sun"></i> Weather Monitoring
  </a>
  <a href="{{ route('currency.index') }}" class="nav-link {{ request()->routeIs('currency.*') ? 'active' : '' }}">
    <i class="bi bi-currency-exchange"></i> Currency Impact
  </a>
  <a href="{{ route('news.index') }}" class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
    <i class="bi bi-newspaper"></i> News Intelligence
  </a>
  <a href="{{ route('ports.index') }}" class="nav-link {{ request()->routeIs('ports.*') ? 'active' : '' }}">
    <i class="bi bi-anchor"></i> Port Locations
  </a>
  <a href="{{ route('comparison.index') }}" class="nav-link {{ request()->routeIs('comparison.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-steps"></i> Country Comparison
  </a>
  <a href="{{ route('visualization.index') }}" class="nav-link {{ request()->routeIs('visualization.*') ? 'active' : '' }}">
    <i class="bi bi-graph-up"></i> Data Visualization
  </a>

  <div class="nav-section mt-2">Personal</div>
  <a href="{{ route('watchlist.index') }}" class="nav-link {{ request()->routeIs('watchlist.*') ? 'active' : '' }}">
    <i class="bi bi-star"></i> Watchlist
  </a>

  @if(auth()->user()?->isAdmin())
  <div class="nav-section mt-2">Admin</div>
  <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
    <i class="bi bi-shield-check"></i> Admin Panel
  </a>
  @endif

  <div class="mt-auto" style="padding:20px;border-top:1px solid rgba(255,255,255,.1);margin-top:20px">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ auth()->user()?->avatar_url }}" width="34" height="34" class="rounded-circle" alt="avatar">
      <div>
        <div style="font-size:.82rem;font-weight:600;color:#fff">{{ auth()->user()?->name }}</div>
        <div style="font-size:.7rem;color:#888">{{ ucfirst(auth()->user()?->role) }}</div>
      </div>
    </div>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">
      @csrf
      <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </form>
  </div>
</nav>

<!-- Main -->
<div class="main-content">
  <!-- Topbar -->
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
      </button>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 small">
          @yield('breadcrumb')
        </ol>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="badge bg-primary-subtle text-primary small">
        <i class="bi bi-circle-fill text-success" style="font-size:.5rem"></i> Live
      </span>
      <span class="text-muted small d-none d-md-inline" id="currentTime"></span>
    </div>
  </div>

  <!-- Alerts -->
  <div style="padding:0 24px">
    @foreach(['success'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'] as $type => $class)
      @if(session($type))
        <div class="alert alert-{{ $class }} alert-dismissible fade show mt-3 mb-0" role="alert">
          {{ session($type) }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
    @endforeach
  </div>

  <!-- Page content -->
  <div class="page-content">
    @yield('content')
  </div>
</div><!-- /main-content -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<!-- Leaflet.js -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Real-time clock
function updateClock(){
  const now = new Date();
  document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'}) + ' WIB';
}
setInterval(updateClock, 1000); updateClock();

// CSRF for AJAX
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// Global spinner
function showSpinner(){ document.getElementById('globalSpinner').style.display='flex'; }
function hideSpinner(){ document.getElementById('globalSpinner').style.display='none'; }

// AJAX helper
function ajaxPost(url, data, onSuccess, onError){
  showSpinner();
  fetch(url, {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
    body: JSON.stringify(data)
  })
  .then(r=>r.json())
  .then(d=>{ hideSpinner(); onSuccess(d); })
  .catch(e=>{ hideSpinner(); if(onError) onError(e); });
}

// Risk color helper
function riskColor(level){
  return {'low':'#198754','medium':'#ffc107','high':'#dc3545','critical':'#212529'}[level]||'#6c757d';
}
function riskBg(level){
  return {'low':'#d1f5e0','medium':'#fff3cd','high':'#fde8e8','critical':'#2d2d2d'}[level]||'#e9ecef';
}
</script>
@stack('scripts')
</body>
</html>
