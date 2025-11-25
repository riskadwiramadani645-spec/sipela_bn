@extends('layouts.app')

@section('title', 'Master Data - Kelas')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Kelas</h6>
                    <p class="mb-0">Kelola data kelas dan pengelompokan siswa di SIPELA</p>
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
                <h6 class="mb-0">Daftar Kelas</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table id="kelasTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th>Kapasitas</th>
                            <th>Wali Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nama_kelas }}</td>
                            <td>{{ $item->jurusan }}</td>
                            <td>{{ $item->kapasitas }}</td>
                            <td>{{ $item->waliKelas->nama_guru ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->kelas_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->kelas_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->kelas_id }})" title="Hapus">
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
            <form action="{{ route('admin.master-data.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Kelas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Kelas</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nama_kelas" placeholder="XII RPL 1, XI TKJ 2, dll" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Jurusan</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="jurusan" placeholder="Rekayasa Perangkat Lunak, Teknik Komputer Jaringan, dll">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kapasitas</label>
                        <input type="number" class="form-control bg-dark text-white border-primary" name="kapasitas" placeholder="36" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Wali Kelas</label>
                        <select class="form-control bg-dark text-white border-primary" name="wali_kelas_id">
                            <option value="">Pilih Wali Kelas</option>
                            @foreach($guru as $g)
                            <option value="{{ $g->guru_id }}">{{ $g->nama_guru }}</option>
                            @endforeach
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
                    <h5 class="modal-title text-white">Edit Kelas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Kelas</label>
                        <input type="text" id="edit_nama_kelas" class="form-control bg-dark text-white border-primary" name="nama_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Jurusan</label>
                        <input type="text" id="edit_jurusan" class="form-control bg-dark text-white border-primary" name="jurusan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kapasitas</label>
                        <input type="number" id="edit_kapasitas" class="form-control bg-dark text-white border-primary" name="kapasitas" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Wali Kelas</label>
                        <select id="edit_wali_kelas_id" class="form-control bg-dark text-white border-primary" name="wali_kelas_id">
                            <!-- Options will be populated by JavaScript -->
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
                <h5 class="modal-title text-white">Detail Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Nama Kelas:</strong>
                        <p class="text-light" id="detail_nama_kelas"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Jurusan:</strong>
                        <p class="text-light" id="detail_jurusan"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Kapasitas:</strong>
                        <p class="text-light" id="detail_kapasitas"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Wali Kelas:</strong>
                        <p class="text-light" id="detail_wali_kelas"></p>
                    </div>
                    <div class="col-md-6">
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
// Cache buster: {{ time() }}
function showDetail(id) {
    console.log('showDetail called with ID:', id);
    
    fetch(`/admin/master-data/kelas/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response received:', response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(response => {
        console.log('Data received:', response);
        const data = response.data;
        document.getElementById('detail_id').textContent = data.kelas_id || '-';
        document.getElementById('detail_nama_kelas').textContent = data.nama_kelas || '-';
        document.getElementById('detail_jurusan').textContent = data.jurusan || '-';
        document.getElementById('detail_kapasitas').textContent = data.kapasitas || '-';
        document.getElementById('detail_wali_kelas').textContent = data.wali_kelas ? data.wali_kelas.nama_guru : '-';
        document.getElementById('detail_created_at').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail kelas: ' + error.message);
    });
}

function editData(id) {
    fetch(`/admin/master-data/kelas/${id}/edit`, {
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
        document.getElementById('editForm').action = `/admin/master-data/kelas/${id}`;
        document.getElementById('edit_nama_kelas').value = data.data.nama_kelas || '';
        document.getElementById('edit_jurusan').value = data.data.jurusan || '';
        document.getElementById('edit_kapasitas').value = data.data.kapasitas || '';
        
        const guruSelect = document.getElementById('edit_wali_kelas_id');
        guruSelect.innerHTML = '<option value="">Pilih Wali Kelas</option>';
        data.guru.forEach(g => {
            const option = document.createElement('option');
            option.value = g.guru_id;
            option.textContent = g.nama_guru;
            option.selected = g.guru_id == data.data.wali_kelas_id;
            guruSelect.appendChild(option);
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
    if(confirm('Yakin ingin menghapus data kelas ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/kelas/${id}`;
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