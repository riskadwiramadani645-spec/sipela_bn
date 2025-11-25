@extends('layouts.app')

@section('title', 'Dashboard Konselor BK - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Konselor BK</h6>
                    <p class="mb-0">Sistem Bimbingan dan Konseling - Penanganan Masalah Siswa</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock">{{ now()->format('H:i') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Dashboard -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Statistik Konseling Saya</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-primary rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-comments fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Konseling</p>
                                <h6 class="mb-0">{{ $totalKonseling }}</h6>
                                <small>Semua layanan</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-warning rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-sync-alt fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Follow-up Sanksi</p>
                                <h6 class="mb-0">{{ $followUpSanksi }}</h6>
                                <small>Perlu tindakan</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-info rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-users fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Siswa Aktif</p>
                                <h6 class="mb-0">{{ $siswaAktif }}</h6>
                                <small>Dalam bimbingan</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-success rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-check-circle fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Success Rate</p>
                                <h6 class="mb-0">{{ $successRate }}%</h6>
                                <small>Berhasil</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('konselor-bk.input-bk') }}" class="btn btn-primary w-100 py-3">
                            <i class="fa fa-plus-circle fa-2x d-block mb-2"></i>
                            <strong>Input BK</strong>
                            <small class="d-block">Konseling baru</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('konselor-bk.notifikasi') }}" class="btn btn-warning w-100 py-3">
                            <i class="fa fa-bell fa-2x d-block mb-2"></i>
                            <strong>Notifikasi</strong>
                            <small class="d-block">Follow-up sanksi</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('konselor-bk.data-bk-saya') }}" class="btn btn-info w-100 py-3">
                            <i class="fa fa-list fa-2x d-block mb-2"></i>
                            <strong>Data BK Saya</strong>
                            <small class="d-block">History konseling</small>
                        </a>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <a href="{{ route('konselor-bk.export-laporan') }}" class="btn btn-success w-100 py-3">
                            <i class="fa fa-download fa-2x d-block mb-2"></i>
                            <strong>Export Laporan</strong>
                            <small class="d-block">Full access</small>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('profile.index') }}" class="btn btn-dark w-100 py-3">
                            <i class="fa fa-user fa-2x d-block mb-2"></i>
                            <strong>Profile</strong>
                            <small class="d-block">Edit profile</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Konseling Terbaru -->
<div class="row g-4 mt-3">
    <div class="col-md-6">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-comments me-2"></i>Konseling Terbaru</h6>
            </div>
            <div class="card-body">
                @if($recentKonseling->count() > 0)
                    <div class="table-responsive">
                        <table id="dashboardTable" class="table table-sm text-white" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentKonseling as $konseling)
                                <tr>
                                    <td>
                                        <strong>{{ $konseling->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $konseling->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($konseling->tanggal_konseling)) }}</td>
                                    <td>
                                        <span class="badge {{ $konseling->status == 'Selesai' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $konseling->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Belum ada data konseling</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-exclamation-triangle me-2"></i>Follow-up Sanksi</h6>
            </div>
            <div class="card-body">
                @if($sanksiFollowUp->count() > 0)
                    <div class="table-responsive">
                        <table id="dashboardTable" class="table table-sm text-white" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Sanksi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sanksiFollowUp as $notifikasi)
                                <tr>
                                    <td>
                                        <strong>{{ $notifikasi->sanksi->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $notifikasi->sanksi->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ Str::limit($notifikasi->sanksi->jenis_sanksi ?? 'N/A', 20) }}</td>
                                    <td>
                                        <span class="badge bg-warning">Belum Dibaca</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('konselor-bk.notifikasi') }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-bell"></i> Lihat Semua Notifikasi
                        </a>
                    </div>
                @else
                    <p class="text-muted">Tidak ada notifikasi follow-up sanksi</p>
                @endif
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
}

setInterval(updateClock, 1000);
updateClock();
</script>
@endpush