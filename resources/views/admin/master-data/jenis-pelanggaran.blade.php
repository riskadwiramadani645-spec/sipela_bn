@extends('layouts.app')

@section('title', 'Master Data - Jenis Pelanggaran')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Jenis Pelanggaran</h6>
                    <p class="mb-0">Kelola jenis pelanggaran dan sistem poin di SIPELA</p>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0">Master Data - Jenis Pelanggaran</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            <div class="table-responsive">
                <table id="jenis-pelanggaranTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggaran</th>
                            <th>Kategori</th>
                            <th>Poin</th>
                            <th>Deskripsi</th>
                            <th>Sanksi Rekomendasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nama_pelanggaran }}</td>
                            <td>
                                @if($item->kategori == 'ringan')
                                    <span class="badge bg-success">Ringan</span>
                                @elseif($item->kategori == 'sedang')
                                    <span class="badge bg-warning">Sedang</span>
                                @elseif($item->kategori == 'berat')
                                    <span class="badge bg-danger">Berat</span>
                                @else
                                    <span class="badge bg-dark">Sangat Berat</span>
                                @endif
                            </td>
                            <td><span class="badge bg-warning">{{ $item->poin }}</span></td>
                            <td>{{ Str::limit($item->deskripsi ?? '-', 50) }}</td>
                            <td>{{ Str::limit($item->sanksi_rekomendasi ?? '-', 50) }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->jenis_pelanggaran_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->jenis_pelanggaran_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->jenis_pelanggaran_id }})" title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <form action="{{ route('admin.master-data.jenis-pelanggaran.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Jenis Pelanggaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Pelanggaran</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nama_pelanggaran" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Poin</label>
                        <input type="number" class="form-control bg-dark text-white border-primary" name="poin" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kategori</label>
                        <select class="form-control bg-dark text-white border-primary" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="ringan">Ringan</option>
                            <option value="sedang">Sedang</option>
                            <option value="berat">Berat</option>
                            <option value="sangat_berat">Sangat Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Deskripsi</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Sanksi Rekomendasi</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="sanksi_rekomendasi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-primary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Edit Jenis Pelanggaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Pelanggaran</label>
                        <input type="text" id="edit_nama_pelanggaran" class="form-control bg-dark text-white border-primary" name="nama_pelanggaran" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Poin</label>
                        <input type="number" id="edit_poin" class="form-control bg-dark text-white border-primary" name="poin" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kategori</label>
                        <select id="edit_kategori" class="form-control bg-dark text-white border-primary" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="ringan">Ringan</option>
                            <option value="sedang">Sedang</option>
                            <option value="berat">Berat</option>
                            <option value="sangat_berat">Sangat Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Deskripsi</label>
                        <textarea id="edit_deskripsi" class="form-control bg-dark text-white border-primary" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Sanksi Rekomendasi</label>
                        <textarea id="edit_sanksi_rekomendasi" class="form-control bg-dark text-white border-primary" name="sanksi_rekomendasi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-primary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header border-bottom border-primary">
                <h5 class="modal-title text-white">Detail Jenis Pelanggaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Nama Pelanggaran:</strong>
                        <p class="text-light" id="detail_nama_pelanggaran"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Poin:</strong>
                        <p class="text-light" id="detail_poin"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Kategori:</strong>
                        <p class="text-light" id="detail_kategori"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong class="text-white">Sanksi Rekomendasi:</strong>
                        <p class="text-light" id="detail_sanksi_rekomendasi"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong class="text-white">Dibuat Tanggal:</strong>
                        <p class="text-light" id="detail_created_at"></p>
                    </div>
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
    fetch(`/admin/master-data/jenis-pelanggaran/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(response => {
        const data = response.data;
        document.getElementById('detail_id').textContent = data.jenis_pelanggaran_id || '-';
        document.getElementById('detail_nama_pelanggaran').textContent = data.nama_pelanggaran || '-';
        document.getElementById('detail_poin').textContent = data.poin || '-';
        document.getElementById('detail_kategori').textContent = data.kategori || '-';
        document.getElementById('detail_sanksi_rekomendasi').textContent = data.sanksi_rekomendasi || '-';
        document.getElementById('detail_created_at').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail jenis pelanggaran');
    });
}

function editData(id) {
    fetch(`/admin/master-data/jenis-pelanggaran/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(response => {
        const data = response.data;
        document.getElementById('editForm').action = `/admin/master-data/jenis-pelanggaran/${id}`;
        document.getElementById('edit_nama_pelanggaran').value = data.nama_pelanggaran || '';
        document.getElementById('edit_poin').value = data.poin || '';
        document.getElementById('edit_kategori').value = data.kategori || '';
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
        document.getElementById('edit_sanksi_rekomendasi').value = data.sanksi_rekomendasi || '';
        
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data untuk edit');
    });
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus jenis pelanggaran ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/jenis-pelanggaran/${id}`;
        form.style.display = 'none';
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function updateClock() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
    const timeStr = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }) + ' WIB';
    
    document.getElementById('current-date').textContent = dateStr;
    document.getElementById('current-time').textContent = timeStr;
}

updateClock();
setInterval(updateClock, 1000);
</script>
@endsection