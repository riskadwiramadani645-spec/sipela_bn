@extends('layouts.app')

@section('title', 'Dashboard Siswa - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Siswa - {{ $siswa->nama_siswa }}</h6>
                    <p class="mb-0">Kelas {{ $siswa->kelas->nama_kelas ?? 'N/A' }} - NIS: {{ $siswa->nis }}</p>
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

<!-- Personal Stats -->
<div class="row g-4 mt-1">
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-exclamation-triangle fa-3x text-danger"></i>
            <div class="ms-3">
                <p class="mb-2">Pelanggaran Saya</p>
                <h6 class="mb-0">{{ $totalPelanggaran }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-trophy fa-3x text-warning"></i>
            <div class="ms-3">
                <p class="mb-2">Prestasi Saya</p>
                <h6 class="mb-0">{{ $totalPrestasi }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-gavel fa-3x text-info"></i>
            <div class="ms-3">
                <p class="mb-2">Sanksi Aktif</p>
                <h6 class="mb-0">{{ $sanksiAktif }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-chart-line fa-3x text-success"></i>
            <div class="ms-3">
                <p class="mb-2">Skor Disiplin</p>
                <h6 class="mb-0">{{ round($skorDisiplin, 1) }}%</h6>
            </div>
        </div>
    </div>
</div>

<!-- Status Kedisiplinan -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-info text-white rounded p-4">
            <h6 class="mb-3"><i class="fa fa-user-check me-2"></i>Status Kedisiplinan Pribadi</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4">{{ round($skorDisiplin, 1) }}%</div>
                        <small>Skor Kedisiplinan</small>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $skorDisiplin >= 80 ? 'success' : ($skorDisiplin >= 60 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $skorDisiplin }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 text-{{ $totalPelanggaran == 0 ? 'success' : 'warning' }}">{{ $totalPelanggaran }}</div>
                        <small>Total Pelanggaran</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 text-success">{{ $totalPrestasi }}</div>
                        <small>Total Prestasi</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 text-{{ $sanksiAktif == 0 ? 'success' : 'danger' }}">{{ $sanksiAktif }}</div>
                        <small>Sanksi Aktif</small>
                        @if($sanksiAktif > 0)
                            <div class="badge bg-danger mt-1">Perlu Diselesaikan!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-warning text-dark rounded p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fa fa-bell me-2"></i>Notifikasi Terbaru 
                    @if($notifications->count() > 5)
                        (5 dari {{ $notifications->count() }})
                    @else
                        ({{ $notifications->count() }})
                    @endif
                </h6>
                <a href="{{ route('siswa.notifikasi') }}" class="btn btn-outline-dark btn-sm">
                    <i class="fa fa-list me-1"></i>Lihat Semua
                </a>
            </div>
            @if($notifications->count() > 0)
                <div class="row">
                    @foreach($notifications->take(5) as $notif)
                    <div class="col-md-6 mb-2">
                        <div class="alert alert-info mb-2">
                            <strong>{{ $notif->title }}</strong><br>
                            <small>{{ $notif->message }}</small><br>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($notifications->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('siswa.notifikasi') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-bell me-1"></i>Lihat {{ $notifications->count() - 5 }} Notifikasi Lainnya
                        </a>
                    </div>
                @endif
            @else
                <div class="alert alert-secondary">
                    <i class="fa fa-info-circle me-2"></i>
                    Tidak ada notifikasi baru. User ID: {{ session('user')->user_id ?? 'N/A' }} | Siswa ID: {{ $siswa->siswa_id }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row g-4 mt-1">
    <div class="col-12 col-lg-6">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0"><i class="fa fa-exclamation-triangle me-2"></i>Pelanggaran Terbaru</h6>
                <span class="badge bg-danger">{{ $recentPelanggaran->count() }}</span>
            </div>
            @if($recentPelanggaran->count() > 0)
                @foreach($recentPelanggaran as $pelanggaran)
                <div class="d-flex align-items-center border-bottom py-2">
                    <i class="fa fa-exclamation-circle text-danger me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $pelanggaran->tanggal ? date('d/m/Y', strtotime($pelanggaran->tanggal)) : '-' }}</small>
                        <small class="text-muted d-block">{{ $pelanggaran->created_at ? $pelanggaran->created_at->diffForHumans() : '-' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $pelanggaran->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                            {{ ucfirst($pelanggaran->status_verifikasi) }}
                        </span>
                    </div>
                </div>
                @endforeach
                <div class="text-center mt-3">
                    <a href="{{ route('siswa.view-data-sendiri') }}" class="btn btn-outline-primary btn-sm">
                        Lihat Semua Pelanggaran
                    </a>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada pelanggaran terbaru</p>
                    <small class="text-success">Pertahankan kedisiplinan!</small>
                </div>
            @endif
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0"><i class="fa fa-trophy me-2"></i>Prestasi Terbaru</h6>
                <span class="badge bg-success">{{ $recentPrestasi->count() }}</span>
            </div>
            @if($recentPrestasi->count() > 0)
                @foreach($recentPrestasi as $prestasi)
                <div class="d-flex align-items-center border-bottom py-2">
                    <i class="fa fa-trophy text-success me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $prestasi->jenisPrestasi->nama_prestasi ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $prestasi->tanggal ? date('d/m/Y', strtotime($prestasi->tanggal)) : '-' }}</small>
                        <small class="text-muted d-block">{{ $prestasi->created_at ? $prestasi->created_at->diffForHumans() : '-' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $prestasi->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                            {{ ucfirst($prestasi->status_verifikasi) }}
                        </span>
                    </div>
                </div>
                @endforeach
                <div class="text-center mt-3">
                    <a href="{{ route('siswa.view-data-sendiri') }}" class="btn btn-outline-success btn-sm">
                        Lihat Semua Prestasi
                    </a>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fa fa-info-circle fa-3x text-info mb-3"></i>
                    <p class="text-muted">Belum ada prestasi</p>
                    <small class="text-info">Terus berprestasi!</small>
                </div>
            @endif
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
// Real-time clock update
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