@extends('layouts.app')

@section('title', 'Master Data - Siswa')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Siswa</h6>
                    <p class="mb-0">Kelola data siswa dan informasi kesiswaan di SIPELA</p>
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
                <h6 class="mb-0">Daftar Siswa</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            <div class="table-responsive">
                <table id="siswaTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Kelas</th>
                            <th>Status Kesiswaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nis }}</td>
                            <td>{{ $item->nisn ?? '-' }}</td>
                            <td>{{ $item->nama_siswa }}</td>
                            <td>{{ $item->jenis_kelamin }}</td>
                            <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->status_kesiswaan == 'aktif' ? 'bg-success' : ($item->status_kesiswaan == 'lulus' ? 'bg-primary' : 'bg-secondary') }}">
                                    {{ ucfirst($item->status_kesiswaan) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->siswa_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->siswa_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->siswa_id }})" title="Hapus">
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
            <form action="{{ route('admin.master-data.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">NIS</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nis" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">NISN</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nisn">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Siswa</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nama_siswa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kelas</label>
                        <select class="form-control bg-dark text-white border-primary" name="kelas_id">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->kelas_id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
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
                        <label class="form-label text-white">Status Kesiswaan</label>
                        <select class="form-control bg-dark text-white border-primary" name="status_kesiswaan">
                            <option value="aktif" selected>Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah">Pindah</option>
                            <option value="drop_out">Drop Out</option>
                            <option value="cuti">Cuti</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tempat Lahir</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="tempat_lahir">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tanggal Lahir</label>
                        <div class="input-group">
                            <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal_lahir" min="1900-01-01" max="2030-12-31" id="tanggal_lahir">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_lahir').showPicker()">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Alamat</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">No. Telepon</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="no_telp">
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
                    <h5 class="modal-title text-white">Edit Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">NIS</label>
                        <input type="text" id="edit_nis" class="form-control bg-dark text-white border-primary" name="nis" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">NISN</label>
                        <input type="text" id="edit_nisn" class="form-control bg-dark text-white border-primary" name="nisn">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Siswa</label>
                        <input type="text" id="edit_nama_siswa" class="form-control bg-dark text-white border-primary" name="nama_siswa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kelas</label>
                        <select id="edit_kelas_id" class="form-control bg-dark text-white border-primary" name="kelas_id">
                            <!-- Options will be populated by JavaScript -->
                        </select>
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
                        <label class="form-label text-white">Status Kesiswaan</label>
                        <select id="edit_status_kesiswaan" class="form-control bg-dark text-white border-primary" name="status_kesiswaan">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah">Pindah</option>
                            <option value="drop_out">Drop Out</option>
                            <option value="cuti">Cuti</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tempat Lahir</label>
                        <input type="text" id="edit_tempat_lahir" class="form-control bg-dark text-white border-primary" name="tempat_lahir">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Tanggal Lahir</label>
                        <div class="input-group">
                            <input type="date" id="edit_tanggal_lahir" class="form-control bg-dark text-white border-primary" name="tanggal_lahir" min="1900-01-01" max="2030-12-31">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('edit_tanggal_lahir').showPicker()">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Alamat</label>
                        <textarea id="edit_alamat" class="form-control bg-dark text-white border-primary" name="alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">No. Telepon</label>
                        <input type="text" id="edit_no_telp" class="form-control bg-dark text-white border-primary" name="no_telp">
                    </div>
                </div>
                <div class="modal-footer border-top border-primary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" onclick="return validateEditForm()">Update</button>
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
                <h5 class="modal-title text-white">Detail Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">NIS:</strong>
                        <p class="text-light" id="detail_nis"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Nama Siswa:</strong>
                        <p class="text-light" id="detail_nama_siswa"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Kelas:</strong>
                        <p class="text-light" id="detail_kelas"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Jenis Kelamin:</strong>
                        <p class="text-light" id="detail_jenis_kelamin"></p>
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
function showDetail(id) {
    fetch(`/admin/master-data/siswa/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail_id').textContent = data.data.siswa_id;
        document.getElementById('detail_nis').textContent = data.data.nis || '-';
        document.getElementById('detail_nama_siswa').textContent = data.data.nama_siswa || '-';
        document.getElementById('detail_kelas').textContent = data.data.kelas ? data.data.kelas.nama_kelas : '-';
        document.getElementById('detail_jenis_kelamin').textContent = data.data.jenis_kelamin || '-';
        document.getElementById('detail_created_at').textContent = data.data.created_at ? new Date(data.data.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail siswa');
    });
}

function editData(id) {
    fetch(`/admin/master-data/siswa/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editForm').action = `/admin/master-data/siswa/${id}`;
        document.getElementById('edit_nis').value = data.data.nis || '';
        document.getElementById('edit_nisn').value = data.data.nisn || '';
        document.getElementById('edit_nama_siswa').value = data.data.nama_siswa || '';
        // Set jenis kelamin dengan validasi
        const jenisKelaminSelect = document.getElementById('edit_jenis_kelamin');
        jenisKelaminSelect.value = data.data.jenis_kelamin || '';
        
        // Jika masih kosong, set default
        if (!jenisKelaminSelect.value && data.data.jenis_kelamin) {
            jenisKelaminSelect.value = data.data.jenis_kelamin;
        }
        document.getElementById('edit_status_kesiswaan').value = data.data.status_kesiswaan || 'aktif';
        document.getElementById('edit_tempat_lahir').value = data.data.tempat_lahir || '';
        document.getElementById('edit_tanggal_lahir').value = data.data.tanggal_lahir || '';
        document.getElementById('edit_alamat').value = data.data.alamat || '';
        document.getElementById('edit_no_telp').value = data.data.no_telp || '';
        
        const kelasSelect = document.getElementById('edit_kelas_id');
        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
        data.kelas.forEach(k => {
            const option = document.createElement('option');
            option.value = k.kelas_id;
            option.textContent = k.nama_kelas;
            option.selected = k.kelas_id == data.data.kelas_id;
            kelasSelect.appendChild(option);
        });
        
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data untuk edit');
    });
}

