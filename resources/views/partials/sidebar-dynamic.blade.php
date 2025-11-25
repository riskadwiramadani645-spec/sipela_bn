@if(session('user') && session('user')->level === 'admin')
    <a href="{{ route('admin.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    
    <!-- Master Data Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.master-data*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-database me-2"></i>Master Data
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.master-data.users') }}">Manage User</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.tahun-ajaran') }}">Tahun Ajaran</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.siswa') }}">Data Siswa</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.orang-tua') }}">Data Orang Tua</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.guru') }}">Data Guru</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.kelas') }}">Data Kelas</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.jenis-pelanggaran') }}">Jenis Pelanggaran</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.jenis-prestasi') }}">Jenis Prestasi</a>
            <a class="dropdown-item" href="{{ route('admin.master-data.jenis-sanksi') }}">Jenis Sanksi</a>
        </div>
    </div>
    
    <!-- Input Data Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.input-data*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-plus-circle me-2"></i>Input Data
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.input-data.pelanggaran') }}">Input Pelanggaran</a>
            <a class="dropdown-item" href="{{ route('admin.input-data.prestasi') }}">Input Prestasi</a>
            <a class="dropdown-item" href="{{ route('admin.input-data.bk') }}">Input BK</a>
        </div>
    </div>
    
    <!-- Verifikasi & Monitoring Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.verifikasi-monitoring*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-check-double me-2"></i>Verifikasi & Monitoring
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.verifikasi-monitoring.verifikasi') }}">Verifikasi Data</a>
            <a class="dropdown-item" href="{{ route('admin.verifikasi-monitoring.monitoring') }}">Monitoring All</a>
        </div>
    </div>
    
    <!-- View Data Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.view-data*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-eye me-2"></i>View Data
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.view-data.anak') }}">View Data Anak</a>
            <a class="dropdown-item" href="{{ route('admin.view-data.pelanggaran') }}">Data Pelanggaran</a>
            <a class="dropdown-item" href="{{ route('admin.view-data.prestasi') }}">Data Prestasi</a>
        </div>
    </div>
    
    <!-- Manajemen Sanksi Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.sanksi*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-gavel me-2"></i>Manajemen Sanksi
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.sanksi.index') }}">Data Sanksi</a>
            <a class="dropdown-item" href="{{ route('admin.sanksi.pelaksanaan') }}">Pelaksanaan Sanksi</a>
        </div>
    </div>
    
    <!-- Laporan & Sistem Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.laporan-sistem*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-cog me-2"></i>Laporan & Sistem
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.laporan-sistem.laporan') }}">Export Laporan</a>
            <a class="dropdown-item" href="{{ route('admin.laporan-sistem.sistem') }}">Backup System</a>
        </div>
    </div>

@elseif(session('user') && session('user')->level === 'kesiswaan')
    <a href="{{ route('kesiswaan.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('kesiswaan.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    
    <!-- Input Data Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('kesiswaan.input-data*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-plus me-2"></i>Input Data
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('kesiswaan.input-data.pelanggaran') }}">Input Pelanggaran</a>
            <a class="dropdown-item" href="{{ route('kesiswaan.input-data.prestasi') }}">Input Prestasi</a>
        </div>
    </div>
    
    <!-- Verifikasi & Monitoring Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('kesiswaan.verifikasi-monitoring*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-check-circle me-2"></i>Verifikasi & Monitoring
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('kesiswaan.verifikasi-monitoring.verifikasi') }}">Verifikasi Data</a>
            <a class="dropdown-item" href="{{ route('kesiswaan.verifikasi-monitoring.monitoring') }}">Monitoring</a>
        </div>
    </div>
    
    <!-- View Data Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('kesiswaan.view-data*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-eye me-2"></i>View Data
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('kesiswaan.view-data.anak') }}">View Data Anak</a>
            <a class="dropdown-item" href="{{ route('kesiswaan.view-data.pelanggaran') }}">Data Pelanggaran</a>
            <a class="dropdown-item" href="{{ route('kesiswaan.view-data.prestasi') }}">Data Prestasi</a>
        </div>
    </div>
    
    <!-- Manajemen Sanksi Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('kesiswaan.sanksi*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-gavel me-2"></i>Manajemen Sanksi
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('kesiswaan.sanksi.index') }}">Data Sanksi</a>
            <a class="dropdown-item" href="{{ route('kesiswaan.sanksi.pelaksanaan') }}">Pelaksanaan Sanksi</a>
        </div>
    </div>

    <!-- Laporan Kesiswaan -->
    <a href="{{ route('kesiswaan.laporan') }}" class="nav-item nav-link {{ request()->routeIs('kesiswaan.laporan*') ? 'active' : '' }}">
        <i class="fas fa-file-alt me-2"></i>Eport Laporan
    </a>

