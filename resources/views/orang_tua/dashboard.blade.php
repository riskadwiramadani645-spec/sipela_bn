@extends('layouts.app')

@section('title', 'Dashboard Orang Tua - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Orang Tua</h6>
                    <p class="mb-0">SMK Bakti Nusantara 666 - Monitoring Anak</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->setTimezone('Asia/Jakarta')->format('d M Y') }}</div>
                    <div class="small real-time-clock">{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
            @if($anak)
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    @if($anak->foto && file_exists(public_path('uploads/profiles/' . $anak->foto)))
                        <div class="rounded-circle d-flex align-items-center justify-content-center overflow-hidden" 
                             style="width: 100px; height: 100px; border: 4px solid white; margin: 0 auto;">
                            <img src="{{ asset('uploads/profiles/' . $anak->foto) }}" alt="Foto {{ $anak->nama_siswa }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @else
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; margin: 0 auto;">
                            <i class="fa fa-user fa-3x text-primary"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <h4 class="mb-1">{{ $anak->nama_siswa }}</h4>
                    <p class="mb-1"><i class="fa fa-id-card me-2"></i>NIS: {{ $anak->nis }}</p>
                    <p class="mb-1"><i class="fa fa-school me-2"></i>{{ $anak->kelas->nama_kelas ?? 'Kelas tidak ditemukan' }}</p>
                    <p class="mb-1"><i class="fa fa-heart me-2"></i>Hubungan: {{ $anak->orangTua->first()->hubungan ?? 'Orang Tua' }}</p>
                    <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>SISWA AKTIF</span>
                </div>
            </div>
            @else
            <div class="text-center">
                <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                <h5>Data Anak Tidak Ditemukan</h5>
                <p class="mb-0">Hubungi admin sekolah untuk verifikasi data anak Anda</p>
            </div>
            @endif
        </div>
    </div>
</div>

@if($anak)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Statistik {{ $anak->nama_siswa }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-xl-4">
                        <div class="bg-danger rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-exclamation-triangle fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Pelanggaran</p>
                                <h6 class="mb-0">{{ $totalPelanggaran }}</h6>
                                <small>Kasus pelanggaran</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="bg-success rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-trophy fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Prestasi</p>
                                <h6 class="mb-0">{{ $totalPrestasi }}</h6>
                                <small>Prestasi yang diraih</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="bg-warning rounded d-flex align-items-center justify-content-between p-4 text-white">
                            <i class="fa fa-gavel fa-3x"></i>
                            <div class="ms-3">
                                <p class="mb-2">Sanksi Aktif</p>
                                <h6 class="mb-0">{{ $sanksiAktif }}</h6>
                                <small>Perlu diselesaikan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-bolt me-2"></i>Menu Utama Orang Tua</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('orang-tua.view-data-anak') }}" class="btn btn-info w-100 py-4 position-relative">
                            <i class="fa fa-child fa-3x d-block mb-2"></i>
                            <strong>Data Anak Lengkap</strong>
                            <small class="d-block">Pelanggaran, Prestasi & Sanksi</small>
                            @if($sanksiAktif > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $sanksiAktif }}
                            </span>
                            @endif
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('profile.index') }}" class="btn btn-secondary w-100 py-4">
                            <i class="fa fa-user-circle fa-3x d-block mb-2"></i>
                            <strong>Profile Saya</strong>
                            <small class="d-block">Data & kontak orang tua</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('orang-tua.laporan') }}" class="btn btn-success w-100 py-4">
                            <i class="fa fa-file-pdf fa-3x d-block mb-2"></i>
                            <strong>Export Laporan</strong>
                            <small class="d-block">⚠️ Limited Access</small>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<div class="row g-4 mt-3">
    <div class="col-md-6">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0 text-danger"><i class="fa fa-exclamation-triangle me-2"></i>Pelanggaran Terbaru</h6>
            </div>
            <div class="card-body">
                @forelse($pelanggaranTerbaru as $p)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-danger bg-opacity-10 border border-danger rounded">
                    <div>
                        <strong class="text-white">{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</strong>
                        <small class="d-block text-light">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') ?? 'N/A' }}</small>
                    </div>
                    <span class="badge bg-{{ $p->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                        {{ ucfirst($p->status_verifikasi ?? 'pending') }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada pelanggaran terbaru</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0 text-success"><i class="fa fa-trophy me-2"></i>Prestasi Terbaru</h6>
            </div>
            <div class="card-body">
                @forelse($prestasiTerbaru as $p)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-success bg-opacity-10 border border-success rounded">
                    <div>
                        <strong class="text-white">{{ $p->jenisPrestasi->nama_prestasi ?? 'N/A' }}</strong>
                        <small class="d-block text-light">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') ?? 'N/A' }} - {{ $p->tingkat ?? 'N/A' }}</small>
                    </div>
                    <span class="badge bg-success">{{ $p->poin ?? 0 }} Poin</span>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada prestasi terbaru</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
.rounded-circle {
    border-radius: 50% !important;
}
.rounded-circle img {
    border-radius: 50% !important;
    object-fit: cover !important;
}
</style>
@endpush

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds} WIB`;
    
    const clockElement = document.querySelector('.real-time-clock');
    if (clockElement) {
        clockElement.textContent = timeString;
    }
}

// Update clock immediately and then every second
updateClock();
setInterval(updateClock, 1000);
</script>
@endpush