@extends('layouts.app')

@section('title', 'Profile Saya - SIPELA')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Profile Saya</h6>
                    <p class="mb-0">Informasi akun dan data pribadi {{ ucfirst($user->level) }} di SIPELA</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock">{{ now()->format('H:i:s') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Foto Profile -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Foto Profile</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" 
                         class="rounded-circle border border-danger" width="120" height="120" 
                         style="object-fit: cover; cursor: pointer;" 
                         onclick="document.getElementById('photoInput').click()">
                </div>
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    <input type="file" id="photoInput" name="profile_photo" 
                           accept="image/*" style="display: none;" 
                           onchange="previewAndSubmit(this)">
                    <p class="text-light small">Klik foto untuk mengganti</p>
                </form>
                
                <script>
                function previewAndSubmit(input) {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.querySelector('img[onclick*="photoInput"]');
                            img.src = e.target.result;
                            setTimeout(() => {
                                document.getElementById('photoForm').submit();
                            }, 500);
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
                </script>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Form -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Edit Profile</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Username</label>
                                <input type="text" class="form-control bg-dark text-white border-danger" 
                                       name="username" value="{{ old('username', $user->username) }}" required>
                            </div>
                        </div>
                        
                        @if($user->level === 'admin')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Lengkap</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}">
                                </div>
                            </div>
                        @elseif(in_array($user->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah', 'wali_kelas']))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Guru</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="nama_guru" value="{{ old('nama_guru', $profileData->nama_guru ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">NIP <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->nip ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Jenis Kelamin</label>
                                    <select class="form-control bg-dark text-white border-danger" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ ($profileData->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ ($profileData->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Bidang Studi</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="bidang_studi" value="{{ old('bidang_studi', $profileData->bidang_studi ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">No. Telepon</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="no_telp" value="{{ old('no_telp', $profileData->no_telp ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Email</label>
                                    <input type="email" class="form-control bg-dark text-white border-danger" 
                                           name="email" value="{{ old('email', $profileData->email ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Status <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->status ?? 'Aktif' }}" readonly>
                                </div>
                            </div>
                        @elseif($user->level === 'siswa')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Siswa</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="nama_siswa" value="{{ old('nama_siswa', $profileData->nama_siswa ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">NIS <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->nis ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">NISN <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->nisn ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Jenis Kelamin</label>
                                    <select class="form-control bg-dark text-white border-danger" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ ($profileData->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ ($profileData->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Status Kesiswaan <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->status_kesiswaan ?? 'Aktif' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Kelas <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->nama_kelas ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Tempat Lahir</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="tempat_lahir" value="{{ old('tempat_lahir', $profileData->tempat_lahir ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Tanggal Lahir</label>
                                    <input type="date" class="form-control bg-dark text-white border-danger" 
                                           name="tanggal_lahir" value="{{ old('tanggal_lahir', $profileData->tanggal_lahir ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">No. Telepon</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="no_telp" value="{{ old('no_telp', $profileData->no_telp ?? '') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label text-white">Alamat</label>
                                    <textarea class="form-control bg-dark text-white border-danger" name="alamat" rows="2">{{ old('alamat', $profileData->alamat ?? '') }}</textarea>
                                </div>
                            </div>
                        @elseif($user->level === 'orang_tua')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Orang Tua</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="nama_orang_tua" value="{{ old('nama_orang_tua', $profileData->nama_orangtua ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Anak <small class="text-muted">(Tidak dapat diubah)</small></label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           value="{{ $profileData->nama_siswa ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Hubungan</label>
                                    <select class="form-control bg-dark text-white border-danger" name="hubungan">
                                        <option value="Ayah" {{ ($profileData->hubungan ?? '') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                        <option value="Ibu" {{ ($profileData->hubungan ?? '') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                        <option value="Wali" {{ ($profileData->hubungan ?? '') == 'Wali' ? 'selected' : '' }}>Wali</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pekerjaan</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="pekerjaan" value="{{ old('pekerjaan', $profileData->pekerjaan ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">Pendidikan</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="pendidikan" value="{{ old('pendidikan', $profileData->pendidikan ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-white">No. Telepon</label>
                                    <input type="text" class="form-control bg-dark text-white border-danger" 
                                           name="no_telp" value="{{ old('no_telp', $profileData->no_telp ?? '') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label text-white">Alamat</label>
                                    <textarea class="form-control bg-dark text-white border-danger" name="alamat" rows="2">{{ old('alamat', $profileData->alamat ?? '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-warning btn-lg px-5">
                            <i class="fa fa-edit me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ganti Password & Informasi Profile -->
<div class="row g-4 mt-1">
    <div class="col-md-6">
        <div class="card bg-secondary rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Ganti Password</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-white">Password Lama</label>
                        <input type="password" class="form-control bg-dark text-white border-danger" 
                               name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-white">Password Baru</label>
                        <input type="password" class="form-control bg-dark text-white border-danger" 
                               name="new_password" required minlength="6">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-white">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control bg-dark text-white border-danger" 
                               name="new_password_confirmation" required minlength="6">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-danger btn-lg px-4">
                            <i class="fa fa-key me-2"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Informasi Profile -->
    <div class="col-md-6">
        <div class="card bg-secondary rounded h-100 shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Informasi Profile</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-white">Level Akses</label>
                    <input type="text" class="form-control bg-dark text-white border-danger" 
                           value="{{ ucfirst($user->level) }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Username</label>
                    <input type="text" class="form-control bg-dark text-white border-danger" 
                           value="{{ $user->username }}" readonly>
                </div>
                
                @if($user->level === 'admin')
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Lengkap</label>
                        <input type="text" class="form-control bg-dark text-white border-danger" 
                               value="{{ $user->nama_lengkap ?? 'Belum diisi' }}" readonly>
                    </div>
                @elseif($profileData)
                    @if(in_array($user->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah', 'wali_kelas']))
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Guru</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nama_guru ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">NIP</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nip ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Status</label>
                            <span class="badge bg-success">{{ $profileData->status ?? 'Aktif' }}</span>
                        </div>
                    @elseif($user->level === 'siswa')
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Siswa</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nama_siswa ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">NIS</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nis ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">NISN</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nisn ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Status Kesiswaan</label>
                            <span class="badge bg-success">{{ $profileData->status_kesiswaan ?? 'Aktif' }}</span>
                        </div>
                    @elseif($user->level === 'orang_tua')
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Orang Tua</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nama_orangtua ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Hubungan</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->hubungan ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Nama Anak</label>
                            <input type="text" class="form-control bg-dark text-white border-danger" 
                                   value="{{ $profileData->nama_siswa ?? '-' }}" readonly>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.real-time-clock {
    font-family: 'Courier New', monospace;
    font-weight: bold;
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

setInterval(updateClock, 1000);
updateClock();
</script>
@endpush