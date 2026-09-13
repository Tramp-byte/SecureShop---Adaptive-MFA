<?php
session_start();
header('Content-Type: application/json');
require 'config.php';

$action = $_POST['action'] ?? '';

if ($action === 'start_login') {
  $u = $_POST['username'] ?? ''; 
  $p = $_POST['password'] ?? '';
  
  if ($u !== DEMO_USER || $p !== DEMO_PASSWORD) { 
    $_SESSION['login_fail'] = ($_SESSION['login_fail'] ?? 0) + 1;
    echo json_encode(['ok' => false, 'message' => 'Username atau password salah.']); 
    exit; 
  }
  
  $risk = 18 + (($_SERVER['HTTP_USER_AGENT'] ?? '') ? 0 : 5);
  if (!empty($_SESSION['login_fail'])) {
    $risk += min(30, $_SESSION['login_fail'] * 10);
  }
  
  $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  $_SESSION['otp'] = $otp; 
  $_SESSION['otp_exp'] = time() + 60; 
  $_SESSION['otp_attempts'] = 0; 
  $_SESSION['risk'] = $risk;
  
  echo json_encode([
    'ok' => true,
    'risk' => $risk,
    'otp' => $otp,
    'extra' => $risk >= 60,
    'expires' => 60
  ]); 
  exit;
}

if ($action === 'verify_otp') {
  $_SESSION['otp_attempts'] = ($_SESSION['otp_attempts'] ?? 0) + 1;
  
  if ($_SESSION['otp_attempts'] > 5) { 
    echo json_encode(['ok' => false, 'blocked' => true, 'message' => 'Terlalu banyak percobaan. Akses dikunci sementara.']); 
    exit; 
  }
  
  if (time() > ($_SESSION['otp_exp'] ?? 0)) { 
    echo json_encode(['ok' => false, 'expired' => true, 'message' => 'OTP telah kedaluwarsa.']); 
    exit; 
  }
  
  $otp = $_POST['otp'] ?? '';
  if ($otp !== ($_SESSION['otp'] ?? '')) { 
    echo json_encode(['ok' => false, 'message' => 'OTP tidak cocok.']); 
    exit; 
  }
  
  $_SESSION['auth'] = true; 
  $_SESSION['login_fail'] = 0;
  echo json_encode(['ok' => true]); 
  exit;
}

if ($action === 'verify_security') {
  echo json_encode(['ok' => strtolower(trim($_POST['answer'] ?? '')) === SECURITY_ANSWER]); 
  exit;
}

if ($action === 'resend_otp') {
  $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT); 
  $_SESSION['otp'] = $otp; 
  $_SESSION['otp_exp'] = time() + 60; 
  $_SESSION['otp_attempts'] = 0;
  echo json_encode(['ok' => true, 'otp' => $otp, 'expires' => 60]); 
  exit;
}

if ($action === 'logout') { 
  session_destroy(); 
  echo json_encode(['ok' => true]); 
  exit; 
}

echo json_encode(['ok' => false, 'message' => 'Invalid action']);
?>