function validateEditForm() {
    const jenisKelamin = document.getElementById('edit_jenis_kelamin').value;
    const namasSiswa = document.getElementById('edit_nama_siswa').value;
    
    if (!namasSiswa.trim()) {
        alert('Nama siswa harus diisi!');
        return false;
    }
    
    if (!jenisKelamin) {
        alert('Jenis kelamin harus dipilih!');
        return false;
    }
    
    return true;
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus data siswa ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/siswa/${id}`;
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

/* Fix untuk input date - bisa ketik dan kalender */
input[type="date"] {
    color: white !important;
    background-color: #212529 !important;
}

/* Sembunyikan tombol kalender default */
input[type="date"]::-webkit-calendar-picker-indicator {
    display: none;
}

input[type="date"]:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Pastikan text terlihat di dark theme */
input[type="date"]::-webkit-datetime-edit {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-fields-wrapper {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-text {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-month-field {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-day-field {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-year-field {
    color: white;
}

/* Style untuk tombol kalender eksternal */
.btn-outline-light {
    border-color: #6c757d;
    color: #f8f9fa;
}

.btn-outline-light:hover {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
    color: #212529;
}
</style>
@endpush

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const timeOptions = {
        timeZone: 'Asia/Jakarta',
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    const timeString = now.toLocaleTimeString('id-ID', timeOptions);
    const clockElements = document.querySelectorAll('.real-time-clock');
    clockElements.forEach(el => el.textContent = timeString + ' WIB');
}

// Setup date inputs untuk bisa ketik dan kalender
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Allow manual typing dan kalender
        input.addEventListener('input', function() {
            // Validasi format tanggal saat mengetik
            const value = this.value;
            if (value && !isValidDate(value)) {
                this.setCustomValidity('Format tanggal tidak valid');
            } else {
                this.setCustomValidity('');
            }
        });
    });
});

function isValidDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date) && dateString === date.toISOString().split('T')[0];
}

setInterval(updateClock, 1000);
updateClock();
</script>
@endpush