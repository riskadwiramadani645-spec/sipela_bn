@extends('layouts.app')

@section('title', 'Data Prestasi - SIPELA')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">View Data - Prestasi</h6>
                    <p class="mb-0">Lihat dan kelola data prestasi siswa di SIPELA</p>
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
                <h6 class="mb-0">Data Prestasi</h6>
                @php
                    $currentPrefix = request()->route()->getPrefix();
                    $inputRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.input-data.prestasi' : 'admin.input-data.prestasi';
                @endphp
                <a href="{{ route($inputRoute) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Prestasi
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
                <table id="prestasiTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Jenis Prestasi</th>
                            <th>Tanggal</th>
                            <th>Tingkat</th>
                            <th>Poin</th>
                            <th>Bukti</th>
                            <th>Status</th>
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
                            <td>{{ $item->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                            <td>{{ $item->tanggal ? date('d/m/Y', strtotime($item->tanggal)) : '-' }}</td>
                            <td><span class="badge bg-info">{{ $item->tingkat ?? '-' }}</span></td>
                            <td><span class="badge bg-success">{{ $item->poin }} poin</span></td>
                            <td>
                                @if($item->bukti_dokumen)
                                    <a href="{{ asset('storage/' . $item->bukti_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-file"></i> Lihat
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
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->prestasi_id }})" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData({{ $item->prestasi_id }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $item->prestasi_id }})" title="Hapus">
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
            @php
                $currentPrefix = request()->route()->getPrefix();
                $storeRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.view-data.prestasi.store' : 'admin.view-data.prestasi.store';
            @endphp
            <form action="{{ route($storeRoute) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Prestasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                                <label class="form-label text-white">Jenis Prestasi</label>
                                <select class="form-control bg-dark text-white border-primary" name="jenis_prestasi_id" required>
                                    <option value="">Pilih Jenis Prestasi</option>
                                    @foreach($jenisPrestasi ?? [] as $jp)
                                        <option value="{{ $jp->jenis_prestasi_id }}">{{ $jp->nama_prestasi }} ({{ $jp->poin ?? 0 }} poin)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal</label>
                                <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tingkat</label>
                                <select class="form-control bg-dark text-white border-primary" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Sekolah">Sekolah</option>
                                    <option value="Kabupaten">Kabupaten</option>
                                    <option value="Provinsi">Provinsi</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Internasional">Internasional</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Keterangan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="keterangan" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bukti Dokumen (Opsional)</label>
                        <input type="file" class="form-control bg-dark text-white border-primary" name="bukti_dokumen" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 5MB</small>
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
                    <h5 class="modal-title text-white">Edit Prestasi</h5>
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
                                <label class="form-label text-white">Jenis Prestasi</label>
                                <select class="form-control bg-dark text-white border-primary" name="jenis_prestasi_id" id="edit_jenis_prestasi_id" required>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal</label>
                                <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal" id="edit_tanggal" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Tingkat</label>
                                <select class="form-control bg-dark text-white border-primary" name="tingkat" id="edit_tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Sekolah">Sekolah</option>
                                    <option value="Kecamatan">Kecamatan</option>
                                    <option value="Kabupaten">Kabupaten</option>
                                    <option value="Provinsi">Provinsi</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Internasional">Internasional</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Keterangan</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="keterangan" id="edit_keterangan" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Bukti Dokumen Saat Ini</label>
                        <div id="current_bukti"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Ganti Bukti Dokumen (Opsional)</label>
                        <input type="file" class="form-control bg-dark text-white border-primary" name="bukti_dokumen" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 5MB. Kosongkan jika tidak ingin mengganti.</small>
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
                <h5 class="modal-title text-white">Detail Prestasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">ID:</strong>
                        <p class="text-light" id="detail_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Siswa:</strong>
                        <p class="text-light" id="detail_siswa"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Jenis Prestasi:</strong>
                        <p class="text-light" id="detail_jenis_prestasi"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Tahun Ajaran:</strong>
                        <p class="text-light" id="detail_tahun_ajaran"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-white">Tanggal:</strong>
                        <p class="text-light" id="detail_tanggal"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-white">Tingkat:</strong>
                        <p class="text-light" id="detail_tingkat"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <strong class="text-white">Poin:</strong>
                        <p class="text-light" id="detail_poin"></p>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-white">Status:</strong>
                        <p class="text-light" id="detail_status"></p>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-white">Bukti Dokumen:</strong>
                        <p class="text-light" id="detail_bukti"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong class="text-white">Keterangan:</strong>
                        <p class="text-light" id="detail_keterangan"></p>
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
    const prefix = window.location.pathname.includes('/kesiswaan/') ? '/kesiswaan' : '/admin';
    fetch(`${prefix}/view-data/prestasi/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail_id').textContent = data.prestasi.prestasi_id;
        document.getElementById('detail_siswa').textContent = data.prestasi.siswa ? (data.prestasi.siswa.nama_siswa + ' - ' + (data.prestasi.siswa.kelas ? data.prestasi.siswa.kelas.nama_kelas : '')) : '-';
        document.getElementById('detail_jenis_prestasi').textContent = data.prestasi.jenisPrestasi ? data.prestasi.jenisPrestasi.nama_prestasi : '-';
        document.getElementById('detail_tahun_ajaran').textContent = data.prestasi.tahunAjaran ? (data.prestasi.tahunAjaran.tahun_ajaran + ' - ' + data.prestasi.tahunAjaran.semester) : '-';
        document.getElementById('detail_tanggal').textContent = data.prestasi.tanggal ? new Date(data.prestasi.tanggal).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail_tingkat').textContent = data.prestasi.tingkat || '-';
        document.getElementById('detail_poin').textContent = (data.prestasi.poin || '0') + ' poin';
        document.getElementById('detail_status').textContent = data.prestasi.status_verifikasi || '-';
        
        // Handle bukti dokumen
        const buktiBadge = data.prestasi.bukti_dokumen ? 
            `<a href="/storage/${data.prestasi.bukti_dokumen}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Lihat Bukti</a>` : 
            'Tidak ada bukti';
        document.getElementById('detail_bukti').innerHTML = buktiBadge;
        
        document.getElementById('detail_keterangan').textContent = data.prestasi.keterangan || '-';
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail prestasi');
    });
}

