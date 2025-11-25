@extends('layouts.app')

@section('title', 'Profile Saya - SIPELA')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card bg-white rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Profile Saya</h6>
                <p class="mb-0">Informasi akun dan data pribadi Administrator</p>
            </div>
            <div class="card-body bg-secondary text-white">
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-user-shield fa-3x mb-2"></i>
                        <h6>Administrator</h6>
                        <small>Super Admin Access</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-key fa-3x mb-2"></i>
                        <h6>Full Access</h6>
                        <small>Semua fitur tersedia</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-shield-alt fa-3x mb-2"></i>
                        <h6>Secure Account</h6>
                        <small>Akun terproteksi</small>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-4">
        <div class="card bg-white rounded h-100 shadow">
            <div class="card-header bg-danger text-white text-center py-3">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fa fa-user-shield fa-3x text-danger"></i>
                </div>
                <h5 class="mt-3 mb-0">{{ $user->username }}</h5>
                <small>{{ ucfirst($user->level) }}</small>
            </div>
            <div class="card-body bg-secondary text-white">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white">Status:</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white">Dapat Verifikasi:</span>
                    @if($user->can_verify)
                        <span class="badge bg-success">Ya</span>
                    @else
                        <span class="badge bg-secondary">Tidak</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white">Bergabung:</span>
                    <span class="text-light">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-white">Last Login:</span>
                    <span class="text-light">{{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card bg-white rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Informasi Akun</h6>
            </div>
            <div class="card-body bg-secondary text-white">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">ID Pengguna</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ $user->id }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">Username</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ $user->username }}" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">Level Akses</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ ucfirst($user->level) }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">
                            @if($user->level == 'guru' || $user->level == 'konselor_bk' || $user->level == 'kepala_sekolah')
                                Nama Lengkap
                            @elseif($user->level == 'siswa')
                                Nama Siswa
                            @elseif($user->level == 'orang_tua')
                                Nama Orang Tua
                            @else
                                Data Terkait
                            @endif
                        </label>
                        <input type="text" class="form-control bg-dark text-white border-danger" 
                               value="@if($profileData)
                                        @if($user->level == 'guru' || $user->level == 'konselor_bk' || $user->level == 'kepala_sekolah')
                                            {{ $profileData->nama_guru }}
                                        @elseif($user->level == 'siswa')
                                            {{ $profileData->nama_siswa }}
                                        @elseif($user->level == 'orang_tua')
                                            {{ $profileData->nama_orangtua }}
                                        @endif
                                      @else
                                        Tidak ada data
                                      @endif" readonly>
                    </div>
                </div>
            </div>
            
            @if($profileData && ($user->level == 'guru' || $user->level == 'konselor_bk' || $user->level == 'kepala_sekolah'))
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">NIP</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ $profileData->nip ?? '-' }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">Bidang Studi</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ $profileData->bidang_studi ?? '-' }}" readonly>
                    </div>
                </div>
            </div>
            @endif
            
            @if($profileData && $user->level == 'siswa')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">NIS</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ $profileData->nis ?? '-' }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-white">Kelas</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" value="{{ $profileData->kelas->nama_kelas ?? '-' }}" readonly>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label text-white">Hak Akses</label>
                        <div class="bg-dark p-3 rounded border border-danger">
                            <div class="row text-light">
                                @if($user->level == 'admin')
                                <div class="col-md-6">
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Kelola Pengguna</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Master Data</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Input Data</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Verifikasi & Monitoring</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>View Semua Data</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Export Laporan</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Backup System</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Full Access</div>
                                </div>
                                @elseif($user->level == 'guru')
                                <div class="col-md-6">
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Input Pelanggaran</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>View Data Siswa</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Export Laporan Terbatas</div>
                                    <div class="mb-2"><i class="fa fa-times text-danger me-2"></i>Master Data</div>
                                </div>
                                @elseif($user->level == 'siswa')
                                <div class="col-md-6">
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>View Data Pribadi</div>
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Riwayat Pelanggaran</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2"><i class="fa fa-check text-success me-2"></i>Riwayat Prestasi</div>
                                    <div class="mb-2"><i class="fa fa-times text-danger me-2"></i>Input Data</div>
                                </div>
                                @else
                                <div class="col-12">
                                    <div class="mb-2"><i class="fa fa-info text-info me-2"></i>Hak akses sesuai level {{ ucfirst($user->level) }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Form -->
<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card bg-white rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Edit Profile</h6>
            </div>
            <div class="card-body bg-secondary text-white">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-white">Username</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" name="username" value="{{ $user->username }}" required>
                    </div>

                    @if($user->level == 'admin')
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Lengkap</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="nama_lengkap" value="{{ $user->nama_lengkap ?? '' }}">
                        </div>
                    @endif

                    @if($user->level == 'guru' || $user->level == 'konselor_bk' || $user->level == 'kepala_sekolah' || $user->level == 'kesiswaan' || $user->level == 'wali_kelas')
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Guru</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="nama_guru" value="{{ $profileData->nama_guru ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">NIP</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="nip" value="{{ $profileData->nip ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Jenis Kelamin</label>
                            <select class="form-control bg-dark text-white border-danger" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ ($profileData->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ ($profileData->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Bidang Studi</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="bidang_studi" value="{{ $profileData->bidang_studi ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">No. Telepon</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="no_telp" value="{{ $profileData->no_telp ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Email</label>
                            <input type="email" class="form-control bg-dark text-white border-danger" name="email" value="{{ $profileData->email ?? '' }}">
                        </div>
                    @endif

                    @if($user->level == 'siswa')
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Siswa</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="nama_siswa" value="{{ $profileData->nama_siswa ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">NIS</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="nis" value="{{ $profileData->nis ?? '' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Jenis Kelamin</label>
                            <select class="form-control bg-dark text-white border-danger" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ ($profileData->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ ($profileData->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Tempat Lahir</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="tempat_lahir" value="{{ $profileData->tempat_lahir ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Tanggal Lahir</label>
                            <div class="input-group">
                                <input type="date" class="form-control bg-dark text-white border-danger" name="tanggal_lahir" value="{{ $profileData->tanggal_lahir ?? '' }}" id="tanggal_lahir_profile">
                                <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_lahir_profile').showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">No. Telepon</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="no_telp" value="{{ $profileData->no_telp ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Alamat</label>
                            <textarea class="form-control bg-dark text-white border-danger" name="alamat" rows="2">{{ $profileData->alamat ?? '' }}</textarea>
                        </div>
                    @endif

                    @if($user->level == 'orang_tua')
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Orang Tua</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="nama_orang_tua" value="{{ $profileData->nama_orangtua ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Hubungan</label>
                            <select class="form-control bg-dark text-white border-danger" name="hubungan">
                                <option value="Ayah" {{ ($profileData->hubungan ?? '') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="Ibu" {{ ($profileData->hubungan ?? '') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                <option value="Wali" {{ ($profileData->hubungan ?? '') == 'Wali' ? 'selected' : '' }}>Wali</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Pekerjaan</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="pekerjaan" value="{{ $profileData->pekerjaan ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Pendidikan</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="pendidikan" value="{{ $profileData->pendidikan ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">No. Telepon</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" name="no_telp" value="{{ $profileData->no_telp ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Alamat</label>
                            <textarea class="form-control bg-dark text-white border-danger" name="alamat" rows="2">{{ $profileData->alamat ?? '' }}</textarea>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fa fa-edit me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card bg-white rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Ganti Password</h6>
            </div>
            <div class="card-body bg-secondary text-white">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-white">Password Lama</label>
                        <input type="password" class="form-control bg-dark text-white border-danger" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Password Baru</label>
                        <input type="password" class="form-control bg-dark text-white border-danger" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control bg-dark text-white border-danger" name="new_password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fa fa-key me-2"></i>Ganti Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-white rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Aktivitas Terbaru</h6>
            </div>
            <div class="card-body bg-secondary text-white">
            <div class="table-responsive">
                <table id="indexTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ now()->format('d/m/Y H:i') }}</td>
                            <td>Login ke sistem</td>
                            <td><span class="badge bg-success">Berhasil</span></td>
                        </tr>
                        <tr>
                            <td>{{ now()->subMinutes(5)->format('d/m/Y H:i') }}</td>
                            <td>Akses dashboard admin</td>
                            <td><span class="badge bg-info">Aktif</span></td>
                        </tr>
                        <tr>
                            <td>{{ now()->subMinutes(10)->format('d/m/Y H:i') }}</td>
                            <td>View profile</td>
                            <td><span class="badge bg-info">Aktif</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection