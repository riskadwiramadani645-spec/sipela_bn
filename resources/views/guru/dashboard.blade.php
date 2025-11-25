@extends('layouts.app')

@section('title', 'Dashboard Guru - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Guru</h6>
                    <p class="mb-0">SMK Bakti Nusantara 666</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock">{{ now()->format('H:i') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-chalkboard-teacher fa-3x mb-2"></i>
                        <h6>{{ $guru->nama_guru ?? 'Guru' }} <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>{{ $guru->bidang_studi ?? 'Mata Pelajaran' }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-edit fa-3x mb-2"></i>
                        <h6>Input Data <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Pencatatan Pelanggaran</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-eye fa-3x mb-2"></i>
                        <h6>View Data <span class="badge bg-info">✓ AKTIF</span></h6>
                        <small>Data Pelanggaran Saya</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Statistik Pelanggaran Saya</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-xl-4">
                        <div class="bg-danger rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-exclamation-triangle fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Input</p>
                                <h6 class="mb-0">{{ $totalPelanggaranInput ?? 0 }}</h6>
                                <small>Pelanggaran yang saya input</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="bg-warning rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-calendar fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Bulan Ini</p>
                                <h6 class="mb-0">{{ $pelanggaranBulanIni ?? 0 }}</h6>
                                <small>Input bulan {{ date('F') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="bg-info rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-check-circle fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Diverifikasi</p>
                                <h6 class="mb-0">{{ $totalPelanggaranInput ?? 0 }}</h6>
                                <small>Status verifikasi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Status Breakdown -->
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-chart-pie me-2"></i>Status Verifikasi</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="bg-warning rounded p-3 text-center text-white">
                            <i class="fa fa-clock fa-2x mb-2"></i>
                            <h5>{{ $recentPelanggaran->where('status_verifikasi', 'menunggu')->count() ?? 0 }}</h5>
                            <small>Menunggu Verifikasi</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-success rounded p-3 text-center text-white">
                            <i class="fa fa-check-circle fa-2x mb-2"></i>
                            <h5>{{ $recentPelanggaran->where('status_verifikasi', 'diverifikasi')->count() ?? 0 }}</h5>
                            <small>Sudah Diverifikasi</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-danger rounded p-3 text-center text-white">
                            <i class="fa fa-times-circle fa-2x mb-2"></i>
                            <h5>{{ $recentPelanggaran->where('status_verifikasi', 'ditolak')->count() ?? 0 }}</h5>
                            <small>Ditolak</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fa fa-history me-2"></i>Aktivitas Terbaru</h6>
                    <a href="{{ route('guru.data-pelanggaran') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body">
                @if($recentPelanggaran && $recentPelanggaran->count() > 0)
                    @foreach($recentPelanggaran->take(5) as $pelanggaran)
                    <div class="d-flex align-items-center border-bottom py-2">
                        <div class="me-3">
                            <i class="fa fa-exclamation-triangle text-danger fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $pelanggaran->siswa->nama_siswa ?? 'N/A' }} - {{ $pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            <br><small class="text-muted">{{ $pelanggaran->created_at ? $pelanggaran->created_at->diffForHumans() : '-' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $pelanggaran->status_verifikasi == 'diverifikasi' ? 'success' : ($pelanggaran->status_verifikasi == 'ditolak' ? 'danger' : 'warning') }}">
                                {{ ucfirst($pelanggaran->status_verifikasi ?? 'menunggu') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada aktivitas pelanggaran</p>
                        <a href="{{ route('guru.input-pelanggaran') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i>Input Pelanggaran Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($isWaliKelas ?? false)
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Wali Kelas</h6>
                    <p class="mb-0">SMK Bakti Nusantara 666</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1" id="current-date">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock" id="current-time">{{ now()->format('H:i') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('guru.wali-kelas.data-kelas') }}" class="btn btn-light w-100 py-3">
                        <i class="fa fa-users fa-2x d-block mb-2 text-success"></i>
                        <strong class="text-dark">Data Kelas Saya</strong>
                        <small class="d-block text-muted">Siswa kelas yang diampu</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('guru.wali-kelas.input-pelanggaran') }}" class="btn btn-light w-100 py-3">
                        <i class="fa fa-plus-circle fa-2x d-block mb-2 text-danger"></i>
                        <strong class="text-dark">Input Pelanggaran Kelas</strong>
                        <small class="d-block text-muted">Khusus siswa kelas</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('guru.wali-kelas.monitoring.pelanggaran') }}" class="btn btn-light w-100 py-3">
                        <i class="fa fa-chart-line fa-2x d-block mb-2 text-info"></i>
                        <strong class="text-dark">Monitoring Kelas</strong>
                        <small class="d-block text-muted">Pelanggaran & Sanksi</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('guru.wali-kelas.export-allaccess') }}" class="btn btn-light w-100 py-3">
                        <i class="fa fa-download fa-2x d-block mb-2 text-primary"></i>
                        <strong class="text-dark">Export Full Access</strong>
                        <small class="d-block text-muted">🔓 Semua data kelas</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

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
function updateDateTime() {
    const now = new Date();
    
    const timeOptions = {
        timeZone: 'Asia/Jakarta',
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    const timeString = now.toLocaleTimeString('id-ID', timeOptions);
    const clockElements = document.querySelectorAll('.real-time-clock');
    clockElements.forEach(el => el.textContent = timeString + ' WIB');
    
    const dateOptions = {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    };
    const dateString = now.toLocaleDateString('id-ID', dateOptions);
    const dateElements = document.querySelectorAll('.real-time-date');
    dateElements.forEach(el => el.textContent = dateString);
    
    const dayOptions = {
        timeZone: 'Asia/Jakarta',
        weekday: 'long'
    };
    const dayString = now.toLocaleDateString('id-ID', dayOptions);
    const dayElements = document.querySelectorAll('.real-time-day');
    dayElements.forEach(el => el.textContent = dayString);
}

setInterval(updateDateTime, 1000);
updateDateTime();
</script>
@endpush