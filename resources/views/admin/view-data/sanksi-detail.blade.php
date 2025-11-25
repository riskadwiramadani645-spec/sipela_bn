<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Detail Sanksi</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="row">
    <div class="col-md-6">
        <h6>Informasi Sanksi</h6>
        <table id="sanksi-detailTable" class="table table-borderless" data-datatable data-page-size="10">
            <tr>
                <td><strong>Siswa</strong></td>
                <td>: {{ $sanksi->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ $sanksi->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Pelanggaran</strong></td>
                <td>: {{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Sanksi</strong></td>
                <td>: {{ $sanksi->jenis_sanksi ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>: 
                    @if($sanksi->status == 'Terdaftar')
                        <span class="badge bg-warning">Terdaftar</span>
                    @elseif($sanksi->status == 'Diproses')
                        <span class="badge bg-info">Diproses</span>
                    @elseif($sanksi->status == 'Selesai')
                        <span class="badge bg-success">Selesai</span>
                    @else
                        <span class="badge bg-secondary">Tindak Lanjut</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Tanggal Mulai</strong></td>
                <td>: {{ $sanksi->tanggal_mulai ? \Carbon\Carbon::parse($sanksi->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Selesai</strong></td>
                <td>: {{ $sanksi->tanggal_selesai ? \Carbon\Carbon::parse($sanksi->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6>Catatan Pelaksanaan</h6>
        <p>{{ $sanksi->catatan_pelaksanaan ?? 'Tidak ada catatan' }}</p>
        
        @if($sanksi->pelaksanaanSanksi && $sanksi->pelaksanaanSanksi->count() > 0)
        <h6 class="mt-3">Riwayat Pelaksanaan</h6>
        @foreach($sanksi->pelaksanaanSanksi as $pelaksanaan)
        <div class="border rounded p-2 mb-2">
            <small class="text-muted">{{ \Carbon\Carbon::parse($pelaksanaan->tanggal_pelaksanaan)->format('d/m/Y') }}</small>
            <p class="mb-0">{{ $pelaksanaan->keterangan }}</p>
        </div>
        @endforeach
        @endif
    </div>
</div>
<div class="mt-3 text-end">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="fa fa-times me-1"></i>Tutup
    </button>
</div>