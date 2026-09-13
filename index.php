<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SecureShop — Adaptive MFA Commerce</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div id="toast"></div>

<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div class="logo">
      <div class="logo-mark">S</div>
      <div><b>SecureShop</b><small>Adaptive Commerce</small></div>
    </div>
    <nav>
      <button class="nav-item active" data-page="overview"><span>⌂</span> Overview</button>
      <button class="nav-item hidden" id="navShop" data-page="shop"><span>◈</span> Marketplace</button>
      <button class="nav-item" data-page="security"><span>◉</span> Security Center</button>
      <button class="nav-item" data-page="activity"><span>◷</span> Audit Activity</button>
    </nav>
    <div class="sidebar-bottom">
      <div class="security-mini">
        <div class="pulse"></div>
        <div><b>Protection Active</b><small>Adaptive MFA online</small></div>
      </div>
      <button class="nav-item" data-page="research"><span>▣</span> Research Mode</button>
      <button class="nav-item hidden" id="btnLogout" onclick="logout()" style="color:#ff6682; margin-top:8px;">
        <span>⊘</span> Logout
      </button>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="mobile-menu" onclick="toggleSidebar()">☰</button>
      <div class="search"><span>⌕</span><input id="globalSearch" placeholder="Cari produk, kategori, aktivitas..."></div>
      <div class="top-actions">
        <!-- Fitur Baru: Cart Icon dengan Counter Badge -->
        <button class="icon-btn" onclick="openCart()" title="Keranjang Belanja" style="position:relative;">
          🛒 <span id="cartBadge" style="position:absolute;top:-4px;right:-4px;background:#7359f6;color:white;font-size:9px;font-weight:bold;padding:2px 6px;border-radius:10px;display:none;">0</span>
        </button>
        <button class="icon-btn" onclick="showToast('Tidak ada notifikasi baru')">♧<i></i></button>
        <div class="user-chip"><div class="avatar">AM</div><div><b>Andi</b><small>Customer</small></div></div>
      </div>
    </header>

    <!-- OVERVIEW DASHBOARD -->
    <section id="overview" class="page active">
      <div class="hero">
        <div>
          <div class="eyebrow">SECURE COMMERCE • 2026</div>
          <h1>Belanja nyaman.<br><em>Keamanan adaptif.</em></h1>
          <p>SecureShop menyesuaikan tingkat autentikasi berdasarkan risiko login secara real-time tanpa mengorbankan pengalaman pengguna.</p>
          <div class="hero-actions">
            <button class="primary" onclick="goPage('shop')">Mulai Belanja <span>→</span></button>
            <button class="ghost" onclick="goPage('security')">Lihat Proteksi</button>
          </div>
        </div>
        <div class="hero-visual">
          <div class="orb"></div><div class="shield">✓</div>
          <div class="float-card fc1"><span class="green-dot"></span> Risk engine active</div>
          <div class="float-card fc2"><b>98.7%</b><small>Protection score</small></div>
        </div>
      </div>

      <div class="section-head">
        <div><span class="eyebrow">DASHBOARD</span><h2>Security overview</h2></div>
        <span class="live"><i></i> LIVE MONITORING</span>
      </div>

      <!-- Fitur Baru: Threat Attack Simulator Bar -->
      <div class="panel" style="padding:15px 20px; margin-bottom:16px; background:#12182a; color:white; border-radius:16px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
          <b style="font-family:'Space Grotesk'; font-size:14px; color:#9b8bff;">⚡ Live Threat Attack Simulator</b>
          <p style="margin:2px 0 0; font-size:11px; color:#8d95a5;">Simulasikan ancaman siber untuk menguji respons Adaptive Risk Engine.</p>
        </div>
        <div style="display:flex; gap:8px;">
          <button style="background:#ff5d7d; color:white; font-size:10px; font-weight:bold; padding:8px 12px; border-radius:8px;" onclick="simulateThreat('Brute Force Attack')">Simulate Brute Force</button>
          <button style="background:#ed9b2d; color:white; font-size:10px; font-weight:bold; padding:8px 12px; border-radius:8px;" onclick="simulateThreat('Bot Login Attempt')">Simulate Bot Access</button>
          <button style="background:#4e83ff; color:white; font-size:10px; font-weight:bold; padding:8px 12px; border-radius:8px;" onclick="simulateThreat('Suspicious IP Access')">Simulate New Location</button>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">↗</div>
          <small>Successful Login</small>
          <strong id="statSuccess">0</strong>
          <span class="up">Live <label>session log</label></span>
        </div>
        <div class="stat-card">
          <div class="stat-icon red">⊘</div>
          <small>Blocked Attempts</small>
          <strong id="statBlocked">0</strong>
          <span class="down">Security <label>interventions</label></span>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange">⌁</div>
          <small>OTP Failures / Reviews</small>
          <strong id="statOtp">0</strong>
          <span class="neutral">Risk monitored</span>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">◉</div>
          <small>Total Security Events</small>
          <strong id="statTotalEvents">0</strong>
          <span class="up">Active <label>tracking</label></span>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="panel chart-panel">
          <div class="panel-head">
            <div><b>Authentication activity</b><small>Real-time Session Events</small></div>
            <span class="status-pill safe">ACTIVE LOGGING</span>
          </div>
          <div class="chart">
            <div class="y-axis"><span>40</span><span>30</span><span>20</span><span>10</span><span>0</span></div>
            <svg viewBox="0 0 700 230" preserveAspectRatio="none">
              <defs><linearGradient id="fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-opacity=".28"/><stop offset="1" stop-opacity="0"/></linearGradient></defs>
              <path class="area" d="M0,190 C45,175 65,130 110,150 S170,120 220,135 S270,80 320,110 S375,60 420,95 S470,115 520,70 S580,85 620,45 S665,65 700,30 V230 H0Z"/>
              <path class="line" d="M0,190 C45,175 65,130 110,150 S170,120 220,135 S270,80 320,110 S375,60 420,95 S470,115 520,70 S580,85 620,45 S665,65 700,30"/>
            </svg>
            <div class="x-axis"><span>Session 1</span><span>Session 2</span><span>Session 3</span><span>Session 4</span><span>Session 5</span><span>Session 6</span><span>Current</span></div>
          </div>
        </div>

        <div class="panel risk-panel">
          <div class="panel-head">
            <div><b>Adaptive risk engine</b><small>Current system state</small></div>
            <span class="status-pill safe">LOW RISK</span>
          </div>
          <div class="risk-ring"><div><strong id="liveRiskNum">18</strong><small>/100</small></div></div>
          <div class="risk-bars">
            <div><span>Device trust</span><b>92%</b><i><em style="width:92%"></em></i></div>
            <div><span>IP reputation</span><b>96%</b><i><em style="width:96%"></em></i></div>
            <div><span>Behavior match</span><b>88%</b><i><em style="width:88%"></em></i></div>
          </div>
          <div class="risk-note" id="liveRiskNote">Normal risk → Password + OTP</div>
        </div>
      </div>

      <div class="panel recent">
        <div class="panel-head">
          <div><b>Recent authentication</b><small>Live audit trail</small></div>
          <button class="text-btn" onclick="goPage('activity')">View all →</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Time</th><th>User / Device</th><th>Event</th><th>Reason</th><th>Risk</th><th>Status</th></tr>
            </thead>
            <tbody id="recentTable"></tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- MARKETPLACE PAGE -->
    <section id="shop" class="page">
      <div class="shop-banner"><div><span class="eyebrow">SECURE MARKETPLACE</span><h1>Find something<br><em>worth protecting.</em></h1><p>Produk pilihan dengan pengalaman checkout yang dilindungi Adaptive MFA.</p></div><div class="banner-art">◈</div></div>
      <div class="shop-toolbar"><div><b>Featured products</b><small>12 items</small></div><div class="filters"><button class="filter active" onclick="filterProducts('all',this)">All</button><button class="filter" onclick="filterProducts('tech',this)">Tech</button><button class="filter" onclick="filterProducts('fashion',this)">Fashion</button><button class="filter" onclick="filterProducts('home',this)">Home</button></div></div>
      <div class="products" id="products"></div>
    </section>

    <!-- SECURITY CENTER PAGE -->
    <section id="security" class="page">
      <div class="section-head"><div><span class="eyebrow">SECURITY CENTER</span><h1>Protection control room</h1><p>Monitoring autentikasi, risiko, dan kebijakan keamanan secara terpusat.</p></div><span class="live"><i></i> SYSTEM HEALTHY</span></div>
      <div class="security-grid">
        <div class="panel big-risk">
          <div class="panel-head"><div><b>Risk scoring</b><small>Adaptive decision engine</small></div><span class="status-pill safe">ACTIVE</span></div>
          <div class="big-risk-body">
            <div class="risk-ring large"><div><strong id="bigRiskNum">18</strong><small id="bigRiskLabel">LOW</small></div></div>
            <div class="risk-rules">
              <div><span>✓</span> Known device <b>−20</b></div>
              <div><span>✓</span> Trusted IP <b>−15</b></div>
              <div><span>!</span> New location <b>+25</b></div>
              <div><span>!</span> Failed password ×3 <b>+30</b></div>
              <div><span>!</span> Bot-like behavior <b>+40</b></div>
            </div>
          </div>
        </div>
        <div class="panel policy">
          <div class="panel-head"><div><b>MFA policy</b><small>Current authentication flow</small></div></div>
          <div class="flow">
            <div class="flow-node done"><i>1</i><b>Password</b><small>Knowledge factor</small></div><span>→</span>
            <div class="flow-node done"><i>2</i><b>Risk Check</b><small>Context analysis</small></div><span>→</span>
            <div class="flow-node active"><i>3</i><b>OTP</b><small>Possession factor</small></div><span>→</span>
            <div class="flow-node optional"><i>4</i><b>Extra Verify</b><small>High-risk only</small></div>
          </div>
          <div class="policy-foot"><span>OTP expiry <b>60 sec</b></span><span>Max attempts <b>5</b></span><span>Rate limit <b>Active</b></span></div>
        </div>
      </div>
      <div class="panel test-panel">
        <div class="panel-head">
          <div><b>Security test matrix</b><small>Live system scenarios & automation</small></div>
          <button class="ghost" style="padding:4px 10px;font-size:10px" onclick="runAutomatedTests()">Run Auto Test</button>
        </div>
        <div class="test-grid" id="testGrid"></div>
      </div>
    </section>

    <!-- AUDIT ACTIVITY PAGE -->
    <section id="activity" class="page">
      <div class="section-head">
        <div><span class="eyebrow">AUDIT ACTIVITY</span><h1>Authentication log</h1><p>Setiap kejadian dicatat secara live untuk evaluasi keamanan dan penelitian.</p></div>
        <button class="ghost" onclick="clearLogs()">Clear local demo logs</button>
      </div>

      <!-- Fitur Baru: Filter & Search Bar untuk Audit Logs -->
      <div style="display:flex; gap:10px; margin-bottom:15px; flex-wrap:wrap;">
        <input id="logSearchInput" placeholder="Cari event, IP, atau alasan..." style="flex:1; padding:9px 13px; border:1px solid #e1e4eb; border-radius:9px; font-size:11px; outline:none;" oninput="filterLogs()">
        <select id="logStatusFilter" style="padding:9px 13px; border:1px solid #e1e4eb; border-radius:9px; font-size:11px; outline:none;" onchange="filterLogs()">
          <option value="ALL">All Status</option>
          <option value="SUCCESS">Success Only</option>
          <option value="REVIEW">Review Only</option>
          <option value="BLOCKED">Blocked Only</option>
          <option value="REJECTED">Rejected Only</option>
        </select>
      </div>

      <div class="activity-summary">
        <div><b id="sumTotal">0</b><span>Total events</span></div>
        <div><b id="sumSuccess">0</b><span>Success</span></div>
        <div><b id="sumReview">0</b><span>Review</span></div>
        <div><b id="sumBlocked">0</b><span>Blocked</span></div>
      </div>
      <div class="panel recent"><div class="table-wrap"><table><thead><tr><th>Timestamp</th><th>IP / Device</th><th>Event</th><th>Reason</th><th>Risk</th><th>Status</th></tr></thead><tbody id="activityTable"></tbody></table></div></div>
    </section>

    <!-- RESEARCH MODE PAGE -->
    <section id="research" class="page">
      <div class="research-hero"><span class="eyebrow">RESEARCH MODE</span><h1>Modifikasi MFA untuk<br><em>Web E-Commerce</em></h1><p>Prototype ini memetakan konsep penelitian ke dalam sistem yang dapat diuji: autentikasi, risk scoring, OTP, rate limit, audit log, dan usability.</p></div>
      <div class="research-grid">
        <div class="research-card"><span>01</span><h3>Analysis</h3><p>Identifikasi risiko login dan kebutuhan pengguna.</p></div>
        <div class="research-card"><span>02</span><h3>Design</h3><p>Rancang alur Password → Risk Check → OTP → Extra Verification.</p></div>
        <div class="research-card"><span>03</span><h3>Development</h3><p>Implementasi prototipe PHP, session, OTP dan logging.</p></div>
        <div class="research-card"><span>04</span><h3>Evaluation</h3><p>Uji login normal, password salah, OTP salah/expired, resend, lockout dan suspicious access.</p></div>
      </div>
      <div class="cia-grid">
        <div class="cia confidentiality"><b>C</b><div><h3>Confidentiality</h3><p>OTP dan autentikasi berlapis membantu membatasi akses.</p></div></div>
        <div class="cia integrity"><b>I</b><div><h3>Integrity</h3><p>Audit trail mencatat perubahan status dan alasan penolakan.</p></div></div>
        <div class="cia availability"><b>A</b><div><h3>Availability</h3><p>Rate limit dan lockout membantu mengurangi penyalahgunaan autentikasi.</p></div></div>
      </div>
    </section>
  </main>
