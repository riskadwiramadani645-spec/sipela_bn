@extends('layouts.app')

@section('title', 'Pelaksanaan Sanksi')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Pelaksanaan Sanksi</h6>
                    <p class="mb-0">Monitor dan kelola pelaksanaan sanksi siswa di SIPELA</p>
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
                <h6 class="mb-0">Pelaksanaan Sanksi</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Pelaksanaan
                </button>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-dark"><i class="fa fa-tasks me-2"></i>Filter Pelaksanaan Sanksi</h6>
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
                                                <option value="{{ $tingkat }}" {{ request('tingkat') == $tingkat ? 'selected' : '' }}>{{ $tingkat }}</option>
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
                                            <option value="terjadwal" {{ request('status') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                            <option value="dikerjakan" {{ request('status') == 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                            <option value="tuntas" {{ request('status') == 'tuntas' ? 'selected' : '' }}>Tuntas</option>
                                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                            <option value="perpanjangan" {{ request('status') == 'perpanjangan' ? 'selected' : '' }}>Perpanjangan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <input type="date" class="form-control" name="tanggal" value="{{ request('tanggal') }}" style="flex: 2; color: white;">
                                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                                <i class="fa fa-search"></i>
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
                <table id="pelaksanaanTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Sanksi</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Status</th>
                            <th>Guru Pengawas</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $item->sanksi->pelanggaran->siswa->nama_siswa ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $item->sanksi->pelanggaran->siswa->kelas->nama_kelas ?? '-' }}</small>
                            </td>
                            <td>
                                {{ $item->sanksi->jenisSanksi->nama_sanksi ?? '-' }}
                                <br><small class="text-muted">{{ $item->sanksi->deskripsi_sanksi ?? '' }}</small>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($item->tanggal_pelaksanaan)) }}</td>
                            <td>
                                @if($item->status == 'terjadwal')
                                    <span class="badge bg-warning">Terjadwal</span>
                                @elseif($item->status == 'dikerjakan')
                                    <span class="badge bg-info">Dikerjakan</span>
                                @elseif($item->status == 'tuntas')
                                    <span class="badge bg-success">Tuntas</span>
                                @elseif($item->status == 'terlambat')
                                    <span class="badge bg-danger">Terlambat</span>
                                @else
                                    <span class="badge bg-secondary">Perpanjangan</span>
                                @endif
                            </td>
                            <td>{{ $item->guruPengawas->nama_guru ?? '-' }}</td>
                            <td>
                                @if($item->bukti_pelaksanaan)
                                    <a href="{{ asset('storage/' . $item->bukti_pelaksanaan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-file"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->pelaksanaan_sanksi_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->pelaksanaan_sanksi_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->pelaksanaan_sanksi_id }})" title="Hapus">
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
            <form action="{{ 
                request()->route()->getPrefix() === 'admin' 
                    ? route('admin.sanksi.pelaksanaan.store') 
                    : route('kesiswaan.sanksi.pelaksanaan.store') 
            }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Pelaksanaan Sanksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Sanksi</label>
                                <select class="form-control bg-dark text-white border-primary" name="sanksi_id" required>
                                    <option value="">Pilih Sanksi</option>
                                    @foreach($sanksiAktif as $sk)
                                        <option value="{{ $sk->sanksi_id }}">
                                            {{ $sk->pelanggaran->siswa->nama_siswa ?? '' }} - {{ $sk->jenisSanksi->nama_sanksi ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hanya sanksi dengan status "berjalan"</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal Pelaksanaan</label>
                                <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal_pelaksanaan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Status Pelaksanaan</label>
                                <select class="form-control bg-dark text-white border-primary" name="status" required>
                                    <option value="terjadwal">Terjadwal</option>
                                    <option value="dikerjakan">Dikerjakan</option>
                                    <option value="tuntas">Tuntas</option>
                                    <option value="terlambat">Terlambat</option>
                                    <option value="perpanjangan">Perpanjangan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Guru Pengawas</label>
                        <select class="form-control bg-dark text-white border-primary" name="guru_pengawas">
                            <option value="">Pilih Guru Pengawas</option>
                            @foreach($guru as $g)
                                <option value="{{ $g->guru_id }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Deskripsi Pelaksanaan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="deskripsi_pelaksanaan" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bukti Pelaksanaan</label>
                        <input type="file" class="form-control bg-dark text-white border-primary" name="bukti_pelaksanaan" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF. Maksimal 5MB (Opsional)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Catatan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="catatan" rows="3"></textarea>
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pelaksanaan Sanksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pelaksanaan Sanksi</h5>
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
    
    fetch(`/${currentPrefix}/sanksi/pelaksanaan/${id}`)
        .then(response => response.json())
        .then(data => {
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Siswa</h6>
                        <p><strong>Nama:</strong> ${data.siswa_nama}</p>
                        <p><strong>Kelas:</strong> ${data.kelas_nama}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Sanksi</h6>
                        <p><strong>Jenis:</strong> ${data.jenis_sanksi}</p>
                        <p><strong>Status:</strong> ${data.status}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Detail Pelaksanaan</h6>
                        <p><strong>Tanggal:</strong> ${data.tanggal_pelaksanaan}</p>
                        <p><strong>Deskripsi:</strong> ${data.deskripsi_pelaksanaan || '-'}</p>
                        <p><strong>Catatan:</strong> ${data.catatan || '-'}</p>
                        <p><strong>Guru Pengawas:</strong> ${data.guru_pengawas || '-'}</p>
                    </div>
                </div>
            `;
            
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
    
    fetch(`/${currentPrefix}/sanksi/pelaksanaan/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            const html = `
                <div class="mb-3">
                    <label class="form-label">Status Pelaksanaan</label>
                    <select name="status" class="form-control" required>
                        <option value="terjadwal" ${data.status === 'terjadwal' ? 'selected' : ''}>Terjadwal</option>
                        <option value="dikerjakan" ${data.status === 'dikerjakan' ? 'selected' : ''}>Dikerjakan</option>
                        <option value="tuntas" ${data.status === 'tuntas' ? 'selected' : ''}>Tuntas</option>
                        <option value="terlambat" ${data.status === 'terlambat' ? 'selected' : ''}>Terlambat</option>
                        <option value="perpanjangan" ${data.status === 'perpanjangan' ? 'selected' : ''}>Perpanjangan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal_pelaksanaan" class="form-control" value="${data.tanggal_pelaksanaan}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Pelaksanaan</label>
                    <textarea name="deskripsi_pelaksanaan" class="form-control" rows="3">${data.deskripsi_pelaksanaan || ''}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3">${data.catatan || ''}</textarea>
                </div>
            `;
            
            document.getElementById('editContent').innerHTML = html;
            document.getElementById('editForm').action = `/${currentPrefix}/sanksi/pelaksanaan/${id}`;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        })
        .catch(error => {
            alert('Error loading edit form');
        });
}

function deleteData(id) {
    if (confirm('Yakin ingin menghapus data ini?')) {
        const currentPrefix = window.location.pathname.includes('/kesiswaan/') ? 'kesiswaan' : 'admin';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/${currentPrefix}/sanksi/pelaksanaan/${id}`;
        
        // Get CSRF token from existing form or create one
        let csrfToken = '';
        const existingToken = document.querySelector('input[name="_token"]');
        if (existingToken) {
            csrfToken = existingToken.value;
        } else {
            // Fallback: get from meta tag if exists
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            } else {
                // Last resort: get from Laravel global if available
                csrfToken = window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : '';
            }
        }
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection