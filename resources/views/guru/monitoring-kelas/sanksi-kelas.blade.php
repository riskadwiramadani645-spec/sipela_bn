@extends('layouts.app')

@section('title', 'Monitoring Sanksi Kelas - SIPELA')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-gavel me-2"></i>Monitoring Sanksi Kelas
                        <span class="badge bg-warning ms-2">{{ $kelas->nama_kelas ?? 'Kelas' }}</span>
                    </h6>
                    <div>
                        <span class="badge bg-info">👁️ READ ONLY</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Info:</strong> Halaman ini hanya untuk monitoring sanksi siswa kelas Anda. 
                    Sanksi dikelola oleh Kesiswaan/Admin. Gunakan informasi ini untuk pembinaan langsung ke siswa.
                </div>

                <!-- Filter -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="belum_dilaksanakan">Belum Dilaksanakan</option>
                            <option value="sedang_dilaksanakan">Sedang Dilaksanakan</option>
                            <option value="selesai">Selesai</option>
                            <option value="terlambat">Terlambat</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama siswa...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterJenisSanksi">
                            <option value="">Semua Jenis Sanksi</option>
                            @foreach($jenisSanksi as $jenis)
                            <option value="{{ $jenis->jenis_sanksi_id }}">{{ $jenis->nama_sanksi }}</option>
                            @endforeach
                        </select>
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
                            <i class="fas fa-gavel fa-2x mb-2"></i>
                            <h5>{{ $totalSanksi ?? 0 }}</h5>
                            <small>Total Sanksi</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning rounded p-3 text-white text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h5>{{ $sanksiPending ?? 0 }}</h5>
                            <small>Belum Dilaksanakan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success rounded p-3 text-white text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h5>{{ $sanksiSelesai ?? 0 }}</h5>
                            <small>Selesai</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info rounded p-3 text-white text-center">
                            <i class="fas fa-exclamation fa-2x mb-2"></i>
                            <h5>{{ $sanksiTerlambat ?? 0 }}</h5>
                            <small>Terlambat</small>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="table-responsive">
                    <table id="sanksi-kelasTable" class="table table-hover" data-datatable data-page-size="10">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Pelanggaran</th>
                                <th>Jenis Sanksi</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sanksiList as $index => $sanksi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $sanksi->pelanggaran->siswa->nama_siswa ?? '-' }}</td>
                                <td>{{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                                <td>{{ $sanksi->jenisSanksi->nama_sanksi ?? $sanksi->jenis_sanksi_manual ?? '-' }}</td>
                                <td>
                                    @if($sanksi->deadline)
                                        {{ date('d/m/Y', strtotime($sanksi->deadline)) }}
                                        @if(strtotime($sanksi->deadline) < time() && $sanksi->status != 'selesai')
                                            <span class="badge bg-danger ms-1">Lewat</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($sanksi->status == 'belum_dilaksanakan')
                                        <span class="badge bg-warning">Belum Dilaksanakan</span>
                                    @elseif($sanksi->status == 'sedang_dilaksanakan')
                                        <span class="badge bg-info">Sedang Dilaksanakan</span>
                                    @elseif($sanksi->status == 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Terlambat</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $progress = 0;
                                        if($sanksi->status == 'sedang_dilaksanakan') $progress = 50;
                                        elseif($sanksi->status == 'selesai') $progress = 100;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%">
                                            {{ $progress }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetail({{ $sanksi->sanksi_id }})">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    @if($sanksi->status != 'selesai')
                                    <button class="btn btn-sm btn-warning" onclick="bimbingSiswa('{{ $sanksi->pelanggaran->siswa->nama_siswa }}')">
                                        <i class="fas fa-comments"></i> Bimbing
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data sanksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Sanksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewDetail(sanksiId) {
    $('#detailModal').modal('show');
    $('#detailContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
}

function bimbingSiswa(namaSiswa) {
    alert('Fitur pembinaan untuk ' + namaSiswa + ' akan segera tersedia.\n\nSaran:\n- Panggil siswa untuk konseling\n- Berikan motivasi dan arahan\n- Pantau progress sanksi\n- Koordinasi dengan orang tua jika perlu');
}

function applyFilter() {
    console.log('Filter applied');
}
</script>

@endsection