</div>

<div class="cart-drawer" id="cartDrawer">
  <div class="drawer-head"><h2>Your cart</h2><button onclick="closeCart()">×</button></div>
  <div id="cartItems"></div>
  <div class="cart-total"><span>Total</span><b id="cartTotal">Rp0</b></div>
  <button class="primary full" onclick="checkout()">Secure Checkout →</button>
</div>
<div class="overlay" id="overlay" onclick="closeCart()"></div>

<!-- MODAL AUTHENTICATION & BIOMETRIC SIMULATOR -->
<div class="modal" id="authModal">
  <div class="auth-card">
    <button class="modal-close" onclick="closeAuth()">×</button>
    <div class="auth-brand"><div class="logo-mark">S</div><b>SecureShop</b></div>
    <div id="authLogin">
      <span class="eyebrow">SECURE SIGN IN</span><h2>Welcome back.</h2><p>Masuk untuk melanjutkan belanja dengan perlindungan Adaptive MFA.</p>
      <label>Email / Username</label><input id="loginUser" value="demo@secureshop.local">
      <label>Password</label><input id="loginPass" type="password" value="Secure123!">
      <button class="primary full" onclick="startLogin()">Continue securely →</button>
      
      <!-- Fitur Baru: Quick Passkey / Biometric Login Mock -->
      <button class="ghost full" style="margin-top:8px; border-color:#7359f6; color:#7359f6; display:flex; align-items:center; justify-content:center; gap:6px;" onclick="simulatePasskey()">
        <span>🔑</span> Login with Passkey / Biometric
      </button>
      
      <small class="demo-note">Demo: demo@secureshop.local / Secure123!</small>
    </div>
    <div id="authMfa" class="hidden">
      <span class="eyebrow">STEP <span id="stepNo">2</span> OF 3</span><h2>Verify your identity.</h2><p id="riskMessage">We detected a normal-risk login.</p>
      <div class="mfa-progress"><i></i><i></i><i></i></div>
      <div id="extraVerify" class="hidden">
        <label>Security question</label>
        <p class="question">Kota kelahiran demo user?</p>
        <input id="securityAnswer" placeholder="Jawaban (makassar)">
      </div>
      <div id="otpBox">
        <label>One-time password</label>
        <div class="otp-inputs">
          <input maxlength="1"><input maxlength="1"><input maxlength="1"><input maxlength="1"><input maxlength="1"><input maxlength="1">
        </div>
        <div class="otp-meta"><span>Expires in <b id="timer">60</b>s</span><button onclick="resendOtp()">Resend OTP</button></div>
        <div class="demo-otp" id="demoOtp"></div>
      </div>
      <button class="primary full" onclick="verifyAuth()">Verify & Continue →</button>
    </div>
  </div>
</div>

<script src="app.js"></script>
</body>
</html>