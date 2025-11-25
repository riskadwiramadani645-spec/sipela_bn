# KREDENSIAL LOGIN SIPELA

## Akses Login

### Portal Utama (6 Role)
**URL**: `http://localhost:8000/login`

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| Kesiswaan | `kesiswaan` | `kesiswaan123` | Dashboard Kesiswaan |
| Guru | `guru` | `guru123` | Dashboard Guru |
| Kepala Sekolah | `kepala_sekolah` | `kepsek123` | Dashboard Kepala Sekolah |
| Konselor BK | `konselor_bk` | `bk123` | Dashboard Konselor BK |
| Siswa | `siswa` | `siswa123` | Dashboard Siswa |
| Orang Tua | `orang_tua` | `ortu123` | Dashboard Orang Tua |

### Admin Panel
**URL**: `http://localhost:8000/admin/login`

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| Admin | `admin` | `admin123` | Admin Dashboard (Full Access) |

## Cara Login

### Login 6 Role:
1. Buka `http://localhost:8000/login`
2. Pilih role dari dropdown
3. Masukkan username dan password sesuai tabel
4. Klik "Login"

### Login Admin:
1. Buka `http://localhost:8000/admin/login`
2. Masukkan username: `admin`
3. Masukkan password: `admin123`
4. Klik "Admin Login"

## Catatan Keamanan

⚠️ **PENTING**:
- Ganti semua password default setelah instalasi
- Jangan bagikan kredensial kepada pihak yang tidak berwenang
- Admin memiliki akses penuh ke seluruh sistem

## Status Akun

✅ Semua akun sudah aktif (`is_active = true`)  
✅ Role dengan hak verifikasi: Admin, Kesiswaan, Kepala Sekolah, Konselor BK  
✅ Role tanpa hak verifikasi: Guru, Siswa, Orang Tua  

---
**Dokumen ini untuk keperluan testing dan development**