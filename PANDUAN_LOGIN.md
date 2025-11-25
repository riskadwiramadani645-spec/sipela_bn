# 🔐 PANDUAN LOGIN SIPELA

## ADMIN LOGIN
**URL**: `http://localhost:8000/admin/login`
- Username: `admin`
- Password: `admin123`
- Redirect: `/admin/dashboard` ✅

## KESISWAAN LOGIN  
**URL**: `http://localhost:8000/login`
- **Role**: Pilih `kesiswaan` dari dropdown
- Username: `kesiswaan`
- Password: `kesiswaan123`
- Redirect: `/kesiswaan/dashboard` ✅

---

## ✅ STATUS TESTING

### Admin Login:
- [x] URL: `/admin/login` 
- [x] Redirect ke: `/admin/dashboard`
- [x] Session: `session('user')` tersimpan
- [x] Middleware: `check.auth` berfungsi

### Kesiswaan Login:
- [x] URL: `/login` dengan role `kesiswaan`
- [x] Redirect ke: `/kesiswaan/dashboard`
- [x] Session: `session('user')` tersimpan
- [x] Controller: `DashboardController@index`
- [x] View: `kesiswaan.dashboard`

---

## 🚨 TROUBLESHOOTING

**Jika login kesiswaan masuk ke admin:**
1. Pastikan pilih role `kesiswaan` di dropdown
2. Clear browser cache/session
3. Cek database: `level = 'kesiswaan'`

**Jika error 404:**
1. Cek routes: `php artisan route:list | grep kesiswaan`
2. Cek controller exists
3. Cek view exists

---

**Login berhasil jika redirect sesuai role masing-masing!**