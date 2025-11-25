@extends('layouts.app')

@section('title', 'Export Laporan - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan Anak</h5>
                    <p class="mb-0">Generate laporan data {{ $anak->nama_siswa }}</p>
                </div>
                <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
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
            <strong>Akses Terbatas:</strong> Anda hanya dapat mengexport data anak sendiri ({{ $anak->nama_siswa }} - {{ $anak->kelas->nama_kelas ?? 'N/A' }}).
        </div>
    </div>
</div>

<!-- Export Form -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4"><i class="fa fa-cog me-2"></i>Konfigurasi Laporan Orang Tua</h6>
            <form id="exportForm" action="{{ route('orang-tua.laporan.export') }}" method="POST" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="pelanggaran">Pelanggaran Anak</option>
                            <option value="prestasi">Prestasi Anak</option>
                            <option value="sanksi">Sanksi Anak</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="periode" class="form-label">Periode</label>
                        <select class="form-control" id="periode" name="periode" required>
                            <option value="bulan_ini" selected>Bulan Ini</option>
                            <option value="semester_ini">Semester Ini</option>
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
                <h6 class="text-danger">Pelanggaran Anak</h6>
                <p class="text-muted small">Data pelanggaran yang dilakukan anak dengan status verifikasi.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-success">Prestasi Anak</h6>
                <p class="text-muted small">Data prestasi dan penghargaan yang diraih anak.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-warning">Sanksi Anak</h6>
                <p class="text-muted small">Data sanksi yang diterima anak dan status penyelesaiannya.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Informasi Anak</h6>
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="bg-info text-white rounded p-3 text-center">
                        <i class="fa fa-child fa-2x mb-2"></i>
                        <h6>{{ $anak->nama_siswa }}</h6>
                        <small>{{ $anak->kelas->nama_kelas ?? 'N/A' }} | NIS: {{ $anak->nis }}</small>
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-4">
                    <div class="h5 text-danger">{{ $anak->pelanggaran()->count() }}</div>
                    <div class="text-muted small">Pelanggaran</div>
                </div>
                <div class="col-4">
                    <div class="h5 text-success">{{ $anak->prestasi()->count() }}</div>
                    <div class="text-muted small">Prestasi</div>
                </div>
                <div class="col-4">
                    <div class="h5 text-warning">{{ \App\Models\Sanksi::whereHas('pelanggaran', function($q) use ($anak) { $q->where('siswa_id', $anak->siswa_id); })->count() }}</div>
                    <div class="text-muted small">Sanksi</div>
                </div>
            </div>
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