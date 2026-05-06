<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — LeakOSINT</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0a0c10; --bg2: #0f1318; --bg3: #141920;
    --amber: #e8a225; --amber-dim: #a06b10;
    --teal: #1fc9a0; --teal-dim: #0d6e57;
    --red: #e24b4a;
    --text: #d4cfc4; --text-muted: #6b7480;
    --border: #1e2840; --border2: #253050;
    --mono: 'Space Mono', monospace;
    --display: 'Syne', sans-serif;
  }
  body {
    background: var(--bg); color: var(--text);
    font-family: var(--mono); font-size: 13px;
    min-height: 100vh; display: flex; align-items: center; justify-content: center;
  }
  body::before {
    content: ''; position: fixed; inset: 0;
    background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.04) 2px, rgba(0,0,0,0.04) 4px);
    pointer-events: none; z-index: 9999;
  }

  /* Animated background grid */
  body::after {
    content: ''; position: fixed; inset: 0;
    background-image:
      linear-gradient(rgba(30,40,64,0.4) 1px, transparent 1px),
      linear-gradient(90deg, rgba(30,40,64,0.4) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none; z-index: 0;
  }

  .login-wrap {
    position: relative; z-index: 10;
    width: 100%; max-width: 420px; padding: 16px;
  }

  .login-header { text-align: center; margin-bottom: 32px; }
  .logo-mark {
    width: 48px; height: 48px; background: var(--amber);
    clip-path: polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
  }
  .logo-mark svg { width: 24px; height: 24px; }
  .logo-text { font-family: var(--display); font-size: 26px; font-weight: 800; letter-spacing: .05em; color: #fff; }
  .logo-text span { color: var(--amber); }
  .login-subtitle { font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: var(--text-muted); margin-top: 6px; }

  .login-box {
    background: var(--bg2); border: 1px solid var(--border);
    padding: 32px;
  }

  .login-box-title {
    font-size: 10px; letter-spacing: .2em; text-transform: uppercase;
    color: var(--amber); margin-bottom: 24px;
    display: flex; align-items: center; gap: 8px;
  }
  .login-box-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

  .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
  .field label { font-size: 10px; letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted); }
  .field input {
    background: var(--bg3); border: 1px solid var(--border);
    color: var(--text); font-family: var(--mono); font-size: 13px;
    padding: 12px 14px; outline: none; transition: border-color .15s; width: 100%;
  }
  .field input:focus { border-color: var(--amber-dim); }
  .field input::placeholder { color: var(--text-muted); }
  .field-error { font-size: 11px; color: var(--red); margin-top: 2px; }

  .remember-row {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 20px; font-size: 11px; color: var(--text-muted);
    cursor: pointer;
  }
  .remember-row input[type="checkbox"] { accent-color: var(--amber); width: 14px; height: 14px; cursor: pointer; }

  .btn-login {
    width: 100%; font-family: var(--mono); font-size: 13px;
    font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    background: var(--amber); border: none; color: #0a0c10;
    padding: 14px; cursor: pointer; transition: background .15s;
  }
  .btn-login:hover { background: #f0b040; }

  .alert-error {
    padding: 10px 14px; border: 1px solid #7a2c2c;
    background: rgba(226,75,74,.08); color: var(--red);
    font-size: 12px; margin-bottom: 16px; letter-spacing: .04em;
    display: flex; gap: 8px; align-items: flex-start;
  }

  .login-footer {
    text-align: center; margin-top: 16px;
    font-size: 10px; letter-spacing: .08em; color: var(--text-muted);
  }

  .security-note {
    margin-top: 24px; padding: 12px;
    border: 1px solid var(--border); background: var(--bg3);
    font-size: 10px; letter-spacing: .06em; color: var(--text-muted);
    line-height: 1.8;
  }
  .security-note span { color: var(--teal); }
</style>
</head>
<body>

<div class="login-wrap">
  <div class="login-header">
    <div class="logo-mark">
      <svg viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="4.5" stroke="#0a0c10" stroke-width="1.8"/><line x1="10.5" y1="10.5" x2="14" y2="14" stroke="#0a0c10" stroke-width="1.8" stroke-linecap="round"/></svg>
    </div>
    <div class="logo-text">Leak<span>OSINT</span></div>
    <div class="login-subtitle">Secure Intelligence Platform</div>
  </div>

  <div class="login-box">
    <div class="login-box-title">Autentikasi</div>

    @if($errors->any())
      <div class="alert-error">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/><line x1="8" y1="5" x2="8" y2="9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="8" cy="11.5" r=".75" fill="currentColor"/></svg>
        <div>
          @foreach($errors->all() as $error)
            {{ $error }}<br>
          @endforeach
        </div>
      </div>
    @endif

    <form method="POST" action="/login">
      @csrf

      <div class="field">
        <label>Username / Email</label>
        <input
          type="text"
          name="username"
          value="{{ old('username') }}"
          placeholder="Masukkan username atau email"
          autocomplete="username"
          autofocus
        >
      </div>

      <div class="field">
        <label>Password</label>
        <input
          type="password"
          name="password"
          placeholder="••••••••"
          autocomplete="current-password"
        >
      </div>

      <label class="remember-row">
        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
        Ingat sesi ini
      </label>

      <button type="submit" class="btn-login">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M6 3H3v10h3M10 5l3 3-3 3M13 8H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Masuk ke Sistem
      </button>
    </form>
  </div>

  <div class="security-note">
    <span>&#9632;</span> Sistem ini dilindungi autentikasi multi-user.<br>
    <span>&#9632;</span> Percobaan login berulang akan dikunci otomatis.<br>
    <span>&#9632;</span> Seluruh aktivitas dicatat dalam log sistem.
  </div>

  <div class="login-footer">
    LeakOSINT Intelligence Platform &mdash; Restricted Access
  </div>
</div>

</body>
</html>
