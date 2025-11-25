<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;



class WaliKelasController extends Controller
{
    public function dataKelas()
    {
        $user = session('user');
        $guru = $user->guru;
        
        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        // Ambil kelas yang diampu
        $kelas = $guru->kelas()->first();
        
        if (!$kelas) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda bukan wali kelas');
        }
        
        // Ambil siswa di kelas yang diampu dengan count pelanggaran dan prestasi
        $siswaList = Siswa::where('kelas_id', $kelas->kelas_id)
            ->withCount(['pelanggaran', 'prestasi'])
            ->get();
        
        // Hitung statistik
        $totalSiswa = $siswaList->count();
        $siswaBerisiko = $siswaList->where('pelanggaran_count', '>', 3)->count();
        $siswaBerprestasi = $siswaList->where('prestasi_count', '>', 0)->count();
        $tingkatKedisiplinan = $totalSiswa > 0 ? round((($totalSiswa - $siswaBerisiko) / $totalSiswa) * 100) : 100;
        
        return view('guru.data-kelas-walikelas', compact(
            'guru', 'kelas', 'siswaList', 'totalSiswa', 
            'siswaBerisiko', 'siswaBerprestasi', 'tingkatKedisiplinan'
        ));
    }
    
    public function monitoringPelanggaran()
    {
        $user = session('user');
        $guru = $user->guru;
        $kelas = $guru ? $guru->kelas()->first() : null;
        
        if (!$kelas) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda bukan wali kelas');
        }
        
        // Ambil data pelanggaran kelas
        $pelanggaranList = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
            ->whereHas('siswa', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->kelas_id);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
            
        // Ambil jenis pelanggaran untuk filter
        $jenisPelanggaran = \App\Models\JenisPelanggaran::all();
            
        return view('guru.monitoring-kelas.pelanggaran-kelas', compact('pelanggaranList', 'kelas', 'guru', 'jenisPelanggaran'));
    }
    
    public function monitoringSanksi()
    {
        $user = session('user');
        $guru = $user->guru;
        $kelas = $guru ? $guru->kelas()->first() : null;
        
        if (!$kelas) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda bukan wali kelas');
        }
        
        // Ambil data sanksi (pelanggaran yang sudah diverifikasi)
        $sanksiList = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
            ->whereHas('siswa', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->kelas_id);
            })
            ->where('status_verifikasi', 'diverifikasi')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
            
        // Ambil jenis sanksi untuk dropdown (menggunakan jenis pelanggaran)
        $jenisSanksi = \App\Models\JenisPelanggaran::all();
            
        return view('guru.monitoring-kelas.sanksi-kelas', compact('sanksiList', 'kelas', 'guru', 'jenisSanksi'));
    }
    
    public function exportAllAccess()
    {
        // Ambil statistik untuk ditampilkan
        $stats = [
            'total_siswa' => Siswa::count(),
            'total_pelanggaran' => Pelanggaran::count(),
            'total_prestasi' => Prestasi::count(),
            'total_sanksi' => Pelanggaran::where('status_verifikasi', 'diverifikasi')->count()
        ];
        
        return view('guru.export-allaccess', compact('stats'));
    }
    
    public function processExport(Request $request)
    {
        $type = $request->input('type');
        $format = $request->input('format', 'pdf');
        $periode = $request->input('periode', 'bulan_ini');
        
        // Get date range based on periode
        $dateRange = $this->getDateRange($periode);
        
        if ($format === 'excel') {
            $filename = 'laporan_' . $type . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            return response()->streamDownload(function() use ($type, $periode) {
                echo "Type: $type, Periode: $periode, Format: Excel";
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);
        } else {
            // Get data and generate PDF
            return $this->generatePDF($type, $dateRange, $periode);
        }
    }
    
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
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->startOfMonth(), now()->endOfMonth()];
        }
    }
    
    private function generatePDF($type, $dateRange, $periode)
    {
        $user = session('user');
        $guru = $user->guru;
        $kelas = $guru ? $guru->kelas()->first() : null;
        
        // Debug: Log user dan guru info
        \Log::info('User Debug', [
            'user_id' => $user ? $user->user_id : null,
            'guru_exists' => $guru ? true : false,
            'guru_id' => $guru ? $guru->guru_id : null,
            'kelas_exists' => $kelas ? true : false,
            'kelas_id' => $kelas ? $kelas->kelas_id : null
        ]);
        
        // Map type to correct template and get data
        switch ($type) {
            case 'pelanggaran-kelas':
                $exportData = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
                    ->whereHas('siswa', function($q) use ($kelas) {
                        if ($kelas) $q->where('kelas_id', $kelas->kelas_id);
                    })
                    ->whereBetween('tanggal', $dateRange)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $template = 'guru.laporan-wakel-pdf.pelanggaran-kelas';
                break;
                
            case 'prestasi-kelas':
                $exportData = Prestasi::with(['siswa.kelas', 'jenisPrestasi'])
                    ->whereHas('siswa', function($q) use ($kelas) {
                        if ($kelas) $q->where('kelas_id', $kelas->kelas_id);
                    })
                    ->whereBetween('tanggal', $dateRange)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $template = 'guru.laporan-wakel-pdf.prestasi-kelas';
                break;
                
            case 'sanksi-kelas':
                $exportData = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
                    ->whereHas('siswa', function($q) use ($kelas) {
                        if ($kelas) $q->where('kelas_id', $kelas->kelas_id);
                    })
                    ->where('status_verifikasi', 'diverifikasi')
                    ->whereBetween('tanggal', $dateRange)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $template = 'guru.laporan-wakel-pdf.sanksi-kelas';
                break;
                
            case 'data-kelas':
                if (!$kelas) {
                    return response('Anda bukan wali kelas', 403);
                }
                $exportData = Siswa::with(['kelas'])
                    ->withCount(['pelanggaran', 'prestasi'])
                    ->where('kelas_id', $kelas->kelas_id)
                    ->get();
                $template = 'guru.laporan-wakel-pdf.data-kelas';
                break;
                
            case 'rekap-kedisiplinan':
                $siswaList = Siswa::with(['pelanggaran'])
                    ->when($kelas, function($q) use ($kelas) {
                        return $q->where('kelas_id', $kelas->kelas_id);
                    })
                    ->get();
                    
                $totalPelanggaran = Pelanggaran::whereHas('siswa', function($q) use ($kelas) {
                    if ($kelas) $q->where('kelas_id', $kelas->kelas_id);
                })->whereBetween('tanggal', $dateRange)->count();
                
                $siswaBerisiko = $siswaList->filter(function($siswa) {
                    return $siswa->pelanggaran->count() > 3;
                })->count();
                
                $exportData = [
                    'stats' => [
                        'total_siswa' => $siswaList->count(),
                        'total_pelanggaran' => $totalPelanggaran,
                        'siswa_bermasalah' => $siswaBerisiko
                    ],
                    'siswa' => $siswaList
                ];
                $template = 'guru.laporan-wakel-pdf.rekap-kedisiplinan';
                break;
                
            case 'progress':
                $exportData = Siswa::with(['kelas', 'pelanggaran' => function($q) use ($dateRange) {
                    $q->whereBetween('tanggal', $dateRange);
                }, 'prestasi' => function($q) use ($dateRange) {
                    $q->whereBetween('tanggal', $dateRange);
                }])
                ->when($kelas, function($q) use ($kelas) {
                    return $q->where('kelas_id', $kelas->kelas_id);
                })
                ->get();
                $template = 'guru.laporan-wakel-pdf.progress';
                break;
                
            case 'ringkasan':
                $exportData = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
                    ->where('guru_pencatat', $user->user_id)
                    ->whereBetween('tanggal', $dateRange)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $template = 'guru.laporan-wakel-pdf.pelanggaran-kelas';
                break;
                
            case 'pelanggaran_saya':
                $exportData = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
                    ->where('guru_pencatat', $user->user_id)
                    ->whereBetween('tanggal', $dateRange)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $template = 'guru.laporan-wakel-pdf.pelanggaran-kelas';
                break;
                
            default:
                return response('Template tidak ditemukan', 404);
        }
        
        // Debug: Log data yang diambil
        \Log::info('Export Data Debug', [
            'type' => $type,
            'kelas_id' => $kelas ? $kelas->kelas_id : null,
            'kelas_nama' => $kelas ? $kelas->nama_kelas : null,
            'data_count' => is_countable($exportData) ? count($exportData) : 'not countable',
            'guru_id' => $guru ? $guru->guru_id : null
        ]);
        
        // Generate HTML
        try {
            $html = view($template, compact('exportData', 'periode', 'guru', 'kelas'))->render();
            return response($html)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error', [
                'template' => $template,
                'error' => $e->getMessage(),
                'data_keys' => is_array($exportData) ? array_keys($exportData) : 'not array'
            ]);
            return response('Error generating PDF: ' . $e->getMessage(), 500);
        }
    }
}