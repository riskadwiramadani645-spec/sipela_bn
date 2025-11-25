@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Kepala Sekolah</h6>
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
                        <i class="fa fa-user-tie fa-3x mb-2"></i>
                        <h6><span class="status-indicator status-online"></span>Kepala Sekolah <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Executive Level Access</small>
                        <div class="mt-2">
                            <small class="badge bg-info">Role: Kepala Sekolah</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-chart-line fa-3x mb-2"></i>
                        <h6><span class="status-indicator status-online"></span>Monitoring All <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Comprehensive School Overview</small>
                        <div class="mt-2">
                            <small class="text-light">Update: <span class="real-time-clock">{{ now()->format('H:i') }} WIB</span></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-clipboard-check fa-3x mb-2"></i>
                        <h6><span class="status-indicator status-online"></span>Kebijakan Disiplin 
                            <span class="badge bg-success">✓ EFEKTIF</span>
                        </h6>
                        <small>Policy Management</small>
                        <div class="mt-2">
                            <a href="{{ route('kepala-sekolah.laporan') }}" class="btn btn-sm btn-success">
                                <i class="fa fa-file-export"></i> Export Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mt-1">
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-users fa-3x text-info"></i>
            <div class="ms-3">
                <p class="mb-2">Total Siswa</p>
                <h6 class="mb-0">{{ number_format($totalSiswa) }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-exclamation-triangle fa-3x text-danger"></i>
            <div class="ms-3">
                <p class="mb-2">Total Pelanggaran</p>
                <h6 class="mb-0">{{ number_format($totalPelanggaran) }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-trophy fa-3x text-warning"></i>
            <div class="ms-3">
                <p class="mb-2">Total Prestasi</p>
                <h6 class="mb-0">{{ number_format($totalPrestasi) }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-calendar-alt fa-3x text-info"></i>
            <div class="ms-3">
                <p class="mb-2">Bulan Ini</p>
                <h6 class="mb-0">{{ number_format($pelanggaranBulanIni) }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Executive Summary Panel -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-info text-white rounded p-4">
            <h6 class="mb-3"><i class="fa fa-chart-bar me-2"></i>Executive Summary - School Performance</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-graduation-cap fa-2x mb-2"></i>
                        <h6>Prestasi Semester</h6>
                        <span class="badge bg-success">{{ number_format($prestasiSemester) }}</span>
                        <small class="d-block text-light">Achievement Rate</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-gavel fa-2x mb-2"></i>
                        <h6>Total Sanksi</h6>
                        <span class="badge bg-warning">{{ number_format($totalSanksi) }}</span>
                        <small class="d-block text-light">Disciplinary Actions</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-percentage fa-2x mb-2"></i>
                        <h6>Efektivitas Sanksi</h6>
                        <span class="badge bg-success">{{ $totalPelanggaran > 0 ? round(($totalSanksi / $totalPelanggaran) * 100, 1) : 0 }}%</span>
                        <small class="d-block text-light">Success Rate</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-shield-alt fa-2x mb-2"></i>
                        <h6>Policy Status</h6>
                        <span class="badge bg-success">✓ AKTIF</span>
                        <small class="d-block text-light">Discipline Policy</small>
                    </div>
                </div>
            </div>
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
                        <h6 class="mb-0">{{ $pelanggaran->siswa->nama_siswa ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</small>
                        <small class="text-muted d-block">{{ $pelanggaran->created_at ? $pelanggaran->created_at->diffForHumans() : 'Tanggal tidak tersedia' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $pelanggaran->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                            {{ ucfirst($pelanggaran->status_verifikasi) }}
                        </span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada pelanggaran terbaru</p>
                    <small class="text-success">Situasi kondusif</small>
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
                        <h6 class="mb-0">{{ $prestasi->siswa->nama_siswa ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $prestasi->jenisPrestasi->nama_prestasi ?? 'N/A' }}</small>
                        <small class="text-muted d-block">{{ $prestasi->created_at ? $prestasi->created_at->diffForHumans() : 'Tanggal tidak tersedia' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $prestasi->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                            {{ ucfirst($prestasi->status_verifikasi) }}
                        </span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fa fa-info-circle fa-3x text-info mb-3"></i>
                    <p class="text-muted">Tidak ada prestasi terbaru</p>
                    <small class="text-info">Belum ada pencapaian baru</small>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Executive Reports Preview -->
<div class="row g-4 mt-1">
    <div class="col-lg-4">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-3"><i class="fa fa-shield-alt me-2 text-danger"></i>Efektivitas Kebijakan Disiplin</h6>
            @php
                $totalPelanggaran = \App\Models\Pelanggaran::count();
                $pelanggaranDenganSanksi = \App\Models\Pelanggaran::whereHas('sanksi')->count();
                $efektivitasSanksi = $totalPelanggaran > 0 ? round(($pelanggaranDenganSanksi / $totalPelanggaran) * 100, 1) : 0;
                $tingkatKedisiplinan = $totalSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $totalSiswa * 5)) : 100;
            @endphp
            <div class="row text-center mb-3">
                <div class="col-6">
                    <div class="h5 text-{{ $efektivitasSanksi >= 75 ? 'success' : ($efektivitasSanksi >= 50 ? 'warning' : 'danger') }}">{{ $efektivitasSanksi }}%</div>
                    <small class="text-muted">Efektivitas Sanksi</small>
                </div>
                <div class="col-6">
                    <div class="h5 text-{{ $tingkatKedisiplinan >= 80 ? 'success' : ($tingkatKedisiplinan >= 60 ? 'warning' : 'danger') }}">{{ round($tingkatKedisiplinan, 1) }}%</div>
                    <small class="text-muted">Tingkat Kedisiplinan</small>
                </div>
            </div>
            <a href="{{ route('kepala-sekolah.laporan') }}?type=kebijakan_efektivitas" class="btn btn-outline-danger btn-sm w-100">
                <i class="fa fa-file-pdf me-1"></i>Export Laporan
            </a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-3"><i class="fa fa-gavel me-2 text-primary"></i>Sanksi Detail</h6>
            @php
                $totalSanksi = \App\Models\Sanksi::count();
                $sanksiAktif = \App\Models\Sanksi::whereNotNull('tanggal_mulai')->whereNull('tanggal_selesai')->count();
                $sanksiSelesai = \App\Models\Sanksi::whereNotNull('tanggal_selesai')->count();
                $sanksiPending = $totalSanksi - $sanksiAktif - $sanksiSelesai;
            @endphp
            <div class="row text-center mb-3">
                <div class="col-4">
                    <div class="h6 text-success">{{ $sanksiSelesai }}</div>
                    <small class="text-muted">Selesai</small>
                </div>
                <div class="col-4">
                    <div class="h6 text-warning">{{ $sanksiAktif }}</div>
                    <small class="text-muted">Aktif</small>
                </div>
                <div class="col-4">
                    <div class="h6 text-danger">{{ $sanksiPending }}</div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
            <a href="{{ route('kepala-sekolah.laporan') }}?type=sanksi_detail" class="btn btn-outline-primary btn-sm w-100">
                <i class="fa fa-file-pdf me-1"></i>Export Laporan
            </a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-3"><i class="fa fa-chart-line me-2 text-danger"></i>Progress Siswa</h6>
            @php
                $siswaBerprestasi = \App\Models\Siswa::whereHas('prestasi')->count();
                
                // Hitung siswa bermasalah (>3 pelanggaran) dengan subquery
                $siswaBermasalah = \DB::select("
                    SELECT COUNT(DISTINCT siswa_id) as total 
                    FROM (
                        SELECT siswa_id, COUNT(*) as jumlah_pelanggaran 
                        FROM pelanggaran 
                        GROUP BY siswa_id 
                        HAVING jumlah_pelanggaran > 3
                    ) as temp
                ")[0]->total ?? 0;
                
                // Hitung siswa perlu perhatian (1-3 pelanggaran)
                $siswaPerluPerhatian = \DB::select("
                    SELECT COUNT(DISTINCT siswa_id) as total 
                    FROM (
                        SELECT siswa_id, COUNT(*) as jumlah_pelanggaran 
                        FROM pelanggaran 
                        GROUP BY siswa_id 
                        HAVING jumlah_pelanggaran BETWEEN 1 AND 3
                    ) as temp
                ")[0]->total ?? 0;
            @endphp
            <div class="row text-center mb-3">
                <div class="col-4">
                    <div class="h6 text-success">{{ $siswaBerprestasi }}</div>
                    <small class="text-muted">Berprestasi</small>
                </div>
                <div class="col-4">
                    <div class="h6 text-warning">{{ $siswaPerluPerhatian }}</div>
                    <small class="text-muted">Perlu Perhatian</small>
                </div>
                <div class="col-4">
                    <div class="h6 text-danger">{{ $siswaBermasalah }}</div>
                    <small class="text-muted">Bermasalah</small>
                </div>
            </div>
            <a href="{{ route('kepala-sekolah.laporan') }}?type=progress_siswa" class="btn btn-outline-danger btn-sm w-100">
                <i class="fa fa-file-pdf me-1"></i>Export Laporan
            </a>
        </div>
    </div>
</div>

<!-- Top Pelanggaran Chart -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-chart-bar me-2"></i>Top 5 Jenis Pelanggaran</h6>
            <canvas id="topPelanggaranChart" height="100"></canvas>
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

.card-hover:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}

.status-online { background-color: #28a745; }
.status-warning { background-color: #ffc107; }
.status-danger { background-color: #dc3545; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Top Pelanggaran Chart
const ctx = document.getElementById('topPelanggaranChart').getContext('2d');
const topPelanggaranChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($topPelanggaran as $item)
                '{{ $item->nama_pelanggaran }}',
            @endforeach
        ],
        datasets: [{
            label: 'Jumlah Pelanggaran',
            data: [
                @foreach($topPelanggaran as $item)
                    {{ $item->total }},
                @endforeach
            ],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Top 5 Jenis Pelanggaran'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

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

// Update clock setiap detik
setInterval(updateClock, 1000);
updateClock(); // Initial call

// Auto refresh dashboard setiap 10 menit untuk data terbaru
setInterval(function() {
    location.reload();
}, 600000);
</script>
@endpush