@extends('layouts.app')

@section('title', 'Verifikasi Data')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <h6 class="mb-4">Verifikasi Data</h6>
            <p>Verifikasi dan validasi data pelanggaran serta prestasi siswa</p>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-check-double fa-3x mb-2"></i>
                        <h6>Validasi Data <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Verifikasi keakuratan data</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-clipboard-check fa-3x mb-2"></i>
                        <h6>Kontrol Kualitas <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Pastikan data berkualitas</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-user-shield fa-3x mb-2"></i>
                        <h6>Otorisasi <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Approve atau tolak data</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Verifikasi Data Pelanggaran</h6>
                <p class="text-muted mb-4">Verifikasi dan validasi data pelanggaran siswa. <strong>Catatan:</strong> Prestasi yang diinput admin langsung disetujui tanpa perlu verifikasi.</p>
                
                <div>
                        <div class="mb-3">
                            <select class="form-select w-auto d-inline" onchange="filterStatus('pelanggaran', this.value)">
                                <option value="menunggu">Menunggu Verifikasi</option>
                                <option value="diverifikasi">Sudah Diverifikasi</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="revisi">Perlu Revisi</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table id="verifikasiTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Tanggal</th>
                                        <th>Pencatat</th>
                                        <th>Bukti Foto</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pelanggaranMenunggu ?? [] as $index => $p)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $p->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $p->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ ucfirst($p->jenisPelanggaran->kategori ?? '') }}</small>
                                        </td>
                                        <td><span class="badge bg-danger">{{ $p->poin }} poin</span></td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($p->guruPencatat)
                                                <strong>{{ $p->guruPencatat->nama_guru }}</strong><br>
                                                <small class="text-muted">{{ $p->guruPencatat->nip ?? 'N/A' }}</small>
                                            @elseif($p->guru_pencatat)
                                                @php
                                                    $guru = \App\Models\Guru::find($p->guru_pencatat);
                                                @endphp
                                                @if($guru)
                                                    <strong>{{ $guru->nama_guru }}</strong><br>
                                                    <small class="text-muted">{{ $guru->nip ?? 'N/A' }}</small>
                                                @else
                                                    <small class="text-muted">Admin/Sistem</small>
                                                @endif
                                            @else
                                                <small class="text-muted">Admin/Sistem</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($p->bukti_foto)
                                                <a href="{{ asset('storage/' . $p->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-file-image"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($p->status_verifikasi == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($p->status_verifikasi == 'diverifikasi')
                                                <span class="badge bg-success">Diverifikasi</span>
                                            @elseif($p->status_verifikasi == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-info">Revisi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($p->status_verifikasi == 'menunggu')
                                                    <button class="btn btn-sm btn-success" onclick="verifikasi({{ $p->pelanggaran_id }}, 'pelanggaran', 'diverifikasi')" title="Verifikasi">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="verifikasi({{ $p->pelanggaran_id }}, 'pelanggaran', 'ditolak')" title="Tolak">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">Sudah diverifikasi</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data pelanggaran</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="verifikasiModal">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="verifikasiForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Verifikasi</label>
                        <select id="status_verifikasi" name="status_verifikasi" class="form-select" required>
                            <option value="diverifikasi">Diverifikasi</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="revisi">Perlu Revisi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Berikan catatan untuk keputusan verifikasi..."></textarea>
                    </div>
                    <input type="hidden" name="guru_verifikator" value="{{ session('user')->user_id ?? 1 }}">
                    <input type="hidden" id="hidden_status" name="status" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
function verifikasi(id, type, status) {
    const currentPrefix = window.location.pathname.includes('/kesiswaan/') ? 'kesiswaan' : 'admin';
    document.getElementById('verifikasiForm').action = `/${currentPrefix}/verifikasi-monitoring/verifikasi/${type}/${id}`;
    document.getElementById('status_verifikasi').value = status;
    document.getElementById('hidden_status').value = status;
    
    // Auto submit untuk tombol ceklis langsung
    if (status === 'diverifikasi') {
        document.getElementById('verifikasiForm').submit();
    } else {
        new bootstrap.Modal(document.getElementById('verifikasiModal')).show();
    }
}





function filterStatus(type, status) {
    // Reload page with filter
    window.location.href = `/admin/verifikasi?type=${type}&status=${status}`;
}
</script>
@endsection