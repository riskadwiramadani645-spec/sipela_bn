@extends('layouts.app')

@section('title', 'Monitoring Pelanggaran Kelas - SIPELA')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Monitoring Pelanggaran Kelas
                        <span class="badge bg-danger ms-2">{{ $kelas->nama_kelas ?? 'Kelas' }}</span>
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-primary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="filterBulan">
                            <option value="">Semua Bulan</option>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="diverifikasi">Diverifikasi</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama siswa...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info w-100" onclick="applyFilter()">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="bg-danger rounded p-3 text-white text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h5>{{ $totalPelanggaran ?? 0 }}</h5>
                            <small>Total Pelanggaran</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning rounded p-3 text-white text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h5>{{ $pelanggaranPending ?? 0 }}</h5>
                            <small>Pending Verifikasi</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success rounded p-3 text-white text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h5>{{ $pelanggaranVerifikasi ?? 0 }}</h5>
                            <small>Diverifikasi</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info rounded p-3 text-white text-center">
                            <i class="fas fa-calendar fa-2x mb-2"></i>
                            <h5>{{ $pelanggaranBulanIni ?? 0 }}</h5>
                            <small>Bulan Ini</small>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="table-responsive">
                    <table id="pelanggaran-kelasTable" class="table table-hover" data-datatable data-page-size="10">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin</th>
                                <th>Guru Pencatat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggaranList as $index => $pelanggaran)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ date('d/m/Y', strtotime($pelanggaran->tanggal)) }}</td>
                                <td>{{ $pelanggaran->siswa->nama_siswa ?? '-' }}</td>
                                <td>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $pelanggaran->poin }}</span>
                                </td>
                                <td>{{ $pelanggaran->guruPencatat->nama_guru ?? '-' }}</td>
                                <td>
                                    @if($pelanggaran->status_verifikasi == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($pelanggaran->status_verifikasi == 'diverifikasi')
                                        <span class="badge bg-success">Diverifikasi</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetail({{ $pelanggaran->pelanggaran_id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pelanggaran</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetail(pelanggaranId) {
    alert('Detail pelanggaran ID: ' + pelanggaranId);
}

function refreshData() {
    location.reload();
}

function applyFilter() {
    console.log('Filter applied');
}
</script>

@endsection