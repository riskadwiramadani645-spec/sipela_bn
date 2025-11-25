# PANDUAN ADMINISTRATOR SIPELA

## Akses Admin

### URL Login Admin
```
http://localhost:8000/admin/login
```

### Struktur Login
- **Login 6 Role**: `/login` (Kesiswaan, Guru, Kepala Sekolah, Konselor BK, Siswa, Orang Tua)
- **Login Admin**: `/admin/login` (Khusus Administrator Sistem - TIDAK ADA DI PORTAL)

### Keamanan Admin
- Admin TIDAK ditampilkan di dropdown portal login
- Admin hanya bisa akses melalui URL langsung: `/admin/login`
- Implementasi "security by obscurity" untuk melindungi akses admin

### Kredensial Default
- **Username**: `admin`
- **Password**: `admin123`

### Kredensial Testing Lainnya
Untuk testing sistem, tersedia juga kredensial untuk 6 role:
- **Kesiswaan**: `kesiswaan` / `kesiswaan123`
- **Guru**: `guru` / `guru123`
- **Kepala Sekolah**: `kepala_sekolah` / `kepsek123`
- **Konselor BK**: `konselor_bk` / `bk123`
- **Siswa**: `siswa` / `siswa123`
- **Orang Tua**: `orang_tua` / `ortu123`

## Catatan Keamanan

⚠️ **PENTING**: 
- URL admin login tidak dipublikasikan di portal utama
- Akses hanya untuk administrator sistem yang berwenang
- Ganti password default setelah instalasi pertama
- Jangan bagikan kredensial kepada pihak yang tidak berwenang

## Fitur Admin Panel

### 1. Dashboard Admin
- Overview sistem secara keseluruhan
- Statistik pengguna dan aktivitas
- Monitoring sistem real-time

### 2. Master Data Management
- **Users**: Kelola semua pengguna sistem
- **Guru**: Data guru dan staff
- **Siswa**: Data siswa dan kelas
- **Kelas**: Manajemen kelas dan jurusan
- **Orang Tua**: Data orang tua siswa
- **Jenis Pelanggaran**: Kategori pelanggaran
- **Jenis Prestasi**: Kategori prestasi
- **Jenis Sanksi**: Kategori sanksi
- **Tahun Ajaran**: Periode akademik

### 3. Input Data
- Input pelanggaran siswa
- Input prestasi siswa
- Input data bimbingan konseling

### 4. Verifikasi & Monitoring
- Verifikasi data pelanggaran
- Verifikasi data prestasi
- Monitoring aktivitas sistem

### 5. View Data
- Lihat data anak (siswa)
- Lihat data pelanggaran
- Lihat data prestasi

### 6. Laporan & Sistem
- Generate laporan komprehensif
- Export data sistem
- Backup dan restore
- Pengaturan sistem

## Cara Akses

### Metode 1: URL Langsung
1. Buka browser
2. Ketik: `http://localhost:8000/admin/login`
3. Masukkan username dan password admin
4. Klik "Admin Login"

### Metode 2: Dari Portal (Jika Diperlukan)
1. Akses portal utama: `http://localhost:8000`
2. Tidak ada link admin di interface (by design)
3. Gunakan URL langsung seperti Metode 1

## Troubleshooting

### Login Gagal
- Pastikan username dan password benar
- Cek apakah akun admin aktif di database
- Periksa koneksi database

### Akses Ditolak
- Pastikan role user adalah 'admin'
- Cek field `is_active` di database
- Verifikasi session dan cookies

## Maintenance

### Ganti Password Admin
1. Login ke admin panel
2. Masuk ke Profile/Settings
3. Update password
4. Logout dan login ulang

### Backup Data
1. Masuk ke menu "Laporan & Sistem"
2. Pilih "Backup Database"
3. Download file backup
4. Simpan di tempat aman

---

**Dokumen ini hanya untuk Administrator Sistem**  
**Jangan disebarkan kepada pengguna umum**