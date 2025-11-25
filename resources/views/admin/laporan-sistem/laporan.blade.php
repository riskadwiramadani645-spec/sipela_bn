@extends('layouts.app')

@section('title', 'Laporan Admin - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Export Laporan - Administrator</h5>
                    <p class="mb-0">Generate laporan lengkap untuk manajemen sekolah</p>
                </div>
                <div class="text-end">

                    <div class="small real-time-clock">{{ now()->format('H:i') }} WIB</div>
                    <div class="small text-light">Report Generator</div>
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
            <h6 class="mb-4"><i class="fa fa-cog me-2"></i>Konfigurasi Laporan Administrator</h6>
            <form id="exportForm" action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="pelanggaran">Laporan Pelanggaran</option>
                            <option value="prestasi">Laporan Prestasi</option>
                            <option value="sanksi">Laporan Sanksi</option>
                            <option value="siswa">Data Siswa</option>
                            <option value="guru">Data Guru</option>
                            <option value="kelas">Data Kelas</option>
                            <option value="rekap">Rekap Keseluruhan</option>
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
                            <label class="form-check-label" for="include_summary">Sertakan Ringkasan Admin</label>
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
                <h6 class="text-success">Laporan Pelanggaran</h6>
                <p class="text-muted small">Data pelanggaran siswa dengan detail verifikasi dan status sanksi.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-warning">Laporan Prestasi</h6>
                <p class="text-muted small">Data prestasi dan penghargaan siswa dengan tingkat kompetisi.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-info">Laporan Sanksi</h6>
                <p class="text-muted small">Status pelaksanaan sanksi dan monitoring kepatuhan siswa.</p>
            </div>
            <div class="mb-3">
                <h6 class="text-danger">Master Data & Rekap</h6>
                <p class="text-muted small">Database lengkap siswa, guru, kelas dan rekap keseluruhan sistem.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Quick Reference - Data Terkini</h6>
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="h4 text-warning">{{ $stats['total_pelanggaran'] ?? 0 }}</div>
                    <div class="text-muted">Total Pelanggaran</div>
                </div>
                <div class="col-6 mb-3">
                    <div class="h4 text-info">{{ $stats['total_prestasi'] ?? 0 }}</div>
                    <div class="text-muted">Total Prestasi</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-success">{{ $stats['total_siswa'] ?? 0 }}</div>
                    <div class="text-muted">Total Siswa</div>
                </div>
                <div class="col-6">
                    <div class="h4 text-danger">{{ $stats['total_sanksi'] ?? 0 }}</div>
                    <div class="text-muted">Total Sanksi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Export Templates -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4 text-white"><i class="fa fa-bolt me-2"></i>Quick Export - Laporan Siap Pakai</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="fa fa-chart-line fa-3x text-primary mb-3"></i>
                                <h6 class="text-dark fw-bold">Laporan Manajemen</h6>
                                <p class="text-secondary small">Rekap statistik lengkap untuk kepala sekolah</p>
                                <form action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="rekap">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="btn btn-primary btn-sm">Export Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h6 class="text-dark fw-bold">Laporan Pelanggaran</h6>
                                <p class="text-secondary small">Detail pelanggaran siswa + status verifikasi</p>
                                <form action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="pelanggaran">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="btn btn-danger btn-sm">Export Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="fa fa-trophy fa-3x text-warning mb-3"></i>
                                <h6 class="text-dark fw-bold">Laporan Prestasi</h6>
                                <p class="text-secondary small">Data prestasi & penghargaan siswa</p>
                                <form action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="prestasi">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="btn btn-warning btn-sm">Export Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="fa fa-gavel fa-3x text-info mb-3"></i>
                                <h6 class="text-dark fw-bold">Laporan Sanksi</h6>
                                <p class="text-secondary small">Data sanksi & status pelaksanaan</p>
                                <form action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="sanksi">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="btn btn-info btn-sm">Export Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="fa fa-users fa-3x text-success mb-3"></i>
                                <h6 class="text-dark fw-bold">Master Data Siswa</h6>
                                <p class="text-secondary small">Database lengkap siswa aktif</p>
                                <form action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="siswa">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="fa fa-chalkboard-teacher fa-3x text-dark mb-3"></i>
                                <h6 class="text-dark fw-bold">Master Data Guru</h6>
                                <p class="text-secondary small">Database lengkap guru & staff</p>
                                <form action="{{ route('admin.laporan-sistem.laporan.export') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="guru">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="btn btn-secondary btn-sm">Export Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Data Preview -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <h6 class="mb-4">Preview Data Terbaru</h6>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger">Pelanggaran Terbaru (5 terakhir)</h6>
                    <div class="table-responsive">
                        <table id="laporanTable" class="table table-sm" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPelanggaran ?? [] as $p)
                                <tr>
                                    <td>{{ $p->siswa->nama_siswa ?? 'N/A' }}</td>
                                    <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                    <td>{{ $p->tanggal ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $p->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                                            {{ ucfirst($p->status_verifikasi ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Prestasi Terbaru (5 terakhir)</h6>
                    <div class="table-responsive">
                        <table id="laporanTable" class="table table-sm" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Jenis</th>
                                    <th>Tingkat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPrestasi ?? [] as $p)
                                <tr>
                                    <td>{{ $p->siswa->nama_siswa ?? 'N/A' }}</td>
                                    <td>{{ $p->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                                    <td>{{ $p->tingkat ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $p->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                                            {{ ucfirst($p->status_verifikasi ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
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