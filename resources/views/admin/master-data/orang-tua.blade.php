@extends('layouts.app')

@section('title', 'Master Data - Orang Tua')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Orang Tua</h6>
                    <p class="mb-0">Kelola data orang tua dan wali siswa di SIPELA</p>
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
                <h6 class="mb-0">Master Data - Orang Tua</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control bg-dark text-white border-primary" id="filterHubungan" onchange="applyFilter()">
                        <option value="">Semua Hubungan</option>
                        <option value="Ayah" {{ request('hubungan') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                        <option value="Ibu" {{ request('hubungan') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                        <option value="Wali" {{ request('hubungan') == 'Wali' ? 'selected' : '' }}>Wali</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control bg-dark text-white border-primary" id="filterKelas" onchange="applyFilter()">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas ?? [] as $k)
                            <option value="{{ $k->kelas_id }}" {{ request('kelas') == $k->kelas_id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control bg-dark text-white border-primary" id="searchNama" placeholder="Cari nama orang tua..." value="{{ request('search') }}" onkeyup="handleSearch(event)">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary" onclick="resetFilter()">
                        <i class="fas fa-refresh"></i> Reset Filter
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="orang-tuaTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Orang Tua</th>
                            <th>Hubungan</th>
                            <th>Siswa</th>
                            <th>Pekerjaan</th>
                            <th>Pendidikan</th>
                            <th>No Telp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nama_orangtua }}</td>
                            <td><span class="badge bg-info">{{ $item->hubungan }}</span></td>
                            <td>{{ $item->siswa->nama_siswa ?? '-' }}</td>
                            <td>{{ $item->pekerjaan ?? '-' }}</td>
                            <td>{{ $item->pendidikan ?? '-' }}</td>
                            <td>{{ $item->no_telp ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->ortu_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->ortu_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->ortu_id }})" title="Hapus">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <form action="{{ route('admin.master-data.orang-tua.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Orang Tua</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Nama Orang Tua</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="nama_orangtua" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Hubungan</label>
                                <select class="form-control bg-dark text-white border-primary" name="hubungan" required>
                                    <option value="">Pilih Hubungan</option>
                                    <option value="Ayah">Ayah</option>
                                    <option value="Ibu">Ibu</option>
                                    <option value="Wali">Wali</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Siswa</label>
                                <select class="form-control bg-dark text-white border-primary" name="siswa_id" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($siswa ?? [] as $s)
                                        <option value="{{ $s->siswa_id }}">{{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Pekerjaan</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="pekerjaan">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Pendidikan</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="pendidikan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">No Telp</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="no_telp">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Alamat</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="alamat" rows="3"></textarea>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Edit Orang Tua</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Nama Orang Tua</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="nama_orangtua" id="edit_nama_orangtua" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Hubungan</label>
                                <select class="form-control bg-dark text-white border-primary" name="hubungan" id="edit_hubungan" required>
                                    <option value="">Pilih Hubungan</option>
                                    <option value="Ayah">Ayah</option>
                                    <option value="Ibu">Ibu</option>
                                    <option value="Wali">Wali</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Siswa</label>
                                <select class="form-control bg-dark text-white border-primary" name="siswa_id" id="edit_siswa_id" required>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Pekerjaan</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="pekerjaan" id="edit_pekerjaan">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Pendidikan</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="pendidikan" id="edit_pendidikan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">No Telp</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="no_telp" id="edit_no_telp">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Alamat</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="alamat" id="edit_alamat" rows="3"></textarea>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <div class="modal-header border-bottom border-primary">
                <h5 class="modal-title text-white">Detail Orang Tua</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Nama Orang Tua:</strong>
                        <p class="text-light" id="detail_nama_orang_tua"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Hubungan:</strong>
                        <p class="text-light" id="detail_hubungan"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Siswa:</strong>
                        <p class="text-light" id="detail_siswa"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">No Telp:</strong>
                        <p class="text-light" id="detail_no_telp"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Dibuat Tanggal:</strong>
                        <p class="text-light" id="detail_created_at"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong class="text-white">Alamat:</strong>
                        <p class="text-light" id="detail_alamat"></p>
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
// Filter functions
function applyFilter() {
    const hubungan = document.getElementById('filterHubungan').value;
    const kelas = document.getElementById('filterKelas').value;
    const search = document.getElementById('searchNama').value;
    
    let url = new URL(window.location.href);
    url.searchParams.delete('hubungan');
    url.searchParams.delete('kelas');
    url.searchParams.delete('search');
    
    if (hubungan) {
        url.searchParams.set('hubungan', hubungan);
    }
    if (kelas) {
        url.searchParams.set('kelas', kelas);
    }
    if (search) {
        url.searchParams.set('search', search);
    }
    
    window.location.href = url.toString();
}

function resetFilter() {
    let url = new URL(window.location.href);
    url.searchParams.delete('hubungan');
    url.searchParams.delete('kelas');
    url.searchParams.delete('search');
    window.location.href = url.toString();
}

function handleSearch(event) {
    if (event.key === 'Enter') {
        applyFilter();
    }
}

function showDetail(id) {
    fetch(`/admin/master-data/orang-tua/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(response => {
        const data = response.data;
        document.getElementById('detail_id').textContent = data.ortu_id || '-';
        document.getElementById('detail_nama_orang_tua').textContent = data.nama_orangtua || '-';
        document.getElementById('detail_hubungan').textContent = data.hubungan || '-';
        document.getElementById('detail_siswa').textContent = data.siswa ? data.siswa.nama_siswa : '-';
        document.getElementById('detail_no_telp').textContent = data.no_telp || '-';
        document.getElementById('detail_alamat').textContent = data.alamat || '-';
        document.getElementById('detail_created_at').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail orang tua');
    });
}

function editData(id) {
    fetch(`/admin/master-data/orang-tua/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(response => {
        const data = response.data;
        document.getElementById('editForm').action = `/admin/master-data/orang-tua/${id}`;
        document.getElementById('edit_nama_orangtua').value = data.nama_orangtua || '';
        document.getElementById('edit_hubungan').value = data.hubungan || '';
        document.getElementById('edit_pekerjaan').value = data.pekerjaan || '';
        document.getElementById('edit_pendidikan').value = data.pendidikan || '';
        document.getElementById('edit_no_telp').value = data.no_telp || '';
        document.getElementById('edit_alamat').value = data.alamat || '';
        
        const siswaSelect = document.getElementById('edit_siswa_id');
        siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>';
        response.siswa.forEach(s => {
            const option = document.createElement('option');
            option.value = s.siswa_id;
            option.textContent = s.nama_siswa + ' - ' + (s.kelas ? s.kelas.nama_kelas : '');
            option.selected = s.siswa_id == data.siswa_id;
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
    if(confirm('Yakin ingin menghapus data orang tua ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/orang-tua/${id}`;
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