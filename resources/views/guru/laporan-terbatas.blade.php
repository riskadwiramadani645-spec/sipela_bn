@extends('layouts.app')

@section('title', 'Laporan Terbatas - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan - Guru</h5>
                    <p class="mb-0">Generate laporan data yang Anda input sendiri</p>
                </div>
                <div class="text-end">
                    <div class="small text-light">Limited Access</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Warning -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Akses Terbatas:</strong> Anda hanya dapat mengexport data pelanggaran dan prestasi yang Anda input sendiri.
        </div>
    </div>
</div>

<!-- Export Form -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-cog me-2"></i>Konfigurasi Laporan Guru</h6>
            <form id="exportForm" action="{{ route('guru.laporan.export') }}" method="POST" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="ringkasan">Ringkasan Input Saya</option>
                            <option value="pelanggaran">Data Pelanggaran Saya</option>
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
                        <label class="form-label">Status Verifikasi</label>
                        <select class="form-control" name="status">
                            <option value="">Semua Status</option>
                            <option value="menunggu">Menunggu Verifikasi</option>
                            <option value="diverifikasi">Sudah Diverifikasi</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Filter Kelas</label>
                        <select class="form-control" name="kelas_id">
                            <option value="">Semua Kelas</option>
                            @php
                                $kelas = \App\Models\Kelas::all();
                            @endphp
                            @foreach($kelas as $k)
                                <option value="{{ $k->kelas_id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
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
                <h6 class="text-warning">Ringkasan Input Saya</h6>
                <p class="text-muted small">Rekap statistik dan pelanggaran terbanyak yang telah Anda input.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-danger">Data Pelanggaran Saya</h6>
                <p class="text-muted small">Data pelanggaran siswa yang Anda input dengan status verifikasi.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Quick Reference - Data Saya</h6>
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="h4 text-danger">{{ $totalPelanggaran ?? 0 }}</div>
                    <div class="text-muted">Total Pelanggaran Input</div>
                </div>
                <div class="col-6 mb-3">
                    <div class="h4 text-info">{{ $bulanIni ?? 0 }}</div>
                    <div class="text-muted">Input Bulan Ini</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-warning">{{ $menungguVerifikasi ?? 0 }}</div>
                    <div class="text-muted">Menunggu Verifikasi</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-success">{{ $diverifikasi ?? 0 }}</div>
                    <div class="text-muted">Sudah Diverifikasi</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>

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