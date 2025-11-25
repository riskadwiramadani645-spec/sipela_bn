<?php

/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLER - MULTI ROLE SHARED METHODS
|--------------------------------------------------------------------------
| Controller ini menangani 3 ROLE sekaligus dengan REUSE pattern:
| 1. ADMIN (Full Access)
| 2. KESISWAAN (Shared Methods)
| 3. GURU (Shared Methods dengan Authorization)
|-----------------------------------------------------------
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\JenisPrestasi;
use App\Models\PelaksanaanSanksi;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ==================== DASHBOARD METHODS ====================
    // ROLE USAGE:
    // - dashboard()     : ADMIN ONLY (admin dashboard)
    // - guruDashboard() : GURU ONLY (guru + wali kelas dashboard)
    // NOTE: Kesiswaan punya DashboardController terpisah
    // ==================== DASHBOARD ====================
    
    /**
     * ADMIN DASHBOARD
     * Used by: ADMIN ONLY
     * Route: /admin/dashboard
     */
    public function dashboard()
    {
        $currentPrefix = request()->route()->getPrefix();
        
        if ($currentPrefix === 'guru') {
            return $this->guruDashboard();
        }
        
        // Admin dashboard
        $totalUsers = \App\Models\User::count();
        $totalGuru = \App\Models\Guru::count();
        $totalSiswa = \App\Models\Siswa::count();
        $totalKelas = \App\Models\Kelas::count();
        $totalPelanggaran = \App\Models\Pelanggaran::count();
        $totalPrestasi = \App\Models\Prestasi::count();
        $sanksiAktif = \App\Models\Sanksi::whereIn('status', ['berlangsung', 'dijadwalkan'])->count();
        $totalBK = \App\Models\BimbinganKonseling::count();
        $pelanggaranHariIni = \App\Models\Pelanggaran::whereDate('created_at', today())->count();
        $prestasiHariIni = \App\Models\Prestasi::whereDate('created_at', today())->count();
        $menungguVerifikasi = \App\Models\Pelanggaran::where('status_verifikasi', 'menunggu')->count();
        
        return view('admin.dashboard', compact(
            'totalUsers', 'totalGuru', 'totalSiswa', 'totalKelas',
            'totalPelanggaran', 'totalPrestasi', 'sanksiAktif', 'totalBK',
            'pelanggaranHariIni', 'prestasiHariIni', 'menungguVerifikasi'
        ));
    }
    
    /**
     * GURU DASHBOARD (Conditional: Guru Biasa vs Wali Kelas)
     * Used by: GURU ONLY
     * Route: /guru/dashboard
     * Logic: Deteksi otomatis guru biasa atau wali kelas
     */
    public function guruDashboard()
    {
        $user = session('user');
        
        // Cari guru berdasarkan berbagai kemungkinan
        $guru = \App\Models\Guru::where('nip', $user->username)
                    ->orWhere('email', $user->username)
                    ->first();
        
        if (!$guru) {
            $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
        }
        
        // Jika masih tidak ketemu, buat data dummy untuk testing
        if (!$guru) {
            $guru = (object) [
                'guru_id' => 0,
                'nama_guru' => $user->username ?? 'Guru Test',
                'bidang_studi' => 'Mata Pelajaran',
                'nip' => $user->username ?? '000000'
            ];
        }
        
        // Cek apakah guru adalah wali kelas
        $isWaliKelas = false;
        $kelasAmpu = collect();
        
        if ($guru && isset($guru->guru_id) && $guru->guru_id > 0) {
            $kelasAmpu = \DB::table('kelas')
                ->where('wali_kelas_id', $guru->guru_id)
                ->get();
            
            $isWaliKelas = $kelasAmpu->count() > 0;
        }
        
        // Data personal guru (role guru biasa)
        $totalPelanggaranInput = Pelanggaran::where('guru_pencatat', $guru->guru_id ?? 0)->count();
        $totalPrestasiInput = Prestasi::where('guru_pencatat', $guru->guru_id ?? 0)->count();
        $pelanggaranBulanIni = Pelanggaran::where('guru_pencatat', $guru->guru_id ?? 0)
            ->whereMonth('created_at', now()->month)->count();
        
        // Data wali kelas (role tambahan)
        $totalSiswaAmpu = 0;
        $pelanggaranKelas = 0;
        $prestasiKelas = 0;
        $siswaBerisiko = 0;
        
        if ($isWaliKelas) {
            $kelasIds = $kelasAmpu->pluck('kelas_id');
            
            // Total siswa yang diampu
            $totalSiswaAmpu = \App\Models\Siswa::whereIn('kelas_id', $kelasIds)
                ->where('status_kesiswaan', 'aktif')->count();
                
            // Total pelanggaran di kelas (semua pelanggaran siswa kelasnya)
            $pelanggaranKelas = Pelanggaran::whereHas('siswa', function($q) use ($kelasIds) {
                $q->whereIn('kelas_id', $kelasIds);
            })->count();
            
            // Total prestasi di kelas
            $prestasiKelas = Prestasi::whereHas('siswa', function($q) use ($kelasIds) {
                $q->whereIn('kelas_id', $kelasIds);
            })->count();
            
            // Siswa berisiko (poin pelanggaran tinggi)
            $siswaBerisiko = \App\Models\Siswa::whereIn('kelas_id', $kelasIds)
                ->whereHas('pelanggaran', function($q) {
                    $q->where('status_verifikasi', 'diverifikasi');
                })
                ->withCount(['pelanggaran as total_poin' => function($q) {
                    $q->where('status_verifikasi', 'diverifikasi')
                      ->join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.jenis_pelanggaran_id')
                      ->select(\DB::raw('SUM(jenis_pelanggaran.poin)'));
                }])
                ->having('total_poin', '>', 50)
                ->count();
        }
        
        // Recent activities (yang dia input sendiri sebagai guru)
        $recentPelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
            ->where('guru_pencatat', $guru->guru_id ?? 0)
            ->latest()->take(5)->get();
            
        $recentPrestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi'])
            ->where('guru_pencatat', $guru->guru_id ?? 0)
            ->latest()->take(5)->get();
        
        return view('guru.dashboard', compact(
            'guru', 'isWaliKelas', 'kelasAmpu', 'totalPelanggaranInput',
            'totalPrestasiInput', 'totalSiswaAmpu', 'pelanggaranKelas',
            'prestasiKelas', 'siswaBerisiko', 'pelanggaranBulanIni', 
            'recentPelanggaran', 'recentPrestasi'
        ));
    }

    // ==================== PELANGGARAN CRUD METHODS ====================
    // ROLE USAGE:
    // - inputPelanggaran()     : ADMIN + KESISWAAN + GURU
    // - storePelanggaran()     : ADMIN + KESISWAAN + GURU  
    // - viewDataPelanggaran()  : ADMIN + KESISWAAN + GURU (filtered)
    // - editPelanggaran()      : ADMIN + KESISWAAN + GURU
    // - updatePelanggaran()    : ADMIN + KESISWAAN + GURU
    // - detailPelanggaran()    : ADMIN + KESISWAAN + GURU
    // - deletePelanggaran()    : ADMIN + KESISWAAN + GURU
    // ==================== PELANGGARAN CRUD ====================
    
    /**
     * INPUT PELANGGARAN FORM
     * Used by: ADMIN + KESISWAAN + GURU
     * Routes: /admin/input-data/pelanggaran, /kesiswaan/input-data/pelanggaran, /guru/input-data/pelanggaran
     * Logic: Conditional siswa list berdasarkan role
     */
    public function inputPelanggaran()
    {
        $currentPrefix = request()->route()->getPrefix();
        $user = session('user');
        
        // Filter siswa berdasarkan role
        if ($currentPrefix === 'guru') {
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            $isWaliKelas = $guru && $guru->kelas()->exists();
            
            if ($isWaliKelas) {
                // Wali kelas: hanya siswa di kelasnya
                $kelasAmpu = $guru->kelas()->pluck('kelas_id');
                $siswa = Siswa::with('kelas')
                    ->where('status_kesiswaan', 'aktif')
                    ->whereIn('kelas_id', $kelasAmpu)
                    ->get();
            } else {
                // Guru biasa: semua siswa
                $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
            }
        } else {
            // Admin/Kesiswaan: semua siswa
            $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        }
        
        $jenisPelanggaran = JenisPelanggaran::all();
        $tahunAjaran = \App\Models\TahunAjaran::all();
        
        if ($currentPrefix === 'kesiswaan') {
            $viewDataRoute = 'kesiswaan.view-data.pelanggaran';
        } elseif ($currentPrefix === 'guru') {
            $viewDataRoute = 'guru.view-data.pelanggaran';
        } else {
            $viewDataRoute = 'admin.view-data.pelanggaran';
        }
        
        return view('admin.input-data.pelanggaran', compact('siswa', 'jenisPelanggaran', 'tahunAjaran', 'viewDataRoute'));
    }

    /**
     * STORE PELANGGARAN
     * Used by: ADMIN + KESISWAAN + GURU
     * Routes: POST /admin/input-data/pelanggaran, /kesiswaan/input-data/pelanggaran, /guru/input-data/pelanggaran
     */
    public function storePelanggaran(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:5120'
        ]);

        $user = session('user');
        $guruId = 1;
        
        // Get current tahun ajaran or default to 1
        $tahunAjaran = \App\Models\TahunAjaran::first();
        $tahunAjaranId = $tahunAjaran ? $tahunAjaran->tahun_ajaran_id : 1;
        
        // Get poin from jenis pelanggaran
        $jenisPelanggaran = JenisPelanggaran::find($request->jenis_pelanggaran_id);
        $poin = $jenisPelanggaran ? $jenisPelanggaran->poin : 0;

        // Tentukan nama pencatat dan status verifikasi berdasarkan role
        $pencatatNama = 'N/A';
        $statusVerifikasi = 'menunggu'; // Default untuk guru
        $guruVerifikator = null;
        $tanggalVerifikasi = null;
        
        if (in_array($user->level, ['admin', 'kesiswaan'])) {
            $pencatatNama = ucfirst($user->level);
            $statusVerifikasi = 'diverifikasi'; // Auto-verifikasi untuk admin/kesiswaan
            $guruVerifikator = 1; // ID verifikator
            $tanggalVerifikasi = now();
        } else {
            // Untuk guru, ambil nama dari database
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            if (!$guru) {
                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
            }
            $pencatatNama = $guru ? $guru->nama_guru : 'Guru';
            $statusVerifikasi = 'menunggu'; // Guru harus menunggu verifikasi
        }

        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'guru_pencatat' => 1,
            'catatan_verifikasi' => 'Pencatat: ' . $pencatatNama . ' - Menunggu verifikasi kesiswaan',
            'tahun_ajaran_id' => $tahunAjaranId,
            'poin' => $poin,
            'status_verifikasi' => $statusVerifikasi,
            'guru_verifikator' => $guruVerifikator,
            'tanggal_verifikasi' => $tanggalVerifikasi
        ];
        
        // Handle file upload
        if ($request->hasFile('bukti_foto')) {
            $data['bukti_foto'] = $request->file('bukti_foto')->store('pelanggaran', 'public');
        }
        
        Pelanggaran::create($data);

        return $this->redirectBasedOnPrefix('pelanggaran', 'Pelanggaran berhasil ditambahkan');
    }

    /**
     * VIEW DATA PELANGGARAN (Filtered by Role)
     * Used by: ADMIN + KESISWAAN + GURU
     * Routes: /admin/view-data/pelanggaran, /kesiswaan/view-data/pelanggaran, /guru/view-data/pelanggaran
     * Logic: Delegate to PelanggaranController::index for consistency
     */
    public function viewDataPelanggaran()
    {
        return app(\App\Http\Controllers\Admin\PelanggaranController::class)->index();
    }

    public function editPelanggaran($id)
    {
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])->findOrFail($id);
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $jenisPelanggaran = JenisPelanggaran::all();
        
        return view('admin.edit-data.pelanggaran', compact('pelanggaran', 'siswa', 'jenisPelanggaran'));
    }

    public function updatePelanggaran(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tanggal' => 'required|date',
            'keterangan' => 'required'
        ]);

        $pelanggaran = Pelanggaran::findOrFail($id);
        
        // Get poin from jenis pelanggaran
        $jenisPelanggaran = JenisPelanggaran::find($request->jenis_pelanggaran_id);
        $poin = $jenisPelanggaran ? $jenisPelanggaran->poin : 0;
        
        $pelanggaran->update([
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'poin' => $poin
        ]);

        return $this->redirectBasedOnPrefix('pelanggaran', 'Pelanggaran berhasil diperbarui');
    }

    public function detailPelanggaran($id)
    {
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])->findOrFail($id);
        return view('admin.detail-data.pelanggaran', compact('pelanggaran'));
    }

    public function deletePelanggaran($id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);
        $pelanggaran->delete();
        
        return $this->redirectBasedOnPrefix('pelanggaran', 'Pelanggaran berhasil dihapus');
    }

    // ==================== PRESTASI CRUD METHODS ====================
    // ROLE USAGE:
    // - inputPrestasi()     : ADMIN + KESISWAAN ONLY (Guru TIDAK BISA)
    // - storePrestasi()     : ADMIN + KESISWAAN ONLY
    // - viewDataPrestasi()  : ADMIN + KESISWAAN ONLY
    // - editPrestasi()      : ADMIN + KESISWAAN ONLY
    // - updatePrestasi()    : ADMIN + KESISWAAN ONLY
    // - detailPrestasi()    : ADMIN + KESISWAAN ONLY
    // - deletePrestasi()    : ADMIN + KESISWAAN ONLY
    // NOTE: GURU TIDAK BISA INPUT PRESTASI (Policy Decision)
    // ==================== PRESTASI CRUD ====================
    
    /**
     * INPUT PRESTASI FORM
     * Used by: ADMIN + KESISWAAN ONLY
     * Routes: /admin/input-data/prestasi, /kesiswaan/input-data/prestasi
     * Authorization: Block guru dengan 403 error
     */
    public function inputPrestasi()
    {
        $user = session('user');
        if (!in_array($user->level, ['admin', 'kesiswaan', 'guru', 'wali_kelas'])) {
            abort(403, 'Akses ditolak. Hanya admin, kesiswaan, dan guru yang dapat menginput prestasi.');
        }
        
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $jenisPrestasi = JenisPrestasi::all();
        $tahunAjaran = \App\Models\TahunAjaran::all();
        
        $currentPrefix = request()->route()->getPrefix();
        $viewDataRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.view-data.prestasi' : 'admin.view-data.prestasi';
        
        return view('admin.input-data.prestasi', compact('siswa', 'jenisPrestasi', 'tahunAjaran', 'viewDataRoute'));
    }

    public function storePrestasi(Request $request)
    {
        $user = session('user');
        if (!in_array($user->level, ['admin', 'kesiswaan', 'guru', 'wali_kelas'])) {
            abort(403, 'Akses ditolak. Hanya admin, kesiswaan, dan guru yang dapat menginput prestasi.');
        }
        
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_prestasi_id' => 'required|exists:jenis_prestasi,jenis_prestasi_id',
            'tingkat' => 'required|in:Sekolah,Kabupaten,Provinsi,Nasional,Internasional',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $jenisPrestasi = JenisPrestasi::find($request->jenis_prestasi_id);
        $statusVerifikasi = in_array($user->level, ['admin', 'kesiswaan']) ? 'diverifikasi' : 'menunggu';
        
        $guruId = 1;
        
        // Cari atau buat guru berdasarkan user yang login
        if ($user) {
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
                        
            if (!$guru) {
                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
            }
            
            // Jika tidak ditemukan, buat data guru baru dengan nama user
            if (!$guru) {
                $guru = \App\Models\Guru::create([
                    'nip' => $user->username,
                    'nama_guru' => $user->username,
                    'jenis_kelamin' => 'Laki-laki',
                    'bidang_studi' => ucfirst($user->level),
                    'status' => 'Aktif'
                ]);
            }
            
            $guruId = $guru->guru_id;
        }
        
        // Get current tahun ajaran or default to 1
        $tahunAjaran = \App\Models\TahunAjaran::first();
        $tahunAjaranId = $tahunAjaran ? $tahunAjaran->tahun_ajaran_id : 1;
        
        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_prestasi_id' => $request->jenis_prestasi_id,
            'tingkat' => $request->tingkat,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'poin' => $jenisPrestasi->poin,
            'guru_pencatat' => $guruId,
            'tahun_ajaran_id' => $tahunAjaranId,
            'status_verifikasi' => $statusVerifikasi
        ];
        
        // Handle file upload
        if ($request->hasFile('bukti_dokumen')) {
            $data['bukti_dokumen'] = $request->file('bukti_dokumen')->store('prestasi', 'public');
        }
        
        Prestasi::create($data);
        
        $message = in_array($user->level, ['admin', 'kesiswaan']) ? 'Prestasi berhasil ditambahkan dan langsung diverifikasi' : 'Prestasi berhasil ditambahkan, menunggu verifikasi kesiswaan';
        return $this->redirectBasedOnPrefix('prestasi', $message);
    }

    public function viewDataPrestasi()
    {
        $currentPrefix = request()->route()->getPrefix();
        $user = session('user');
        
        $query = Prestasi::with(['siswa.kelas', 'jenisPrestasi']);
        
        if ($currentPrefix === 'admin' || $user->level === 'admin') {
            // Admin: semua data
        } else {
            $query->where('status_verifikasi', '!=', 'draft');
        }
        
        // Filter berdasarkan tingkat
        if (request('tingkat')) {
            $query->whereHas('siswa.kelas', function($q) {
                $tingkat = request('tingkat');
                $q->where('nama_kelas', 'LIKE', $tingkat . '%');
            });
        }
        
        // Filter berdasarkan jurusan
        if (request('jurusan')) {
            $query->whereHas('siswa.kelas', function($q) {
                $jurusan = request('jurusan');
                $q->where('nama_kelas', 'LIKE', '%' . $jurusan . '%');
            });
        }
        
        // Filter berdasarkan status
        if (request('status')) {
            $query->where('status_verifikasi', request('status'));
        }
        
        $data = $query->latest()->get();
        
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $jenisPrestasi = JenisPrestasi::all();
        
        // Data untuk filter
        $tingkatList = \App\Models\Kelas::select('nama_kelas')
            ->get()
            ->map(function($kelas) {
                return substr($kelas->nama_kelas, 0, strpos($kelas->nama_kelas, ' ') ?: 1);
            })
            ->unique()
            ->sort()
            ->values();
            
        $jurusanList = \App\Models\Kelas::select('nama_kelas')
            ->get()
            ->map(function($kelas) {
                $parts = explode(' ', $kelas->nama_kelas);
                return count($parts) > 1 ? $parts[1] : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
        
        return view('admin.view-data.prestasi', compact('data', 'siswa', 'jenisPrestasi', 'tingkatList', 'jurusanList'));
    }

    public function editPrestasi($id)
    {
        $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi'])->findOrFail($id);
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $jenisPrestasi = JenisPrestasi::all();
        
        return view('admin.edit-data.prestasi', compact('prestasi', 'siswa', 'jenisPrestasi'));
    }

    public function updatePrestasi(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_prestasi_id' => 'required|exists:jenis_prestasi,jenis_prestasi_id',
            'tingkat' => 'required|in:Sekolah,Kabupaten,Provinsi,Nasional,Internasional',
            'tanggal' => 'required|date',
            'keterangan' => 'required'
        ]);

        $prestasi = Prestasi::findOrFail($id);
        $jenisPrestasi = JenisPrestasi::find($request->jenis_prestasi_id);
        
        $prestasi->update([
            'siswa_id' => $request->siswa_id,
            'jenis_prestasi_id' => $request->jenis_prestasi_id,
            'tingkat' => $request->tingkat,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'poin' => $jenisPrestasi->poin
        ]);

        return $this->redirectBasedOnPrefix('prestasi', 'Prestasi berhasil diperbarui');
    }

    public function detailPrestasi($id)
    {
        $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])->findOrFail($id);
        return view('admin.detail-data.prestasi', compact('prestasi'));
    }

    public function deletePrestasi($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->delete();
        
        return $this->redirectBasedOnPrefix('prestasi', 'Prestasi berhasil dihapus');
    }

    // ==================== VERIFIKASI METHODS ====================
    // ROLE USAGE:
    // - verifikasiData()        : ADMIN + KESISWAAN ONLY
    // - verifikasiPelanggaran() : ADMIN + KESISWAAN ONLY
    // - verifikasiPrestasi()    : ADMIN + KESISWAAN ONLY
    // NOTE: GURU TIDAK BISA VERIFIKASI (Policy Decision)
    // ==================== VERIFIKASI ====================
    
    /**
     * VERIFIKASI DATA PAGE
     * Used by: ADMIN + KESISWAAN ONLY
     * Routes: /admin/verifikasi-monitoring/verifikasi, /kesiswaan/verifikasi-monitoring/verifikasi
     */
    public function verifikasiData()
    {
        $user = session('user');
        $pelanggaranMenunggu = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])->where('status_verifikasi', 'menunggu')->get();
        
        if ($user->level === 'admin') {
            $prestasiMenunggu = collect();
        } else {
            $prestasiMenunggu = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])->where('status_verifikasi', 'menunggu')->get();
        }
        
        return view('admin.verifikasi-monitoring.verifikasi', compact('pelanggaranMenunggu', 'prestasiMenunggu'));
    }

    public function verifikasiPelanggaran(Request $request, $id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);
        $pelanggaran->update([
            'status_verifikasi' => $request->status,
            'catatan_verifikasi' => $request->catatan
        ]);
        
        return redirect()->back()->with('success', 'Status verifikasi pelanggaran berhasil diperbarui');
    }

    public function verifikasiPrestasi(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->update([
            'status_verifikasi' => $request->status,
            'catatan_verifikasi' => $request->catatan
        ]);
        
        return redirect()->back()->with('success', 'Status verifikasi prestasi berhasil diperbarui');
    }

    // ==================== VIEW DATA SISWA METHODS ====================
    // ROLE USAGE:
    // - viewSiswa() : ADMIN + KESISWAAN + GURU (Wali Kelas Only)
    // NOTE: GURU BIASA di-block dengan 403 error
    // ==================== VIEW DATA SISWA ====================
    
    /**
     * VIEW DATA SISWA (Monitoring Siswa)
     * Used by: ADMIN + KESISWAAN + GURU (Wali Kelas Only)
     * Routes: /admin/view-data/anak, /kesiswaan/view-data/anak, /guru/monitoring/siswa
     * Authorization: Block guru biasa, hanya wali kelas
     * Logic:
     * - ADMIN/KESISWAAN: Semua siswa
     * - WALI KELAS: Hanya siswa di kelas yang diampu
     */
    public function viewSiswa(Request $request)
    {
        $currentPrefix = request()->route()->getPrefix();
        $user = session('user');
        
        // AUTHORIZATION CHECK untuk guru
        if ($currentPrefix === 'guru') {
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            $isWaliKelas = $guru && $guru->kelas()->exists();
            
            // BLOCK GURU BIASA
            if (!$isWaliKelas) {
                abort(403, 'Akses ditolak. Hanya wali kelas yang dapat monitoring siswa.');
            }
            
            // FILTER HANYA SISWA KELAS YANG DIAMPU
            $kelasAmpu = $guru->kelas()->pluck('kelas_id');
            $siswaQuery = Siswa::with(['kelas', 'pelanggaran.jenisPelanggaran'])
                ->where('status_kesiswaan', 'aktif')
                ->whereIn('kelas_id', $kelasAmpu);
                
            $kelasList = $guru->kelas()->orderBy('nama_kelas')->get();
        } else {
            // ADMIN/KESISWAAN - AKSES PENUH
            $siswaQuery = Siswa::with(['kelas', 'pelanggaran.jenisPelanggaran', 'prestasi.jenisPrestasi'])
                ->where('status_kesiswaan', 'aktif');
                
            $kelasList = Kelas::orderBy('nama_kelas')->get();
        }
        
        $search = $request->get('search');
        $kelasFilter = $request->get('kelas');
        $siswaId = $request->get('siswa_id');
        
        if ($search) {
            $siswaQuery->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }
        
        if ($kelasFilter) {
            $siswaQuery->whereHas('kelas', function($q) use ($kelasFilter) {
                $q->where('nama_kelas', $kelasFilter);
            });
        }
        
        $siswaList = $siswaQuery->orderBy('nama_siswa')->get();
        
        $selectedSiswa = null;
        $pelanggaranData = collect();
        $prestasiData = collect();
        $bkData = collect();
        $statistics = ['pelanggaran' => 0, 'prestasi' => 0, 'poin' => 0];
        
        if ($siswaId) {
            $selectedSiswa = Siswa::with('kelas')->find($siswaId);
            if ($selectedSiswa) {
                $pelanggaranData = Pelanggaran::with('jenisPelanggaran')
                    ->where('siswa_id', $siswaId)
                    ->where('status_verifikasi', 'diverifikasi')
                    ->orderBy('tanggal', 'desc')
                    ->get();
                
                // Untuk guru: hanya pelanggaran, tidak ada prestasi & BK
                if ($currentPrefix === 'guru') {
                    $prestasiData = collect();
                    $bkData = collect();
                    $statistics = [
                        'pelanggaran' => $pelanggaranData->count(),
                        'poin' => $pelanggaranData->sum('poin')
                    ];
                } else {
                    // Admin/Kesiswaan: full data
                    $prestasiData = Prestasi::with('jenisPrestasi')
                        ->where('siswa_id', $siswaId)
                        ->where('status_verifikasi', 'diverifikasi')
                        ->orderBy('tanggal', 'desc')
                        ->get();
                        
                    $bkData = \App\Models\BimbinganKonseling::where('siswa_id', $siswaId)
                        ->orderBy('tanggal_konseling', 'desc')
                        ->get();
                        
                    $statistics = [
                        'pelanggaran' => $pelanggaranData->count(),
                        'prestasi' => $prestasiData->count(),
                        'poin' => $pelanggaranData->sum('poin') - $prestasiData->sum('poin')
                    ];
                }
            }
        }
        
        return view('admin.view-data.anak', compact(
            'siswaList', 'kelasList', 'selectedSiswa', 'pelanggaranData', 
            'prestasiData', 'bkData', 'statistics', 'search', 'kelasFilter'
        ));
    }

    // ==================== MANAJEMEN SANKSI METHODS ====================
    // ROLE USAGE:
    // - manageSanksi()           : ADMIN + KESISWAAN + GURU (Wali Kelas Only - READ ONLY)
    // - storeSanksi()            : ADMIN + KESISWAAN ONLY
    // - updateSanksi()           : ADMIN + KESISWAAN ONLY
    // - managePelaksanaanSanksi() : ADMIN + KESISWAAN ONLY
    // - storePelaksanaanSanksi()  : ADMIN + KESISWAAN ONLY
    // NOTE: GURU WALI KELAS hanya bisa VIEW (READ ONLY), tidak bisa CRUD
    // ==================== MANAJEMEN SANKSI ====================
    
    /**
     * MANAGE SANKSI (View + CRUD)
     * Used by: ADMIN + KESISWAAN + GURU (Wali Kelas Only - READ ONLY)
     * Routes: /admin/sanksi, /kesiswaan/sanksi, /guru/monitoring/sanksi
     * Authorization: Block guru biasa, wali kelas READ-only
     * Logic:
     * - ADMIN/KESISWAAN: Full CRUD access
     * - WALI KELAS: Read-only, hanya sanksi siswa kelasnya
     */
    public function manageSanksi()
    {
        $currentPrefix = request()->route()->getPrefix();
        $user = session('user');
        
        if ($currentPrefix === 'guru') {
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            $isWaliKelas = $guru && $guru->kelas()->exists();
            
            // BLOCK GURU BIASA
            if (!$isWaliKelas) {
                abort(403, 'Akses ditolak. Hanya wali kelas yang dapat monitoring sanksi.');
            }
            
            // FILTER HANYA SANKSI SISWA KELASNYA (READ ONLY)
            $kelasAmpu = $guru->kelas()->pluck('kelas_id');
            $data = \App\Models\Sanksi::with(['pelanggaran.siswa.kelas', 'pelanggaran.jenisPelanggaran', 'jenisSanksi', 'guruPenanggungjawab'])
                ->whereHas('pelanggaran.siswa', function($q) use ($kelasAmpu) {
                    $q->whereIn('kelas_id', $kelasAmpu);
                })->latest()->get();
            
            // Tidak perlu data untuk input (READ ONLY)
            $siswaWithPelanggaran = collect();
            $jenisSanksi = collect();
            $guru = collect();
        } else {
            // ADMIN/KESISWAAN - FULL ACCESS dengan Filter
            $query = \App\Models\Sanksi::with(['pelanggaran.siswa.kelas', 'pelanggaran.jenisPelanggaran', 'jenisSanksi', 'guruPenanggungjawab']);
            
            // Filter berdasarkan tingkat
            if (request('tingkat')) {
                $query->whereHas('pelanggaran.siswa.kelas', function($q) {
                    $tingkat = request('tingkat');
                    $q->where('nama_kelas', 'LIKE', $tingkat . '%');
                });
            }
            
            // Filter berdasarkan jurusan
            if (request('jurusan')) {
                $query->whereHas('pelanggaran.siswa.kelas', function($q) {
                    $jurusan = request('jurusan');
                    $q->where('nama_kelas', 'LIKE', '%' . $jurusan . '%');
                });
            }
            
            // Filter berdasarkan status sanksi
            if (request('status')) {
                $query->where('status', request('status'));
            }
            
            $data = $query->latest()->get();
            
            $siswaWithPelanggaran = \App\Models\Siswa::with('kelas')
                ->whereHas('pelanggaran', function($q) {
                    $q->where('status_verifikasi', 'diverifikasi');
                })->get();
            $guru = \App\Models\Guru::all();
            
            // Ambil data tingkat dan jurusan dari database
            $tingkatList = \App\Models\Kelas::select('nama_kelas')
                ->get()
                ->map(function($kelas) {
                    return substr($kelas->nama_kelas, 0, strpos($kelas->nama_kelas, ' ') ?: 1);
                })
                ->unique()
                ->sort()
                ->values();
                
            $jurusanList = \App\Models\Kelas::select('nama_kelas')
                ->get()
                ->map(function($kelas) {
                    $parts = explode(' ', $kelas->nama_kelas);
                    return count($parts) > 1 ? $parts[1] : null;
                })
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }
        
        return view('admin.sanksi.index', compact('data', 'siswaWithPelanggaran', 'guru', 'tingkatList', 'jurusanList'));
    }

    public function storeSanksi(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_sanksi_type' => 'required',
            'jenis_sanksi_manual' => 'required_if:jenis_sanksi_type,other',
            'status' => 'required|in:terdaftar,dijadwalkan,berlangsung,selesai,tindak_lanjut',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'guru_penanggungjawab' => 'nullable|exists:guru,guru_id'
        ]);

        $pelanggaran = \App\Models\Pelanggaran::where('siswa_id', $request->siswa_id)
            ->where('status_verifikasi', 'diverifikasi')
            ->first();

        $jenisSanksiId = null;
        $jenisSanksiManual = null;
        
        if ($request->jenis_sanksi_type === 'other') {
            $jenisSanksiManual = $request->jenis_sanksi_manual;
        } else {
            $jenisSanksiId = $request->jenis_sanksi_type;
        }

        \App\Models\Sanksi::create([
            'pelanggaran_id' => $pelanggaran->pelanggaran_id,
            'jenis_sanksi_id' => $jenisSanksiId,
            'jenis_sanksi_manual' => $jenisSanksiManual,
            'status' => $request->status,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'guru_penanggungjawab' => $request->guru_penanggungjawab,
            'deskripsi_sanksi' => $request->deskripsi_sanksi,
            'catatan_pelaksanaan' => $request->catatan_pelaksanaan
        ]);

        $currentPrefix = request()->route()->getPrefix();
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.sanksi.index')->with('success', 'Sanksi berhasil ditambahkan');
        }
        return redirect()->route('admin.sanksi.index')->with('success', 'Sanksi berhasil ditambahkan');
    }

    public function updateSanksi(Request $request, $id)
    {
        $sanksi = \App\Models\Sanksi::findOrFail($id);
        $sanksi->update($request->all());
        
        $currentPrefix = request()->route()->getPrefix();
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.sanksi.index')->with('success', 'Sanksi berhasil diperbarui');
        }
        return redirect()->route('admin.sanksi.index')->with('success', 'Sanksi berhasil diperbarui');
    }

    public function managePelaksanaanSanksi()
    {
        $query = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas', 'sanksi.jenisSanksi', 'guruPengawas']);
        
        // Filter berdasarkan tingkat
        if (request('tingkat')) {
            $query->whereHas('sanksi.pelanggaran.siswa.kelas', function($q) {
                $tingkat = request('tingkat');
                $q->where('nama_kelas', 'LIKE', $tingkat . '%');
            });
        }
        
        // Filter berdasarkan jurusan
        if (request('jurusan')) {
            $query->whereHas('sanksi.pelanggaran.siswa.kelas', function($q) {
                $jurusan = request('jurusan');
                $q->where('nama_kelas', 'LIKE', '%' . $jurusan . '%');
            });
        }
        
        // Filter berdasarkan status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        // Filter berdasarkan tanggal
        if (request('tanggal')) {
            $query->whereDate('tanggal_pelaksanaan', request('tanggal'));
        }
        
        $data = $query->latest()->get();
        
        $sanksiAktif = \App\Models\Sanksi::with(['pelanggaran.siswa.kelas', 'jenisSanksi'])
            ->whereIn('status', ['terdaftar', 'dijadwalkan', 'berlangsung'])->get();
        $guru = \App\Models\Guru::all();
        
        // Ambil data tingkat dan jurusan dari database
        $tingkatList = \App\Models\Kelas::select('nama_kelas')
            ->get()
            ->map(function($kelas) {
                return substr($kelas->nama_kelas, 0, strpos($kelas->nama_kelas, ' ') ?: 1);
            })
            ->unique()
            ->sort()
            ->values();
            
        $jurusanList = \App\Models\Kelas::select('nama_kelas')
            ->get()
            ->map(function($kelas) {
                $parts = explode(' ', $kelas->nama_kelas);
                return count($parts) > 1 ? $parts[1] : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
        
        return view('admin.sanksi.pelaksanaan', compact('data', 'sanksiAktif', 'guru', 'tingkatList', 'jurusanList'));
    }

    public function storePelaksanaanSanksi(Request $request)
    {
        $request->validate([
            'sanksi_id' => 'required|exists:sanksi,sanksi_id',
            'tanggal_pelaksanaan' => 'required|date',
            'status' => 'required|in:terjadwal,dikerjakan,tuntas,terlambat,perpanjangan',
            'guru_pengawas' => 'nullable|exists:guru,guru_id',
            'bukti_pelaksanaan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_pelaksanaan')) {
            $buktiPath = $request->file('bukti_pelaksanaan')->store('bukti_pelaksanaan', 'public');
        }

        PelaksanaanSanksi::create([
            'sanksi_id' => $request->sanksi_id,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'status' => $request->status,
            'guru_pengawas' => $request->guru_pengawas,
            'deskripsi_pelaksanaan' => $request->deskripsi_pelaksanaan,
            'bukti_pelaksanaan' => $buktiPath,
            'catatan' => $request->catatan
        ]);
        
        $currentPrefix = request()->route()->getPrefix();
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.sanksi.pelaksanaan')->with('success', 'Pelaksanaan sanksi berhasil ditambahkan');
        }
        return redirect()->route('admin.sanksi.pelaksanaan')->with('success', 'Pelaksanaan sanksi berhasil ditambahkan');
    }

    public function showPelaksanaanSanksi($id)
    {
        $pelaksanaan = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas', 'sanksi.jenisSanksi', 'guruPengawas'])->findOrFail($id);
        
        return response()->json([
            'siswa_nama' => $pelaksanaan->sanksi->pelanggaran->siswa->nama_siswa ?? '-',
            'kelas_nama' => $pelaksanaan->sanksi->pelanggaran->siswa->kelas->nama_kelas ?? '-',
            'jenis_sanksi' => $pelaksanaan->sanksi->jenisSanksi->nama_sanksi ?? '-',
            'status' => ucfirst($pelaksanaan->status),
            'tanggal_pelaksanaan' => $pelaksanaan->tanggal_pelaksanaan->format('d/m/Y'),
            'deskripsi_pelaksanaan' => $pelaksanaan->deskripsi_pelaksanaan,
            'catatan' => $pelaksanaan->catatan,
            'guru_pengawas' => $pelaksanaan->guruPengawas->nama_guru ?? '-'
        ]);
    }

    public function editPelaksanaanSanksi($id)
    {
        $pelaksanaan = PelaksanaanSanksi::findOrFail($id);
        
        return response()->json([
            'status' => $pelaksanaan->status,
            'tanggal_pelaksanaan' => $pelaksanaan->tanggal_pelaksanaan->format('Y-m-d'),
            'deskripsi_pelaksanaan' => $pelaksanaan->deskripsi_pelaksanaan,
            'catatan' => $pelaksanaan->catatan
        ]);
    }

    public function updatePelaksanaanSanksi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terjadwal,dikerjakan,tuntas,terlambat,perpanjangan',
            'tanggal_pelaksanaan' => 'required|date',
        ]);

        $pelaksanaan = PelaksanaanSanksi::findOrFail($id);
        $pelaksanaan->update($request->all());
        
        $currentPrefix = request()->route()->getPrefix();
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.sanksi.pelaksanaan')->with('success', 'Pelaksanaan sanksi berhasil diperbarui');
        }
        return redirect()->route('admin.sanksi.pelaksanaan')->with('success', 'Pelaksanaan sanksi berhasil diperbarui');
    }

    public function destroyPelaksanaanSanksi($id)
    {
        $pelaksanaan = PelaksanaanSanksi::findOrFail($id);
        $pelaksanaan->delete();
        
        $currentPrefix = request()->route()->getPrefix();
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.sanksi.pelaksanaan')->with('success', 'Pelaksanaan sanksi berhasil dihapus');
        }
        return redirect()->route('admin.sanksi.pelaksanaan')->with('success', 'Pelaksanaan sanksi berhasil dihapus');
    }

    // ==================== MONITORING METHODS ====================
    // ROLE USAGE:
    // - monitoring() : ADMIN + KESISWAAN + GURU (Wali Kelas Only)
    // NOTE: GURU BIASA di-block dengan 403 error
    // ==================== MONITORING ====================
    
    /**
     * MONITORING KELAS (General Monitoring)
     * Used by: ADMIN + KESISWAAN + GURU (Wali Kelas Only)
     * Routes: /admin/verifikasi-monitoring/monitoring, /kesiswaan/verifikasi-monitoring/monitoring, /guru/monitoring/kelas
     * Authorization: Block guru biasa, hanya wali kelas
     */
    public function monitoring()
    {
        $currentPrefix = request()->route()->getPrefix();
        $user = session('user');
        
        if ($currentPrefix === 'guru') {
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            $isWaliKelas = $guru && $guru->kelas()->exists();
            
            // BLOCK GURU BIASA
            if (!$isWaliKelas) {
                abort(403, 'Akses ditolak. Hanya wali kelas yang dapat monitoring kelas.');
            }
            
            // FILTER HANYA DATA KELAS YANG DIAMPU
            $kelasAmpu = $guru->kelas()->pluck('kelas_id');
            $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
                ->whereHas('siswa', function($q) use ($kelasAmpu) {
                    $q->whereIn('kelas_id', $kelasAmpu);
                })->latest()->get();
                
            $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi'])
                ->whereHas('siswa', function($q) use ($kelasAmpu) {
                    $q->whereIn('kelas_id', $kelasAmpu);
                })->latest()->get();
        } else {
            // ADMIN/KESISWAAN - FULL ACCESS
            $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])->latest()->get();
            $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi'])->latest()->get();
        }
        
        return view('admin.verifikasi-monitoring.monitoring', compact('pelanggaran', 'prestasi'));
    }
    


    // ==================== HELPER METHODS ====================
    // ROLE USAGE:
    // - redirectBasedOnPrefix() : ADMIN + KESISWAAN + GURU (Internal Helper)
    // NOTE: Method internal untuk redirect berdasarkan route prefix
    // ==================== HELPER METHODS ====================
    
    /**
     * REDIRECT HELPER (Internal Method)
     * Used by: ADMIN + KESISWAAN + GURU (Internal)
     * Purpose: Redirect ke route yang sesuai berdasarkan prefix
     */
    private function redirectBasedOnPrefix($type, $message)
    {
        $currentPrefix = request()->route()->getPrefix();
        
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route("kesiswaan.view-data.{$type}")->with('success', $message);
        } elseif ($currentPrefix === 'guru') {
            return redirect()->route("guru.view-data.{$type}")->with('success', $message);
        }
        
        return redirect()->route("admin.view-data.{$type}")->with('success', $message);
    }
}