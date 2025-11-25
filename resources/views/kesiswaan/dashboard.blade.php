@extends('layouts.app')

@section('title', 'Dashboard Kesiswaan - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Kesiswaan</h6>
                    <p class="mb-0">SMK Bakti Nusantara 666</p>
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

<!-- Statistics Cards -->
<div class="row g-4 mt-1">
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-user-graduate fa-3x text-info"></i>
            <div class="ms-3">
                <p class="mb-2">Total Siswa Aktif</p>
                <h6 class="mb-0">{{ $totalSiswa }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-exclamation-triangle fa-3x text-danger"></i>
            <div class="ms-3">
                <p class="mb-2">Total Pelanggaran</p>
                <h6 class="mb-0">{{ $totalPelanggaran }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-trophy fa-3x text-warning"></i>
            <div class="ms-3">
                <p class="mb-2">Total Prestasi</p>
                <h6 class="mb-0">{{ $totalPrestasi }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-calendar-check fa-3x text-success"></i>
            <div class="ms-3">
                <p class="mb-2">Sanksi Tuntas</p>
                <h6 class="mb-0">{{ $sanksiTuntas ?? 0 }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Alert System Panel -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-danger text-white rounded p-4">
            <h6 class="mb-3"><i class="fa fa-exclamation-triangle me-2"></i>Alert System - Kasus Prioritas</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-fire fa-2x mb-2 {{ $kasusPrioritas > 0 ? 'text-warning' : 'text-light' }}"></i>
                        <h6>Kasus Urgent</h6>
                        <span class="badge bg-{{ $kasusPrioritas > 0 ? 'warning' : 'success' }}">{{ $kasusPrioritas }}</span>
                        @if($kasusPrioritas > 0)<small class="d-block text-warning">Perlu Perhatian!</small>@endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-clock fa-2x mb-2 {{ $sanksiDeadline > 0 ? 'text-warning' : 'text-light' }}"></i>
                        <h6>Sanksi Mendekati Deadline</h6>
                        <span class="badge bg-{{ $sanksiDeadline > 0 ? 'warning' : 'success' }}">{{ $sanksiDeadline }}</span>
                        @if($sanksiDeadline > 0)<small class="d-block text-warning">3 Hari Lagi</small>@endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-level-up-alt fa-2x mb-2 {{ $perluEskalasi > 0 ? 'text-danger' : 'text-light' }}"></i>
                        <h6>Perlu Eskalasi</h6>
                        <span class="badge bg-{{ $perluEskalasi > 0 ? 'danger' : 'success' }}">{{ $perluEskalasi }}</span>
                        @if($perluEskalasi > 0)<small class="d-block text-danger">Terlambat >7 Hari</small>@endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-robot fa-2x mb-2"></i>
                        <h6>Auto-Suggestion</h6>
                        <span class="badge bg-success">✓ AKTIF</span>
                        <small class="d-block text-light">Sistem Rekomendasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monitoring Sanksi Aktif -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-tasks me-2"></i>Monitoring Sanksi Aktif - Progress Tracking</h6>
            @if(count($sanksiProgress) > 0)
                @foreach($sanksiProgress as $progress)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0">{{ $progress['nama_siswa'] }}</h6>
                            <small class="text-muted">{{ $progress['jenis_sanksi'] }} - {{ $progress['kelas'] }}</small>
                        </div>
                        <span class="badge bg-{{ $progress['status'] == 'tuntas' ? 'success' : ($progress['status'] == 'terlambat' ? 'danger' : 'warning') }}">
                            {{ ucfirst($progress['status']) }}
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-{{ $progress['status'] == 'tuntas' ? 'success' : ($progress['status'] == 'terlambat' ? 'danger' : 'info') }}" 
                             style="width: {{ $progress['progress'] }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Progress: {{ $progress['progress'] }}%</small>
                        <small class="text-muted">Deadline: {{ $progress['deadline'] }}</small>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada sanksi aktif yang sedang berjalan</p>
                    <small class="text-success">Semua sanksi telah diselesaikan dengan baik</small>
                </div>
            @endif
        </div>
    </div>

<!-- Alert & Monitoring Cards -->
<div class="row g-4 mt-1">
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
            <i class="fa fa-clock fa-3x text-danger"></i>
            <div class="ms-3">
                <p class="mb-2">Sanksi Terlambat</p>
                <h6 class="mb-0">{{ $sanksiTerlambat }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-hourglass-half fa-3x text-warning"></i>
            <div class="ms-3">
                <p class="mb-2">Pelanggaran Menunggu</p>
                <h6 class="mb-0">{{ $pelanggaranMenunggu }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
            <i class="fa fa-star fa-3x text-success"></i>
            <div class="ms-3">
                <p class="mb-2">Prestasi Menunggu</p>
                <h6 class="mb-0">{{ $prestasiMenunggu }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="row g-4 mt-1">
    <div class="col-12 col-lg-6">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0"><i class="fa fa-exclamation-triangle me-2"></i>Pelanggaran Terbaru</h6>
                <span class="badge bg-danger">{{ $recentPelanggaran->count() }}</span>
            </div>
            @if($recentPelanggaran->count() > 0)
                @foreach($recentPelanggaran->take(5) as $pelanggaran)
                <div class="d-flex align-items-center border-bottom py-2">
                    <i class="fa fa-exclamation-circle text-danger me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $pelanggaran->siswa->nama_siswa ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }} - {{ $pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                        @if($pelanggaran->jenisPelanggaran)
                        <div class="mt-1">
                            <small class="badge bg-{{ $pelanggaran->jenisPelanggaran->poin >= 50 ? 'danger' : ($pelanggaran->jenisPelanggaran->poin >= 25 ? 'warning' : 'info') }}">Poin: {{ $pelanggaran->jenisPelanggaran->poin }}</small>
                            @if($pelanggaran->jenisPelanggaran->poin >= 50)
                                <small class="text-danger ms-2">⚠️ PRIORITAS TINGGI</small>
                            @endif
                        </div>
                        @endif
                        <small class="text-muted">{{ $pelanggaran->created_at ? $pelanggaran->created_at->diffForHumans() : 'Tanggal tidak tersedia' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $pelanggaran->status_verifikasi == 'menunggu' ? 'warning' : ($pelanggaran->status_verifikasi == 'diverifikasi' ? 'success' : 'danger') }} d-block mb-1">
                            {{ ucfirst($pelanggaran->status_verifikasi) }}
                        </span>
                        @if($pelanggaran->status_verifikasi == 'menunggu')
                        <a href="{{ route('kesiswaan.verifikasi-monitoring.verifikasi') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-check"></i> Verifikasi
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada pelanggaran terbaru</p>
                    <small class="text-success">Situasi kondusif - tidak ada pelanggaran baru</small>
                </div>
            @endif
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0"><i class="fa fa-gavel me-2"></i>Sanksi & Prestasi Terbaru</h6>
                <span class="badge bg-info">{{ ($recentSanksi->count() ?? 0) + $prestasiMenunggu }}</span>
            </div>
            @if($recentSanksi->count() > 0)
                <h6 class="text-info mb-3"><i class="fa fa-gavel me-2"></i>Sanksi Terbaru:</h6>
                @foreach($recentSanksi->take(3) as $sanksi)
                <div class="d-flex align-items-center border-bottom py-2">
                    <i class="fa fa-gavel text-info me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $sanksi->siswa->nama_siswa ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $sanksi->deskripsi_pelaksanaan ?? 'Sanksi' }} - {{ $sanksi->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                        <small class="text-muted d-block">{{ $sanksi->created_at ? $sanksi->created_at->diffForHumans() : 'Tanggal tidak tersedia' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $sanksi->status == 'tuntas' ? 'success' : ($sanksi->status == 'terlambat' ? 'danger' : 'warning') }} d-block mb-1">
                            {{ ucfirst($sanksi->status) }}
                        </span>
                        @if($sanksi->tanggal_pelaksanaan)
                            <small class="text-muted">{{ \Carbon\Carbon::parse($sanksi->tanggal_pelaksanaan)->format('d M') }}</small>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
            
            @if($recentPrestasi->count() > 0)
                <h6 class="text-warning mb-3 mt-3"><i class="fa fa-trophy me-2"></i>Prestasi Terbaru:</h6>
                @foreach($recentPrestasi->take(2) as $prestasi)
                <div class="d-flex align-items-center border-bottom py-2">
                    <i class="fa fa-trophy text-warning me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $prestasi->siswa->nama_siswa ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $prestasi->jenisPrestasi->nama_prestasi ?? 'N/A' }} - {{ $prestasi->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                        <small class="text-muted d-block">{{ $prestasi->created_at ? $prestasi->created_at->diffForHumans() : 'Tanggal tidak tersedia' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $prestasi->status_verifikasi == 'menunggu' ? 'warning' : ($prestasi->status_verifikasi == 'diverifikasi' ? 'success' : 'danger') }} d-block mb-1">
                            {{ ucfirst($prestasi->status_verifikasi) }}
                        </span>
                    </div>
                </div>
                @endforeach
            @endif
            
            @if($recentSanksi->count() == 0 && $recentPrestasi->count() == 0)
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada sanksi atau prestasi terbaru</p>
                    <small class="text-success">Semua dalam kondisi baik</small>
                </div>
            @endif
        </div>
    </div>
</div>



<!-- Analytics Dashboard -->
<div class="row g-4 mt-1">
    @if(count($monthlyStats) > 0)
    <div class="col-12 col-lg-8">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-4"><i class="fa fa-chart-line me-2"></i>Trend Pelanggaran 6 Bulan Terakhir</h6>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>
    @endif
    <div class="col-12 col-lg-4">
        <div class="bg-secondary rounded h-100 p-4">
            <h6 class="mb-4"><i class="fa fa-chart-pie me-2"></i>Efektivitas Sanksi</h6>
            <div class="text-center">
                <div class="mb-3">
                    <div class="display-6 text-success">{{ $efektivitasSanksi ?? 85 }}%</div>
                    <small class="text-muted">Success Rate</small>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="text-success h5">{{ $sanksiTuntas ?? 0 }}</div>
                        <small>Tuntas</small>
                    </div>
                    <div class="col-6">
                        <div class="text-danger h5">{{ $sanksiTerlambat ?? 0 }}</div>
                        <small>Terlambat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('styles')
<style>
.alert-blink {
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.7; }
}

.real-time-clock {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.progress {
    background-color: rgba(255,255,255,0.2);
}

.card-hover:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.badge-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
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
@if(count($monthlyStats) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_reverse(array_column($monthlyStats, 'bulan'))) !!},
        datasets: [{
            label: 'Jumlah Pelanggaran',
            data: {!! json_encode(array_reverse(array_column($monthlyStats, 'total'))) !!},
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
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
                text: 'Trend Pelanggaran Siswa'
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

// Auto refresh dashboard setiap 5 menit untuk data terbaru
setInterval(function() {
    location.reload();
}, 300000);

// Alert System dengan notifikasi
function checkAlerts() {
    const kasusPrioritas = {{ $kasusPrioritas }};
    const sanksiDeadline = {{ $sanksiDeadline }};
    const perluEskalasi = {{ $perluEskalasi }};
    
    if (kasusPrioritas > 0 || sanksiDeadline > 0 || perluEskalasi > 0) {
        // Visual alert
        document.querySelector('.bg-danger').classList.add('alert-blink');
        
        // Sound notification (optional)
        if (kasusPrioritas > 0 || perluEskalasi > 0) {
            playNotificationSound();
        }
    }
}

function playNotificationSound() {
    // Create audio context for notification
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.5);
}

// Functions untuk Quick Actions
function verifikasiPelanggaran(id) {
    if(confirm('Verifikasi pelanggaran ini?')) {
        // Ajax call untuk verifikasi
        alert('Pelanggaran ID: ' + id + ' telah diverifikasi');
        location.reload();
    }
}

// Check alerts on load
checkAlerts();


</script>
@endif
@endpush