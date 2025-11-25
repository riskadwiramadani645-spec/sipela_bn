<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\JenisSanksi;
use App\Models\TahunAjaran;
use App\Models\Sanksi;
use App\Models\PelaksanaanSanksi;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    public function index()
    {
        $user = session('user');
        $currentPrefix = request()->route()->getPrefix();
        
        $query = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat', 'guruVerifikator', 'tahunAjaran', 'sanksi']);
        
        // Role-based data filtering
        if ($currentPrefix === 'admin' || $user->level === 'admin') {
            // Admin: Full access - no additional filter
        } elseif ($currentPrefix === 'kesiswaan' || $user->level === 'kesiswaan') {
            // Kesiswaan: Only verified data
            $query->where('status_verifikasi', '!=', 'draft');
        } elseif ($currentPrefix === 'guru' || $user->level === 'guru') {
            // Guru: Only data they input themselves
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            if (!$guru) {
                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
            }
            
            $guruId = $guru ? $guru->guru_id : -1; // Use -1 if guru not found to show no data
            $query->where('guru_pencatat', $guruId);
        } else {
            // Other roles: Limited access
            $query->where('status_verifikasi', 'diverifikasi');
        }
        
        // Apply filters
        if (request('tingkat')) {
            $query->whereHas('siswa.kelas', function($q) {
                $tingkat = request('tingkat');
                $q->where('nama_kelas', 'LIKE', $tingkat . '%');
            });
        }
        
        if (request('jurusan')) {
            $query->whereHas('siswa.kelas', function($q) {
                $jurusan = request('jurusan');
                $q->where('nama_kelas', 'LIKE', '%' . $jurusan . '%');
            });
        }
        
        if (request('status')) {
            $query->where('status_verifikasi', request('status'));
        }
        
        $data = $query->latest()->get();
        
        try {
            $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
            $jenisPelanggaran = JenisPelanggaran::all();
            $jenisSanksi = JenisSanksi::all();
            $guru = \App\Models\Guru::all();
            
        } catch (\Exception $e) {
            \Log::error('Database error in PelanggaranController: ' . $e->getMessage());
            
            // Fallback empty collections
            $siswa = collect([]);
            $jenisPelanggaran = collect([]);
            $jenisSanksi = collect([]);
            $guru = collect([]);
        }
        
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
        
        return view('admin.view-data.pelanggaran', compact('data', 'siswa', 'jenisPelanggaran', 'jenisSanksi', 'guru', 'tingkatList', 'jurusanList'));
    }

    public function create()
    {
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $jenisPelanggaran = JenisPelanggaran::all();
        $tahunAjaran = TahunAjaran::where('status_aktif', true)->get();
        return view('admin.input-data.pelanggaran', compact('siswa', 'jenisPelanggaran', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,tahun_ajaran_id',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $jenisPelanggaran = JenisPelanggaran::where('jenis_pelanggaran_id', $request->jenis_pelanggaran_id)->first();
        $user = session('user');
        
        $bukti_foto = null;
        if ($request->hasFile('bukti_foto')) {
            $bukti_foto = $request->file('bukti_foto')->store('pelanggaran', 'public');
        }
        
        // Cek role user untuk status verifikasi
        $user = session('user');
        $statusVerifikasi = 'menunggu'; // Default untuk guru
        $guruVerifikator = null;
        
        // Tentukan nama pencatat berdasarkan role
        $pencatatNama = 'N/A';
        if (in_array($user->level, ['admin', 'kesiswaan'])) {
            $pencatatNama = ucfirst($user->level);
            $statusVerifikasi = 'diverifikasi';
            $guruVerifikator = $user->guru_id ?? 1;
        } else {
            // Untuk guru, ambil nama dari database
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            if (!$guru) {
                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
            }
            $pencatatNama = $guru ? $guru->nama_guru : 'Guru';
        }
        
        Pelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'bukti_foto' => $bukti_foto,
            'poin' => $jenisPelanggaran->poin,
            'guru_pencatat' => $request->guru_pencatat ?? $user->guru_id ?? 1,
            'catatan_verifikasi' => 'Pencatat: ' . $pencatatNama,
            'status_verifikasi' => $statusVerifikasi,
            'guru_verifikator' => $guruVerifikator,
            'tanggal_verifikasi' => $statusVerifikasi == 'diverifikasi' ? now() : null
        ]);
        
        $currentPrefix = request()->route()->getPrefix();
        
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.view-data.pelanggaran')->with('success', 'Pelanggaran berhasil ditambahkan');
        }
        
        return redirect()->route('admin.view-data.pelanggaran')->with('success', 'Pelanggaran berhasil ditambahkan');
    }

    public function show($id)
    {
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat', 'guruVerifikator', 'tahunAjaran'])
                                 ->findOrFail($id);
        return view('admin.view-data.pelanggaran-detail', compact('pelanggaran'));
    }

    public function edit($id)
    {
        $user = session('user');
        $currentPrefix = request()->route()->getPrefix();
        
        $query = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran']);
        
        // Check access rights
        if ($currentPrefix === 'guru' || $user->level === 'guru') {
            // Guru: Only data they input themselves
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            if (!$guru) {
                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
            }
            
            $guruId = $guru ? $guru->guru_id : -1;
            $query->where('guru_pencatat', $guruId);
        }
        
        $pelanggaran = $query->findOrFail($id);
        $siswa = Siswa::with('kelas')->get();
        $jenisPelanggaran = JenisPelanggaran::all();
        $tahunAjaran = TahunAjaran::all();
        
        return response()->json([
            'pelanggaran' => $pelanggaran,
            'siswa' => $siswa,
            'jenisPelanggaran' => $jenisPelanggaran,
            'tahunAjaran' => $tahunAjaran
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,tahun_ajaran_id',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:5120'
        ]);

        $user = session('user');
        $currentPrefix = request()->route()->getPrefix();
        
        $query = Pelanggaran::query();
        
        // Check access rights
        if ($currentPrefix === 'guru' || $user->level === 'guru') {
            // Guru: Only data they input themselves
            $guru = \App\Models\Guru::where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
            
            if (!$guru) {
                $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
            }
            
            $guruId = $guru ? $guru->guru_id : -1;
            $query->where('guru_pencatat', $guruId);
        }
        
        $pelanggaran = $query->findOrFail($id);
        $jenisPelanggaran = JenisPelanggaran::findOrFail($request->jenis_pelanggaran_id);
        
        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'poin' => $jenisPelanggaran->poin
        ];
        
        // Handle file upload
        if ($request->hasFile('bukti_foto')) {
            // Delete old file if exists
            if ($pelanggaran->bukti_foto && file_exists(storage_path('app/public/' . $pelanggaran->bukti_foto))) {
                unlink(storage_path('app/public/' . $pelanggaran->bukti_foto));
            }
            
            $data['bukti_foto'] = $request->file('bukti_foto')->store('pelanggaran', 'public');
        }
        
        $pelanggaran->update($data);
        
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.pelanggaran.index')->with('success', 'Pelanggaran berhasil diperbarui');
        } elseif ($currentPrefix === 'guru') {
            return redirect()->route('guru.data-pelanggaran')->with('success', 'Pelanggaran berhasil diperbarui');
        }
        
        return redirect()->route('admin.view-data.pelanggaran')->with('success', 'Pelanggaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        try {
            $user = session('user');
            $currentPrefix = request()->route()->getPrefix();
            
            $query = Pelanggaran::query();
            
            // Check access rights
            if ($currentPrefix === 'guru' || $user->level === 'guru') {
                // Guru: Only data they input themselves
                $guru = \App\Models\Guru::where('nip', $user->username)
                            ->orWhere('email', $user->username)
                            ->first();
                
                if (!$guru) {
                    $guru = \App\Models\Guru::where('nama_guru', 'LIKE', '%' . $user->username . '%')->first();
                }
                
                $guruId = $guru ? $guru->guru_id : -1;
                $query->where('guru_pencatat', $guruId);
            }
            
            $pelanggaran = $query->findOrFail($id);
            
            // Cek apakah ada sanksi terkait
            if ($pelanggaran->sanksi) {
                return redirect()->back()->with('error', 'Pelanggaran tidak dapat dihapus karena sudah memiliki sanksi. Hapus sanksi terlebih dahulu.');
            }
            
            $pelanggaran->delete();
            return redirect()->back()->with('success', 'Pelanggaran berhasil dihapus');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint error
            if ($e->getCode() == '23000') {
                return redirect()->back()->with('error', 'Pelanggaran tidak dapat dihapus karena masih terkait dengan data lain (sanksi/pelaksanaan). Hapus data terkait terlebih dahulu.');
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus pelanggaran.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    
    // VERIFIKASI PELANGGARAN (khusus admin/kesiswaan)
    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:diverifikasi,ditolak',
            'catatan_verifikasi' => 'nullable|string'
        ]);
        
        $user = session('user');
        
        // Hanya admin/kesiswaan yang bisa verifikasi
        if (!in_array($user->role, ['admin', 'kesiswaan'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk verifikasi');
        }
        
        $pelanggaran = Pelanggaran::findOrFail($id);
        
        $pelanggaran->update([
            'status_verifikasi' => $request->status_verifikasi,
            'guru_verifikator' => $user->guru_id ?? 1,
            'tanggal_verifikasi' => now(),
            'catatan_verifikasi' => $request->catatan_verifikasi
        ]);
        
        $message = $request->status_verifikasi == 'diverifikasi' 
            ? 'Pelanggaran berhasil diverifikasi' 
            : 'Pelanggaran ditolak';
            
        return redirect()->back()->with('success', $message);
    }
    
    public function indexVerifikasi()
    {
        // Halaman khusus untuk verifikasi pelanggaran
        $pelanggaranMenunggu = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
            ->where('status_verifikasi', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.verifikasi.pelanggaran', compact('pelanggaranMenunggu'));
    }

    // SANKSI TERINTEGRASI (sesuai ujikom - tidak ada menu terpisah)
    public function createSanksi(Request $request, $id)
    {
        try {
            // Cek apakah pelanggaran sudah diverifikasi
            $pelanggaran = Pelanggaran::findOrFail($id);
            
            if ($pelanggaran->status_verifikasi !== 'diverifikasi') {
                return redirect()->back()->with('error', 'Sanksi hanya dapat dibuat untuk pelanggaran yang sudah diverifikasi');
            }
            
            \Log::info('Creating sanksi for pelanggaran ID: ' . $id, $request->all());
            
            $request->validate([
                'jenis_sanksi_id' => 'required|exists:jenis_sanksi,jenis_sanksi_id',
                'status' => 'required|in:terdaftar,dijadwalkan,berlangsung,selesai,tindak_lanjut',
                'guru_penanggungjawab' => 'nullable|integer',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'catatan_pelaksanaan' => 'nullable|string'
            ]);

            // Get jenis sanksi from database
            $jenisSanksiModel = JenisSanksi::findOrFail($request->jenis_sanksi_id);
            $jenisSanksi = $jenisSanksiModel->nama_sanksi;

            $sanksi = Sanksi::create([
                'pelanggaran_id' => $id,
                'jenis_sanksi_id' => $request->jenis_sanksi_id,
                'jenis_sanksi' => $jenisSanksi,
                'status' => $request->status ?? 'terdaftar',
                'guru_penanggungjawab' => $request->guru_penanggungjawab,

                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'catatan_pelaksanaan' => $request->catatan_pelaksanaan
            ]);
            
            \Log::info('Sanksi created successfully', ['sanksi_id' => $sanksi->sanksi_id]);
            
            // Buat notifikasi untuk BK jika ada guru penanggung jawab
            if ($request->guru_penanggungjawab) {
                $guruBK = \App\Models\Guru::find($request->guru_penanggungjawab);
                
                if ($guruBK) {
                    // Kirim notifikasi ke semua user BK
                    $usersBK = \App\Models\User::where('level', 'konselor_bk')
                        ->whereNotNull('user_id')
                        ->get();
                    
                    foreach ($usersBK as $userBK) {
                        if ($userBK->user_id) {
                            \App\Models\Notification::create([
                                'type' => 'sanksi_followup',
                                'user_id' => $userBK->user_id,
                                'sanksi_id' => $sanksi->sanksi_id,
                                'title' => 'Follow-up Sanksi Diperlukan',
                                'message' => 'Sanksi untuk siswa ' . ($pelanggaran->siswa->nama_siswa ?? 'N/A') . ' memerlukan follow-up konseling. Guru penanggung jawab: ' . $guruBK->nama_guru . '. Jenis sanksi: ' . $jenisSanksi,
                                'is_read' => false
                            ]);
                        }
                    }
                }
            }
            
            $currentPrefix = request()->route()->getPrefix();
            $message = 'Sanksi berhasil dibuat dan masuk ke manajemen sanksi';
            
            if ($currentPrefix === 'kesiswaan') {
                return redirect()->route('kesiswaan.view-data.pelanggaran')->with('success', $message);
            }
            
            return redirect()->route('admin.view-data.pelanggaran', ['sanksi_success' => 1])->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Error creating sanksi: ' . $e->getMessage(), [
                'pelanggaran_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Gagal membuat sanksi: ' . $e->getMessage());
        }
    }

    public function editSanksi($id)
    {
        $sanksi = Sanksi::with(['pelanggaran.siswa'])->findOrFail($id);
        $jenisSanksi = JenisSanksi::all();
        $guru = \App\Models\Guru::all();
        
        return response()->json([
            'sanksi' => $sanksi,
            'jenisSanksi' => $jenisSanksi,
            'guru' => $guru
        ]);
    }

    public function showSanksi($pelanggaranId)
    {
        $sanksi = Sanksi::with(['pelanggaran.siswa.kelas', 'pelanggaran.jenisPelanggaran', 'pelaksanaanSanksi'])
            ->where('pelanggaran_id', $pelanggaranId)
            ->firstOrFail();
        return view('admin.view-data.sanksi-detail', compact('sanksi'));
    }

    public function updateSanksi(Request $request, $id)
    {
        $request->validate([
            'jenis_sanksi_id' => 'nullable|exists:jenis_sanksi,jenis_sanksi_id',
            'status' => 'required|in:direncanakan,berjalan,selesai,ditunda,dibatalkan',
            'guru_penanggungjawab' => 'nullable|integer',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan_pelaksanaan' => 'nullable|string'
        ]);

        $sanksi = Sanksi::findOrFail($id);
        
        $updateData = $request->only(['status', 'guru_penanggungjawab', 'tanggal_mulai', 'tanggal_selesai', 'catatan_pelaksanaan']);
        
        if ($request->jenis_sanksi_id) {
            $jenisSanksiModel = JenisSanksi::findOrFail($request->jenis_sanksi_id);
            $updateData['jenis_sanksi'] = $jenisSanksiModel->nama_sanksi;
        }
        
        $sanksi->update($updateData);
        
        return redirect()->back()->with('success', 'Data sanksi berhasil diperbarui');
    }

    public function createPelaksanaan(Request $request, $id)
    {
        $request->validate([
            'tanggal_pelaksanaan' => 'required|date',
            'keterangan' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $sanksi = Sanksi::findOrFail($id);
        $user = session('user');
        
        $bukti_foto = null;
        if ($request->hasFile('bukti_foto')) {
            $bukti_foto = $request->file('bukti_foto')->store('pelaksanaan_sanksi', 'public');
        }
        
        PelaksanaanSanksi::create([
            'sanksi_id' => $id,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'keterangan' => $request->keterangan,
            'bukti_foto' => $bukti_foto,
            'guru_pengawas' => $user->guru_id ?? 1
        ]);
        
        return redirect()->back()->with('success', 'Pelaksanaan sanksi berhasil dicatat');
    }

    public function sanksiIndex()
    {
        return view('kesiswaan.sanksi.index');
    }

    public function pelaksanaanIndex()
    {
        return view('kesiswaan.sanksi.pelaksanaan');
    }

    public function destroySanksi($id)
    {
        $sanksi = Sanksi::findOrFail($id);
        $sanksi->delete();
        
        return redirect()->back()->with('success', 'Sanksi berhasil dihapus');
    }
}