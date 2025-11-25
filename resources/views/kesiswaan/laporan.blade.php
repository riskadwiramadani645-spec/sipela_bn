@extends('layouts.app')

@section('title', 'Laporan Kesiswaan - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan - Kesiswaan</h5>
                    <p class="mb-0">Generate laporan pembinaan dan monitoring siswa</p>
                </div>
                <div class="text-end">

                    <div class="small real-time-clock">{{ now()->format('H:i:s') }} WIB</div>
                    <div class="small text-light">Report Generator</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Form -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-cog me-2"></i>Konfigurasi Laporan Kesiswaan</h6>
            <form id="exportForm" action="{{ route('kesiswaan.laporan.export') }}" method="POST" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="pembinaan">Laporan Pembinaan Siswa</option>
                            <option value="kedisiplinan">Laporan Kedisiplinan</option>
                            <option value="monitoring">Monitoring Sanksi</option>
                            <option value="konseling">Hasil Konseling</option>
                            <option value="progress">Progress Siswa</option>
                            <option value="rekap_kelas">Rekap Per Kelas</option>
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opsi Tambahan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" value="1" checked>
                            <label class="form-check-label" for="include_charts">Sertakan Grafik/Chart</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_summary" name="include_summary" value="1" checked>
                            <label class="form-check-label" for="include_summary">Sertakan Ringkasan Kesiswaan</label>
                        </div>
                    </div>
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
                <h6 class="text-success">Laporan Pembinaan Siswa</h6>
                <p class="text-muted small">Data siswa dalam pembinaan dengan detail pelanggaran dan progress perkembangan.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-warning">Laporan Kedisiplinan</h6>
                <p class="text-muted small">Tingkat kedisiplinan per kelas dan jurusan dengan analisis tren pelanggaran.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-info">Monitoring Sanksi</h6>
                <p class="text-muted small">Status pelaksanaan sanksi dan kepatuhan siswa terhadap sanksi yang diberikan.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-danger">Hasil Konseling & Progress</h6>
                <p class="text-muted small">Catatan konseling, tindak lanjut, dan perkembangan perilaku siswa.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Quick Reference - Data Terkini</h6>
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="h4 text-warning">{{ $stats['pelanggaran_bulan_ini'] ?? 0 }}</div>
                    <div class="text-muted">Pelanggaran Bulan Ini</div>
                </div>
                <div class="col-6 mb-3">
                    <div class="h4 text-info">{{ $stats['siswa_pembinaan'] ?? 0 }}</div>
                    <div class="text-muted">Siswa Pembinaan</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-success">{{ $stats['sanksi_tuntas'] ?? 0 }}</div>
                    <div class="text-muted">Sanksi Tuntas</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-danger">{{ $stats['sanksi_terlambat'] ?? 0 }}</div>
                    <div class="text-muted">Sanksi Terlambat</div>
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
// Real-time clock and date update
function updateDateTime() {
    const now = new Date();
    
    // Update time
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
    
    // Update date
    const dateOptions = {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    };
    const dateString = now.toLocaleDateString('id-ID', dateOptions);
    const dateElements = document.querySelectorAll('.real-time-date');
    dateElements.forEach(el => el.textContent = dateString);
}

setInterval(updateDateTime, 1000);
updateDateTime();

function previewReport() {
    const form = document.getElementById('exportForm');
    const type = document.getElementById('type').value;
    const periode = document.getElementById('periode').value;
    
    if (!type || !periode) {
        alert('Mohon lengkapi jenis laporan dan periode terlebih dahulu');
        return;
    }
    
    alert('Preview laporan ' + type + ' untuk periode ' + periode + ' akan ditampilkan');
}

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
    
    // Re-enable button after 2 seconds
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
});
</script>
@endpush