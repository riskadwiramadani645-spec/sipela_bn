<?php

/*
|--------------------------------------------------------------------------
| SIPELA - Sistem Informasi Pelanggaran Siswa
| SMK Bakti Nusantara 666
|--------------------------------------------------------------------------
| Routes untuk aplikasi SIPELA dengan multi-role authentication
| Status: Production Ready untuk Admin & Kesiswaan
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortalController;

// ========================================
// PORTAL & AUTHENTICATION ROUTES
// ========================================

// Portal utama
Route::get('/', [PortalController::class, 'index'])->name('portal.index');

// Login page (universal)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Authentication handler
Route::post('/login', [AuthController::class, 'login'])->name('login.post');



// Logout handlers (both GET and POST to avoid 419 errors)
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');

// ========================================
// PROFILE ROUTES (SHARED BY ALL ROLES)
// ========================================

Route::get('/profile', function() {
    $user = session('user');
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    }
    return app(\App\Http\Controllers\ProfileController::class)->index();
})->name('profile.index');

Route::put('/profile', function() {
    $user = session('user');
    if (!$user) {
        return redirect()->route('login');
    }
    return app(\App\Http\Controllers\ProfileController::class)->updateProfile(request());
})->name('profile.update');

Route::put('/profile/password', function() {
    $user = session('user');
    if (!$user) {
        return redirect()->route('login');
    }
    return app(\App\Http\Controllers\ProfileController::class)->updatePassword(request());
})->name('profile.password');

Route::post('/profile/photo', function() {
    $user = session('user');
    if (!$user) {
        return redirect()->route('login');
    }
    return app(\App\Http\Controllers\ProfileController::class)->updatePhoto(request());
})->name('profile.photo');

// ========================================
// PROTECTED ROUTES (REQUIRE AUTHENTICATION)
// ========================================

Route::middleware(['check.auth'])->group(function () {
    // Dashboard router - redirects to appropriate dashboard based on user role
    Route::get('/dashboard', function() {
        $user = session('user');
        if (!$user) {
            return redirect()->route('login');
        }
        
        switch ($user->level) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'kesiswaan':
                return redirect()->route('kesiswaan.dashboard');
            case 'guru':
                return redirect()->route('guru.dashboard');
            case 'konselor_bk':
                return redirect()->route('konselor-bk.dashboard');
            case 'kepala_sekolah':
                return redirect()->route('kepala-sekolah.dashboard');
            case 'siswa':
                return redirect()->route('siswa.dashboard');
            case 'orang_tua':
                return redirect()->route('orang-tua.dashboard');
            default:
                return redirect()->route('login')->with('error', 'Role tidak dikenali');
        }
    })->name('dashboard');
});

// ========================================
// ADMIN ROUTES (SUPER USER)
// ========================================
// Login URL: http://localhost:8000/admin/login
// Credentials: admin / admin123
// Access Level: FULL ACCESS - All features
// Status: ✅ PRODUCTION READY
// ========================================

Route::prefix('admin')->middleware(['check.auth'])->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');
    
    // User Management (Admin Only)
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // Master Data - Users (alias untuk konsistensi)
    Route::get('/master-data/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('master-data.users');
    Route::post('/master-data/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('master-data.users.store');
    Route::get('/master-data/users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('master-data.users.edit');
    Route::put('/master-data/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('master-data.users.update');
    Route::delete('/master-data/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('master-data.users.destroy');
    
    // Master Data Management (Admin Only)
    Route::get('/master-data', [\App\Http\Controllers\Admin\MasterDataController::class, 'index'])->name('master-data');
    Route::get('/master-data/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('master-data.users');
    
    // Master Data - Guru
    Route::get('/master-data/guru', [\App\Http\Controllers\Admin\MasterDataController::class, 'dataGuru'])->name('master-data.guru');
    Route::post('/master-data/guru', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeGuru'])->name('master-data.guru.store');
    Route::get('/master-data/guru/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editGuru'])->name('master-data.guru.edit');
    Route::put('/master-data/guru/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateGuru'])->name('master-data.guru.update');
    Route::delete('/master-data/guru/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyGuru'])->name('master-data.guru.destroy');
    
    // Master Data - Siswa
    Route::get('/master-data/siswa', [\App\Http\Controllers\Admin\MasterDataController::class, 'dataSiswa'])->name('master-data.siswa');
    Route::post('/master-data/siswa', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeSiswa'])->name('master-data.siswa.store');
    Route::get('/master-data/siswa/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editSiswa'])->name('master-data.siswa.edit');
    Route::put('/master-data/siswa/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateSiswa'])->name('master-data.siswa.update');
    Route::delete('/master-data/siswa/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroySiswa'])->name('master-data.siswa.destroy');
    
    // Master Data - Kelas
    Route::get('/master-data/kelas', [\App\Http\Controllers\Admin\MasterDataController::class, 'dataKelas'])->name('master-data.kelas');
    Route::post('/master-data/kelas', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeKelas'])->name('master-data.kelas.store');
    Route::get('/master-data/kelas/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editKelas'])->name('master-data.kelas.edit');
    Route::put('/master-data/kelas/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateKelas'])->name('master-data.kelas.update');
    Route::delete('/master-data/kelas/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyKelas'])->name('master-data.kelas.destroy');
    
    // Master Data - Jenis Pelanggaran
    Route::get('/master-data/jenis-pelanggaran', [\App\Http\Controllers\Admin\MasterDataController::class, 'jenisPelanggaran'])->name('master-data.jenis-pelanggaran');
    Route::post('/master-data/jenis-pelanggaran', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeJenisPelanggaran'])->name('master-data.jenis-pelanggaran.store');
    Route::get('/master-data/jenis-pelanggaran/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editJenisPelanggaran'])->name('master-data.jenis-pelanggaran.edit');
    Route::put('/master-data/jenis-pelanggaran/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateJenisPelanggaran'])->name('master-data.jenis-pelanggaran.update');
    Route::delete('/master-data/jenis-pelanggaran/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyJenisPelanggaran'])->name('master-data.jenis-pelanggaran.destroy');
    
    // Master Data - Jenis Prestasi
    Route::get('/master-data/jenis-prestasi', [\App\Http\Controllers\Admin\MasterDataController::class, 'jenisPrestasi'])->name('master-data.jenis-prestasi');
    Route::post('/master-data/jenis-prestasi', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeJenisPrestasi'])->name('master-data.jenis-prestasi.store');
    Route::get('/master-data/jenis-prestasi/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editJenisPrestasi'])->name('master-data.jenis-prestasi.edit');
    Route::put('/master-data/jenis-prestasi/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateJenisPrestasi'])->name('master-data.jenis-prestasi.update');
    Route::delete('/master-data/jenis-prestasi/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyJenisPrestasi'])->name('master-data.jenis-prestasi.destroy');
    
    // Master Data - Jenis Sanksi
    Route::get('/master-data/jenis-sanksi', [\App\Http\Controllers\Admin\MasterDataController::class, 'jenisSanksi'])->name('master-data.jenis-sanksi');
    Route::post('/master-data/jenis-sanksi', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeJenisSanksi'])->name('master-data.jenis-sanksi.store');
    Route::get('/master-data/jenis-sanksi/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editJenisSanksi'])->name('master-data.jenis-sanksi.edit');
    Route::put('/master-data/jenis-sanksi/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateJenisSanksi'])->name('master-data.jenis-sanksi.update');
    Route::delete('/master-data/jenis-sanksi/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyJenisSanksi'])->name('master-data.jenis-sanksi.destroy');
    
    // Master Data - Tahun Ajaran
    Route::get('/master-data/tahun-ajaran', [\App\Http\Controllers\Admin\MasterDataController::class, 'tahunAjaran'])->name('master-data.tahun-ajaran');
    Route::post('/master-data/tahun-ajaran', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeTahunAjaran'])->name('master-data.tahun-ajaran.store');
    Route::get('/master-data/tahun-ajaran/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editTahunAjaran'])->name('master-data.tahun-ajaran.edit');
    Route::put('/master-data/tahun-ajaran/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateTahunAjaran'])->name('master-data.tahun-ajaran.update');
    Route::delete('/master-data/tahun-ajaran/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyTahunAjaran'])->name('master-data.tahun-ajaran.destroy');
    
    // Master Data - Orang Tua
    Route::get('/master-data/orang-tua', [\App\Http\Controllers\Admin\MasterDataController::class, 'dataOrangTua'])->name('master-data.orang-tua');
    Route::post('/master-data/orang-tua', [\App\Http\Controllers\Admin\MasterDataController::class, 'storeOrangTua'])->name('master-data.orang-tua.store');
    Route::get('/master-data/orang-tua/{id}/edit', [\App\Http\Controllers\Admin\MasterDataController::class, 'editOrangTua'])->name('master-data.orang-tua.edit');
    Route::put('/master-data/orang-tua/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'updateOrangTua'])->name('master-data.orang-tua.update');
    Route::delete('/master-data/orang-tua/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyOrangTua'])->name('master-data.orang-tua.destroy');
    
    // ==================== SHARED METHODS WITH KESISWAAN + GURU ====================
    // Methods di bawah ini REUSE oleh Kesiswaan dan Guru
    // Update di sini = Update untuk 3 role sekaligus
    // ==================== SHARED METHODS ====================
    
    // Input Data Features (Shared: ADMIN + KESISWAAN + GURU)
    Route::get('/input-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'inputPelanggaran'])->name('input-data.pelanggaran');
    Route::post('/input-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'storePelanggaran'])->name('input-data.pelanggaran.store');
    
    Route::get('/input-data/prestasi', [\App\Http\Controllers\AdminController::class, 'inputPrestasi'])->name('input-data.prestasi');
    Route::post('/input-data/prestasi', [\App\Http\Controllers\AdminController::class, 'storePrestasi'])->name('input-data.prestasi.store');
    
    Route::get('/input-data/bk', [\App\Http\Controllers\Admin\BKController::class, 'create'])->name('input-data.bk');
    Route::post('/input-data/bk', [\App\Http\Controllers\Admin\BKController::class, 'store'])->name('input-data.bk.store');
    
    // Verifikasi & Monitoring Features (Shared: ADMIN + KESISWAAN ONLY - GURU TIDAK BISA)
    Route::get('/verifikasi-monitoring/verifikasi', [\App\Http\Controllers\AdminController::class, 'verifikasiData'])->name('verifikasi-monitoring.verifikasi');
    Route::post('/verifikasi-monitoring/verifikasi/pelanggaran/{id}', [\App\Http\Controllers\Admin\VerifikasiController::class, 'verifikasiPelanggaran'])->name('verifikasi-monitoring.verifikasi.pelanggaran');
    Route::post('/verifikasi-monitoring/verifikasi/prestasi/{id}', [\App\Http\Controllers\Admin\VerifikasiController::class, 'verifikasiPrestasi'])->name('verifikasi-monitoring.verifikasi.prestasi');
    
    Route::get('/verifikasi-monitoring/monitoring', [\App\Http\Controllers\AdminController::class, 'monitoring'])->name('verifikasi-monitoring.monitoring');
    
    // View Data Features (Shared: ADMIN + KESISWAAN + GURU dengan Authorization)
    Route::get('/view-data/anak', [\App\Http\Controllers\AdminController::class, 'viewSiswa'])->name('view-data.anak');
    Route::get('/view-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'viewDataPelanggaran'])->name('view-data.pelanggaran');
    Route::get('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'show'])->name('view-data.pelanggaran.show');
    Route::get('/view-data/pelanggaran/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editPelanggaran'])->name('view-data.pelanggaran.edit');
    Route::put('/view-data/pelanggaran/{id}', [\App\Http\Controllers\AdminController::class, 'updatePelanggaran'])->name('view-data.pelanggaran.update');
    Route::get('/view-data/prestasi', [\App\Http\Controllers\AdminController::class, 'viewDataPrestasi'])->name('view-data.prestasi');
    Route::get('/view-data/prestasi/{id}', [\App\Http\Controllers\Admin\PrestasiController::class, 'show'])->name('view-data.prestasi.show');
    Route::get('/view-data/prestasi/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editPrestasi'])->name('view-data.prestasi.edit');
    Route::put('/view-data/prestasi/{id}', [\App\Http\Controllers\AdminController::class, 'updatePrestasi'])->name('view-data.prestasi.update');
    
    // CRUD Operations for Pelanggaran & Prestasi
    Route::post('/view-data/pelanggaran', [\App\Http\Controllers\Admin\PelanggaranController::class, 'store'])->name('view-data.pelanggaran.store');
    Route::get('/view-data/pelanggaran/{id}/edit', [\App\Http\Controllers\Admin\PelanggaranController::class, 'edit'])->name('view-data.pelanggaran.edit');
    Route::put('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'update'])->name('view-data.pelanggaran.update');
    Route::delete('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'destroy'])->name('view-data.pelanggaran.destroy');
    Route::post('/view-data/pelanggaran/{id}/sanksi', [\App\Http\Controllers\Admin\PelanggaranController::class, 'createSanksi'])->name('view-data.pelanggaran.sanksi.create');
    
    Route::post('/view-data/prestasi', [\App\Http\Controllers\Admin\PrestasiController::class, 'store'])->name('view-data.prestasi.store');
    Route::get('/view-data/prestasi/{id}/edit', [\App\Http\Controllers\Admin\PrestasiController::class, 'edit'])->name('view-data.prestasi.edit');
    Route::put('/view-data/prestasi/{id}', [\App\Http\Controllers\Admin\PrestasiController::class, 'update'])->name('view-data.prestasi.update');
    Route::delete('/view-data/prestasi/{id}', [\App\Http\Controllers\Admin\PrestasiController::class, 'destroy'])->name('view-data.prestasi.destroy');
    
    // BK Management
    Route::get('/bk', [\App\Http\Controllers\Admin\BKController::class, 'index'])->name('bk');
    Route::post('/bk', [\App\Http\Controllers\Admin\BKController::class, 'store'])->name('bk.store');
    Route::get('/bk/{id}/edit', [\App\Http\Controllers\Admin\BKController::class, 'edit'])->name('bk.edit');
    Route::put('/bk/{id}', [\App\Http\Controllers\Admin\BKController::class, 'update'])->name('bk.update');
    Route::delete('/bk/{id}', [\App\Http\Controllers\Admin\BKController::class, 'destroy'])->name('bk.destroy');
    
    // Sanksi Management (Shared Methods)
    Route::get('/sanksi', [\App\Http\Controllers\AdminController::class, 'manageSanksi'])->name('sanksi.index');
    Route::post('/sanksi', [\App\Http\Controllers\AdminController::class, 'storeSanksi'])->name('sanksi.store');
    Route::get('/sanksi/pelaksanaan', [\App\Http\Controllers\AdminController::class, 'managePelaksanaanSanksi'])->name('sanksi.pelaksanaan');
    Route::put('/sanksi/{id}', [\App\Http\Controllers\AdminController::class, 'updateSanksi'])->name('sanksi.update');
    Route::delete('/sanksi/{id}', [\App\Http\Controllers\Admin\SanksiController::class, 'destroy'])->name('sanksi.destroy');
    Route::post('/sanksi/pelaksanaan', [\App\Http\Controllers\AdminController::class, 'storePelaksanaanSanksi'])->name('sanksi.pelaksanaan.store');
    Route::get('/sanksi/pelaksanaan/{id}', [\App\Http\Controllers\AdminController::class, 'showPelaksanaanSanksi'])->name('sanksi.pelaksanaan.show');
    Route::get('/sanksi/pelaksanaan/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editPelaksanaanSanksi'])->name('sanksi.pelaksanaan.edit');
    Route::put('/sanksi/pelaksanaan/{id}', [\App\Http\Controllers\AdminController::class, 'updatePelaksanaanSanksi'])->name('sanksi.pelaksanaan.update');
    Route::delete('/sanksi/pelaksanaan/{id}', [\App\Http\Controllers\AdminController::class, 'destroyPelaksanaanSanksi'])->name('sanksi.pelaksanaan.destroy');
    
    // Laporan & Sistem Features (Admin Only - Tidak Shared)
    // Setiap role memiliki controller laporan terpisah dengan fitur berbeda
    Route::get('/laporan-sistem/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan-sistem.laporan');
    Route::post('/laporan-sistem/laporan/export', [\App\Http\Controllers\Admin\LaporanController::class, 'export'])->name('laporan-sistem.laporan.export');
    
    Route::get('/laporan-sistem/sistem', [\App\Http\Controllers\Admin\LaporanController::class, 'sistem'])->name('laporan-sistem.sistem');
    Route::post('/laporan-sistem/sistem/backup', [\App\Http\Controllers\Admin\LaporanController::class, 'backup'])->name('laporan-sistem.sistem.backup');
    Route::get('/laporan-sistem/sistem/download/{filename}', [\App\Http\Controllers\Admin\LaporanController::class, 'downloadBackup'])->name('laporan-sistem.sistem.download');
    Route::delete('/laporan-sistem/sistem/delete/{filename}', [\App\Http\Controllers\Admin\LaporanController::class, 'deleteBackup'])->name('laporan-sistem.sistem.delete');
    
    // Sanksi Detail & Edit Routes
    Route::get('/view-data/pelanggaran/sanksi/{id}/edit', [\App\Http\Controllers\Admin\PelanggaranController::class, 'editSanksi'])->name('view-data.pelanggaran.sanksi.edit');
    Route::put('/view-data/pelanggaran/sanksi/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'updateSanksi'])->name('view-data.pelanggaran.sanksi.update');
    Route::get('/view-data/pelanggaran/{id}/sanksi/detail', [\App\Http\Controllers\Admin\PelanggaranController::class, 'showSanksi'])->name('view-data.pelanggaran.sanksi.detail');
});

// ========================================
// KESISWAAN ROUTES (COORDINATOR)
// ========================================
// Login URL: http://localhost:8000/login (select role: kesiswaan)
// Credentials: kesiswaan / kesiswaan123
// Access Level: Verifikator & Koordinator Disiplin
// Features: Input Pelanggaran, Input Prestasi, Verifikasi Data, Monitoring, Export Laporan
// Implementation: REUSE Admin Controllers & Views (Best Practice)
// Status: ✅ PRODUCTION READY
// ========================================

Route::prefix('kesiswaan')->middleware(['check.auth'])->name('kesiswaan.')->group(function () {
    
    // Dashboard (Custom for Kesiswaan - TIDAK REUSE AdminController)
    Route::get('/dashboard', [\App\Http\Controllers\Kesiswaan\DashboardController::class, 'index'])->name('dashboard');
    
    // ==================== REUSE ADMIN CONTROLLER METHODS ====================
    // Semua route di bawah ini REUSE method dari AdminController
    // Jika ada bug/update di AdminController, otomatis terupdate di sini
    // ==================== REUSE ADMIN CONTROLLER ====================
    
    // Input Data Features (REUSE: AdminController::inputPelanggaran)
    Route::get('/input-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'inputPelanggaran'])->name('input-data.pelanggaran');
    Route::post('/input-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'storePelanggaran'])->name('input-data.pelanggaran.store');
    
    Route::get('/input-data/prestasi', [\App\Http\Controllers\AdminController::class, 'inputPrestasi'])->name('input-data.prestasi');
    Route::post('/input-data/prestasi', [\App\Http\Controllers\AdminController::class, 'storePrestasi'])->name('input-data.prestasi.store');
    
    // Verifikasi & Monitoring Features
    Route::get('/verifikasi-monitoring/verifikasi', [\App\Http\Controllers\Kesiswaan\VerifikasiController::class, 'index'])->name('verifikasi-monitoring.verifikasi');
    Route::post('/verifikasi-monitoring/verifikasi/pelanggaran/{id}', [\App\Http\Controllers\Kesiswaan\VerifikasiController::class, 'verifikasiPelanggaran'])->name('verifikasi-monitoring.verifikasi.pelanggaran');
    Route::post('/verifikasi-monitoring/verifikasi/prestasi/{id}', [\App\Http\Controllers\Kesiswaan\VerifikasiController::class, 'verifikasiPrestasi'])->name('verifikasi-monitoring.verifikasi.prestasi');
    
    Route::get('/verifikasi-monitoring/monitoring', [\App\Http\Controllers\AdminController::class, 'monitoring'])->name('verifikasi-monitoring.monitoring');
    
    // View Data Features
    Route::get('/view-data/anak', [\App\Http\Controllers\AdminController::class, 'viewSiswa'])->name('view-data.anak');
    Route::get('/view-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'viewDataPelanggaran'])->name('view-data.pelanggaran');
    Route::get('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'show'])->name('view-data.pelanggaran.show');
    Route::get('/view-data/pelanggaran/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editPelanggaran'])->name('view-data.pelanggaran.edit');
    Route::put('/view-data/pelanggaran/{id}', [\App\Http\Controllers\AdminController::class, 'updatePelanggaran'])->name('view-data.pelanggaran.update');
    Route::get('/view-data/prestasi', [\App\Http\Controllers\AdminController::class, 'viewDataPrestasi'])->name('view-data.prestasi');
    Route::get('/view-data/prestasi/{id}', [\App\Http\Controllers\Admin\PrestasiController::class, 'show'])->name('view-data.prestasi.show');
    Route::get('/view-data/prestasi/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editPrestasi'])->name('view-data.prestasi.edit');
    Route::put('/view-data/prestasi/{id}', [\App\Http\Controllers\AdminController::class, 'updatePrestasi'])->name('view-data.prestasi.update');
    
    // CRUD Operations for Pelanggaran & Prestasi
    Route::post('/view-data/pelanggaran', [\App\Http\Controllers\Admin\PelanggaranController::class, 'store'])->name('view-data.pelanggaran.store');
    Route::get('/view-data/pelanggaran/{id}/edit', [\App\Http\Controllers\Admin\PelanggaranController::class, 'edit'])->name('view-data.pelanggaran.edit');
    Route::put('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'update'])->name('view-data.pelanggaran.update');
    Route::delete('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'destroy'])->name('view-data.pelanggaran.destroy');
    Route::post('/view-data/pelanggaran/{id}/sanksi', [\App\Http\Controllers\Admin\PelanggaranController::class, 'createSanksi'])->name('view-data.pelanggaran.sanksi.create');
    
    Route::post('/view-data/prestasi', [\App\Http\Controllers\Admin\PrestasiController::class, 'store'])->name('view-data.prestasi.store');
    Route::get('/view-data/prestasi/{id}/edit', [\App\Http\Controllers\Admin\PrestasiController::class, 'edit'])->name('view-data.prestasi.edit');
    Route::put('/view-data/prestasi/{id}', [\App\Http\Controllers\Admin\PrestasiController::class, 'update'])->name('view-data.prestasi.update');
    Route::delete('/view-data/prestasi/{id}', [\App\Http\Controllers\Admin\PrestasiController::class, 'destroy'])->name('view-data.prestasi.destroy');
    
    // Sanksi Management (Shared Methods)
    Route::get('/sanksi', [\App\Http\Controllers\AdminController::class, 'manageSanksi'])->name('sanksi.index');
    Route::post('/sanksi', [\App\Http\Controllers\AdminController::class, 'storeSanksi'])->name('sanksi.store');
    Route::get('/sanksi/pelaksanaan', [\App\Http\Controllers\AdminController::class, 'managePelaksanaanSanksi'])->name('sanksi.pelaksanaan');
    Route::put('/sanksi/{id}', [\App\Http\Controllers\AdminController::class, 'updateSanksi'])->name('sanksi.update');
    Route::delete('/sanksi/{id}', [\App\Http\Controllers\Admin\SanksiController::class, 'destroy'])->name('sanksi.destroy');
    Route::post('/sanksi/pelaksanaan', [\App\Http\Controllers\AdminController::class, 'storePelaksanaanSanksi'])->name('sanksi.pelaksanaan.store');
    Route::get('/sanksi/pelaksanaan/{id}', [\App\Http\Controllers\AdminController::class, 'showPelaksanaanSanksi'])->name('sanksi.pelaksanaan.show');
    Route::get('/sanksi/pelaksanaan/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editPelaksanaanSanksi'])->name('sanksi.pelaksanaan.edit');
    Route::put('/sanksi/pelaksanaan/{id}', [\App\Http\Controllers\AdminController::class, 'updatePelaksanaanSanksi'])->name('sanksi.pelaksanaan.update');
    Route::delete('/sanksi/pelaksanaan/{id}', [\App\Http\Controllers\AdminController::class, 'destroyPelaksanaanSanksi'])->name('sanksi.pelaksanaan.destroy');
    
    // Laporan Features (Kesiswaan Controller)
    Route::get('/laporan', [\App\Http\Controllers\Kesiswaan\LaporanController::class, 'index'])->name('laporan');
    Route::post('/laporan/export', [\App\Http\Controllers\Kesiswaan\LaporanController::class, 'export'])->name('laporan.export');
    
    // Sanksi Detail & Edit Routes
    Route::get('/view-data/pelanggaran/sanksi/{id}/edit', [\App\Http\Controllers\Admin\PelanggaranController::class, 'editSanksi'])->name('view-data.pelanggaran.sanksi.edit');
    Route::put('/view-data/pelanggaran/sanksi/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'updateSanksi'])->name('view-data.pelanggaran.sanksi.update');
    Route::get('/view-data/pelanggaran/{id}/sanksi/detail', [\App\Http\Controllers\Admin\PelanggaranController::class, 'showSanksi'])->name('view-data.pelanggaran.sanksi.detail');

});
// ========================================
// OTHER ROLES ROUTES (FUTURE IMPLEMENTATION)
// ========================================
// Status: 🚧 PLACEHOLDER - Ready for future development
// ========================================

// ========================================
// GURU ROUTES (TEACHER & HOMEROOM TEACHER)
// ========================================
// Login URL: http://localhost:8000/login (select role: guru)
// Credentials: guru / guru123
// Access Level: Input Pelanggaran, View Data Sendiri, Export Laporan
// Features: Conditional access (Guru vs Wali Kelas)
// Implementation: REUSE Admin Controllers & Views (Shared Methods)
// Status: ✅ PRODUCTION READY
// ========================================

Route::prefix('guru')->middleware(['check.auth'])->name('guru.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Guru\GuruDashboardController::class, 'index'])->name('dashboard');
    
    // ==================== REUSE ADMIN CONTROLLER METHODS ====================
    // Semua route di bawah ini REUSE method dari AdminController
    // Dengan AUTHORIZATION CHECK untuk Guru Biasa vs Wali Kelas
    // Update AdminController = Update Guru otomatis
    // ==================== REUSE ADMIN CONTROLLER ====================
    
    // Profile Management (Shared)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');
    
    // Input Pelanggaran (Guru Controller khusus)
    Route::get('/input-pelanggaran', [\App\Http\Controllers\Guru\PelanggaranController::class, 'inputPelanggaran'])->name('input-pelanggaran');
    Route::post('/input-pelanggaran', [\App\Http\Controllers\Guru\PelanggaranController::class, 'storePelanggaran'])->name('input-pelanggaran.store');
    
    // Route lama untuk backward compatibility
    Route::get('/input-data/pelanggaran', [\App\Http\Controllers\Guru\PelanggaranController::class, 'inputPelanggaran'])->name('input-data.pelanggaran');
    Route::post('/input-data/pelanggaran', [\App\Http\Controllers\Guru\PelanggaranController::class, 'storePelanggaran'])->name('input-data.pelanggaran.store');
    
    // Data Pelanggaran (Guru Controller khusus)
    Route::get('/data-pelanggaran', [\App\Http\Controllers\Guru\PelanggaranController::class, 'dataPelanggaran'])->name('data-pelanggaran');
    Route::get('/view-data/pelanggaran', [\App\Http\Controllers\AdminController::class, 'viewDataPelanggaran'])->name('view-data.pelanggaran');
    Route::get('/view-data/pelanggaran/{id}/edit', [\App\Http\Controllers\Admin\PelanggaranController::class, 'edit'])->name('view-data.pelanggaran.edit');
    Route::put('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'update'])->name('view-data.pelanggaran.update');
    Route::delete('/view-data/pelanggaran/{id}', [\App\Http\Controllers\Admin\PelanggaranController::class, 'destroy'])->name('view-data.pelanggaran.destroy');
    Route::post('/view-data/pelanggaran/{id}/sanksi', [\App\Http\Controllers\Admin\PelanggaranController::class, 'createSanksi'])->name('view-data.pelanggaran.sanksi.create');
    Route::get('/pelanggaran/{id}', [\App\Http\Controllers\Guru\PelanggaranController::class, 'show'])->name('pelanggaran.show');
    
    // Input Prestasi (GURU BISA input prestasi juga)
    Route::get('/input-prestasi', [\App\Http\Controllers\AdminController::class, 'inputPrestasi'])->name('input-prestasi');
    Route::post('/input-prestasi', [\App\Http\Controllers\AdminController::class, 'storePrestasi'])->name('input-prestasi.store');
    
    // Monitoring Khusus Wali Kelas (REUSE AdminController dengan Authorization)
    // GURU BIASA akan dapat 403 ERROR - Hanya WALI KELAS yang bisa akses
    Route::get('/monitoring/siswa', [\App\Http\Controllers\AdminController::class, 'viewSiswa'])->name('monitoring.siswa');
    Route::get('/monitoring/sanksi', [\App\Http\Controllers\AdminController::class, 'manageSanksi'])->name('monitoring.sanksi');
    Route::get('/monitoring/kelas', [\App\Http\Controllers\AdminController::class, 'monitoring'])->name('monitoring.kelas');
    

    
    // Laporan Terbatas (Guru Biasa - Limited Access)
    Route::get('/laporan-terbatas', [\App\Http\Controllers\Guru\GuruDashboardController::class, 'laporanTerbatas'])->name('laporan-terbatas');
    Route::post('/laporan-terbatas/export', [\App\Http\Controllers\Guru\GuruDashboardController::class, 'exportLaporan'])->name('laporan.export');
    
    // Data Kelas Saya (Khusus Wali Kelas)
    Route::get('/data-kelas-saya', [\App\Http\Controllers\Guru\WaliKelasController::class, 'dataKelas'])->name('data-kelas-saya');
    
    // Fitur Wali Kelas (Extended Access)
    Route::prefix('wali-kelas')->name('wali-kelas.')->group(function () {
        Route::get('/data-kelas', [\App\Http\Controllers\Guru\WaliKelasController::class, 'dataKelas'])->name('data-kelas');
        Route::get('/input-pelanggaran', [\App\Http\Controllers\AdminController::class, 'inputPelanggaran'])->name('input-pelanggaran');
        Route::get('/monitoring-kelas/pelanggaran', [\App\Http\Controllers\Guru\WaliKelasController::class, 'monitoringPelanggaran'])->name('monitoring.pelanggaran');
        Route::get('/monitoring-kelas/sanksi', [\App\Http\Controllers\Guru\WaliKelasController::class, 'monitoringSanksi'])->name('monitoring.sanksi');
        Route::get('/export-allaccess', [\App\Http\Controllers\Guru\WaliKelasController::class, 'exportAllAccess'])->name('export-allaccess');
        Route::post('/export-allaccess', [\App\Http\Controllers\Guru\WaliKelasController::class, 'processExport'])->name('export.process');
    });
    

    


});

Route::prefix('wali-kelas')->middleware(['check.auth'])->name('wali-kelas.')->group(function () {
    Route::get('/dashboard', function() { return view('wali-kelas.dashboard'); })->name('dashboard');
});

Route::prefix('konselor-bk')->middleware(['check.auth'])->name('konselor-bk.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\BK\BKController::class, 'dashboard'])->name('dashboard');
    
    // Input BK (Shared dengan Admin)
    Route::get('/input-bk', [\App\Http\Controllers\BK\BKController::class, 'inputBK'])->name('input-bk');
    Route::post('/input-bk', [\App\Http\Controllers\Admin\BKController::class, 'store'])->name('input-bk.store');
    
    // Notifikasi Follow-up Sanksi (Tidak reuse form input)
    Route::get('/notifikasi', [\App\Http\Controllers\BK\NotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notification/{id}/mark-read', [\App\Http\Controllers\BK\NotifikasiController::class, 'markAsRead'])->name('notification.mark-read');
    Route::post('/followup-complete/{sanksiId}', [\App\Http\Controllers\BK\NotifikasiController::class, 'completeFollowup'])->name('followup.complete');
    
    // Data BK Saya (BK-Specific)
    Route::get('/data-bk-saya', [\App\Http\Controllers\BK\BKController::class, 'viewDataSaya'])->name('data-bk-saya');
    Route::get('/data-bk-saya/{id}/edit', [\App\Http\Controllers\BK\BKController::class, 'editDataSaya'])->name('data-bk-saya.edit');
    Route::put('/data-bk-saya/{id}', [\App\Http\Controllers\BK\BKController::class, 'updateDataSaya'])->name('data-bk-saya.update');
    Route::delete('/data-bk-saya/{id}', [\App\Http\Controllers\BK\BKController::class, 'destroyDataSaya'])->name('data-bk-saya.destroy');
    Route::get('/view-data-sendiri', [\App\Http\Controllers\BK\BKController::class, 'viewDataSaya'])->name('view-data-sendiri');
    
    Route::get('/laporan', [\App\Http\Controllers\BK\BKController::class, 'exportLaporan'])->name('laporan');
    Route::post('/laporan/export', [\App\Http\Controllers\BK\BKController::class, 'processExport'])->name('laporan.export');
    Route::get('/export-laporan', [\App\Http\Controllers\BK\BKController::class, 'exportLaporan'])->name('export-laporan');
    
    // Notifikasi Follow-up Sanksi
    Route::get('/notifikasi', [\App\Http\Controllers\BK\NotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notification/{id}/mark-read', [\App\Http\Controllers\BK\NotifikasiController::class, 'markAsRead'])->name('notification.mark-read');
    Route::post('/followup-sanksi', [\App\Http\Controllers\BK\BKController::class, 'followupSanksi'])->name('followup-sanksi');
});

// ========================================
// BK ROUTES (KONSELOR BK)
// ========================================
// Routes khusus untuk BK dengan sistem notifikasi follow-up sanksi
// ========================================





// ========================================
// KEPALA SEKOLAH ROUTES (EXECUTIVE LEVEL)
// ========================================
// Login URL: http://localhost:8000/login (select role: kepala_sekolah)
// Access Level: View All Data, Dashboard, Laporan
// Tanggung Jawab: Kebijakan disiplin sekolah
// Features: Monitoring All, View Data Anak, Export Laporan, Profile
// Status: ✅ PRODUCTION READY
// ========================================

Route::prefix('kepala-sekolah')->middleware(['check.auth'])->name('kepala-sekolah.')->group(function () {
    
    // Dashboard - Executive Level Monitoring
    Route::get('/dashboard', [\App\Http\Controllers\KepalaSekolah\KepalaSekolahController::class, 'dashboard'])->name('dashboard');
    
    // Monitoring All - Comprehensive School Overview
    Route::get('/monitoring', [\App\Http\Controllers\KepalaSekolah\KepalaSekolahController::class, 'monitoring'])->name('monitoring');
    
    // View Data Sendiri (Profile) - SHARED dengan ProfileController
    Route::get('/view-data-sendiri', [\App\Http\Controllers\ProfileController::class, 'index'])->name('view-data-sendiri');
    Route::put('/view-data-sendiri', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('view-data-sendiri.update');
    Route::put('/view-data-sendiri/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('view-data-sendiri.password');
    Route::post('/view-data-sendiri/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('view-data-sendiri.photo');
    
    // View Data Anak - SHARED dengan AdminController (READ-ONLY)
    Route::get('/view-data-anak', [\App\Http\Controllers\AdminController::class, 'viewSiswa'])->name('view-data-anak');
    
    // Export Laporan - Executive Reports
    Route::get('/laporan', [\App\Http\Controllers\KepalaSekolah\KepalaSekolahController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/export', [\App\Http\Controllers\KepalaSekolah\KepalaSekolahController::class, 'exportLaporan'])->name('laporan.export');
    
});

Route::prefix('siswa')->middleware(['check.auth'])->name('siswa.')->group(function () {
    
    // Dashboard Siswa
    Route::get('/dashboard', [\App\Http\Controllers\Siswa\SiswaController::class, 'dashboard'])->name('dashboard');
    
    // Riwayat Notifikasi
    Route::get('/notifikasi', [\App\Http\Controllers\Siswa\SiswaController::class, 'notifikasi'])->name('notifikasi');
    
    // View Data Sendiri - Riwayat Pribadi
    Route::get('/view-data-sendiri', [\App\Http\Controllers\Siswa\SiswaController::class, 'viewDataSendiri'])->name('view-data-sendiri');
    
    // Export Laporan Terbatas (Hanya Data Pribadi)
    Route::get('/laporan', [\App\Http\Controllers\Siswa\SiswaController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/export', [\App\Http\Controllers\Siswa\SiswaController::class, 'exportLaporan'])->name('laporan.export');
    
});

Route::prefix('orang-tua')->middleware(['check.auth'])->name('orang-tua.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\OrangTuaController::class, 'dashboard'])->name('dashboard');
    Route::get('/view-data-anak', [\App\Http\Controllers\OrangTuaController::class, 'viewDataAnak'])->name('view-data-anak');
    Route::get('/view-data-sendiri', [\App\Http\Controllers\OrangTuaController::class, 'viewDataSendiri'])->name('view-data-sendiri');
    Route::get('/laporan', [\App\Http\Controllers\OrangTuaController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/export', [\App\Http\Controllers\OrangTuaController::class, 'exportLaporan'])->name('laporan.export');

});
