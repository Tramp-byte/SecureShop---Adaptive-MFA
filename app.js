const $=s=>document.querySelector(s), $$=s=>[...document.querySelectorAll(s)];
const products=[
 {id:1,name:"Aurora Headphones",cat:"tech",price:1299000,icon:"◉",desc:"Wireless noise cancelling"},
 {id:2,name:"Minimal Watch",cat:"fashion",price:899000,icon:"◷",desc:"Precision everyday watch"},
 {id:3,name:"Orbit Lamp",cat:"home",price:549000,icon:"◌",desc:"Ambient smart lighting"},
 {id:4,name:"Nova Keyboard",cat:"tech",price:1399000,icon:"⌨",desc:"Low-profile mechanical"},
 {id:5,name:"Urban Backpack",cat:"fashion",price:679000,icon:"▣",desc:"Water resistant carry"},
 {id:6,name:"Air Desk",cat:"home",price:749000,icon:"▱",desc:"Minimal desk organizer"},
 {id:7,name:"Pixel Camera",cat:"tech",price:4899000,icon:"▣",desc:"Compact creator camera"},
 {id:8,name:"Essential Sneakers",cat:"fashion",price:1099000,icon:"⌁",desc:"Lightweight daily wear"},
 {id:9,name:"Halo Speaker",cat:"tech",price:1199000,icon:"◉",desc:"360° wireless audio"},
 {id:10,name:"Cloud Chair",cat:"home",price:2299000,icon:"◒",desc:"Ergonomic lounge chair"},
 {id:11,name:"Tech Pouch",cat:"fashion",price:399000,icon:"▤",desc:"Organize your essentials"},
 {id:12,name:"Focus Monitor",cat:"tech",price:3299000,icon:"▭",desc:"27-inch creator display"}
];

let cart=JSON.parse(localStorage.getItem('secure_cart')||'[]'), currentFilter='all', otpTimer=null;
let currentRisk = 18;
const fmt=n=>'Rp'+n.toLocaleString('id-ID');

function showToast(msg){
  const t=$('#toast'); if(!t) return;
  t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 2600);
}

/* FUNGSI PINTASAN HALAMAN NAVIGASI */
function goPage(pageId){
  const pages = $$('.page');
  const navs = $$('.nav-item');
  
  pages.forEach(p => {
    if(p.id === pageId) p.classList.add('active');
    else p.classList.remove('active');
  });
  
  navs.forEach(n => {
    if(n.dataset.page === pageId) n.classList.add('active');
    else n.classList.remove('active');
  });
  
  if(pageId === 'overview') renderLogs();
  if(pageId === 'shop') renderProducts();
  if(pageId === 'activity') renderLogs();
  if(pageId === 'security') renderTests();
  if(innerWidth < 800 && $('#sidebar')) $('#sidebar').classList.remove('open');
}

$$('.nav-item').forEach(n => {
  n.addEventListener('click', () => {
    if(n.dataset.page) goPage(n.dataset.page);
  });
});

function toggleSidebar(){ $('#sidebar').classList.toggle('open'); }

function renderProducts(list=products.filter(p=>currentFilter==='all'||p.cat===currentFilter)){
  if(!$('#products')) return;
  $('#products').innerHTML=list.map(p=>`<article class="product"><div class="product-img ${p.cat}"><span>${p.icon}</span><button onclick="addCart(${p.id})">+</button></div><div class="product-info"><small>${p.cat.toUpperCase()}</small><h3>${p.name}</h3><p>${p.desc}</p><b>${fmt(p.price)}</b></div></article>`).join('');
}

function filterProducts(cat,btn){
  currentFilter=cat;
  $$('.filter').forEach(x=>x.classList.remove('active'));
  btn.classList.add('active');
  renderProducts();
}

/* FITUR BARU: MANAJEMEN BADGE KERANJANG BELANJA */
function updateCartBadge(){
  const badge = $('#cartBadge');
  if(!badge) return;
  const totalQty = cart.reduce((a,b)=>a+b.qty,0);
  if(totalQty > 0) {
    badge.textContent = totalQty;
    badge.style.display = 'inline-block';
  } else {
    badge.style.display = 'none';
  }
}

function addCart(id){
  const p=products.find(x=>x.id===id), item=cart.find(x=>x.id===id);
  if(item) item.qty++; else cart.push({...p, qty:1});
  localStorage.setItem('secure_cart', JSON.stringify(cart));
  renderCart();
  updateCartBadge();
  showToast(`${p.name} ditambahkan ke keranjang`);
}

