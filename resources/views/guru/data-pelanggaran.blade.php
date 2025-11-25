@extends('layouts.app')

@section('title', 'Data Pelanggaran - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Data Pelanggaran {{ $isWaliKelas ? '(Wali Kelas)' : '(Guru)' }}</h6>
                    <p class="mb-0">{{ $filter === 'kelas' ? 'Lihat semua pelanggaran siswa di kelas yang Anda ampu' : 'Lihat pelanggaran yang Anda input' }}</p>
                </div>
                <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-0 text-white">Data Pelanggaran Saya</h6>
                    <small class="text-muted">Menampilkan pelanggaran yang Anda input sendiri</small>
                </div>
                <a href="{{ route('guru.input-pelanggaran') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Input Pelanggaran Baru
                </a>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                        @if($filter)
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        <div class="flex-fill" style="min-width: 150px;">
                            <label class="form-label text-white small mb-1">Tingkat</label>
                            <select name="tingkat" class="form-select form-select-sm bg-dark text-white border-primary">
                                <option value="">Semua Tingkat</option>
                                @if(isset($tingkatList))
                                    @foreach($tingkatList as $tingkat)
                                        <option value="{{ $tingkat }}" {{ request('tingkat') == $tingkat ? 'selected' : '' }}>{{ $tingkat }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex-fill" style="min-width: 150px;">
                            <label class="form-label text-white small mb-1">Jurusan</label>
                            <select name="jurusan" class="form-select form-select-sm bg-dark text-white border-primary">
                                <option value="">Semua Jurusan</option>
                                @if(isset($jurusanList))
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>{{ $jurusan }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex-fill" style="min-width: 150px;">
                            <label class="form-label text-white small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm bg-dark text-white border-primary">
                                <option value="">Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('guru.data-pelanggaran') }}{{ $filter ? '?filter=' . $filter : '' }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="data-pelanggaranTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Tanggal</th>
                            <th>Poin</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $item->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $item->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $item->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                            <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge bg-danger">{{ $item->poin }} poin</span></td>
                            <td>
                                @if($item->bukti_foto)
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-file-image"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status_verifikasi == 'menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($item->status_verifikasi == 'diverifikasi')
                                    <span class="badge bg-success">Diverifikasi</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->pelanggaran_id }})" title="Detail">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada data pelanggaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <div class="modal-header border-bottom border-primary">
                <h5 class="modal-title text-white">Detail Pelanggaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong class="text-white">Siswa:</strong>
                        <p class="text-light" id="detail_siswa"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Kelas:</strong>
                        <p class="text-light" id="detail_kelas"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong class="text-white">Jenis Pelanggaran:</strong>
                        <p class="text-light" id="detail_jenis_pelanggaran"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Tanggal:</strong>
                        <p class="text-light" id="detail_tanggal"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong class="text-white">Poin:</strong>
                        <p class="text-light" id="detail_poin"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Status:</strong>
                        <p class="text-light" id="detail_status"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong class="text-white">Bukti Foto:</strong>
                        <div id="detail_bukti_foto"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <strong class="text-white">Keterangan:</strong>
                    <p class="text-light" id="detail_keterangan"></p>
                </div>
            </div>
            <div class="modal-footer border-top border-primary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetail(id) {
    fetch(`/guru/pelanggaran/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail_siswa').textContent = data.siswa_nama;
        document.getElementById('detail_kelas').textContent = data.kelas_nama;
        document.getElementById('detail_jenis_pelanggaran').textContent = data.jenis_pelanggaran;
        document.getElementById('detail_tanggal').textContent = data.tanggal;
        document.getElementById('detail_poin').textContent = data.poin + ' poin';
        document.getElementById('detail_status').textContent = data.status;
        document.getElementById('detail_keterangan').textContent = data.keterangan;
        
        const buktiFotoDiv = document.getElementById('detail_bukti_foto');
        if (data.bukti_foto) {
            const storageUrl = '{{ asset("storage") }}';
            const imageUrl = `${storageUrl}/${data.bukti_foto}`;
            buktiFotoDiv.innerHTML = `<img src="${imageUrl}" alt="Bukti Foto" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;"/>`;
        } else {
            buktiFotoDiv.innerHTML = '<span class="text-muted">Tidak ada bukti foto</span>';
        }
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail pelanggaran');
    });
}
</script>

@endsection