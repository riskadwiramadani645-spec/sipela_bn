@extends('layouts.app')

@section('title', 'Dashboard Admin - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Admin</h6>
                    <p class="mb-0">SMK Bakti Nusantara 666</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1" id="current-date">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock" id="current-time">{{ now()->format('H:i') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mt-1">
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-users fa-3x text-primary"></i>
            <div class="ms-3">
                <p class="mb-2">Total Users</p>
                <h6 class="mb-0">{{ $totalUsers ?? 0 }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-user-graduate fa-3x text-info"></i>
            <div class="ms-3">
                <p class="mb-2">Total Siswa</p>
                <h6 class="mb-0">{{ $totalSiswa ?? 0 }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-chalkboard-teacher fa-3x text-success"></i>
            <div class="ms-3">
                <p class="mb-2">Total Guru</p>
                <h6 class="mb-0">{{ $totalGuru ?? 0 }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-school fa-3x text-warning"></i>
            <div class="ms-3">
                <p class="mb-2">Total Kelas</p>
                <h6 class="mb-0">{{ $totalKelas ?? 0 }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- SIPELA Stats -->
<div class="row g-4 mt-1">
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-exclamation-triangle fa-3x text-danger"></i>
            <div class="ms-3">
                <p class="mb-2">Total Pelanggaran</p>
                <h6 class="mb-0">{{ $totalPelanggaran ?? 0 }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-trophy fa-3x text-warning"></i>
            <div class="ms-3">
                <p class="mb-2">Total Prestasi</p>
                <h6 class="mb-0">{{ $totalPrestasi ?? 0 }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-gavel fa-3x text-info"></i>
            <div class="ms-3">
                <p class="mb-2">Sanksi Aktif</p>
                <h6 class="mb-0">{{ $sanksiAktif ?? 0 }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-user-md fa-3x text-success"></i>
            <div class="ms-3">
                <p class="mb-2">Sesi BK</p>
                <h6 class="mb-0">{{ $totalBK ?? 0 }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4 mt-1">
    <div class="col-12 col-lg-8">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-4">Ringkasan Sistem</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-calendar fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Tahun Ajaran <span class="badge bg-success">✓</span></h6>
                            <small class="text-muted">2024/2025 - Ganjil</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-database fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-0">Database <span class="badge bg-success">✓</span></h6>
                            <small class="text-muted">Terhubung & Aktif</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-shield-alt fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-0">Keamanan <span class="badge bg-success">✓</span></h6>
                            <small class="text-muted">Role-based Access</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-user-edit fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-0">Profile System <span class="badge bg-success">✓</span></h6>
                            <small class="text-muted">Edit & Upload Ready</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-4">Notifikasi</h6>
            <div class="d-flex align-items-center border-bottom py-3">
                <i class="fa fa-info-circle text-info me-3"></i>
                <div>
                    <h6 class="mb-0">Sistem Siap <span class="badge bg-success">✓</span></h6>
                    <small class="text-muted">SIPELA telah dikonfigurasi</small>
                </div>
            </div>
            <div class="d-flex align-items-center border-bottom py-3">
                <i class="fa fa-check-circle text-success me-3"></i>
                <div>
                    <h6 class="mb-0">Database OK <span class="badge bg-success">✓</span></h6>
                    <small class="text-muted">Semua tabel tersedia</small>
                </div>
            </div>
            <div class="d-flex align-items-center border-bottom py-3">
                <i class="fa fa-user-shield text-primary me-3"></i>
                <div>
                    <h6 class="mb-0">Admin Active <span class="badge bg-success">✓</span></h6>
                    <small class="text-muted">Akses penuh tersedia</small>
                </div>
            </div>
            <div class="d-flex align-items-center border-bottom py-3">
                <i class="fa fa-camera text-warning me-3"></i>
                <div>
                    <h6 class="mb-0">Profile Photo <span class="badge bg-success">✓</span></h6>
                    <small class="text-muted">Upload foto tersedia</small>
                </div>
            </div>
            <div class="d-flex align-items-center pt-3">
                <i class="fa fa-trophy text-success me-3"></i>
                <div>
                    <h6 class="mb-0">Prestasi Admin <span class="badge bg-info">AUTO</span></h6>
                    <small class="text-muted">Langsung disetujui</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-4">Aktivitas Terbaru</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-clock fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="mb-0">Pelanggaran Hari Ini</h6>
                            <small class="text-muted">{{ $pelanggaranHariIni ?? 0 }} kasus baru</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-star fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="mb-0">Prestasi Hari Ini</h6>
                            <small class="text-muted">{{ $prestasiHariIni ?? 0 }} pencapaian baru</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-exclamation-circle fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="mb-0">Verifikasi Pelanggaran</h6>
                            <small class="text-muted">{{ $menungguVerifikasi ?? 0 }} pelanggaran menunggu</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fa fa-tasks fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-0">Sanksi Aktif</h6>
                            <small class="text-muted">{{ $sanksiAktif ?? 0 }} sedang berjalan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.real-time-clock {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
    const timeStr = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }) + ' WIB';
    
    document.getElementById('current-date').textContent = dateStr;
    document.getElementById('current-time').textContent = timeStr;
}

updateClock();
setInterval(updateClock, 1000);

setInterval(updateClock, 1000);
updateClock();
</script>
@endpush