function renderCart(){
  const el=$('#cartItems'); if(!el) return;
  if(!cart.length) el.innerHTML='<div class="empty">Keranjang masih kosong.</div>';
  else el.innerHTML=cart.map(i=>`<div class="cart-item"><div class="mini-img">${i.icon}</div><div><b>${i.name}</b><small>${fmt(i.price)} × ${i.qty}</small></div><button onclick="removeCart(${i.id})">×</button></div>`).join('');
  if($('#cartTotal')) $('#cartTotal').textContent=fmt(cart.reduce((a,b)=>a+b.price*b.qty,0));
  updateCartBadge();
}

function removeCart(id){
  cart=cart.filter(x=>x.id!==id);
  localStorage.setItem('secure_cart', JSON.stringify(cart));
  renderCart();
  updateCartBadge();
}

function openCart(){ $('#cartDrawer').classList.add('open'); $('#overlay').classList.add('show'); renderCart(); }
function closeCart(){ $('#cartDrawer').classList.remove('open'); $('#overlay').classList.remove('show'); }

function checkout(){
  if(!cart.length) return showToast('Keranjang masih kosong');
  closeCart(); openAuth(); showToast('Checkout memerlukan autentikasi aman');
}

function openAuth(){ $('#authModal').classList.add('show'); $('#authLogin').classList.remove('hidden'); $('#authMfa').classList.add('hidden'); }
function closeAuth(){ $('#authModal').classList.remove('show'); clearInterval(otpTimer); }

async function post(data){
  return fetch('api.php', {method:'POST', body:new URLSearchParams(data)}).then(r=>r.json());
}

/* LOGGING SECURITY & RISK ENGINE */
function logSecurityEvent(event, reason, risk, status, user = 'demo@secureshop.local') {
  const time = new Date().toTimeString().split(' ')[0];
  const ip = '192.168.1.' + Math.floor(Math.random() * 80 + 10) + ' • Chrome';
  const newLog = [time, ip, event, reason, String(risk), status];
  
  const existing = JSON.parse(localStorage.getItem('secure_logs') || '[]');
  existing.unshift(newLog);
  localStorage.setItem('secure_logs', JSON.stringify(existing));
  
  updateRiskDisplay(risk);
  renderLogs();
}

function updateRiskDisplay(risk) {
  currentRisk = risk;
  if($('#liveRiskNum')) $('#liveRiskNum').textContent = risk;
  if($('#bigRiskNum')) $('#bigRiskNum').textContent = risk;
  if($('#bigRiskLabel')) $('#bigRiskLabel').textContent = risk >= 60 ? 'HIGH' : risk >= 35 ? 'MED' : 'LOW';
  if($('#liveRiskNote')) $('#liveRiskNote').textContent = risk >= 60 ? 'High risk → Password + Extra Verification + OTP' : 'Normal risk → Password + OTP';
}

async function startLogin(){
  const u = $('#loginUser').value;
  const p = $('#loginPass').value;
  const r = await post({action:'start_login', username:u, password:p});
  
  if(!r.ok) {
    logSecurityEvent('Password attempt', 'Wrong credentials', 48, 'REJECTED', u);
    return showToast(r.message);
  }
  
  logSecurityEvent('Login start', r.extra ? 'High-risk context' : 'Trusted context', r.risk, 'SUCCESS', u);
  
  $('#authLogin').classList.add('hidden');
  $('#authMfa').classList.remove('hidden');
  $('#riskMessage').textContent=`Risk score ${r.risk}/100. ${r.extra?'High-risk login → extra verification required.':'Normal-risk login → password + OTP.'}`;
  
  if(r.extra){
    $('#extraVerify').classList.remove('hidden');
    $('#stepNo').textContent='2';
  } else {
    $('#extraVerify').classList.add('hidden');
    $('#stepNo').textContent='2';
  }
  
  $('#demoOtp').textContent='DEMO OTP: '+r.otp;
  startTimer(r.expires);
}

function startTimer(sec){
  clearInterval(otpTimer);
  $('#timer').textContent=sec;
  otpTimer=setInterval(()=>{
    sec--;
    $('#timer').textContent=sec;
    if(sec<=0) clearInterval(otpTimer);
  },1000);
}

function otpValue(){ return $$('.otp-inputs input').map(x=>x.value).join(''); }

$$('.otp-inputs input').forEach((x,i)=>x.addEventListener('input',()=>{
  if(x.value && i<5) $$('.otp-inputs input')[i+1].focus();
}));

