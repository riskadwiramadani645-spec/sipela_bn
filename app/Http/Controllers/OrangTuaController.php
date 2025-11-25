<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use App\Models\OrangTua;
use App\Models\Notification;

class OrangTuaController extends Controller
{
    public function dashboard()
    {
        $user = session('user');
        
        if (!$user || $user->level !== 'orang_tua') {
            return redirect()->route('login')->with('error', 'Akses ditolak');
        }

        $anak = $user->getDataAnak();
        
        if (!$anak) {
            return view('orang_tua.dashboard', [
                'anak' => null,
                'totalPelanggaran' => 0,
                'totalPrestasi' => 0,
                'sanksiAktif' => 0,
                'pelanggaranTerbaru' => collect(),
                'prestasiTerbaru' => collect()
            ]);
        }
        
        $totalPelanggaran = $anak->pelanggaran()->count();
        $totalPrestasi = $anak->prestasi()->count();
        $sanksiAktif = Sanksi::whereHas('pelanggaran', function($q) use ($anak) {
            $q->where('siswa_id', $anak->siswa_id);
        })->where('status', '!=', 'selesai')->count();
        
        $pelanggaranTerbaru = $anak->pelanggaran()
            ->with(['jenisPelanggaran', 'guruPencatat'])
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();
            
        $prestasiTerbaru = $anak->prestasi()
            ->with(['jenisPrestasi'])
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();
            
        return view('orang_tua.dashboard', compact(
            'anak', 'totalPelanggaran', 'totalPrestasi', 'sanksiAktif',
            'pelanggaranTerbaru', 'prestasiTerbaru'
        ));
    }

    public function viewDataAnak()
    {
        $user = session('user');
        
        if (!$user || $user->level !== 'orang_tua') {
            return redirect()->route('login')->with('error', 'Akses ditolak');
        }

        $anak = $user->getDataAnak();
        
        if (!$anak) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Data anak tidak ditemukan');
        }
        
        $pelanggaran = $anak->pelanggaran()
            ->with(['jenisPelanggaran', 'guruPencatat'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $prestasi = $anak->prestasi()
            ->with(['jenisPrestasi'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $sanksi = Sanksi::whereHas('pelanggaran', function($q) use ($anak) {
            $q->where('siswa_id', $anak->siswa_id);
        })->with(['jenisSanksi', 'pelanggaran.jenisPelanggaran'])
        ->orderBy('created_at', 'desc')
        ->get();
        
        return view('orang_tua.view-data-anak', compact(
            'anak', 'pelanggaran', 'prestasi', 'sanksi'
        ));
    }

    public function viewDataSendiri()
    {
        $user = session('user');
        
        if (!$user || $user->level !== 'orang_tua') {
            return redirect()->route('login')->with('error', 'Akses ditolak');
        }

        $orangTua = $user->orangTua;
        
        return view('orang_tua.view-data-sendiri', compact('orangTua'));
    }

    public function laporan()
    {
        $user = session('user');
        
        if (!$user || $user->level !== 'orang_tua') {
            return redirect()->route('login')->with('error', 'Akses ditolak');
        }

        $anak = $user->getDataAnak();
        
        if (!$anak) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Data anak tidak ditemukan');
        }
        
        return view('orang_tua.laporan', compact('anak'));
    }
    
    public function exportLaporan(Request $request)
    {
        $user = session('user');
        
        if (!$user || $user->level !== 'orang_tua') {
            return redirect()->route('login')->with('error', 'Akses ditolak');
        }
        
        $request->validate([
            'type' => 'required|in:pelanggaran,prestasi,sanksi',
            'periode' => 'required|in:bulan_ini,semester_ini'
        ]);
        
        $anak = $user->getDataAnak();
        
        if (!$anak) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Data anak tidak ditemukan');
        }
        
        $dateRange = $this->getDateRange($request->periode);
        $data = $this->getExportData($request->type, $anak, $dateRange);
        $periode = $this->getPeriodeLabel($request->periode);
        
        return view('orang_tua.laporan-pdf.' . $request->type . '-anak', 
            compact('data', 'anak', 'periode'));
    }
    
    private function getExportData($type, $anak, $dateRange)
    {
        switch ($type) {
            case 'pelanggaran':
                $query = $anak->pelanggaran()->with(['jenisPelanggaran', 'guruPencatat']);
                if ($dateRange) {
                    $query->whereBetween('tanggal', $dateRange);
                }
                return $query->orderBy('tanggal', 'desc')->get();
                
            case 'prestasi':
                $query = $anak->prestasi()->with(['jenisPrestasi']);
                if ($dateRange) {
                    $query->whereBetween('tanggal', $dateRange);
                }
                return $query->orderBy('tanggal', 'desc')->get();
                
            case 'sanksi':
                $query = Sanksi::whereHas('pelanggaran', function($q) use ($anak) {
                    $q->where('siswa_id', $anak->siswa_id);
                })->with(['jenisSanksi', 'pelanggaran.jenisPelanggaran']);
                if ($dateRange) {
                    $query->whereBetween('tanggal_mulai', $dateRange);
                }
                return $query->orderBy('created_at', 'desc')->get();
                
            default:
                return collect();
        }
    }
    
    private function getDateRange($periode)
    {
        switch ($periode) {
            case 'bulan_ini':
                return [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')];
            case 'semester_ini':
                $start = now()->month >= 7 ? now()->year . '-07-01' : (now()->year - 1) . '-07-01';
                $end = now()->month >= 7 ? (now()->year + 1) . '-01-31' : now()->year . '-06-30';
                return [$start, $end];
            default:
                return null;
        }
    }
    
    private function getPeriodeLabel($periode)
    {
        switch ($periode) {
            case 'bulan_ini': return 'Bulan Ini';
            case 'semester_ini': return 'Semester Ini';
            default: return 'Semua Data';
        }
    }
    

}