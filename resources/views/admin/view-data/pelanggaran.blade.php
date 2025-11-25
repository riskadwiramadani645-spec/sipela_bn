@extends('layouts.app')

@section('title', 'Data Pelanggaran - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    @php
                        $filter = request()->get('filter');
                        $currentPrefix = request()->route()->getPrefix();
                        $user = session('user');
                        $isGuruKelas = $currentPrefix === 'guru' && $filter === 'kelas';
                        $isGuru = $currentPrefix === 'guru' || $user->level === 'guru';
                    @endphp
                    <h6 class="mb-1">View Data - {{ $isGuruKelas ? 'Pelanggaran Kelas' : 'Pelanggaran' }}</h6>
                    <p class="mb-0">
                        @if($isGuru)
                            Lihat pelanggaran yang Anda input sendiri
                        @else
                            Lihat dan kelola data pelanggaran siswa di SIPELA
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $currentPrefix = request()->route()->getPrefix();
    $viewDataRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.pelanggaran.index' : 'admin.view-data.pelanggaran';
    $storeRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.input-pelanggaran.store' : 'admin.input-data.pelanggaran.store';
@endphp
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-0">{{ $isGuruKelas ? 'Daftar Pelanggaran Kelas' : 'Daftar Pelanggaran' }}</h6>
                    @if($isGuru)
                        <div class="mt-2">
                            <span class="badge bg-info">Menampilkan: Pelanggaran yang Anda input sendiri</span>
                            <small class="text-muted ms-2">Anda hanya dapat melihat dan mengedit data pelanggaran yang Anda input</small>
                        </div>
                    @endif
                </div>
                @php
                    $currentPrefix = request()->route()->getPrefix();
                    $inputRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.input-data.pelanggaran' : 'admin.input-data.pelanggaran';
                @endphp
                <a href="{{ route($inputRoute) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pelanggaran
                </a>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                        <div class="flex-fill" style="min-width: 150px;">
                            <label class="form-label text-white small mb-1">Tingkat</label>
                            <select name="tingkat" class="form-select form-select-sm bg-dark text-white border-primary">
                                <option value="">Semua Tingkat</option>
                                @if(isset($tingkatList))
                                    @foreach($tingkatList as $tingkat)
                                        <option value="{{ $tingkat }}" {{ request('tingkat') == $tingkat ? 'selected' : '' }}>{{ $tingkat }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex-fill" style="min-width: 150px;">
                            <label class="form-label text-white small mb-1">Jurusan</label>
                            <select name="jurusan" class="form-select form-select-sm bg-dark text-white border-primary">
                                <option value="">Semua Jurusan</option>
                                @if(isset($jurusanList))
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>{{ $jurusan }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex-fill" style="min-width: 150px;">
                            <label class="form-label text-white small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm bg-dark text-white border-primary">
                                <option value="">Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table id="pelanggaranTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Tanggal</th>
                            <th>Poin</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Sanksi</th>
                            <th>Pencatat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $item->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $item->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $item->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                            <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge bg-danger">{{ $item->poin }} poin</span></td>
                            <td>
                                @if($item->bukti_foto)
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-file-image"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status_verifikasi == 'menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($item->status_verifikasi == 'diverifikasi')
                                    <span class="badge bg-success">Diverifikasi</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($item->sanksi)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> {{ ucfirst($item->sanksi->status ?? 'Ada') }}</span>
                                @elseif($item->status_verifikasi === 'diverifikasi')
                                    <button class="btn btn-sm btn-outline-warning" onclick="createSanksi({{ $item->pelanggaran_id }})">
                                        <i class="fas fa-gavel"></i> Buat Sanksi
                                    </button>
                                @else
                                    <span class="badge bg-secondary">Belum Diverifikasi</span>
                                @endif
                            </td>
                            <td>
                                @if($item->catatan_verifikasi && str_contains($item->catatan_verifikasi, 'Pencatat:'))
                                    {{ trim(str_replace('Pencatat:', '', $item->catatan_verifikasi)) }}
                                @elseif($item->guruPencatat)
                                    {{ $item->guruPencatat->nama_guru }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->pelanggaran_id }})" title="Detail & Sanksi">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    @php
                                        $canEdit = true;
                                        if($isGuru) {
                                            // Guru hanya bisa edit data yang mereka input sendiri
                                            $guru = \App\Models\Guru::where('nip', $user->username)
                                                        ->orWhere('email', $user->username)
                                                        ->first();
                                            if (!$guru) {
                                                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
                                            }
                                            $guruId = $guru ? $guru->guru_id : -1;
                                            $canEdit = $item->guru_pencatat == $guruId;
                                        }
                                    @endphp
                                    @if($canEdit)
                                        <button class="btn btn-sm btn-warning" onclick="editData({{ $item->pelanggaran_id }})" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->pelanggaran_id }})" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat mengedit data orang lain">
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    @endif
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
            @php
                $currentPrefix = request()->route()->getPrefix();
                $storeRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.view-data.pelanggaran.store' : 'admin.view-data.pelanggaran.store';
            @endphp
            <form action="{{ route($storeRoute) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Pelanggaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-long">
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
                                <label class="form-label text-white">Jenis Pelanggaran</label>
                                <select class="form-control bg-dark text-white border-primary" name="jenis_pelanggaran_id" required>
                                    <option value="">Pilih Jenis Pelanggaran</option>
                                    @foreach($jenisPelanggaran ?? [] as $jp)
                                        <option value="{{ $jp->jenis_pelanggaran_id }}">{{ $jp->nama_pelanggaran }} ({{ $jp->poin }} poin)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal</label>
                                <div class="input-group">
                                    <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal" value="{{ date('Y-m-d') }}" required id="add_tanggal_pelanggaran">
                                    <button type="button" class="btn btn-outline-light" onclick="document.getElementById('add_tanggal_pelanggaran').showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tahun Ajaran</label>
                                <select class="form-control bg-dark text-white border-primary" name="tahun_ajaran_id" required>
                                    @php $tahunAjaran = \App\Models\TahunAjaran::where('status_aktif', true)->first(); @endphp
                                    @if($tahunAjaran)
                                        <option value="{{ $tahunAjaran->tahun_ajaran_id }}" selected>{{ $tahunAjaran->tahun_ajaran }} - {{ $tahunAjaran->semester }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Keterangan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="keterangan" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bukti Foto (Opsional)</label>
                        <input type="file" class="form-control bg-dark text-white border-primary" name="bukti_foto" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF. Maksimal 5MB</small>
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
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Edit Pelanggaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                                <label class="form-label text-white">Jenis Pelanggaran</label>
                                <select class="form-control bg-dark text-white border-primary" name="jenis_pelanggaran_id" id="edit_jenis_pelanggaran_id" required>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal</label>
                                <div class="input-group">
                                    <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal" id="edit_tanggal" required>
                                    <button type="button" class="btn btn-outline-light" onclick="document.getElementById('edit_tanggal').showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Keterangan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="keterangan" id="edit_keterangan" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bukti Foto Saat Ini</label>
                        <div id="current_bukti_foto"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Ganti Bukti Foto (Opsional)</label>
                        <input type="file" class="form-control bg-dark text-white border-primary" name="bukti_foto" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF. Maksimal 5MB. Kosongkan jika tidak ingin mengganti.</small>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-secondary">
            <div class="modal-header border-bottom border-primary">
                <h5 class="modal-title text-white">Detail Pelanggaran & Sanksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Detail Pelanggaran -->
                <h6 class="text-white mb-3">Detail Pelanggaran</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong class="text-white">Siswa:</strong>
                        <p class="text-light" id="detail_siswa"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Jenis Pelanggaran:</strong>
                        <p class="text-light" id="detail_jenis_pelanggaran"></p>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong class="text-white">Tanggal:</strong>
                        <p class="text-light" id="detail_tanggal"></p>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-white">Poin:</strong>
                        <p class="text-light" id="detail_poin"></p>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-white">Status:</strong>
                        <p class="text-light" id="detail_status"></p>
                    </div>
                </div>
                
                <!-- Sanksi Section -->
                <hr class="border-primary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white mb-0">Sanksi</h6>
                    <button class="btn btn-sm btn-warning" id="btnCreateSanksi" onclick="showCreateSanksiForm()">
                        <i class="fas fa-gavel"></i> Buat Sanksi
                    </button>
                </div>
                <div id="sanksiContent">
                    <!-- Sanksi content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer border-top border-primary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal Buat Sanksi (Form Lengkap dari Manajemen Sanksi) -->
<div class="modal fade" id="sanksiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <form id="sanksiForm" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Buat Sanksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Jenis Sanksi</label>
                                <select class="form-control bg-dark text-white border-primary" name="jenis_sanksi_id" required>
                                    <option value="">Pilih Jenis Sanksi</option>
                                    @if(isset($jenisSanksi) && count($jenisSanksi) > 0)
                                        @foreach($jenisSanksi as $js)
                                            <option value="{{ $js->jenis_sanksi_id }}">
                                                {{ $js->nama_sanksi }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Status Sanksi</label>
                                <select class="form-control bg-dark text-white border-primary" name="status" required>
                                    <option value="terdaftar" selected>Terdaftar</option>
                                    <option value="dijadwalkan">Dijadwalkan</option>
                                    <option value="berlangsung">Berlangsung</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="tindak_lanjut">Tindak Lanjut</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Guru Penanggungjawab</label>
                                <select class="form-control bg-dark text-white border-primary" name="guru_penanggungjawab" required>
                                    <option value="">Pilih Guru Penanggungjawab</option>
                                    @php
                                        $guruList = \App\Models\Guru::where('status', 'Aktif')->get();
                                    @endphp
                                    @foreach($guruList as $g)
                                        <option value="{{ $g->guru_id }}">
                                            {{ $g->nama_guru }} - {{ $g->bidang_studi ?? 'Guru' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal Mulai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal_mulai" id="sanksi_tanggal_mulai" value="{{ date('Y-m-d') }}">
                                    <button type="button" class="btn btn-outline-light" onclick="document.getElementById('sanksi_tanggal_mulai').showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal Selesai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal_selesai" id="sanksi_tanggal_selesai">
                                    <button type="button" class="btn btn-outline-light" onclick="document.getElementById('sanksi_tanggal_selesai').showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Spacer untuk layout -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Catatan Pelaksanaan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="catatan_pelaksanaan" rows="3" placeholder="Catatan khusus untuk pelaksanaan sanksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-primary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Buat Sanksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPelanggaranId = null;

function showDetail(id) {
    currentPelanggaranId = id;
    let prefix = '/admin';
    if (window.location.pathname.includes('/kesiswaan/')) {
        prefix = '/kesiswaan';
    } else if (window.location.pathname.includes('/guru/')) {
        prefix = '/guru';
    }
    
    fetch(`${prefix}/view-data/pelanggaran/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail_siswa').textContent = data.pelanggaran.siswa ? data.pelanggaran.siswa.nama_siswa : '-';
        document.getElementById('detail_jenis_pelanggaran').textContent = data.pelanggaran.jenisPelanggaran ? data.pelanggaran.jenisPelanggaran.nama_pelanggaran : '-';
        document.getElementById('detail_tanggal').textContent = data.pelanggaran.tanggal ? new Date(data.pelanggaran.tanggal).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail_poin').textContent = data.pelanggaran.poin || '0';
        document.getElementById('detail_status').textContent = data.pelanggaran.status_verifikasi || '-';
        
        // Load sanksi data
        loadSanksiData(id);
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail pelanggaran');
    });
}

function loadSanksiData(pelanggaranId) {
    const sanksiContent = document.getElementById('sanksiContent');
    sanksiContent.innerHTML = '<p class="text-light">Belum ada sanksi untuk pelanggaran ini.</p>';
}

function createSanksi(id) {
    console.log('createSanksi called with id:', id);
    currentPelanggaranId = id;
    let prefix = '/admin';
    if (window.location.pathname.includes('/kesiswaan/')) {
        prefix = '/kesiswaan';
    } else if (window.location.pathname.includes('/guru/')) {
        prefix = '/guru';
    }
    
    const sanksiForm = document.getElementById('sanksiForm');
    const sanksiModalElement = document.getElementById('sanksiModal');
    
    if (!sanksiForm) {
        console.error('sanksiForm not found');
        return;
    }
    
    if (!sanksiModalElement) {
        console.error('sanksiModal not found');
        return;
    }
    
    sanksiForm.action = `${prefix}/view-data/pelanggaran/${id}/sanksi`;
    
    // Reset form
    sanksiForm.reset();
    
    // Set default tanggal mulai ke hari ini
    const tanggalMulai = document.getElementById('sanksi_tanggal_mulai');
    if (tanggalMulai) {
        tanggalMulai.value = new Date().toISOString().split('T')[0];
    }
    
    // Try different ways to show modal
    if (typeof bootstrap !== 'undefined') {
        const sanksiModal = new bootstrap.Modal(sanksiModalElement);
        sanksiModal.show();
    } else if (typeof $ !== 'undefined') {
        $('#sanksiModal').modal('show');
    } else {
        console.error('Neither Bootstrap nor jQuery found');
        alert('Modal tidak dapat dibuka. Silakan refresh halaman.');
    }
}

// Form validation and modal handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    // Check Bootstrap availability
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap 5 is available');
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        console.log('jQuery with Bootstrap modal is available');
    } else {
        console.error('No modal library found');
    }
    
    // Test modal functionality
    const sanksiModal = document.getElementById('sanksiModal');
    if (sanksiModal) {
        console.log('Sanksi modal found');
    } else {
        console.error('Sanksi modal not found');
    }
    
    // Auto refresh after sanksi creation
    if (window.location.search.includes('sanksi_success=1')) {
        setTimeout(() => {
            window.location.href = window.location.pathname;
        }, 2000);
    }
});

function showCreateSanksiForm() {
    if (currentPelanggaranId) {
        createSanksi(currentPelanggaranId);
    }
}

function editData(id) {
    let prefix = '/admin';
    if (window.location.pathname.includes('/kesiswaan/')) {
        prefix = '/kesiswaan';
    } else if (window.location.pathname.includes('/guru/')) {
        prefix = '/guru';
    }
    
    fetch(`${prefix}/view-data/pelanggaran/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editForm').action = `${prefix}/view-data/pelanggaran/${id}`;
        document.getElementById('edit_tanggal').value = data.pelanggaran.tanggal || '';
        document.getElementById('edit_keterangan').value = data.pelanggaran.keterangan || '';
        
        // Show current bukti foto
        const currentBuktiFoto = document.getElementById('current_bukti_foto');
        if (data.pelanggaran.bukti_foto) {
            const storageUrl = '{{ asset("storage") }}';
            const imageUrl = `${storageUrl}/${data.pelanggaran.bukti_foto}`;
            currentBuktiFoto.innerHTML = `<img src="${imageUrl}" alt="Bukti Foto" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="showImageModal('${imageUrl}')"/>`;
        } else {
            currentBuktiFoto.innerHTML = '<span class="text-muted">Tidak ada bukti foto</span>';
        }
        
        // Populate siswa dropdown
        const siswaSelect = document.getElementById('edit_siswa_id');
        siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>';
        data.siswa.forEach(s => {
            const option = document.createElement('option');
            option.value = s.siswa_id;
            option.textContent = s.nama_siswa + ' - ' + (s.kelas ? s.kelas.nama_kelas : '');
            option.selected = s.siswa_id == data.pelanggaran.siswa_id;
            siswaSelect.appendChild(option);
        });
        
        // Populate jenis pelanggaran dropdown
        const jenisSelect = document.getElementById('edit_jenis_pelanggaran_id');
        jenisSelect.innerHTML = '<option value="">Pilih Jenis Pelanggaran</option>';
        data.jenisPelanggaran.forEach(jp => {
            const option = document.createElement('option');
            option.value = jp.jenis_pelanggaran_id;
            option.textContent = jp.nama_pelanggaran + ' (' + jp.poin + ' poin)';
            option.selected = jp.jenis_pelanggaran_id == data.pelanggaran.jenis_pelanggaran_id;
            jenisSelect.appendChild(option);
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
    if(confirm('Yakin ingin menghapus data pelanggaran ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        let prefix = '/admin';
        if (window.location.pathname.includes('/kesiswaan/')) {
            prefix = '/kesiswaan';
        } else if (window.location.pathname.includes('/guru/')) {
            prefix = '/guru';
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${prefix}/view-data/pelanggaran/${id}`;
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