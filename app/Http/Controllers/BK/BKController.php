<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BimbinganKonseling;
use App\Models\PelaksanaanSanksi;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Sanksi;
use App\Models\Pelanggaran;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BKController extends Controller
{
    public function dashboard()
    {
        try {
            $totalKonseling = BimbinganKonseling::count();
            // Hitung notifikasi follow-up sanksi yang belum dibaca
            $user = session('user');
            $followUpSanksi = Notification::where('user_id', $user->user_id)
                ->where('type', 'sanksi_followup')
                ->where('is_read', false)
                ->count();
            $siswaAktif = BimbinganKonseling::distinct('siswa_id')->count('siswa_id');
            $successRate = $totalKonseling > 0 ? round((BimbinganKonseling::where('status', 'Selesai')->count() / $totalKonseling) * 100) : 0;
            
            // Data untuk tabel recent konseling
            $recentKonseling = BimbinganKonseling::with(['siswa.kelas', 'guru'])
                ->orderBy('tanggal_konseling', 'desc')
                ->limit(5)
                ->get();
                
            // Data sanksi yang perlu follow-up dari notifikasi
            $user = session('user');
            $sanksiFollowUp = Notification::with(['sanksi.pelanggaran.siswa.kelas'])
                ->where('user_id', $user->user_id)
                ->where('type', 'sanksi_followup')
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
        } catch (\Exception $e) {
            $totalKonseling = 0;
            $followUpSanksi = 0;
            $siswaAktif = 0;
            $successRate = 0;
            $recentKonseling = collect();
            $sanksiFollowUp = collect();
        }
        
        return view('bk.dashboard', compact(
            'totalKonseling', 'followUpSanksi', 'siswaAktif', 'successRate',
            'recentKonseling', 'sanksiFollowUp'
        ));
    }
    
    public function exportLaporan()
    {
        // Stats untuk quick reference
        $stats = [
            'konseling_bulan_ini' => BimbinganKonseling::whereMonth('tanggal_konseling', now()->month)
                                                      ->whereYear('tanggal_konseling', now()->year)
                                                      ->count(),
            'siswa_konseling' => BimbinganKonseling::distinct('siswa_id')->count('siswa_id'),
            'konseling_selesai' => BimbinganKonseling::where('status', 'selesai')->count(),
            'perlu_tindak_lanjut' => BimbinganKonseling::where('status', 'tindak_lanjut')->count()
        ];
        
        return view('bk.laporan', compact('stats'));
    }
    
    public function processExport(Request $request)
    {
        $type = $request->type ?? 'konseling';
        $periode = $request->periode ?? 'bulan_ini';
        $status = $request->status;
        $kelas_id = $request->kelas_id;
        
        // Query data berdasarkan jenis laporan
        switch($type) {
            case 'konseling':
                $query = BimbinganKonseling::with(['siswa.kelas']);
                break;
            case 'follow_up':
                $query = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas']);
                break;
            case 'progress':
                $query = BimbinganKonseling::with(['siswa.kelas'])
                        ->whereNotNull('tanggal_tindak_lanjut');
                break;
            case 'siswa_bermasalah':
                $query = Siswa::with(['kelas', 'pelanggaran'])
                        ->whereHas('pelanggaran', function($q) {
                            $q->where('status_verifikasi', 'diverifikasi');
                        });
                break;
            default:
                $query = BimbinganKonseling::with(['siswa.kelas']);
        }
        
        // Filter berdasarkan periode (kecuali untuk siswa_bermasalah)
        if ($type !== 'siswa_bermasalah') {
            $dateField = $type === 'follow_up' ? 'tanggal_mulai' : 'tanggal_konseling';
            
            switch($periode) {
                case 'hari_ini':
                    $query->whereDate($dateField, today());
                    break;
                case 'minggu_ini':
                    $query->whereBetween($dateField, [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'bulan_ini':
                    $query->whereMonth($dateField, now()->month)
                          ->whereYear($dateField, now()->year);
                    break;
                case 'semester_ini':
                    $query->whereBetween($dateField, [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
        }
        
        // Filter berdasarkan status
        if ($status) {
            $query->where('status', $status);
        }
        
        // Filter berdasarkan kelas
        if ($kelas_id) {
            $query->whereHas('siswa', function($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }
        
        $data = $query->orderBy($type === 'follow_up' ? 'tanggal_mulai' : ($type === 'siswa_bermasalah' ? 'nama_siswa' : 'tanggal_konseling'), 'desc')->get();
        
        return view('bk.laporan-pdf.' . $type, compact('data', 'type', 'periode'));
    }
    
    public function viewDataSaya()
    {
        $user = session('user');
        $bk = BimbinganKonseling::with(['siswa.kelas', 'sanksi'])
            ->where('guru_konselor', $user->guru_id ?? $user->user_id)
            ->latest()
            ->get();
        $siswa = \App\Models\Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        
        // Sanksi yang perlu follow-up
        $sanksiFollowup = Sanksi::with(['siswa.kelas', 'pelanggaran.jenisPelanggaran'])
            ->where('assigned_to_bk', true)
            ->where('followup_status', 'pending')
            ->get();
            
        return view('bk.view-data', compact('bk', 'siswa', 'sanksiFollowup'));
    }
    
    public function editDataSaya($id)
    {
        $user = session('user');
        $bk = BimbinganKonseling::with(['siswa.kelas'])
            ->where('guru_konselor', $user->guru_id ?? $user->user_id)
            ->findOrFail($id);
        $siswa = \App\Models\Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        
        return response()->json([
            'bk' => $bk,
            'siswa' => $siswa
        ]);
    }
    
    public function updateDataSaya(Request $request, $id)
    {
        $user = session('user');
        $bk = BimbinganKonseling::where('guru_konselor', $user->guru_id ?? $user->user_id)
            ->findOrFail($id);
            
        $bk->update([
            'siswa_id' => $request->siswa_id,
            'topik' => $request->topik,
            'tindakan_solusi' => $request->tindakan,
            'status' => $request->status
        ]);
        
        return redirect()->route('konselor-bk.data-bk-saya')->with('success', 'Data BK berhasil diperbarui');
    }
    
    public function destroyDataSaya($id)
    {
        $user = session('user');
        $bk = BimbinganKonseling::where('guru_konselor', $user->guru_id ?? $user->user_id)
            ->findOrFail($id);
        $bk->delete();
        
        return redirect()->back()->with('success', 'Data BK berhasil dihapus');
    }
    
    public function panggilSiswa(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_layanan' => 'required|string',
            'tanggal_konseling' => 'required|date',
            'waktu_konseling' => 'required',
            'topik' => 'required|string',
            'keluhan_masalah' => 'nullable|string'
        ]);
        
        $user = session('user');
        
        // Buat data bimbingan konseling baru
        BimbinganKonseling::create([
            'siswa_id' => $request->siswa_id,
            'guru_konselor' => $user->guru_id ?? $user->user_id,
            'tahun_ajaran_id' => 1, // Default tahun ajaran aktif
            'jenis_layanan' => $request->jenis_layanan,
            'topik' => $request->topik,
            'keluhan_masalah' => $request->keluhan_masalah,
            'tanggal_konseling' => $request->tanggal_konseling,
            'status' => 'terdaftar'
        ]);
        
        // Buat notifikasi untuk siswa
        $siswa = Siswa::with(['orangTua.user', 'user'])->find($request->siswa_id);
        
        // Debug: Log siswa data
        \Log::info('Siswa data:', ['siswa' => $siswa ? $siswa->toArray() : null]);
        
        if ($siswa && $siswa->user) {
            Notification::create([
                'user_id' => $siswa->user->user_id,
                'title' => 'Panggilan Konseling BK',
                'message' => "Anda dipanggil untuk konseling BK pada tanggal {$request->tanggal_konseling} pukul {$request->waktu_konseling}. Topik: {$request->topik}",
                'type' => 'bk_call',
                'is_read' => false
            ]);
            \Log::info('Notifikasi siswa berhasil dibuat');
        }
        
        // Buat notifikasi untuk orang tua
        if ($siswa && $siswa->orangTua) {
            \Log::info('Jumlah orang tua:', ['count' => $siswa->orangTua->count()]);
            foreach ($siswa->orangTua as $ortu) {
                \Log::info('Orang tua data:', ['ortu' => $ortu->toArray()]);
                if ($ortu->user) {
                    Notification::create([
                        'user_id' => $ortu->user->user_id,
                        'title' => 'Panggilan Konseling BK - ' . $siswa->nama_siswa,
                        'message' => "Anak Anda ({$siswa->nama_siswa}) dipanggil untuk konseling BK pada tanggal {$request->tanggal_konseling} pukul {$request->waktu_konseling}. Topik: {$request->topik}",
                        'type' => 'bk_call_parent',
                        'is_read' => false
                    ]);
                    \Log::info('Notifikasi orang tua berhasil dibuat untuk user_id:', ['user_id' => $ortu->user->user_id]);
                } else {
                    \Log::warning('Orang tua tidak memiliki user account:', ['ortu_id' => $ortu->ortu_id]);
                }
            }
        } else {
            \Log::warning('Siswa tidak memiliki orang tua atau siswa tidak ditemukan');
        }
        
        // Debug: Cek data orang tua dan user
        $debugInfo = [];
        if ($siswa) {
            $debugInfo['siswa_nama'] = $siswa->nama_siswa;
            $debugInfo['orangtua_count'] = $siswa->orangTua->count();
            foreach ($siswa->orangTua as $ortu) {
                $debugInfo['orangtua'][] = [
                    'ortu_id' => $ortu->ortu_id,
                    'nama' => $ortu->nama_orangtua,
                    'has_user' => $ortu->user ? true : false,
                    'user_id' => $ortu->user ? $ortu->user->user_id : null
                ];
            }
        }
        
        return redirect()->back()->with('success', 'Siswa berhasil dipanggil untuk konseling. Debug: ' . json_encode($debugInfo));
    }
    
    public function inputBK(Request $request)
    {
        $siswa = \App\Models\Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $bk = BimbinganKonseling::with(['siswa.kelas', 'guruKonselor'])->latest()->get();
        
        // Data untuk auto-fill jika dari follow-up sanksi
        $followupData = null;
        if ($request->has('followup') && $request->siswa_id) {
            $selectedSiswa = \App\Models\Siswa::with(['kelas', 'pelanggaran.sanksi'])
                ->find($request->siswa_id);
            
            if ($selectedSiswa) {
                $followupData = [
                    'siswa_id' => $selectedSiswa->siswa_id,
                    'siswa_nama' => $selectedSiswa->nama_siswa,
                    'kelas_nama' => $selectedSiswa->kelas->nama_kelas ?? 'N/A',
                    'sanksi_id' => $request->sanksi_id,
                    'notification_id' => $request->notification_id,
                    'topik_default' => 'Follow-up Sanksi Pelanggaran'
                ];
            }
        }
        
        return view('admin.input-data.bk', compact('siswa', 'bk', 'followupData'));
    }
    
    public function followupSanksi(Request $request)
    {
        $request->validate([
            'sanksi_id' => 'required|exists:sanksi,sanksi_id',
            'topik' => 'required|string',
            'notification_id' => 'nullable|exists:notifications,id'
        ]);
        
        $user = session('user');
        $sanksi = Sanksi::with(['pelanggaran.siswa'])->findOrFail($request->sanksi_id);
        
        // Buat data bimbingan konseling untuk follow-up sanksi
        BimbinganKonseling::create([
            'siswa_id' => $sanksi->pelanggaran->siswa_id,
            'guru_konselor' => $user->guru_id ?? $user->user_id,
            'tahun_ajaran_id' => 1,
            'jenis_layanan' => 'Individu',
            'topik' => 'Follow-up Sanksi: ' . $request->topik,
            'keluhan_masalah' => 'Follow-up sanksi: ' . ($sanksi->jenis_sanksi ?? 'N/A'),
            'tanggal_konseling' => now()->format('Y-m-d'),
            'status' => 'terdaftar'
        ]);
        
        // Mark notification as read jika ada
        if ($request->notification_id) {
            Notification::where('id', $request->notification_id)
                ->where('user_id', $user->user_id)
                ->update(['is_read' => true]);
        }
        
        return redirect()->route('konselor-bk.notifikasi')->with('success', 'Follow-up sanksi berhasil diproses dan data konseling telah dibuat');
    }
}