<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Passport API Login Demo</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto; background:#0b1220; color:#e5e7eb; margin:0; }
    .wrap { max-width:900px; margin: 30px auto; padding: 0 16px; }
    .card { background:#111827; border:1px solid #1f2937; border-radius:14px; padding:18px; margin-bottom:14px; }
    input, button, textarea { padding:10px 12px; border-radius:10px; border:1px solid #243044; background:#0b1220; color:#e5e7eb; }
    button { background:#22c55e; color:#06210f; border:none; font-weight:700; cursor:pointer; }
    .row{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .grid{ display:grid; gap:10px; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); }
    code.k { display:inline-block; padding:8px 10px; background:#0b1220; border:1px solid #243044; border-radius:10px; user-select:all; }
    .muted{ color:#9ca3af; font-size:13px; }
    pre{ white-space:pre-wrap; background:#0b1220; border:1px solid #243044; padding:12px; border-radius:10px; }
  </style>
</head>
<body>
<div class="wrap">
  <h2>🔐 Laravel Passport – API Login Demo</h2>

  <div class="card">
    <h3 style="margin:0 0 10px">1) Register (demo)</h3>
    <div class="grid">
      <input id="r_name" placeholder="Name" value="Moosa">
      <input id="r_email" placeholder="Email" value="moosa@example.com">
      <input id="r_pass" placeholder="Password" type="password" value="secret1234">
    </div>
    <div style="margin-top:10px" class="row">
      <button onclick="register()">Register</button>
      <span class="muted">already created? skip</span>
    </div>
    <pre id="out_register" class="muted"></pre>
  </div>

  <div class="card">
    <h3 style="margin:0 0 10px">2) Login (Password Grant via /api/login)</h3>
    <div class="grid">
      <input id="l_email" placeholder="Email" value="moosa@example.com">
      <input id="l_pass" placeholder="Password" type="password" value="secret1234">
    </div>
    <div class="row" style="margin-top:10px">
      <button onclick="login()">Login</button>
      <span class="muted">Token will be saved in localStorage</span>
    </div>
    <div style="margin-top:10px">
      <div class="muted">access_token:</div>
      <code class="k" id="tok">(none)</code>
    </div>
    <div class="row" style="margin-top:10px">
      <button onclick="me()">/api/me</button>
      <button onclick="logout()">Logout</button>
    </div>
    <pre id="out" class="muted"></pre>
  </div>

  <p class="muted">Tip: API endpoints are <code class="k">/api/register</code>, <code class="k">/api/login</code>, <code class="k">/api/refresh</code>, <code class="k">/api/me</code>, <code class="k">/api/logout</code></p>
</div>

<script>
const j = (x)=>JSON.stringify(x,null,2);
const TOK_KEY='demo_access'; const REF_KEY='demo_refresh';

function setTokenUI() {
  const t = localStorage.getItem(TOK_KEY) || '(none)';
  document.getElementById('tok').textContent = t.length>120 ? t.slice(0,120)+'...' : t;
}

async function register(){
  const res = await fetch('/api/register',{method:'POST',headers:{'Content-Type':'application/json'},
    body: JSON.stringify({name:document.getElementById('r_name').value,
                          email:document.getElementById('r_email').value,
                          password:document.getElementById('r_pass').value})});
  document.getElementById('out_register').textContent = j(await res.json());
}

async function login(){
  const res = await fetch('/api/login',{method:'POST',headers:{'Content-Type':'application/json'},
    body: JSON.stringify({email:document.getElementById('l_email').value,
                          password:document.getElementById('l_pass').value})});
  const data = await res.json();
  document.getElementById('out').textContent = j(data);
  if(data.access_token){
    localStorage.setItem(TOK_KEY, data.access_token);
    if(data.refresh_token) localStorage.setItem(REF_KEY, data.refresh_token);
    setTokenUI();
  }
}

async function me(){
  const t = localStorage.getItem(TOK_KEY);
  const res = await fetch('/api/me',{headers:{'Authorization':'Bearer '+t}});
  document.getElementById('out').textContent = j(await res.json());
}

async function refresh(){
  const rt = localStorage.getItem(REF_KEY);
  if(!rt){ document.getElementById('out').textContent='No refresh_token in storage'; return; }
  const res = await fetch('/api/refresh',{method:'POST',headers:{'Content-Type':'application/json'},
    body: JSON.stringify({refresh_token: rt})});
  const data = await res.json();
  document.getElementById('out').textContent = j(data);
  if(data.access_token){
    localStorage.setItem(TOK_KEY, data.access_token);
    if(data.refresh_token) localStorage.setItem(REF_KEY, data.refresh_token);
    setTokenUI();
  }
}

async function logout(){
  const t = localStorage.getItem(TOK_KEY);
  if(!t){ document.getElementById('out').textContent='No token'; return; }
  const res = await fetch('/api/logout',{method:'POST',headers:{'Authorization':'Bearer '+t}});
  document.getElementById('out').textContent = j(await res.json());
}

function clearStorage(){ localStorage.removeItem(TOK_KEY); localStorage.removeItem(REF_KEY); setTokenUI(); document.getElementById('out').textContent='Storage cleared'; }
setTokenUI();
</script>
</body>
</html>
