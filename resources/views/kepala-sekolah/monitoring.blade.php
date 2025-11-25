@extends('layouts.app')

@section('title', 'Monitoring All - Kepala Sekolah')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Monitoring All - Comprehensive School Overview</h5>
                    <p class="mb-0">Real-time monitoring semua aspek sekolah</p>
                </div>
                <div class="text-end">

                    <div class="small real-time-clock">{{ now()->format('H:i:s') }} WIB</div>
                    <div class="small text-light">Live Monitoring</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Siswa Statistics -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-users me-2"></i>Statistik Siswa</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-primary">{{ number_format($stats['siswa']['total']) }}</div>
                        <div class="text-muted">Total Siswa</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-success">{{ number_format($stats['siswa']['aktif']) }}</div>
                        <div class="text-muted">Siswa Aktif</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Distribusi Per Kelas:</h6>
                    @foreach($stats['siswa']['per_kelas'] as $kelas)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $kelas->nama_kelas }}</span>
                        <span class="badge bg-primary">{{ $kelas->siswa_count }} siswa</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pelanggaran Statistics -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-exclamation-triangle me-2"></i>Statistik Pelanggaran</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-warning">{{ number_format($stats['pelanggaran']['total']) }}</div>
                        <div class="text-muted">Total Pelanggaran</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-info">{{ number_format($stats['pelanggaran']['bulan_ini']) }}</div>
                        <div class="text-muted">Bulan Ini</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-danger">{{ number_format($stats['pelanggaran']['belum_sanksi']) }}</div>
                        <div class="text-muted">Belum Sanksi</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Per Kategori:</h6>
                    @foreach($stats['pelanggaran']['by_kategori'] as $kategori)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ ucfirst($kategori->kategori) }}</span>
                        <span class="badge bg-warning">{{ $kategori->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prestasi Statistics -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-trophy me-2"></i>Statistik Prestasi</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-success">{{ number_format($stats['prestasi']['total']) }}</div>
                        <div class="text-muted">Total Prestasi</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h2 text-info">{{ number_format($stats['prestasi']['semester_ini']) }}</div>
                        <div class="text-muted">Semester Ini</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Per Tingkat:</h6>
                    @foreach($stats['prestasi']['by_tingkat'] as $tingkat)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ ucfirst($tingkat->tingkat) }}</span>
                        <span class="badge bg-success">{{ $tingkat->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BK Statistics -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-comments me-2"></i>Statistik Bimbingan Konseling</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <div class="h2 text-info">{{ number_format($stats['bk']['total']) }}</div>
                        <div class="text-muted">Total Sesi BK</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <div class="h2 text-primary">{{ number_format($stats['bk']['bulan_ini']) }}</div>
                        <div class="text-muted">Sesi Bulan Ini</div>
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
// Real-time clock update
function updateClock() {
    const now = new Date();
    const options = {
        timeZone: 'Asia/Jakarta',
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    const timeString = now.toLocaleTimeString('id-ID', options);
    const clockElements = document.querySelectorAll('.real-time-clock');
    clockElements.forEach(el => el.textContent = timeString + ' WIB');
}

// Update clock setiap detik
setInterval(updateClock, 1000);
updateClock(); // Initial call
</script>
@endpush