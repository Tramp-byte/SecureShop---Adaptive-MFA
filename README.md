# SecureShop MFA Pro

Prototype e-commerce + Adaptive MFA untuk mendukung penelitian "Modifikasi Algoritma Multi-Factor Authentication (MFA) pada Website E-Commerce".

## Fitur
- Dashboard security modern
- Marketplace 12 produk + filter
- Shopping cart + secure checkout
- Login + OTP 6 digit
- OTP expiry 60 detik
- Resend OTP
- Maksimal 5 percobaan OTP
- Risk scoring 0–100
- Extra verification untuk high risk
- Audit activity/log
- Security test matrix
- CIA mapping
- Research Mode
- Responsive desktop/tablet/mobile

## Demo
Username: demo@secureshop.local
Password: Secure123!

Untuk demo, OTP ditampilkan di layar. Pada sistem produksi OTP harus dikirim melalui kanal aman dan tidak ditampilkan di frontend.

## Menjalankan
1. Install XAMPP.
2. Copy folder ke `C:\xampp\htdocs\SecureShop_MFA_Pro\`.
3. Start Apache.
4. Buka `http://localhost/SecureShop_MFA_Pro/`.

Tidak memerlukan database untuk demo. Session PHP digunakan untuk autentikasi.

## Catatan penelitian
Prototype memetakan alur: Password → Risk Check → OTP → Extra Verification (high risk), serta skenario pengujian login normal, password salah, OTP salah/expired, resend, rate limit/lockout, suspicious access dan audit logging.

Untuk produksi: gunakan HTTPS, database, password hashing, CSRF protection, secure session cookie, server-side audit log, OTP delivery service, device fingerprint yang privacy-aware, dan monitoring terpusat.