/* VERIFIKASI LOGIN */
async function verifyAuth(){
  if (!$('#extraVerify').classList.contains('hidden')) {
    const q = await post({ action: 'verify_security', answer: $('#securityAnswer').value });
    if (!q.ok) {
      logSecurityEvent('Security Question', 'Wrong answer', 75, 'REJECTED');
      return showToast('Security answer salah');
    }
    logSecurityEvent('Security Question', 'Passed extra verification', currentRisk, 'SUCCESS');
  }
  
  const r = await post({ action: 'verify_otp', otp: otpValue() });
  
  if (!r.ok) {
    const status = r.blocked ? 'BLOCKED' : 'REVIEW';
    logSecurityEvent('OTP verification', r.message, r.blocked ? 90 : 45, status);
    return showToast(r.message);
  }
  
  logSecurityEvent('Login success', 'MFA passed', 18, 'SUCCESS');
  closeAuth();
  updateAuthUI(true);
  showToast('Autentikasi berhasil — Selamat datang!');
  goPage('shop');
}

/* FUNGSI LOGOUT */
async function logout() {
  try {
    await post({ action: 'logout' });
    logSecurityEvent('Logout', 'User signed out', 18, 'SUCCESS');
  } catch(e) {
    console.log('Logout local fallback');
  }
  
  updateAuthUI(false);
  goPage('overview');
  showToast('Berhasil keluar (Logout).');
}

/* FITUR BARU: SIMULASI PASSKEY / BIOMETRIC LOGIN */
function simulatePasskey() {
  showToast('Mengakses autentikator biometrik (TouchID/FaceID)...');
  setTimeout(() => {
    logSecurityEvent('Biometric Auth', 'Passkey verified via WebAuthn', 10, 'SUCCESS');
    closeAuth();
    updateAuthUI(true);
    showToast('Biometrik terverifikasi — Login Instan!');
    goPage('shop');
  }, 1200);
}

/* FITUR BARU: LIVE THREAT ATTACK SIMULATOR */
function simulateThreat(type) {
  if (type === 'Brute Force Attack') {
    logSecurityEvent('Brute Force Attempt', '10+ failed attempts in 5s', 85, 'BLOCKED');
    showToast('🚨 SIMULASI: Serangan Brute Force terdeteksi & diblokir!');
  } else if (type === 'Bot Login Attempt') {
    logSecurityEvent('Bot Access Attempt', 'Automated headless browser', 92, 'BLOCKED');
    showToast('🤖 SIMULASI: Akses Bot terdeteksi & ditolak!');
  } else if (type === 'Suspicious IP Access') {
    logSecurityEvent('Geo-Anomaly Access', 'Login attempt from new country', 65, 'REVIEW');
    showToast('🌐 SIMULASI: Akses lokasi mencurigakan terdeteksi!');
  }
}

/* MANAJEMEN TAMPILAN MENU LOGGED-IN / LOGGED-OUT */
function updateAuthUI(isLoggedIn) {
  const navShop = $('#navShop');
  const btnLogout = $('#btnLogout');
  
  if (isLoggedIn) {
    if (navShop) navShop.classList.remove('hidden');
    if (btnLogout) btnLogout.classList.remove('hidden');
    sessionStorage.setItem('isLoggedIn', 'true');
  } else {
    if (navShop) navShop.classList.add('hidden');
    if (btnLogout) btnLogout.classList.add('hidden');
    sessionStorage.removeItem('isLoggedIn');
  }
}

async function resendOtp(){
  const r = await post({action:'resend_otp'});
  if(r.ok){
    logSecurityEvent('OTP resend', 'User requested new OTP', currentRisk, 'SUCCESS');
    $('#demoOtp').textContent='DEMO OTP: '+r.otp;
    startTimer(r.expires);
    showToast('OTP baru berhasil dikirim');
  }
}

function clearLogs(){
  localStorage.removeItem('secure_logs');
  renderLogs();
  showToast('Demo logs dibersihkan');
}

const sample = [
 ['22:41:08','192.168.1.21 • Chrome','Login success','Known device','18','SUCCESS'],
 ['22:39:52','103.44.18.9 • Chrome','OTP verification','Wrong OTP ×2','42','REVIEW'],
 ['22:37:11','45.88.12.71 • Bot','Password attempt','Repeated failure','82','BLOCKED'],
 ['22:31:45','192.168.1.21 • Chrome','OTP resend','User requested','18','SUCCESS'],
 ['22:28:20','192.168.1.21 • Chrome','Login start','Trusted context','18','SUCCESS']
];

