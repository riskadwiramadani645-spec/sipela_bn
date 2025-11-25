-- 1. Tabel TAHUN_AJARAN (Tidak ada perubahan pada status)
CREATE TABLE TAHUN_AJARAN (
tahun_ajaran_id INT PRIMARY KEY,
kode_tahun VARCHAR(10) NOT NULL,
tahun_ajaran VARCHAR(20) NOT NULL,
semester ENUM('Ganjil', 'Genap') NOT NULL,
status_aktif BOOLEAN NOT NULL DEFAULT FALSE,
tanggal_mulai DATE,
tanggal_selesai DATE,
created_at DATETIME
);

-- 2. Tabel GURU (Tidak ada perubahan pada status)
CREATE TABLE GURU (
guru_id INT PRIMARY KEY,
nip VARCHAR(20) UNIQUE NOT NULL,
nama_guru VARCHAR(100) NOT NULL,
jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
bidang_studi VARCHAR(50),
no_telp VARCHAR(15),
email VARCHAR(100),
status ENUM('Aktif', 'Cuti', 'Pensiun') NOT NULL DEFAULT 'Aktif',
created_at DATETIME
);

-- 3. Tabel KELAS (Tidak ada perubahan pada status)
CREATE TABLE KELAS (
kelas_id INT PRIMARY KEY,
nama_kelas VARCHAR(50) NOT NULL,
jurusan VARCHAR(50),
kapasitas INT,
wali_kelas_id INT,
created_at DATETIME,
FOREIGN KEY (wali_kelas_id) REFERENCES GURU(guru_id)
);

-- 4. Tabel SISWA (DITAMBAH status_kesiswaan)
CREATE TABLE SISWA (
siswa_id INT PRIMARY KEY,
nis VARCHAR(20) UNIQUE NOT NULL,
nisn VARCHAR(20) UNIQUE,
nama_siswa VARCHAR(100) NOT NULL,
jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
-- Status Kesiswaan Baru
status_kesiswaan ENUM('aktif', 'lulus', 'pindah', 'drop_out', 'cuti') NOT NULL DEFAULT 'aktif',
tanggal_lahir DATE,
tempat_lahir VARCHAR(50),
alamat VARCHAR(255),
no_telp VARCHAR(15),
kelas_id INT,
foto VARCHAR(255),
created_at DATETIME,
FOREIGN KEY (kelas_id) REFERENCES KELAS(kelas_id)
);

-- 5. Tabel ORANGTUA (Wali) (Tidak ada perubahan pada status)
CREATE TABLE ORANGTUA (
ortu_id INT PRIMARY KEY,
siswa_id INT NOT NULL,
hubungan ENUM('Ayah', 'Ibu', 'Wali') NOT NULL,
nama_orangtua VARCHAR(100) NOT NULL,
pekerjaan VARCHAR(50),
pendidikan VARCHAR(50),
no_telp VARCHAR(15),
alamat TEXT,
created_at DATETIME,
FOREIGN KEY (siswa_id) REFERENCES SISWA(siswa_id)
);

-- 6. Tabel USER (Untuk otentikasi/login) (Tidak ada perubahan pada status)
CREATE TABLE USER (
user_id INT PRIMARY KEY,
username VARCHAR(50) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
nama_lengkap VARCHAR(100),
level ENUM('Admin', 'Guru', 'Konselor', 'WaliMurid') NOT NULL,
can_verify BOOLEAN NOT NULL DEFAULT FALSE,
is_active BOOLEAN NOT NULL DEFAULT TRUE,
last_login DATETIME,
created_at DATETIME
);

-- 7. Tabel JENIS_PELANGGARAN (Kategori Pelanggaran DIBARUI)
CREATE TABLE JENIS_PELANGGARAN (
jenis_pelanggaran_id INT PRIMARY KEY,
nama_pelanggaran VARCHAR(100) NOT NULL,
-- Kategori Pelanggaran DIBARUI
kategori ENUM('ringan', 'sedang', 'berat', 'sangat_berat') NOT NULL,
poin INT NOT NULL,
deskripsi TEXT,
sanksi_rekomendasi VARCHAR(255),
created_at DATETIME
);

-- 8. Tabel PELANGGARAN (Status Verifikasi DIBARUI)
CREATE TABLE PELANGGARAN (
pelanggaran_id INT PRIMARY KEY,
siswa_id INT NOT NULL,
guru_pencatat INT NOT NULL,
jenis_pelanggaran_id INT NOT NULL,
tahun_ajaran_id INT NOT NULL,
poin INT NOT NULL,
keterangan TEXT,
bukti_foto VARCHAR(255),
-- Status Verifikasi DIBARUI
status_verifikasi ENUM('menunggu', 'diverifikasi', 'ditolak', 'revisi') NOT NULL DEFAULT 'menunggu',
guru_verifikator INT,
catatan_verifikasi TEXT,
tanggal DATE NOT NULL,
created_at DATETIME,
FOREIGN KEY (siswa_id) REFERENCES SISWA(siswa_id),
FOREIGN KEY (guru_pencatat) REFERENCES GURU(guru_id),
FOREIGN KEY (jenis_pelanggaran_id) REFERENCES JENIS_PELANGGARAN(jenis_pelanggaran_id),
FOREIGN KEY (tahun_ajaran_id) REFERENCES TAHUN_AJARAN(tahun_ajaran_id),
FOREIGN KEY (guru_verifikator) REFERENCES GURU(guru_id)
);

