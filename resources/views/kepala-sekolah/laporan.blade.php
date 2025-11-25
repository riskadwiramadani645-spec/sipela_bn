@extends('layouts.app')

@section('title', 'Export Laporan - Kepala Sekolah')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan - Executive Reports</h5>
                    <p class="mb-0">Generate laporan komprehensif untuk kepala sekolah</p>
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
            <h6 class="mb-4"><i class="fa fa-cog me-2"></i>Konfigurasi Laporan</h6>
            <form id="exportForm" action="{{ route('kepala-sekolah.laporan.export') }}" method="POST" target="_blank">
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opsi Tambahan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" value="1" checked>
                            <label class="form-check-label" for="include_charts">Sertakan Grafik/Chart</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_summary" name="include_summary" value="1" checked>
                            <label class="form-check-label" for="include_summary">Sertakan Executive Summary</label>
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
                <h6 class="text-success">Executive Summary</h6>
                <p class="text-muted small">Ringkasan eksekutif dengan key metrics, trend analysis, dan rekomendasi kebijakan untuk kepala sekolah.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-warning">Laporan Pelanggaran Detail</h6>
                <p class="text-muted small">Detail lengkap semua pelanggaran siswa dengan analisis kategori, sanksi, dan efektivitas penanganan.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-info">Laporan Prestasi Detail</h6>
                <p class="text-muted small">Kompilasi prestasi siswa dengan breakdown per tingkat, kategori, dan analisis pencapaian sekolah.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-primary">Laporan Sanksi Detail</h6>
                <p class="text-muted small">Detail pelaksanaan sanksi dengan tingkat efektivitas dan follow-up yang diperlukan.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-danger">Progress Siswa</h6>
                <p class="text-muted small">Monitoring perkembangan siswa secara individual dengan riwayat pelanggaran dan prestasi.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-dark">Rekap Per Kelas</h6>
                <p class="text-muted small">Ringkasan statistik per kelas untuk evaluasi kinerja wali kelas dan siswa.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-danger">Efektivitas Kebijakan Disiplin</h6>
                <p class="text-muted small">Analisis mendalam efektivitas kebijakan disiplin sekolah dengan rekomendasi perbaikan.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Quick Reference - Data Terkini</h6>
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="h4 text-primary">{{ \App\Models\Siswa::count() }}</div>
                    <div class="text-muted">Total Siswa</div>
                </div>
                <div class="col-6 mb-3">
                    <div class="h4 text-warning">{{ \App\Models\Pelanggaran::count() }}</div>
                    <div class="text-muted">Total Pelanggaran</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-success">{{ \App\Models\Prestasi::count() }}</div>
                    <div class="text-muted">Total Prestasi</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-info">{{ \App\Models\BimbinganKonseling::count() }}</div>
                    <div class="text-muted">Total Sesi BK</div>
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
    
    // Re-enable button after 3 seconds (in case of error)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
 
</script>
@endpush