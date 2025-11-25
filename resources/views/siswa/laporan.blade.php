@extends('layouts.app')

@section('title', 'Export Laporan - Siswa')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan - Terbatas</h5>
                    <p class="mb-0">Generate laporan pribadi untuk siswa</p>
                </div>
                <div class="text-end">
                    <div class="small real-time-clock">{{ now()->format('H:i:s') }} WIB</div>
                    <div class="small text-light">Personal Report</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Form -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-cog me-2"></i>Konfigurasi Laporan Pribadi</h6>
            <form id="exportForm" action="{{ route('siswa.laporan.export') }}" method="POST" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            @foreach($reportTypes as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="periode" class="form-label">Periode</label>
                        <select class="form-control" id="periode" name="periode" required>
                            <option value="hari_ini">Hari Ini</option>
                            <option value="minggu_ini">Minggu Ini</option>
                            <option value="bulan_ini" selected>Bulan Ini</option>
                            <option value="semester_ini">Semester Ini</option>
                        </select>
                    </div>
                    <input type="hidden" name="format" value="pdf">
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fa fa-file-pdf me-2"></i>Print/Download PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Types Description -->
<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Deskripsi Jenis Laporan</h6>
            <div class="mb-3">
                <h6 class="text-success">Riwayat Pribadi</h6>
                <p class="text-muted small">Laporan lengkap pelanggaran dan prestasi pribadi dalam periode tertentu.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-warning">Status Sanksi</h6>
                <p class="text-muted small">Detail sanksi yang pernah diterima dengan status pelaksanaan.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-info">Progress Kedisiplinan</h6>
                <p class="text-muted small">Perkembangan skor kedisiplinan dan rekomendasi perbaikan.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-primary">Sertifikat Prestasi</h6>
                <p class="text-muted small">Daftar prestasi yang telah diverifikasi sebagai portofolio.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Data Pribadi Terkini</h6>
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="h4 text-danger">{{ \App\Models\Pelanggaran::where('siswa_id', session('user')->siswa_id)->count() }}</div>
                    <div class="text-muted">Pelanggaran Saya</div>
                </div>
                <div class="col-6 mb-3">
                    <div class="h4 text-success">{{ \App\Models\Prestasi::where('siswa_id', session('user')->siswa_id)->count() }}</div>
                    <div class="text-muted">Prestasi Saya</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-warning">{{ \App\Models\Sanksi::whereHas('pelanggaran', function($q) { $q->where('siswa_id', session('user')->siswa_id); })->count() }}</div>
                    <div class="text-muted">Total Sanksi</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-info">{{ \App\Models\Sanksi::whereHas('pelanggaran', function($q) { $q->where('siswa_id', session('user')->siswa_id); })->whereNotNull('tanggal_mulai')->whereNull('tanggal_selesai')->count() }}</div>
                    <div class="text-muted">Sanksi Aktif</div>
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fa fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Laporan ini hanya berisi data pribadi Anda dan tidak dapat diakses oleh siswa lain.
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
}

setInterval(updateDateTime, 1000);
updateDateTime();

// Form submission with PDF download
document.getElementById('exportForm').addEventListener('submit', function(e) {
    const type = document.getElementById('type').value;
    const periode = document.getElementById('periode').value;
    
    if (!type || !periode) {
        e.preventDefault();
        alert('Mohon lengkapi jenis laporan dan periode terlebih dahulu');
        return;
    }
    
    // Show loading message
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Generating PDF...';
    submitBtn.disabled = true;
    
    // Re-enable button after 3 seconds
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endpush