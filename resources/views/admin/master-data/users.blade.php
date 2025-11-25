@extends('layouts.app')

@section('title', 'Kelola Pengguna - SIPELA')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Users</h6>
                    <p class="mb-0">Kelola pengguna dan hak akses sistem di SIPELA</p>
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
                <h6 class="mb-0">Kelola Pengguna</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-control bg-dark text-white border-primary" id="filterRole" onchange="handleFilterChange()">
                        <option value="">Semua User</option>
                        <option value="Guru" {{ request('role') == 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Siswa" {{ request('role') == 'Siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
                
                <!-- Filter Tingkat (hanya muncul jika pilih Siswa) -->
                <div class="col-md-4" id="filterTingkat" style="{{ request('role') == 'Siswa' ? 'display:block;' : 'display:none;' }}">
                    <select class="form-control bg-dark text-white border-primary" id="filterTingkatSelect" onchange="applyFilter()">
                        <option value="">Semua Tingkat</option>
                        <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
                        <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <button class="btn btn-secondary" onclick="resetFilter()">
                        <i class="fas fa-refresh"></i> Reset Filter
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="usersTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Level</th>
                            <th>Profil Terkait</th>
                            <th>Dapat Verifikasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key => $user)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $user->username }}</td>
                            <td><span class="badge bg-primary">{{ ucfirst($user->level) }}</span></td>
                            <td>
                                @if($user->guru)
                                    <small class="text-info">Guru:</small> {{ $user->guru->nama_guru }}
                                @elseif($user->siswa)
                                    <small class="text-info">Siswa:</small> {{ $user->siswa->nama_siswa }}<br>
                                    <small class="text-muted">{{ $user->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</small>
                                @elseif($user->orangTua)
                                    <small class="text-info">Ortu:</small> {{ $user->orangTua->nama_orangtua }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->can_verify)
                                    <span class="badge bg-success">Ya</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $user->user_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $user->user_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $user->user_id }})" title="Hapus">
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
            <form action="{{ route('admin.master-data.users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Pengguna</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Basic User Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Username</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Password</label>
                                <input type="password" class="form-control bg-dark text-white border-primary" name="password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Level</label>
                                <select class="form-control bg-dark text-white border-primary" name="level" id="userLevel" required onchange="handleLevelChange()">
                                    <option value="">Pilih Level</option>
                                    <option value="admin">Admin</option>
                                    <option value="kesiswaan">Kesiswaan</option>
                                    <option value="guru">Guru</option>
                                    <option value="konselor_bk">Konselor BK</option>
                                    <option value="kepala_sekolah">Kepala Sekolah</option>
                                    <option value="siswa">Siswa</option>
                                    <option value="orang_tua">Orang Tua</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Nama Lengkap (Opsional)</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="nama_lengkap">
                            </div>
                        </div>
                    </div>

                    <!-- Profile Mode Selection -->
                    <div id="profileSection" style="display: none;">
                        <hr class="border-primary">
                        <h6 class="text-white mb-3">Data Profil</h6>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="input_mode" id="existingMode" value="existing" onchange="handleModeChange()">
                                <label class="form-check-label text-white" for="existingMode">
                                    Pilih dari data yang sudah ada
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="input_mode" id="newMode" value="new" onchange="handleModeChange()">
                                <label class="form-check-label text-white" for="newMode">
                                    Buat data profil baru
                                </label>
                            </div>
                        </div>

                        <!-- Existing Profile Selection -->
                        <div id="existingProfileSection" style="display: none;">
                            <div id="existingGuruSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pilih Guru</label>
                                    <select class="form-control bg-dark text-white border-primary" name="guru_id">
                                        <option value="">Pilih Guru</option>
                                        @foreach($allGuru as $g)
                                            <option value="{{ $g->guru_id }}">{{ $g->nama_guru }} - NIP: {{ $g->nip ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Debug: Total guru: {{ count($allGuru) }}, Available: {{ count($availableGuru) }}</small>
                                </div>
                            </div>
                            
                            <div id="existingSiswaSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pilih Siswa</label>
                                    <select class="form-control bg-dark text-white border-primary" name="siswa_id">
                                        <option value="">Pilih Siswa</option>
                                        @foreach($allSiswa as $s)
                                            <option value="{{ $s->siswa_id }}">{{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Debug: Total siswa: {{ count($allSiswa) }}, Available: {{ count($availableSiswa) }}</small>
                                </div>
                            </div>
                            
                            <div id="existingOrtuSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pilih Orang Tua</label>
                                    <select class="form-control bg-dark text-white border-primary" name="ortu_id">
                                        <option value="">Pilih Orang Tua</option>
                                        @foreach($allOrangTua as $o)
                                            <option value="{{ $o->ortu_id }}">{{ $o->nama_orangtua }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Debug: Total orang tua: {{ count($allOrangTua) }}, Available: {{ count($availableOrangTua) }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- New Profile Creation -->
                        <div id="newProfileSection" style="display: none;">
                            <div id="newGuruSection" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Nama Guru</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nama_guru">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">NIP</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nip">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Mata Pelajaran</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="mata_pelajaran">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">No. HP</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="no_hp_guru">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="newSiswaSection" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Nama Siswa</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nama_siswa">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">NIS</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nis">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Kelas</label>
                                            <select class="form-control bg-dark text-white border-primary" name="kelas_id">
                                                <option value="">Pilih Kelas</option>
                                                @foreach($allKelas as $k)
                                                    <option value="{{ $k->kelas_id }}">{{ $k->nama_kelas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Tahun Masuk</label>
                                            <input type="number" class="form-control bg-dark text-white border-primary" name="tahun_masuk" min="2000" max="{{ date('Y') + 1 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">No. HP</label>
                                    <input type="text" class="form-control bg-dark text-white border-primary" name="no_hp_siswa">
                                </div>
                            </div>
                            
                            <div id="newOrtuSection" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Nama Orang Tua</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nama_orangtua">
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
                                            <label class="form-label text-white">No. HP</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="no_hp_ortu">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Alamat</label>
                                            <textarea class="form-control bg-dark text-white border-primary" name="alamat" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="can_verify" value="1">
                            <label class="form-check-label text-white">Dapat Verifikasi</label>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Edit Pengguna</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Basic User Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Username</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="username" id="edit_username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Password (Kosongkan jika tidak diubah)</label>
                                <input type="password" class="form-control bg-dark text-white border-primary" name="password">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Level</label>
                                <select class="form-control bg-dark text-white border-primary" name="level" id="edit_level" required onchange="handleEditLevelChange()">
                                    <option value="">Pilih Level</option>
                                    <option value="admin">Admin</option>
                                    <option value="kesiswaan">Kesiswaan</option>
                                    <option value="guru">Guru</option>
                                    <option value="konselor_bk">Konselor BK</option>
                                    <option value="kepala_sekolah">Kepala Sekolah</option>
                                    <option value="siswa">Siswa</option>
                                    <option value="orang_tua">Orang Tua</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Nama Lengkap (Opsional)</label>
                                <input type="text" class="form-control bg-dark text-white border-primary" name="nama_lengkap" id="edit_nama_lengkap">
                            </div>
                        </div>
                    </div>

                    <!-- Profile Mode Selection -->
                    <div id="editProfileSection" style="display: none;">
                        <hr class="border-primary">
                        <h6 class="text-white mb-3">Data Profil</h6>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="input_mode" id="editExistingMode" value="existing" onchange="handleEditModeChange()">
                                <label class="form-check-label text-white" for="editExistingMode">
                                    Pilih dari data yang sudah ada
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="input_mode" id="editNewMode" value="new" onchange="handleEditModeChange()">
                                <label class="form-check-label text-white" for="editNewMode">
                                    Buat data profil baru
                                </label>
                            </div>
                        </div>

                        <!-- Existing Profile Selection -->
                        <div id="editExistingProfileSection" style="display: none;">
                            <div id="editExistingGuruSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pilih Guru</label>
                                    <select class="form-control bg-dark text-white border-primary" name="guru_id" id="edit_guru_id">
                                        <option value="">Pilih Guru</option>
                                        @foreach($allGuru as $g)
                                            <option value="{{ $g->guru_id }}">{{ $g->nama_guru }} - NIP: {{ $g->nip ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div id="editExistingSiswaSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pilih Siswa</label>
                                    <select class="form-control bg-dark text-white border-primary" name="siswa_id" id="edit_siswa_id">
                                        <option value="">Pilih Siswa</option>
                                        @foreach($allSiswa as $s)
                                            <option value="{{ $s->siswa_id }}">{{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div id="editExistingOrtuSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pilih Orang Tua</label>
                                    <select class="form-control bg-dark text-white border-primary" name="ortu_id" id="edit_ortu_id">
                                        <option value="">Pilih Orang Tua</option>
                                        @foreach($allOrangTua as $o)
                                            <option value="{{ $o->ortu_id }}">{{ $o->nama_orangtua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- New Profile Creation -->
                        <div id="editNewProfileSection" style="display: none;">
                            <div id="editNewGuruSection" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Nama Guru</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nama_guru" id="edit_nama_guru">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">NIP</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nip" id="edit_nip">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Mata Pelajaran</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="mata_pelajaran" id="edit_mata_pelajaran">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">No. HP</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="no_hp_guru" id="edit_no_hp_guru">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="editNewSiswaSection" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Nama Siswa</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nama_siswa" id="edit_nama_siswa">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">NIS</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nis" id="edit_nis">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Kelas</label>
                                            <select class="form-control bg-dark text-white border-primary" name="kelas_id" id="edit_kelas_id">
                                                <option value="">Pilih Kelas</option>
                                                @foreach($allKelas as $k)
                                                    <option value="{{ $k->kelas_id }}">{{ $k->nama_kelas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Tahun Masuk</label>
                                            <input type="number" class="form-control bg-dark text-white border-primary" name="tahun_masuk" id="edit_tahun_masuk" min="2000" max="{{ date('Y') + 1 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">No. HP</label>
                                    <input type="text" class="form-control bg-dark text-white border-primary" name="no_hp_siswa" id="edit_no_hp_siswa">
                                </div>
                            </div>
                            
                            <div id="editNewOrtuSection" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Nama Orang Tua</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="nama_orangtua" id="edit_nama_orangtua">
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
                                            <label class="form-label text-white">No. HP</label>
                                            <input type="text" class="form-control bg-dark text-white border-primary" name="no_hp_ortu" id="edit_no_hp_ortu">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-white">Alamat</label>
                                            <textarea class="form-control bg-dark text-white border-primary" name="alamat" id="edit_alamat" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="can_verify" value="1" id="edit_can_verify">
                            <label class="form-check-label text-white">Dapat Verifikasi</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
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
                <h5 class="modal-title text-white">Detail Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Username:</strong>
                        <p class="text-light" id="detail_username"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Level:</strong>
                        <p class="text-light" id="detail_level"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Guru:</strong>
                        <p class="text-light" id="detail_guru"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Dapat Verifikasi:</strong>
                        <p class="text-light" id="detail_can_verify"></p>
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
// Filter functions
function handleFilterChange() {
    const role = document.getElementById('filterRole').value;
    const tingkatDiv = document.getElementById('filterTingkat');
    
    if (role === 'Siswa') {
        tingkatDiv.style.display = 'block';
    } else {
        tingkatDiv.style.display = 'none';
        // Auto apply filter when role changes
        applyFilter();
    }
}

function applyFilter() {
    const role = document.getElementById('filterRole').value;
    const tingkat = document.getElementById('filterTingkatSelect').value;
    
    let url = new URL(window.location.href);
    url.searchParams.delete('role');
    url.searchParams.delete('tingkat');
    
    if (role) {
        url.searchParams.set('role', role);
    }
    if (tingkat && role === 'Siswa') {
        url.searchParams.set('tingkat', tingkat);
    }
    
    window.location.href = url.toString();
}

function resetFilter() {
    let url = new URL(window.location.href);
    url.searchParams.delete('role');
    url.searchParams.delete('tingkat');
    window.location.href = url.toString();
}

// Auto apply filter when tingkat changes
document.getElementById('filterRole').addEventListener('change', function() {
    if (this.value !== 'Siswa') {
        applyFilter();
    }
});

function handleLevelChange() {
    const level = document.getElementById('userLevel').value;
    const profileSection = document.getElementById('profileSection');
    
    if (level && level !== 'admin') {
        profileSection.style.display = 'block';
        // Reset mode selection
        document.getElementById('existingMode').checked = false;
        document.getElementById('newMode').checked = false;
        handleModeChange();
    } else {
        profileSection.style.display = 'none';
    }
}

function handleModeChange() {
    const level = document.getElementById('userLevel').value;
    const existingMode = document.getElementById('existingMode').checked;
    const newMode = document.getElementById('newMode').checked;
    
    const existingSection = document.getElementById('existingProfileSection');
    const newSection = document.getElementById('newProfileSection');
    
    // Hide all sections first
    existingSection.style.display = 'none';
    newSection.style.display = 'none';
    
    // Hide all profile type sections
    ['existingGuruSection', 'existingSiswaSection', 'existingOrtuSection'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    ['newGuruSection', 'newSiswaSection', 'newOrtuSection'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    
    if (existingMode) {
        existingSection.style.display = 'block';
        if (['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'].includes(level)) {
            document.getElementById('existingGuruSection').style.display = 'block';
        } else if (level === 'siswa') {
            document.getElementById('existingSiswaSection').style.display = 'block';
        } else if (level === 'orang_tua') {
            document.getElementById('existingOrtuSection').style.display = 'block';
        }
    } else if (newMode) {
        newSection.style.display = 'block';
        if (['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'].includes(level)) {
            document.getElementById('newGuruSection').style.display = 'block';
        } else if (level === 'siswa') {
            document.getElementById('newSiswaSection').style.display = 'block';
        } else if (level === 'orang_tua') {
            document.getElementById('newOrtuSection').style.display = 'block';
        }
    }
}

// Edit mode functions
function handleEditLevelChange() {
    const level = document.getElementById('edit_level').value;
    const profileSection = document.getElementById('editProfileSection');
    
    if (level && level !== 'admin') {
        profileSection.style.display = 'block';
        // Reset mode selection
        document.getElementById('editExistingMode').checked = false;
        document.getElementById('editNewMode').checked = false;
        handleEditModeChange();
    } else {
        profileSection.style.display = 'none';
    }
}

function handleEditModeChange() {
    const level = document.getElementById('edit_level').value;
    const existingMode = document.getElementById('editExistingMode').checked;
    const newMode = document.getElementById('editNewMode').checked;
    
    const existingSection = document.getElementById('editExistingProfileSection');
    const newSection = document.getElementById('editNewProfileSection');
    
    // Hide all sections first
    existingSection.style.display = 'none';
    newSection.style.display = 'none';
    
    // Hide all profile type sections
    ['editExistingGuruSection', 'editExistingSiswaSection', 'editExistingOrtuSection'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    ['editNewGuruSection', 'editNewSiswaSection', 'editNewOrtuSection'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    
    if (existingMode) {
        existingSection.style.display = 'block';
        if (['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'].includes(level)) {
            document.getElementById('editExistingGuruSection').style.display = 'block';
        } else if (level === 'siswa') {
            document.getElementById('editExistingSiswaSection').style.display = 'block';
        } else if (level === 'orang_tua') {
            document.getElementById('editExistingOrtuSection').style.display = 'block';
        }
    } else if (newMode) {
        newSection.style.display = 'block';
        if (['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'].includes(level)) {
            document.getElementById('editNewGuruSection').style.display = 'block';
        } else if (level === 'siswa') {
            document.getElementById('editNewSiswaSection').style.display = 'block';
        } else if (level === 'orang_tua') {
            document.getElementById('editNewOrtuSection').style.display = 'block';
        }
    }
}

function showDetail(id) {
    fetch(`/admin/master-data/users/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail_id').textContent = data.user.user_id;
        document.getElementById('detail_username').textContent = data.user.username;
        document.getElementById('detail_level').textContent = data.user.level;
        document.getElementById('detail_guru').textContent = data.user.guru ? data.user.guru.nama_guru : '-';
        document.getElementById('detail_can_verify').textContent = data.user.can_verify ? 'Ya' : 'Tidak';
        document.getElementById('detail_created_at').textContent = data.user.created_at ? new Date(data.user.created_at).toLocaleDateString('id-ID') : '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail pengguna');
    });
}

function editData(id) {
    fetch(`/admin/master-data/users/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Set form action
        document.getElementById('editForm').action = `/admin/master-data/users/${id}`;
        
        // Fill basic user data
        document.getElementById('edit_username').value = data.user.username || '';
        document.getElementById('edit_level').value = data.user.level || '';
        document.getElementById('edit_nama_lengkap').value = data.user.nama_lengkap || '';
        document.getElementById('edit_can_verify').checked = data.user.can_verify || false;
        document.getElementById('edit_is_active').checked = data.user.is_active || false;
        
        // Handle profile section based on level
        handleEditLevelChange();
        
        // If user has existing profile, set to existing mode and fill data
        if (data.user.guru_id || data.user.siswa_id || data.user.ortu_id) {
            document.getElementById('editExistingMode').checked = true;
            handleEditModeChange();
            
            // Set selected profile
            if (data.user.guru_id) {
                document.getElementById('edit_guru_id').value = data.user.guru_id;
                // Fill profile data for potential new profile creation
                if (data.user.guru) {
                    document.getElementById('edit_nama_guru').value = data.user.guru.nama_guru || '';
                    document.getElementById('edit_nip').value = data.user.guru.nip || '';
                    document.getElementById('edit_mata_pelajaran').value = data.user.guru.mata_pelajaran || '';
                    document.getElementById('edit_no_hp_guru').value = data.user.guru.no_hp || '';
                }
            } else if (data.user.siswa_id) {
                document.getElementById('edit_siswa_id').value = data.user.siswa_id;
                if (data.user.siswa) {
                    document.getElementById('edit_nama_siswa').value = data.user.siswa.nama_siswa || '';
                    document.getElementById('edit_nis').value = data.user.siswa.nis || '';
                    document.getElementById('edit_kelas_id').value = data.user.siswa.kelas_id || '';
                    document.getElementById('edit_tahun_masuk').value = data.user.siswa.tahun_masuk || '';
                    document.getElementById('edit_no_hp_siswa').value = data.user.siswa.no_hp || '';
                }
            } else if (data.user.ortu_id) {
                document.getElementById('edit_ortu_id').value = data.user.ortu_id;
                if (data.user.orang_tua) {
                    document.getElementById('edit_nama_orangtua').value = data.user.orang_tua.nama_orangtua || '';
                    document.getElementById('edit_pekerjaan').value = data.user.orang_tua.pekerjaan || '';
                    document.getElementById('edit_no_hp_ortu').value = data.user.orang_tua.no_hp || '';
                    document.getElementById('edit_alamat').value = data.user.orang_tua.alamat || '';
                }
            }
        }
        
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data untuk edit');
    });
}

function deleteData(id) {
    if(confirm('Yakin ingin menghapus pengguna ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/master-data/users/${id}`;
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