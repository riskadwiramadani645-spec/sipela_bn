@extends('layouts.app')

@section('title', 'Master Data - Guru')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Guru</h6>
                    <p class="mb-0">Kelola data guru dan tenaga pendidik di SIPELA</p>
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
                <h6 class="mb-0">Daftar Guru</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table id="guruTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Jenis Kelamin</th>
                            <th>Bidang Studi</th>
                            <th>No. Telepon</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nip }}</td>
                            <td>{{ $item->nama_guru }}</td>
                            <td>{{ $item->jenis_kelamin }}</td>
                            <td>{{ $item->bidang_studi }}</td>
                            <td>{{ $item->no_telp }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                                <span class="badge {{ $item->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->guru_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->guru_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->guru_id }})" title="Hapus">
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
            <form action="{{ route('admin.master-data.guru.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">NIP</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nip" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Guru</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nama_guru" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Jenis Kelamin</label>
                        <select class="form-control bg-dark text-white border-primary" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bidang Studi</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="bidang_studi" placeholder="Matematika, Bahasa Indonesia, dll">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">No. Telepon</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="no_telp" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Email</label>
                        <input type="email" class="form-control bg-dark text-white border-primary" name="email" placeholder="guru@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Status</label>
                        <select class="form-control bg-dark text-white border-primary" name="status">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Pensiun">Pensiun</option>
                        </select>
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
                    <h5 class="modal-title text-white">Edit Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">NIP</label>
                        <input type="text" id="edit_nip" class="form-control bg-dark text-white border-primary" name="nip">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Guru</label>
                        <input type="text" id="edit_nama_guru" class="form-control bg-dark text-white border-primary" name="nama_guru" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Jenis Kelamin</label>
                        <select id="edit_jenis_kelamin" class="form-control bg-dark text-white border-primary" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bidang Studi</label>
                        <input type="text" id="edit_bidang_studi" class="form-control bg-dark text-white border-primary" name="bidang_studi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">No. Telepon</label>
                        <input type="text" id="edit_no_telp" class="form-control bg-dark text-white border-primary" name="no_telp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Email</label>
                        <input type="email" id="edit_email" class="form-control bg-dark text-white border-primary" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Status</label>
                        <select id="edit_status" class="form-control bg-dark text-white border-primary" name="status">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Pensiun">Pensiun</option>
                        </select>
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
                <h5 class="modal-title text-white">Detail Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">NIP:</strong>
                        <p class="text-light" id="detail_nip"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Nama Guru:</strong>
                        <p class="text-light" id="detail_nama_guru"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Jenis Kelamin:</strong>
                        <p class="text-light" id="detail_jenis_kelamin"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Bidang Studi:</strong>
                        <p class="text-light" id="detail_bidang_studi"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">No. Telepon:</strong>
                        <p class="text-light" id="detail_no_telp"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Email:</strong>
                        <p class="text-light" id="detail_email"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Status:</strong>
                        <p class="text-light" id="detail_status"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
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
    fetch(`/admin/master-data/guru/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail_id').textContent = data.data.guru_id;
        document.getElementById('detail_nip').textContent = data.data.nip || '-';
        document.getElementById('detail_nama_guru').textContent = data.data.nama_guru || '-';
        document.getElementById('detail_jenis_kelamin').textContent = data.data.jenis_kelamin || '-';
        document.getElementById('detail_bidang_studi').textContent = data.data.bidang_studi || '-';
        document.getElementById('detail_no_telp').textContent = data.data.no_telp || '-';
        document.getElementById('detail_email').textContent = data.data.email || '-';
        document.getElementById('detail_status').textContent = data.data.status || '-';
        document.getElementById('detail_created_at').textContent = data.data.created_at ? new Date(data.data.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail guru');
    });
}

function editData(id) {
    fetch(`/admin/master-data/guru/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('editForm').action = `/admin/master-data/guru/${id}`;
        document.getElementById('edit_nip').value = data.data.nip || '';
        document.getElementById('edit_nama_guru').value = data.data.nama_guru || '';
        document.getElementById('edit_jenis_kelamin').value = data.data.jenis_kelamin || '';
        document.getElementById('edit_bidang_studi').value = data.data.bidang_studi || '';
        document.getElementById('edit_no_telp').value = data.data.no_telp || '';
        document.getElementById('edit_email').value = data.data.email || '';
        document.getElementById('edit_status').value = data.data.status || '';
        
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data untuk edit');
    });
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus data guru ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/guru/${id}`;
        form.style.display = 'none';
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
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
@endpush