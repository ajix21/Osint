<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'LeakOSINT Search Tool')</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0a0c10; --bg2: #0f1318; --bg3: #141920; --bg4: #1a2130;
    --amber: #e8a225; --amber-dim: #a06b10;
    --teal: #1fc9a0; --teal-dim: #0d6e57;
    --red: #e24b4a; --green: #22c55e;
    --text: #d4cfc4; --text-muted: #6b7480;
    --border: #1e2840; --border2: #253050;
    --mono: 'Space Mono', monospace;
    --display: 'Syne', sans-serif;
  }

  body {
    background: var(--bg); color: var(--text);
    font-family: var(--mono); font-size: 13px;
    line-height: 1.6; min-height: 100vh;
  }

  body::before {
    content: ''; position: fixed; inset: 0;
    background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.04) 2px, rgba(0,0,0,0.04) 4px);
    pointer-events: none; z-index: 9999;
  }

  /* ── TOPBAR ── */
  .topbar {
    background: var(--bg2); border-bottom: 1px solid var(--border);
    padding: 14px 32px; display: flex; align-items: center; gap: 16px;
    position: sticky; top: 0; z-index: 100;
  }
  .logo-mark {
    width: 32px; height: 32px; background: var(--amber);
    clip-path: polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .logo-mark svg { width: 16px; height: 16px; }
  .logo-text { font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: .05em; color: #fff; }
  .logo-text span { color: var(--amber); }
  .topbar-nav { display: flex; align-items: center; gap: 4px; margin-left: 24px; }
  .nav-link {
    font-size: 11px; letter-spacing: .08em; text-transform: uppercase;
    color: var(--text-muted); text-decoration: none;
    padding: 6px 14px; border: 1px solid transparent;
    transition: all .15s;
  }
  .nav-link:hover, .nav-link.active {
    border-color: var(--border2); color: var(--text);
  }
  .nav-link.active { color: var(--amber); border-color: var(--amber-dim); }
  .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
  .user-badge {
    font-size: 10px; letter-spacing: .1em; color: var(--text-muted);
    border: 1px solid var(--border2); padding: 4px 10px; text-transform: uppercase;
  }
  .role-chip {
    font-size: 9px; padding: 2px 8px; border: 1px solid; text-transform: uppercase; letter-spacing: .1em;
  }
  .role-admin    { border-color: var(--amber-dim); color: var(--amber); }
  .role-operator { border-color: var(--teal-dim);  color: var(--teal); }
  .role-viewer   { border-color: var(--border2);   color: var(--text-muted); }

  .btn-logout {
    font-family: var(--mono); font-size: 10px; letter-spacing: .1em;
    text-transform: uppercase; background: transparent;
    border: 1px solid var(--border2); color: var(--text-muted);
    padding: 5px 12px; cursor: pointer; transition: all .15s;
  }
  .btn-logout:hover { border-color: var(--red); color: var(--red); }

  /* ── MAIN ── */
  .main { max-width: 1280px; margin: 0 auto; padding: 32px 24px; }

  /* ── ALERTS ── */
  .alert {
    padding: 12px 16px; border: 1px solid; margin-bottom: 16px;
    font-size: 12px; letter-spacing: .04em;
  }
  .alert-error   { border-color: #7a2c2c; background: rgba(226,75,74,.08); color: var(--red); }
  .alert-success { border-color: var(--teal-dim); background: rgba(31,201,160,.08); color: var(--teal); }

  /* ── FORMS ── */
  .section-label {
    font-size: 10px; letter-spacing: .18em; text-transform: uppercase;
    color: var(--amber); margin-bottom: 12px;
    display: flex; align-items: center; gap: 8px;
  }
  .section-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

  .field { display: flex; flex-direction: column; gap: 6px; }
  .field label { font-size: 10px; letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted); }
  .field input, .field select, .field textarea {
    background: var(--bg3); border: 1px solid var(--border);
    color: var(--text); font-family: var(--mono); font-size: 13px;
    padding: 10px 14px; outline: none; transition: border-color .15s;
  }
  .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--amber-dim); }
  .field input::placeholder, .field textarea::placeholder { color: var(--text-muted); }
  .field select option { background: var(--bg3); }
  .field textarea { resize: vertical; min-height: 80px; }
  .field-error { font-size: 11px; color: var(--red); margin-top: 2px; }

  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; }

  .panel { background: var(--bg2); border: 1px solid var(--border); padding: 24px; margin-bottom: 20px; }

  .btn-row { display: flex; gap: 12px; margin-top: 16px; flex-wrap: wrap; }
  .btn {
    font-family: var(--mono); font-size: 12px; letter-spacing: .08em;
    text-transform: uppercase; padding: 11px 24px;
    border: 1px solid; cursor: pointer; transition: all .15s;
    display: flex; align-items: center; gap: 8px; text-decoration: none;
  }
  .btn-primary { background: var(--amber); border-color: var(--amber); color: #0a0c10; font-weight: 700; }
  .btn-primary:hover { background: #f0b040; border-color: #f0b040; }
  .btn-primary:disabled { background: var(--amber-dim); border-color: var(--amber-dim); opacity: .5; cursor: not-allowed; }
  .btn-ghost { background: transparent; border-color: var(--border2); color: var(--text-muted); }
  .btn-ghost:hover { border-color: var(--teal-dim); color: var(--teal); }
  .btn-ghost:disabled { opacity: .4; cursor: not-allowed; }
  .btn-teal { background: transparent; border-color: var(--teal-dim); color: var(--teal); }
  .btn-teal:hover { background: rgba(31,201,160,.08); }
  .btn-teal:disabled { opacity: .3; cursor: not-allowed; }
  .btn-danger { background: transparent; border-color: #7a2c2c; color: var(--red); }
  .btn-danger:hover { background: rgba(226,75,74,.08); }
  .btn-sm { padding: 5px 12px; font-size: 10px; }

  /* ── TABLE ── */
  .table-wrap { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  thead tr { background: var(--bg3); border-bottom: 1px solid var(--border2); }
  thead th { padding: 10px 14px; text-align: left; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: var(--text-muted); font-weight: 400; white-space: nowrap; }
  tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
  tbody tr:hover { background: var(--bg3); }
  tbody td { padding: 9px 14px; color: var(--text); vertical-align: top; max-width: 280px; word-break: break-all; font-size: 12px; }

  /* ── STATUS / BADGE ── */
  .status-bar { display: flex; align-items: center; gap: 10px; padding: 10px 16px; background: var(--bg3); border: 1px solid var(--border); margin-bottom: 20px; font-size: 11px; letter-spacing: .06em; min-height: 40px; }
  .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--text-muted); flex-shrink: 0; }
  .dot.ready   { background: var(--teal); box-shadow: 0 0 6px var(--teal); }
  .dot.loading { background: var(--amber); animation: pulse .9s infinite; }
  .dot.error   { background: var(--red); }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
  .badge { font-size: 10px; padding: 3px 8px; letter-spacing: .06em; border: 1px solid; }
  .badge-amber { border-color: var(--amber-dim); color: var(--amber); background: rgba(232,162,37,.08); }
  .badge-teal  { border-color: var(--teal-dim);  color: var(--teal);  background: rgba(31,201,160,.08); }
  .badge-red   { border-color: #7a2c2c; color: var(--red); background: rgba(226,75,74,.08); }
  .badge-muted { border-color: var(--border2); color: var(--text-muted); }

  /* ── SPINNER ── */
  .spinner { width: 32px; height: 32px; border: 2px solid var(--border2); border-top-color: var(--amber); border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 16px; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .loading-state { padding: 48px 32px; text-align: center; color: var(--text-muted); }
  .empty-state   { padding: 64px 32px; text-align: center; color: var(--text-muted); }
  .empty-state .big { font-size: 32px; margin-bottom: 12px; opacity: .2; }
  .empty-state p { font-size: 12px; letter-spacing: .06em; line-height: 1.8; }

  /* ── MISC ── */
  .page-title { font-family: var(--display); font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 20px; letter-spacing: .05em; }
  .text-amber { color: var(--amber); }
  .text-teal  { color: var(--teal); }
  .text-muted { color: var(--text-muted); }

  @media (max-width: 700px) {
    .grid-2, .grid-4 { grid-template-columns: 1fr; }
    .topbar { padding: 12px 16px; flex-wrap: wrap; }
    .topbar-nav { display: none; }
    .main { padding: 20px 12px; }
  }
</style>
@stack('styles')
</head>
<body>

<div class="topbar">
  <div class="logo-mark">
    <svg viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="4.5" stroke="#0a0c10" stroke-width="1.5"/><line x1="10.5" y1="10.5" x2="14" y2="14" stroke="#0a0c10" stroke-width="1.5" stroke-linecap="round"/></svg>
  </div>
  <div class="logo-text">Leak<span>OSINT</span></div>

  <nav class="topbar-nav">
    <a href="{{ route('search') }}" class="nav-link {{ request()->routeIs('search*') ? 'active' : '' }}">Search</a>
    <a href="{{ route('search.history') }}" class="nav-link {{ request()->routeIs('search.history') ? 'active' : '' }}">History</a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
    @endif
  </nav>

  <div class="topbar-right">
    <span class="user-badge">{{ auth()->user()->name }}</span>
    <span class="role-chip role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
      @csrf
      <button type="submit" class="btn-logout">Logout</button>
    </form>
  </div>
</div>

<div class="main">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->has('error'))
    <div class="alert alert-error">{{ $errors->first('error') }}</div>
  @endif

  @yield('content')
</div>

@stack('scripts')
</body>
</html>
