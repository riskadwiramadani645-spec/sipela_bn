@extends('layouts.app')

@section('title', 'Data BK Saya - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <h6 class="mb-4">Data BK Saya</h6>
            <p>Lihat data bimbingan konseling yang telah saya lakukan</p>
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-list fa-3x mb-2"></i>
                        <h6>Data BK <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>History Konseling</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-bell fa-3x mb-2"></i>
                        <h6>Follow-up Sanksi</h6>
                        @if(isset($sanksiFollowup) && $sanksiFollowup->count() > 0)
                            <span class="badge bg-warning">{{ $sanksiFollowup->count() }} Pending</span>
                        @else
                            <span class="badge bg-success">✓ Clear</span>
                        @endif
                        <small>Sanksi Perlu Tindak Lanjut</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-user-md fa-3x mb-2"></i>
                        <h6>Progress <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Monitoring Siswa</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fa fa-chart-line fa-3x mb-2"></i>
                        <h6>Evaluasi <span class="badge bg-success">✓ AKTIF</span></h6>
                        <small>Hasil Konseling</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sanksi Follow-up Section -->
@if(isset($sanksiFollowup) && $sanksiFollowup->count() > 0)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-warning rounded h-100 p-4">
            <div class="mb-4">
                <h6 class="mb-0 text-dark"><i class="fa fa-exclamation-triangle me-2"></i>Sanksi Perlu Follow-up</h6>
                <p class="text-dark mb-0">{{ $sanksiFollowup->count() }} sanksi memerlukan tindak lanjut dari BK</p>
            </div>
            
            <div class="table-responsive">
                <table id="view-dataTable" class="table table-dark table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Pelanggaran</th>
                            <th>Sanksi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sanksiFollowup as $sanksi)
                        <tr>
                            <td>
                                <strong>{{ $sanksi->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $sanksi->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                            <td>{{ Str::limit($sanksi->deskripsi_sanksi, 50) }}</td>
                            <td>
                                <span class="badge bg-warning">{{ ucfirst($sanksi->followup_status) }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success" onclick="followupSanksi({{ $sanksi->sanksi_id }})">
                                    <i class="fa fa-plus"></i> Follow-up
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="mb-4">
                <h6 class="mb-0">Data Bimbingan Konseling Saya</h6>
            </div>
            
            <div class="table-responsive">
                <table id="view-dataTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Topik</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bk ?? [] as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $b->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $b->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $b->topik }}</td>
                            <td>{{ Str::limit($b->tindakan_solusi, 50) }}</td>
                            <td>
                                @if($b->status == 'terdaftar')
                                    <span class="badge bg-warning">Terdaftar</span>
                                @elseif($b->status == 'diproses')
                                    <span class="badge bg-info">Diproses</span>
                                @elseif($b->status == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($b->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $b->tanggal_konseling ? $b->tanggal_konseling->format('d/m/Y') : '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick="showDetail({{ $b->bk_id }})">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning me-1" onclick="editData({{ $b->bk_id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteData({{ $b->bk_id }})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data bimbingan konseling</td>
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
            <div class="modal-header">
                <h5 class="modal-title">Detail Bimbingan Konseling</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Siswa:</strong>
                        <p id="detail_siswa"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p id="detail_status"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Tanggal:</strong>
                        <p id="detail_tanggal"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Jenis Layanan:</strong>
                        <p id="detail_jenis_layanan"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Topik:</strong>
                    <p id="detail_topik"></p>
                </div>
                <div class="mb-3">
                    <strong>Keluhan/Masalah:</strong>
                    <p id="detail_keluhan"></p>
                </div>
                <div class="mb-3">
                    <strong>Tindakan/Solusi:</strong>
                    <p id="detail_tindakan"></p>
                </div>
                <div class="mb-3">
                    <strong>Hasil Evaluasi:</strong>
                    <p id="detail_evaluasi"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bimbingan Konseling</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Siswa</label>
                            <select id="edit_siswa_id" name="siswa_id" class="form-select" required>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select id="edit_status" name="status" class="form-select">
                                <option value="terdaftar">Terdaftar</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="tindak_lanjut">Tindak Lanjut</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Topik</label>
                        <input type="text" id="edit_topik" name="topik" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tindakan</label>
                        <textarea id="edit_tindakan" name="tindakan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDetail(id) {
    fetch(`/konselor-bk/data-bk-saya/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detail_siswa').textContent = `${data.bk.siswa.nama_siswa} - ${data.bk.siswa.kelas ? data.bk.siswa.kelas.nama_kelas : 'N/A'}`;
            document.getElementById('detail_status').innerHTML = getStatusBadge(data.bk.status);
            document.getElementById('detail_tanggal').textContent = data.bk.tanggal_konseling ? new Date(data.bk.tanggal_konseling).toLocaleDateString('id-ID') : '-';
            document.getElementById('detail_jenis_layanan').textContent = data.bk.jenis_layanan || '-';
            document.getElementById('detail_topik').textContent = data.bk.topik;
            document.getElementById('detail_keluhan').textContent = data.bk.keluhan_masalah || '-';
            document.getElementById('detail_tindakan').textContent = data.bk.tindakan_solusi || '-';
            document.getElementById('detail_evaluasi').textContent = data.bk.hasil_evaluasi || '-';
            
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail');
        });
}

function getStatusBadge(status) {
    const badges = {
        'terdaftar': '<span class="badge bg-warning">Terdaftar</span>',
        'diproses': '<span class="badge bg-info">Diproses</span>',
        'selesai': '<span class="badge bg-success">Selesai</span>',
        'tindak_lanjut': '<span class="badge bg-secondary">Tindak Lanjut</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function editData(id) {
    fetch(`/konselor-bk/data-bk-saya/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editForm').action = `/konselor-bk/data-bk-saya/${id}`;
            document.getElementById('edit_topik').value = data.bk.topik;
            document.getElementById('edit_tindakan').value = data.bk.tindakan_solusi || '';
            document.getElementById('edit_status').value = data.bk.status;
            
            const siswaSelect = document.getElementById('edit_siswa_id');
            siswaSelect.innerHTML = '';
            data.siswa.forEach(s => {
                const option = document.createElement('option');
                option.value = s.siswa_id;
                option.textContent = `${s.nama_siswa} - ${s.kelas ? s.kelas.nama_kelas : ''}`;
                option.selected = s.siswa_id == data.bk.siswa_id;
                siswaSelect.appendChild(option);
            });
            
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data untuk edit');
        });
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus data konseling ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/konselor-bk/data-bk-saya/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function followupSanksi(sanksiId) {
    const topik = prompt('Masukkan topik konseling untuk follow-up sanksi:');
    if (topik) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/konselor-bk/followup-sanksi';
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="sanksi_id" value="${sanksiId}">
            <input type="hidden" name="topik" value="${topik}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection