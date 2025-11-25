<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Dashboard Siswa
     */
    public function dashboard()
    {
        $user = session('user');
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
        }
        
        // Cari siswa berdasarkan username atau NIS
        $siswa = null;
        if (isset($user->siswa_id) && $user->siswa_id) {
            $siswa = Siswa::with(['kelas'])->find($user->siswa_id);
        } else {
            // Cari berdasarkan username/NIS
            $siswa = Siswa::with(['kelas'])
                ->where('nis', $user->username)
                ->first();
            
            if (!$siswa) {
                // Coba cari berdasarkan nama siswa
                $siswa = Siswa::with(['kelas'])
                    ->where('nama_siswa', 'LIKE', '%' . $user->username . '%')
                    ->first();
            }
        }
        
        if (!$siswa) {
            \Log::error('Siswa not found for user', ['user_id' => $user->id, 'username' => $user->username, 'siswa_id' => $user->siswa_id ?? 'null']);
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan. Username: ' . $user->username . '. Hubungi admin untuk mengatur akun Anda.');
        }
        
        // Personal Stats
        $totalPelanggaran = Pelanggaran::where('siswa_id', $siswa->siswa_id)->count();
        $totalPrestasi = Prestasi::where('siswa_id', $siswa->siswa_id)->count();
        
        // Sanksi Aktif
        $sanksiAktif = Sanksi::whereHas('pelanggaran', function($q) use ($siswa) {
            $q->where('siswa_id', $siswa->siswa_id);
        })->whereNotNull('tanggal_mulai')->whereNull('tanggal_selesai')->count();
        
        // Skor Kedisiplinan
        $poinPelanggaran = DB::table('pelanggaran')
            ->join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.jenis_pelanggaran_id')
            ->where('pelanggaran.siswa_id', $siswa->siswa_id)
            ->sum('jenis_pelanggaran.poin');
            
        $poinPrestasi = DB::table('prestasi')
            ->join('jenis_prestasi', 'prestasi.jenis_prestasi_id', '=', 'jenis_prestasi.jenis_prestasi_id')
            ->where('prestasi.siswa_id', $siswa->siswa_id)
            ->sum('jenis_prestasi.poin');
            
        $skorDisiplin = max(0, 100 - ($poinPelanggaran - $poinPrestasi));
        
        // Recent Data
        $recentPelanggaran = Pelanggaran::with(['jenisPelanggaran'])
            ->where('siswa_id', $siswa->siswa_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $recentPrestasi = Prestasi::with(['jenisPrestasi'])
            ->where('siswa_id', $siswa->siswa_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        // Ambil notifikasi untuk siswa (semua untuk dashboard, akan dibatasi di view)
        $notifications = \App\Models\Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('siswa.dashboard', compact(
            'siswa', 'totalPelanggaran', 'totalPrestasi', 'sanksiAktif', 
            'skorDisiplin', 'recentPelanggaran', 'recentPrestasi', 'notifications'
        ));
    }
    
    /**
     * Halaman Riwayat Notifikasi
     */
    public function notifikasi()
    {
        $user = session('user');
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
        }
        
        // Cari siswa
        $siswa = null;
        if (isset($user->siswa_id) && $user->siswa_id) {
            $siswa = Siswa::with(['kelas'])->find($user->siswa_id);
        } else {
            $siswa = Siswa::with(['kelas'])
                ->where('nis', $user->username)
                ->first();
        }
        
        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }
        
        // Ambil semua notifikasi untuk siswa
        $notifications = \App\Models\Notification::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Mark notifikasi sebagai dibaca
        \App\Models\Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return view('siswa.notifikasi', compact('siswa', 'notifications'));
    }
    
    /**
     * View Data Sendiri
     */
    public function viewDataSendiri()
    {
        $user = session('user');
        
        // Cari siswa berdasarkan username atau NIS
        $siswa = null;
        if (isset($user->siswa_id) && $user->siswa_id) {
            $siswa = Siswa::with(['kelas'])->find($user->siswa_id);
        } else {
            $siswa = Siswa::with(['kelas'])
                ->where('nis', $user->username)
                ->orWhere('nama_siswa', 'LIKE', '%' . $user->username . '%')
                ->first();
        }
        
        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Riwayat Pelanggaran
        $pelanggaran = Pelanggaran::with(['jenisPelanggaran', 'guruPencatat'])
            ->where('siswa_id', $siswa->siswa_id)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Riwayat Prestasi
        $prestasi = Prestasi::with(['jenisPrestasi'])
            ->where('siswa_id', $siswa->siswa_id)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Riwayat Sanksi
        $sanksi = Sanksi::with(['pelanggaran.jenisPelanggaran', 'jenisSanksi'])
            ->whereHas('pelanggaran', function($q) use ($siswa) {
                $q->where('siswa_id', $siswa->siswa_id);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->get();
        
        return view('siswa.view-data-sendiri', compact('siswa', 'pelanggaran', 'prestasi', 'sanksi'));
    }
    
    /**
     * Laporan Interface
     */
    public function laporan()
    {
        $reportTypes = [
            'riwayat_pribadi' => 'Riwayat Pribadi',
            'status_sanksi' => 'Status Sanksi',
            'progress_kedisiplinan' => 'Progress Kedisiplinan',
            'sertifikat_prestasi' => 'Sertifikat Prestasi'
        ];
        
        return view('siswa.laporan', compact('reportTypes'));
    }
    
    /**
     * Export Laporan Terbatas
     */
    public function exportLaporan(Request $request)
    {
        $user = session('user');
        $siswa = Siswa::with(['kelas'])->find($user->siswa_id);
        $type = $request->input('type', 'riwayat_pribadi');
        $periode = $request->input('periode', 'bulan_ini');
        
        $data = $this->generateReportData($siswa, $type, $periode);
        $filename = 'Laporan_Siswa_' . ucfirst($type) . '_' . $periode . '_' . date('Y-m-d_H-i-s');
        
        return $this->exportToPDF($data, $filename, $type);
    }
    
    /**
     * Generate report data
     */
    private function generateReportData($siswa, $type, $periode)
    {
        $dateRange = $this->getDateRange($periode);
        
        switch ($type) {
            case 'riwayat_pribadi':
                return [
                    'title' => 'Riwayat Pribadi',
                    'siswa' => $siswa,
                    'periode' => $periode,
                    'pelanggaran' => Pelanggaran::with(['jenisPelanggaran', 'guruPencatat'])
                        ->where('siswa_id', $siswa->siswa_id)
                        ->whereBetween('tanggal', $dateRange)
                        ->orderBy('tanggal', 'desc')
                        ->get(),
                    'prestasi' => Prestasi::with(['jenisPrestasi'])
                        ->where('siswa_id', $siswa->siswa_id)
                        ->whereBetween('tanggal', $dateRange)
                        ->orderBy('tanggal', 'desc')
                        ->get()
                ];
                
            case 'status_sanksi':
                return [
                    'title' => 'Status Sanksi',
                    'siswa' => $siswa,
                    'periode' => $periode,
                    'sanksi' => Sanksi::with(['pelanggaran.jenisPelanggaran', 'jenisSanksi'])
                        ->whereHas('pelanggaran', function($q) use ($siswa) {
                            $q->where('siswa_id', $siswa->siswa_id);
                        })
                        ->whereBetween('tanggal_mulai', $dateRange)
                        ->orderBy('tanggal_mulai', 'desc')
                        ->get()
                ];
                
            default:
                return ['title' => 'Report', 'siswa' => $siswa, 'data' => []];
        }
    }
    
    /**
     * Get date range
     */
    private function getDateRange($periode)
    {
        switch ($periode) {
            case 'hari_ini':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'minggu_ini':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'bulan_ini':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'semester_ini':
                return [now()->subMonths(6), now()];
            default:
                return [now()->startOfMonth(), now()->endOfMonth()];
        }
    }
    
    /**
     * Export to PDF
     */
    private function exportToPDF($data, $filename, $type)
    {
        switch ($type) {
            case 'riwayat_pribadi':
                return view('siswa.laporan-pdf.riwayat-pribadi', compact('data', 'filename'));
            case 'status_sanksi':
                return view('siswa.laporan-pdf.status-sanksi', compact('data', 'filename'));
            default:
                return view('siswa.laporan-pdf.riwayat-pribadi', compact('data', 'filename'));
        }
    }
}