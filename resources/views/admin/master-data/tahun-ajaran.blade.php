@extends('layouts.app')

@section('title', 'Master Data - Tahun Ajaran')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Tahun Ajaran</h6>
                    <p class="mb-0">Kelola tahun ajaran dan periode akademik di SIPELA</p>
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
                <h6 class="mb-0">Master Data - Tahun Ajaran</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            <div class="table-responsive">
                <table id="tahun-ajaranTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Tahun</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->kode_tahun }}</td>
                            <td>{{ $item->tahun_ajaran }}</td>
                            <td><span class="badge bg-info">{{ $item->semester }}</span></td>
                            <td>
                                @if($item->status_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                @if($item->tanggal_mulai && $item->tanggal_selesai)
                                    {{ date('d/m/Y', strtotime($item->tanggal_mulai)) }} - {{ date('d/m/Y', strtotime($item->tanggal_selesai)) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->tahun_ajaran_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->tahun_ajaran_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->tahun_ajaran_id }})" title="Hapus">
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
            <form action="{{ route('admin.master-data.tahun-ajaran.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Kode Tahun</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="kode_tahun" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tahun Ajaran</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="tahun_ajaran" placeholder="2024/2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Semester</label>
                        <select class="form-control bg-dark text-white border-primary" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tanggal Mulai</label>
                        <div class="input-group">
                            <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal_mulai" id="tanggal_mulai">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_mulai').showPicker()">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tanggal Selesai</label>
                        <div class="input-group">
                            <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal_selesai" id="tanggal_selesai">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_selesai').showPicker()">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status_aktif" value="1">
                            <label class="form-check-label text-white">Status Aktif</label>
                        </div>
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
                    <h5 class="modal-title text-white">Edit Tahun Ajaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Kode Tahun</label>
                        <input type="text" id="edit_kode_tahun" class="form-control bg-dark text-white border-primary" name="kode_tahun" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tahun Ajaran</label>
                        <input type="text" id="edit_tahun_ajaran" class="form-control bg-dark text-white border-primary" name="tahun_ajaran" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Semester</label>
                        <select id="edit_semester" class="form-control bg-dark text-white border-primary" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tanggal Mulai</label>
                        <div class="input-group">
                            <input type="date" id="edit_tanggal_mulai" class="form-control bg-dark text-white border-primary" name="tanggal_mulai">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('edit_tanggal_mulai').showPicker()">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tanggal Selesai</label>
                        <div class="input-group">
                            <input type="date" id="edit_tanggal_selesai" class="form-control bg-dark text-white border-primary" name="tanggal_selesai">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('edit_tanggal_selesai').showPicker()">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status_aktif" value="1" id="edit_status_aktif">
                            <label class="form-check-label text-white">Status Aktif</label>
                        </div>
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
                <h5 class="modal-title text-white">Detail Tahun Ajaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Kode Tahun:</strong>
                        <p class="text-light" id="detail_kode_tahun"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Tahun Ajaran:</strong>
                        <p class="text-light" id="detail_tahun_ajaran"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Semester:</strong>
                        <p class="text-light" id="detail_semester"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Status:</strong>
                        <p class="text-light" id="detail_status_aktif"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Dibuat Tanggal:</strong>
                        <p class="text-light" id="detail_created_at"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Tanggal Mulai:</strong>
                        <p class="text-light" id="detail_tanggal_mulai"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Tanggal Selesai:</strong>
                        <p class="text-light" id="detail_tanggal_selesai"></p>
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
    fetch(`/admin/master-data/tahun-ajaran/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(response => {
        const data = response.data;
        document.getElementById('detail_id').textContent = data.tahun_ajaran_id || '-';
        document.getElementById('detail_kode_tahun').textContent = data.kode_tahun || '-';
        document.getElementById('detail_tahun_ajaran').textContent = data.tahun_ajaran || '-';
        document.getElementById('detail_semester').textContent = data.semester || '-';
        document.getElementById('detail_status_aktif').textContent = data.status_aktif ? 'Aktif' : 'Tidak Aktif';
        document.getElementById('detail_tanggal_mulai').textContent = data.tanggal_mulai ? new Date(data.tanggal_mulai).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail_tanggal_selesai').textContent = data.tanggal_selesai ? new Date(data.tanggal_selesai).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail_created_at').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail tahun ajaran');
    });
}

function editData(id) {
    fetch(`/admin/master-data/tahun-ajaran/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(response => {
        const data = response.data;
        document.getElementById('editForm').action = `/admin/master-data/tahun-ajaran/${id}`;
        document.getElementById('edit_kode_tahun').value = data.kode_tahun || '';
        document.getElementById('edit_tahun_ajaran').value = data.tahun_ajaran || '';
        document.getElementById('edit_semester').value = data.semester || '';
        document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai || '';
        document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai || '';
        document.getElementById('edit_status_aktif').checked = data.status_aktif || false;
        
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data untuk edit');
    });
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus tahun ajaran ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/tahun-ajaran/${id}`;
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

