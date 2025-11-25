@extends('layouts.app')

@section('title', 'Manajemen Sanksi')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Manajemen Sanksi</h6>
                    <p class="mb-0">Kelola sanksi dan tindakan disiplin siswa di SIPELA</p>
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
            <div class="mb-4">
                <h6 class="mb-0">Manajemen Sanksi</h6>
                <p class="text-muted mb-0">Kelola sanksi yang sudah dibuat dari View Data Pelanggaran</p>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-dark"><i class="fa fa-tasks me-2"></i>Filter Data Sanksi</h6>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="filterCollapse">
                            <form method="GET" action="{{ request()->url() }}">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <select class="form-control" name="tingkat">
                                            <option value="">Semua Tingkat</option>
                                            @foreach($tingkatList ?? [] as $tingkat)
                                                <option value="{{ $tingkat }}" {{ request('tingkat') == $tingkat ? 'selected' : '' }}>Kelas {{ $tingkat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" name="jurusan">
                                            <option value="">Semua Jurusan</option>
                                            @foreach($jurusanList ?? [] as $jurusan)
                                                <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>{{ $jurusan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" name="status">
                                            <option value="">Semua Status</option>
                                            <option value="terdaftar" {{ request('status') == 'terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                                            <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                                            <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="tindak_lanjut" {{ request('status') == 'tindak_lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-fill">
                                                <i class="fa fa-search"></i> Filter
                                            </button>
                                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                                                <i class="fa fa-refresh"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="indexTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Pelanggaran</th>
                            <th>Jenis Sanksi</th>
                            <th>Status Sanksi</th>
                            <th>Tanggal Ditetapkan</th>
                            <th>PIC</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $item->pelanggaran->siswa->nama_siswa ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $item->pelanggaran->siswa->kelas->nama_kelas ?? '-' }}</small>
                            </td>
                            <td>{{ $item->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                            <td>{{ $item->jenisSanksi->nama_sanksi ?? '-' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'terdaftar' => 'secondary',
                                        'dijadwalkan' => 'info', 
                                        'berlangsung' => 'warning',
                                        'selesai' => 'success',
                                        'tindak_lanjut' => 'primary'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                            <td>{{ $item->guruPenanggungjawab->nama_guru ?? 'Belum Ditentukan' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->sanksi_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->sanksi_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->sanksi_id }})" title="Hapus">
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

<!-- Modal Detail Sanksi -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">Detail Sanksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Sanksi -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sanksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editContent">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDetail(id) {
    const currentPrefix = window.location.pathname.includes('/kesiswaan/') ? 'kesiswaan' : 'admin';
    
    fetch(`/${currentPrefix}/view-data/pelanggaran/${id}/sanksi/detail`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('detailContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = '<p class="text-danger">Error loading detail</p>';
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        });
}

function editData(id) {
    const currentPrefix = window.location.pathname.includes('/kesiswaan/') ? 'kesiswaan' : 'admin';
    
    fetch(`/${currentPrefix}/view-data/pelanggaran/sanksi/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="mb-3">
                    <label class="form-label">Status Sanksi</label>
                    <select name="status" class="form-control" required>
                        <option value="terdaftar" ${data.sanksi.status === 'terdaftar' ? 'selected' : ''}>Terdaftar</option>
                        <option value="dijadwalkan" ${data.sanksi.status === 'dijadwalkan' ? 'selected' : ''}>Dijadwalkan</option>
                        <option value="berlangsung" ${data.sanksi.status === 'berlangsung' ? 'selected' : ''}>Berlangsung</option>
                        <option value="selesai" ${data.sanksi.status === 'selesai' ? 'selected' : ''}>Selesai</option>
                        <option value="tindak_lanjut" ${data.sanksi.status === 'tindak_lanjut' ? 'selected' : ''}>Tindak Lanjut</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="${data.sanksi.tanggal_mulai || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="${data.sanksi.tanggal_selesai || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Pelaksanaan</label>
                    <textarea name="catatan_pelaksanaan" class="form-control" rows="3">${data.sanksi.catatan_pelaksanaan || ''}</textarea>
                </div>
            `;
            
            document.getElementById('editContent').innerHTML = html;
            document.getElementById('editForm').action = `/${currentPrefix}/view-data/pelanggaran/sanksi/${id}`;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        })
        .catch(error => {
            alert('Error loading edit form');
        });
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus sanksi ini? Data yang dihapus tidak dapat dikembalikan.')) {
        const currentPrefix = window.location.pathname.includes('/kesiswaan/') ? 'kesiswaan' : 'admin';
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/${currentPrefix}/sanksi/${id}`;
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