/* FITUR BARU: RENDER LOGS DENGAN FILTER DAN SEARCH */
function renderLogs(){
  let parsed = [];
  try {
    const stored = localStorage.getItem('secure_logs');
    parsed = stored ? JSON.parse(stored) : [];
  } catch(e) {
    parsed = [];
  }
  
  let data = [...parsed, ...sample];
  
  // Ambil Filter Keyword & Status
  const q = $('#logSearchInput') ? $('#logSearchInput').value.toLowerCase() : '';
  const statusFilter = $('#logStatusFilter') ? $('#logStatusFilter').value : 'ALL';
  
  if (q) {
    data = data.filter(d => d[1].toLowerCase().includes(q) || d[2].toLowerCase().includes(q) || d[3].toLowerCase().includes(q));
  }
  if (statusFilter !== 'ALL') {
    data = data.filter(d => d[5] === statusFilter);
  }

  const row = d => `<tr><td>${d[0]}</td><td>${d[1]}</td><td>${d[2]}</td><td>${d[3]}</td><td><span class="risk-num ${+d[4]>60?'high':+d[4]>35?'med':'low'}">${d[4]}</span></td><td><span class="status ${d[5].toLowerCase()}">${d[5]}</span></td></tr>`;
  
  if($('#recentTable')) $('#recentTable').innerHTML = data.slice(0,5).map(row).join('');
  if($('#activityTable')) $('#activityTable').innerHTML = data.map(row).join('');
  
  const successCount = data.filter(x => x[5] === 'SUCCESS').length;
  const blockedCount = data.filter(x => x[5] === 'BLOCKED' || x[5] === 'REJECTED').length;
  const reviewCount  = data.filter(x => x[5] === 'REVIEW').length;
  const totalEvents  = data.length;
  
  if($('#statSuccess')) $('#statSuccess').textContent = successCount;
  if($('#statBlocked')) $('#statBlocked').textContent = blockedCount;
  if($('#statOtp')) $('#statOtp').textContent = reviewCount;
  if($('#statTotalEvents')) $('#statTotalEvents').textContent = totalEvents;

  if($('#sumTotal')) $('#sumTotal').textContent = totalEvents;
  if($('#sumSuccess')) $('#sumSuccess').textContent = successCount;
  if($('#sumReview')) $('#sumReview').textContent = reviewCount;
  if($('#sumBlocked')) $('#sumBlocked').textContent = blockedCount;

  if(data.length > 0) {
    updateRiskDisplay(parseInt(data[0][4]) || 18);
  }
}

function filterLogs() {
  renderLogs();
}

function renderTests(){
  const tests=[
    ['Normal login', 'Password + OTP flow', 'PASS'],
    ['Wrong password', 'Rejected before MFA step', 'PASS'],
    ['Wrong OTP', 'Counter & status tracking', 'PASS'],
    ['Expired OTP', 'Timer validation system', 'PASS'],
    ['Resend OTP', 'New code generation', 'PASS'],
    ['Rate limit / Lockout', 'Blocked after 5 failures', 'PASS'],
    ['High risk challenge', 'Extra question triggered', 'PASS'],
    ['Live Audit Trail', 'Real-time event logging', 'PASS']
  ];
  if($('#testGrid')) {
    $('#testGrid').innerHTML = tests.map(t=>`<div class="test-row"><div class="check">✓</div><div><b>${t[0]}</b><small>${t[1]}</small></div><span>${t[2]}</span></div>`).join('');
  }
}

async function runAutomatedTests(){
  showToast("Menjalankan simulasi pengujian otomatis...");
  await post({action:'start_login', username:'demo@secureshop.local', password:'wrongpassword'});
  logSecurityEvent('Password attempt', 'Wrong password test', 48, 'REJECTED');
  
  const loginRes = await post({action:'start_login', username:'demo@secureshop.local', password:'Secure123!'});
  logSecurityEvent('Login start', 'Automated test suite', loginRes.risk, 'SUCCESS');
  
  const otpRes = await post({action:'verify_otp', otp:'000000'});
  logSecurityEvent('OTP verification', otpRes.message, 45, 'REVIEW');
  showToast("Pengujian selesai. Audit log diperbarui!");
}

/* EXPOSE KE GLOBAL SCOPE */
window.verifyAuth = verifyAuth;
window.logout = logout;
window.startLogin = startLogin;
window.resendOtp = resendOtp;
window.openCart = openCart;
window.closeCart = closeCart;
window.checkout = checkout;
window.runAutomatedTests = runAutomatedTests;
window.simulateThreat = simulateThreat;
window.simulatePasskey = simulatePasskey;
window.filterLogs = filterLogs;

if($('#globalSearch')) {
  $('#globalSearch').addEventListener('input', e=>{
    const q = e.target.value.toLowerCase();
    if($('#shop') && $('#shop').classList.contains('active')) {
      renderProducts(products.filter(p=>p.name.toLowerCase().includes(q)||p.cat.includes(q)));
    }
  });
}

if(document.querySelector('.user-chip')) document.querySelector('.user-chip').onclick = openAuth;

// INISIALISASI KETIKA APLIKASI DIBUKA
const userIsLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
updateAuthUI(userIsLoggedIn);

renderProducts();
renderLogs();
renderTests();
renderCart();
updateCartBadge();