function editData(id) {
    const prefix = window.location.pathname.includes('/kesiswaan/') ? '/kesiswaan' : '/admin';
    fetch(`${prefix}/view-data/prestasi/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editForm').action = `${prefix}/view-data/prestasi/${id}`;
        document.getElementById('edit_tanggal').value = data.prestasi.tanggal || '';
        document.getElementById('edit_tingkat').value = data.prestasi.tingkat || '';
        document.getElementById('edit_keterangan').value = data.prestasi.keterangan || '';
        
        // Show current bukti dokumen
        const currentBukti = document.getElementById('current_bukti');
        if (data.prestasi.bukti_dokumen) {
            const storageUrl = '{{ asset("storage") }}';
            const fileUrl = `${storageUrl}/${data.prestasi.bukti_dokumen}`;
            const fileName = data.prestasi.bukti_dokumen.toLowerCase();
            
            if (fileName.includes('.jpg') || fileName.includes('.jpeg') || fileName.includes('.png') || fileName.includes('.gif')) {
                currentBukti.innerHTML = `<img src="${fileUrl}" alt="Bukti Dokumen" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="showImageModal('${fileUrl}')"/>`;
            } else {
                currentBukti.innerHTML = `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf"></i> Lihat Dokumen Saat Ini</a>`;
            }
        } else {
            currentBukti.innerHTML = '<span class="text-muted">Tidak ada bukti dokumen</span>';
        }
        
        // Populate siswa dropdown
        const siswaSelect = document.getElementById('edit_siswa_id');
        siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>';
        data.siswa.forEach(s => {
            const option = document.createElement('option');
            option.value = s.siswa_id;
            option.textContent = s.nama_siswa + ' - ' + (s.kelas ? s.kelas.nama_kelas : '');
            option.selected = s.siswa_id == data.prestasi.siswa_id;
            siswaSelect.appendChild(option);
        });
        
        // Populate jenis prestasi dropdown
        const jenisSelect = document.getElementById('edit_jenis_prestasi_id');
        jenisSelect.innerHTML = '<option value="">Pilih Jenis Prestasi</option>';
        data.jenisPrestasi.forEach(jp => {
            const option = document.createElement('option');
            option.value = jp.jenis_prestasi_id;
            option.textContent = jp.nama_prestasi + ' (' + (jp.poin || 0) + ' poin)';
            option.selected = jp.jenis_prestasi_id == data.prestasi.jenis_prestasi_id;
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
    if(confirm('Yakin ingin menghapus data prestasi ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const prefix = window.location.pathname.includes('/kesiswaan/') ? '/kesiswaan' : '/admin';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${prefix}/view-data/prestasi/${id}`;
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