@elseif(session('user') && session('user')->level === 'guru')
    @php
        $user = session('user');
        $guru = null;
        $isWaliKelas = false;
        
        try {
            // Ambil data guru berdasarkan guru_id dari user
            if (isset($user->guru_id) && $user->guru_id) {
                $guru = \App\Models\Guru::find($user->guru_id);
                
                if ($guru && isset($guru->guru_id)) {
                    $kelasAmpu = \DB::table('kelas')
                        ->where('wali_kelas_id', $guru->guru_id)
                        ->count();
                    $isWaliKelas = $kelasAmpu > 0;
                } else {
                    $guru = null;
                    $isWaliKelas = false;
                }
            } else {
                $guru = null;
                $isWaliKelas = false;
            }
        } catch (\Exception $e) {
            // Jika ada error, set default values
            $guru = null;
            $isWaliKelas = false;
        }
    @endphp
    
    <a href="{{ route('guru.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    
    <!-- FITUR GURU BIASA (SEMUA GURU) -->
    <a href="{{ route('guru.input-pelanggaran') }}" class="nav-item nav-link {{ request()->routeIs('guru.input-pelanggaran*') ? 'active' : '' }}">
        <i class="fas fa-plus-circle me-2"></i>Input Pelanggaran
    </a>
    
    <a href="{{ route('guru.data-pelanggaran') }}" class="nav-item nav-link {{ request()->routeIs('guru.data-pelanggaran*') ? 'active' : '' }}">
        <i class="fas fa-eye me-2"></i>Data Pelanggaran
    </a>
    
    <a href="{{ route('guru.laporan-terbatas') }}" class="nav-item nav-link {{ request()->routeIs('guru.laporan-terbatas*') ? 'active' : '' }}">
        <i class="fas fa-file-alt me-2"></i>Export Laporan <span class="badge bg-warning ms-1"></span>
    </a>
    
    @if($isWaliKelas)
    <!-- FITUR KHUSUS WALI KELAS -->
    <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0; padding-top: 10px;">
        <small class="text-muted px-3">FITUR WALI KELAS</small>
    </div>
    
    <a href="{{ route('guru.wali-kelas.data-kelas') }}" class="nav-item nav-link {{ request()->routeIs('guru.wali-kelas.data-kelas') ? 'active' : '' }}">
        <i class="fas fa-users me-2"></i>Data Kelas Saya
    </a>
    
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('guru.wali-kelas.monitoring*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-chart-bar me-2"></i>Monitoring Kelas
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('guru.wali-kelas.monitoring.pelanggaran') }}">Pelanggaran Kelas</a>
            <a class="dropdown-item" href="{{ route('guru.wali-kelas.monitoring.sanksi') }}">Sanksi Kelas 👁️</a>
        </div>
    </div>
    
    <a href="{{ route('guru.wali-kelas.export-allaccess') }}" class="nav-item nav-link {{ request()->routeIs('guru.wali-kelas.export-allaccess') ? 'active' : '' }}">
        <i class="fas fa-download me-2"></i>Export Full Access <span class="badge bg-success ms-1">🔓</span>
    </a>
    @endif
    


@elseif(session('user') && session('user')->level === 'konselor_bk')
    <a href="{{ route('konselor-bk.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('konselor-bk.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    <a href="{{ route('konselor-bk.input-bk') }}" class="nav-item nav-link {{ request()->routeIs('konselor-bk.input-bk*') ? 'active' : '' }}">
        <i class="fas fa-comments me-2"></i>Input BK
    </a>
    
    <a href="{{ route('konselor-bk.notifikasi') }}" class="nav-item nav-link {{ request()->routeIs('konselor-bk.notifikasi*') ? 'active' : '' }}">
        <i class="fas fa-bell me-2"></i>Notifikasi Follow-up
    </a>
    
    <!-- Data & Laporan Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('konselor-bk.view-') || request()->routeIs('konselor-bk.laporan') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-eye me-2"></i>Data & Laporan
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('konselor-bk.view-data-sendiri') }}">View Data Sendiri</a>
            <a class="dropdown-item" href="{{ route('konselor-bk.laporan') }}">Export Laporan</a>
        </div>
    </div>

@elseif(session('user') && session('user')->level === 'kepala_sekolah')
    <a href="{{ route('kepala-sekolah.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('kepala-sekolah.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    <a href="{{ route('kepala-sekolah.monitoring') }}" class="nav-item nav-link {{ request()->routeIs('kepala-sekolah.monitoring*') ? 'active' : '' }}">
        <i class="fas fa-chart-line me-2"></i>Monitoring All
    </a>

        <a href="{{ route('kepala-sekolah.view-data-anak') }}" class="nav-item nav-link {{ request()->routeIs('kepala-sekolah.view-data-anak*') ? 'active' : '' }}">
        <i class="fas fa-users me-2"></i>View Data Anak
    </a>
              <a href="{{ route('kepala-sekolah.laporan') }}" class="nav-item nav-link {{ request()->routeIs('kepala-sekolah.laporan*') ? 'active' : '' }}">
        <i class="fas fa-file-export me-2"></i>Export Laporan
    </a>
    
            

@elseif(session('user') && session('user')->level === 'siswa')
    <a href="{{ route('siswa.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    
    <!-- Data & Laporan Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('siswa.view-') || request()->routeIs('siswa.laporan') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-eye me-2"></i>Data & Laporan
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('siswa.view-data-sendiri') }}">View Data Sendiri</a>
            <a class="dropdown-item" href="{{ route('siswa.laporan') }}">Export Laporan (Terbatas)</a>
        </div>
    </div>

@elseif(session('user') && session('user')->level === 'orang_tua')
    <a href="{{ route('orang-tua.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('orang-tua.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    
    <!-- Data & Laporan Dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('orang-tua.view-') || request()->routeIs('orang-tua.laporan') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-eye me-2"></i>Data & Laporan
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('orang-tua.view-data-anak') }}">View Data Anak</a>
            <a class="dropdown-item" href="{{ route('orang-tua.laporan') }}">Export Laporan (Terbatas)</a>
        </div>
    </div>

@endif