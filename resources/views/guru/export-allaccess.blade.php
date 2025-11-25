@extends('layouts.app')

@section('title', 'Export Laporan - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan Menyeluruh</h5>
                    <p class="mb-0">Generate laporan lengkap sistem pelanggaran siswa</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small text-light">📊 All-in-One Report</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Statistik Sistem</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="bg-info text-white rounded p-3 text-center">
                        <i class="fa fa-users fa-2x mb-2"></i>
                        <h5>{{ $stats['total_siswa'] ?? 0 }}</h5>
                        <small>Total Siswa</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-danger text-white rounded p-3 text-center">
                        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                        <h5>{{ $stats['total_pelanggaran'] ?? 0 }}</h5>
                        <small>Total Pelanggaran</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-success text-white rounded p-3 text-center">
                        <i class="fa fa-trophy fa-2x mb-2"></i>
                        <h5>{{ $stats['total_prestasi'] ?? 0 }}</h5>
                        <small>Total Prestasi</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-warning text-white rounded p-3 text-center">
                        <i class="fa fa-gavel fa-2x mb-2"></i>
                        <h5>{{ $stats['total_sanksi'] ?? 0 }}</h5>
                        <small>Total Sanksi</small>
                    </div>
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
            <form id="exportForm" action="{{ route('guru.wali-kelas.export.process') }}" method="POST" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="pelanggaran-kelas">Laporan Pelanggaran Kelas</option>
                            <option value="prestasi-kelas">Laporan Prestasi Kelas</option>
                            <option value="sanksi-kelas">Laporan Sanksi Kelas</option>
                            <option value="data-kelas">Data Kelas Saya</option>
                            <option value="rekap-kedisiplinan">Rekap Kedisiplinan</option>
                            <option value="progress">Progress Kelas</option>
                            <option value="ringkasan">Ringkasan Input Saya</option>
                            <option value="pelanggaran_saya">Data Pelanggaran Saya</option>
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



@endsection

@push('scripts')
<script>
document.getElementById('exportForm').addEventListener('submit', function(e) {
    const type = document.getElementById('type').value;
    const periode = document.getElementById('periode').value;
    
    if (!type || !periode) {
        e.preventDefault();
        alert('Mohon lengkapi jenis laporan dan periode terlebih dahulu');
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Generating PDF...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
});
</script>
@endpush