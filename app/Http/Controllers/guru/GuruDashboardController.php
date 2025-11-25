<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use Response;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = session('user');
        
        // Cari data guru dari database
        $guru = null;
        $isWaliKelas = false;
        
        try {
            // Ambil data guru berdasarkan guru_id dari user
            if (isset($user->guru_id) && $user->guru_id) {
                $guru = \App\Models\Guru::find($user->guru_id);
            } else {
                $guru = null;
            }
            
            if ($guru && isset($guru->guru_id) && $guru->guru_id > 0) {
                // Cek apakah guru ini adalah wali kelas
                $kelasAmpu = \DB::table('kelas')
                    ->where('wali_kelas_id', $guru->guru_id)
                    ->count();
                $isWaliKelas = $kelasAmpu > 0;
                
                // Hitung statistik guru
                $totalPelanggaranInput = Pelanggaran::where('guru_pencatat', $guru->guru_id)->count();
                $totalPrestasiInput = Prestasi::where('guru_pencatat', $guru->guru_id)->count();
                $pelanggaranBulanIni = Pelanggaran::where('guru_pencatat', $guru->guru_id)
                    ->whereMonth('tanggal', date('m'))
                    ->whereYear('tanggal', date('Y'))
                    ->count();
                    
                // Ambil data pelanggaran terbaru untuk recent activities
                $recentPelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
                    ->where('guru_pencatat', $guru->guru_id)
                    ->latest()
                    ->take(10)
                    ->get();
            } else {
                // Fallback jika guru tidak ditemukan - untuk guru biasa tanpa guru_id
                $guru = (object) [
                    'guru_id' => 0,
                    'nama_guru' => $user->nama ?? $user->username ?? 'Guru Biasa',
                    'bidang_studi' => 'Mata Pelajaran',
                    'nip' => $user->username ?? '000000'
                ];
                $isWaliKelas = false; // Pastikan guru biasa tidak punya akses wali kelas
                $totalPelanggaranInput = 0;
                $totalPrestasiInput = 0;
                $pelanggaranBulanIni = 0;
                $recentPelanggaran = collect();
            }
        } catch (\Exception $e) {
            // Jika ada error, set default values
            $guru = (object) [
                'guru_id' => 0,
                'nama_guru' => $user->nama ?? $user->username ?? 'Guru',
                'bidang_studi' => 'Mata Pelajaran',
                'nip' => $user->username ?? '000000'
            ];
            $isWaliKelas = false;
            $totalPelanggaranInput = 0;
            $totalPrestasiInput = 0;
            $pelanggaranBulanIni = 0;
            $recentPelanggaran = collect();
        }
        
        // Default values untuk data lainnya
        $kelasAmpu = collect();
        $totalSiswaAmpu = 0;
        $pelanggaranKelas = 0;
        $prestasiKelas = 0;
        $siswaBerisiko = 0;
        
        return view('guru.dashboard', compact(
            'guru', 'isWaliKelas', 'kelasAmpu', 'totalPelanggaranInput',
            'totalPrestasiInput', 'totalSiswaAmpu', 'pelanggaranKelas',
            'prestasiKelas', 'siswaBerisiko', 'pelanggaranBulanIni', 'recentPelanggaran'
        ));
    }
    
    public function laporanTerbatas()
    {
        $user = session('user');
        
        // Data terbatas hanya untuk guru yang login
        try {
            // Gunakan guru_id dari user yang login
            $guru = $user->guru;
            $guruId = $user->guru_id ?? 0;
            
            $totalPelanggaran = Pelanggaran::where('guru_pencatat', $guruId)->count();
            $totalPrestasi = Prestasi::where('guru_pencatat', $guruId)->count();
            $bulanIni = Pelanggaran::where('guru_pencatat', $guruId)
                ->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'))
                ->count();
        } catch (\Exception $e) {
            $totalPelanggaran = 0;
            $totalPrestasi = 0;
            $bulanIni = 0;
        }
        
        return view('guru.laporan-terbatas', compact(
            'totalPelanggaran', 'totalPrestasi', 'bulanIni'
        ));
    }
    
    public function exportLaporan(Request $request)
    {
        $user = session('user');
        
        // Validasi input
        $request->validate([
            'type' => 'required|in:pelanggaran,ringkasan,progress',
            'periode' => 'required|in:hari_ini,minggu_ini,bulan_ini,semester_ini',
            'format' => 'required|in:pdf'
        ]);
        
        try {
            // Validasi user memiliki guru_id
            if (!$user->guru_id) {
                return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan data guru. Silakan hubungi admin.');
            }
            
            // Gunakan guru_id dari user yang login
            $guru = $user->guru;
            $guruId = $user->guru_id;
            
            // Konversi periode ke tanggal
            $dates = $this->getPeriodeDates($request->periode);
            
            // Export hanya data yang diinput oleh guru ini (LIMITED ACCESS)
            $data = $this->getGuruData($request->type, $dates['start'], $dates['end'], $guruId);
            
            // Return view PDF langsung
            return $this->exportToPdf($data, $request->type, $guru);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export laporan: ' . $e->getMessage());
        }
    }
    
    private function getGuruData($jenis, $tanggalMulai, $tanggalSelesai, $guruId)
    {
        switch ($jenis) {
            case 'pelanggaran':
                return Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
                    ->where('guru_pencatat', $guruId)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                    
            case 'progress':
                return \App\Models\Siswa::with(['kelas', 'pelanggaran'])
                    ->whereHas('pelanggaran', function($q) use ($guruId) {
                        $q->where('guru_pencatat', $guruId);
                    })
                    ->get();
                    
            case 'ringkasan':
                $pelanggaran = Pelanggaran::where('guru_pencatat', $guruId)->count();
                $prestasi = Prestasi::where('guru_pencatat', $guruId)->count();
                return [
                    'pelanggaran' => $pelanggaran,
                    'prestasi' => $prestasi,
                    'periode' => 'Semua Data'
                ];
                
            default:
                return collect();
        }
    }
    
    private function exportToExcel($data, $jenis, $namaGuru)
    {
        $filename = 'laporan_guru_' . $jenis . '_' . date('Y-m-d_H-i-s') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = $this->generateHtmlReport($data, $jenis, $namaGuru);
        return response($html, 200, $headers);
    }
    
    private function exportToPdf($data, $jenis, $guru = null)
    {
        $periode = 'Semua Data';
        return view('guru.laporan-pdf.' . $jenis, compact('data', 'guru', 'periode'));
    }
    
    private function getPeriodeDates($periode)
    {
        $today = now();
        
        switch ($periode) {
            case 'hari_ini':
                return [
                    'start' => $today->format('Y-m-d'),
                    'end' => $today->format('Y-m-d')
                ];
            case 'minggu_ini':
                return [
                    'start' => $today->startOfWeek()->format('Y-m-d'),
                    'end' => $today->endOfWeek()->format('Y-m-d')
                ];
            case 'bulan_ini':
                return [
                    'start' => $today->startOfMonth()->format('Y-m-d'),
                    'end' => $today->endOfMonth()->format('Y-m-d')
                ];
            case 'semester_ini':
                $month = $today->month;
                if ($month >= 7) {
                    // Semester 1 (Juli - Desember)
                    return [
                        'start' => $today->year . '-07-01',
                        'end' => $today->year . '-12-31'
                    ];
                } else {
                    // Semester 2 (Januari - Juni)
                    return [
                        'start' => $today->year . '-01-01',
                        'end' => $today->year . '-06-30'
                    ];
                }
            default:
                return [
                    'start' => $today->format('Y-m-d'),
                    'end' => $today->format('Y-m-d')
                ];
        }
    }
    
    private function generateHtmlReport($data, $jenis, $namaGuru)
    {
        $html = '<html><head><title>Laporan Guru - ' . ucfirst($jenis) . '</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;margin:20px;}table{border-collapse:collapse;width:100%;margin-top:20px;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}.header{text-align:center;margin-bottom:20px;}.warning{background-color:#fff3cd;border:1px solid #ffeaa7;padding:10px;margin:10px 0;border-radius:5px;}</style>';
        $html .= '</head><body>';
        $html .= '<div class="header">';
        $html .= '<h2>LAPORAN GURU TERBATAS - SIPELA</h2>';
        $html .= '<h3>SMK Bakti Nusantara 666</h3>';
        $html .= '</div>';
        $html .= '<div class="warning">⚠️ <strong>LIMITED ACCESS:</strong> Laporan ini hanya menampilkan data yang diinput oleh ' . $namaGuru . '</div>';
        $html .= '<p><strong>Guru:</strong> ' . $namaGuru . '</p>';
        $html .= '<p><strong>Tanggal Export:</strong> ' . date('d/m/Y H:i:s') . '</p>';
        $html .= '<p><strong>Jenis Laporan:</strong> ' . ucfirst($jenis) . '</p>';
        
        switch ($jenis) {
            case 'pelanggaran':
                $html .= '<table><tr><th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Jenis Pelanggaran</th><th>Tanggal</th><th>Poin</th><th>Status</th></tr>';
                foreach ($data as $index => $item) {
                    $html .= '<tr>';
                    $html .= '<td>' . ($index + 1) . '</td>';
                    $html .= '<td>' . ($item->siswa->nama_siswa ?? '-') . '</td>';
                    $html .= '<td>' . ($item->siswa->kelas->nama_kelas ?? '-') . '</td>';
                    $html .= '<td>' . ($item->jenisPelanggaran->nama_pelanggaran ?? '-') . '</td>';
                    $html .= '<td>' . date('d/m/Y', strtotime($item->tanggal)) . '</td>';
                    $html .= '<td>' . ($item->poin ?? '-') . '</td>';
                    $html .= '<td>' . ($item->status_verifikasi ?? 'Pending') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
                $html .= '<p><strong>Total Data:</strong> ' . count($data) . ' pelanggaran</p>';
                break;
                
            case 'prestasi':
                $html .= '<table><tr><th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Jenis Prestasi</th><th>Tanggal</th><th>Tingkat</th></tr>';
                foreach ($data as $index => $item) {
                    $html .= '<tr>';
                    $html .= '<td>' . ($index + 1) . '</td>';
                    $html .= '<td>' . ($item->siswa->nama_siswa ?? '-') . '</td>';
                    $html .= '<td>' . ($item->siswa->kelas->nama_kelas ?? '-') . '</td>';
                    $html .= '<td>' . ($item->jenisPrestasi->nama_prestasi ?? '-') . '</td>';
                    $html .= '<td>' . date('d/m/Y', strtotime($item->tanggal)) . '</td>';
                    $html .= '<td>' . ($item->tingkat ?? '-') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
                $html .= '<p><strong>Total Data:</strong> ' . count($data) . ' prestasi</p>';
                break;
                
            case 'ringkasan':
                $html .= '<table><tr><th>Jenis Data</th><th>Jumlah</th></tr>';
                $html .= '<tr><td>Pelanggaran</td><td>' . $data['pelanggaran'] . '</td></tr>';
                $html .= '<tr><td>Prestasi</td><td>' . $data['prestasi'] . '</td></tr>';
                $html .= '</table>';
                $html .= '<p><strong>Periode:</strong> ' . $data['periode'] . '</p>';
                break;
        }
        
        $html .= '<div style="margin-top:30px;text-align:right;">';
        $html .= '<p>Mengetahui,<br><br><br><br>' . $namaGuru . '<br>Guru</p>';
        $html .= '</div>';
        $html .= '</body></html>';
        return $html;
    }
}