-- 9. Tabel JENIS_PRESTASI (Tidak ada perubahan pada status)
CREATE TABLE JENIS_PRESTASI (
jenis_prestasi_id INT PRIMARY KEY,
nama_prestasi VARCHAR(100) NOT NULL,
poin INT NOT NULL,
kategori ENUM('Akademik', 'Non-Akademik') NOT NULL,
deskripsi TEXT,
reward VARCHAR(255),
created_at DATETIME
);

-- 10. Tabel PRESTASI (Status Verifikasi DIBARUI)
CREATE TABLE PRESTASI (
prestasi_id INT PRIMARY KEY,
siswa_id INT NOT NULL,
guru_pencatat INT NOT NULL,
jenis_prestasi_id INT NOT NULL,
tahun_ajaran_id INT NOT NULL,
poin INT NOT NULL,
keterangan TEXT,
tingkat ENUM('Sekolah', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'),
penghargaan VARCHAR(100),
bukti_dokumen VARCHAR(255),
-- Status Verifikasi DIBARUI
status_verifikasi ENUM('menunggu', 'diverifikasi', 'ditolak', 'revisi') NOT NULL DEFAULT 'menunggu',
guru_verifikator INT,
tanggal DATE NOT NULL,
created_at DATETIME,
FOREIGN KEY (siswa_id) REFERENCES SISWA(siswa_id),
FOREIGN KEY (guru_pencatat) REFERENCES GURU(guru_id),
FOREIGN KEY (jenis_prestasi_id) REFERENCES JENIS_PRESTASI(jenis_prestasi_id),
FOREIGN KEY (tahun_ajaran_id) REFERENCES TAHUN_AJARAN(tahun_ajaran_id),
FOREIGN KEY (guru_verifikator) REFERENCES GURU(guru_id)
);

-- 11. Tabel BIMBINGAN_KONSELING (Status BK DIBARUI)
CREATE TABLE BIMBINGAN_KONSELING (
bk_id INT PRIMARY KEY,
siswa_id INT NOT NULL,
guru_konselor INT NOT NULL,
tahun_ajaran_id INT NOT NULL,
jenis_layanan ENUM('Individu', 'Kelompok', 'Klasikal') NOT NULL,
topik VARCHAR(255) NOT NULL,
keluhan_masalah TEXT,
tindakan_solusi TEXT,
-- Status BK DIBARUI
status ENUM('terdaftar', 'diproses', 'selesai', 'tindak_lanjut') NOT NULL DEFAULT 'terdaftar',
tanggal_konseling DATE NOT NULL,
tanggal_tindak_lanjut DATE,
hasil_evaluasi TEXT,
created_at DATETIME,
FOREIGN KEY (siswa_id) REFERENCES SISWA(siswa_id),
FOREIGN KEY (guru_konselor) REFERENCES GURU(guru_id),
FOREIGN KEY (tahun_ajaran_id) REFERENCES TAHUN_AJARAN(tahun_ajaran_id)
);

-- 12. Tabel SANKSI (DITAMBAH status_sanksi)
CREATE TABLE SANKSI (
sanksi_id INT PRIMARY KEY,
jenis_sanksi VARCHAR(100) NOT NULL,
deskripsi_sanksi TEXT,
-- Status Sanksi Baru
status_sanksi ENUM('direncanakan', 'berjalan', 'selesai', 'ditunda', 'dibatalkan') NOT NULL DEFAULT 'direncanakan',
bobot INT,
tanggal_mulai DATE,
tanggal_selesai DATE,
status_intern ENUM('Ya', 'Tidak') NOT NULL,
catatan_pelaksanaan TEXT,
pic_penanggungjawab INT,
created_at DATETIME,
FOREIGN KEY (pic_penanggungjawab) REFERENCES GURU(guru_id)
);

-- 13. Tabel PELAKSANAAN_SANKSI (Status Pelaksanaan DIBARUI)
CREATE TABLE PELAKSANAAN_SANKSI (
pelaksanaan_sanksi_id INT PRIMARY KEY,
siswa_id INT NOT NULL,
sanksi_id INT NOT NULL,
tanggal_pelaksanaan DATE NOT NULL,
deskripsi_pelaksanaan TEXT,
bukti_pelaksanaan VARCHAR(255),
-- Status Pelaksanaan Sanksi DIBARUI
status ENUM('terjadwal', 'dikerjakan', 'tuntas', 'terlambat', 'perpanjangan') NOT NULL DEFAULT 'terjadwal',
catatan TEXT,
guru_pengawas INT,
created_at DATETIME,
FOREIGN KEY (siswa_id) REFERENCES SISWA(siswa_id),
FOREIGN KEY (sanksi_id) REFERENCES SANKSI(sanksi_id),
FOREIGN KEY (guru_pengawas) REFERENCES GURU(guru_id)
);

-- 14. Tabel MONITORING_ORANGTUA (Tidak ada perubahan pada status)
CREATE TABLE MONITORING_ORANGTUA (
monitoring_id INT PRIMARY KEY,
ortu_id INT NOT NULL,
pelanggaran_id INT,
prestasi_id INT,
status_kontak ENUM('Panggilan', 'Pertemuan', 'Surat') NOT NULL,
status_monitoring ENUM('Pending', 'Selesai') NOT NULL DEFAULT 'Pending',
tindak_lanjut TEXT,
tanggal_monitoring DATE NOT NULL,
dokumen_berita VARCHAR(255),
created_at DATETIME,
FOREIGN KEY (ortu_id) REFERENCES ORANGTUA(ortu_id),
FOREIGN KEY (pelanggaran_id) REFERENCES PELANGGARAN(pelanggaran_id),
FOREIGN KEY (prestasi_id) REFERENCES PRESTASI(prestasi_id)
);

database ini sudah di terapkan di migration dan di model update ke dtabase php my admin menggunakan database yang sekarang