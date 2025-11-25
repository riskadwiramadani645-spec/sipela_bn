<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pelanggaran;
use App\Models\Sanksi;
use App\Models\PelaksanaanSanksi;
use Carbon\Carbon;
use Response;

class LaporanController extends Controller
{
    public function index()
    {
        // Statistik untuk dashboard
        $stats = [
            'pelanggaran_bulan_ini' => Pelanggaran::whereMonth('tanggal', Carbon::now()->month)
                ->whereYear('tanggal', Carbon::now()->year)
                ->count(),
            'siswa_pembinaan' => Siswa::whereHas('pelanggaran', function($q) {
                $q->where('status_verifikasi', 'diverifikasi')
                  ->whereMonth('tanggal', '>=', Carbon::now()->subMonths(3)->month);
            })->count(),
            'sanksi_tuntas' => PelaksanaanSanksi::where('status', 'tuntas')->count(),
            'sanksi_terlambat' => PelaksanaanSanksi::where('status', 'terlambat')->count(),
        ];

        // Data untuk preview
        $siswaPembinaan = Siswa::whereHas('pelanggaran', function($q) {
            $q->where('status_verifikasi', 'diverifikasi')
              ->whereMonth('tanggal', '>=', Carbon::now()->subMonths(3)->month);
        })->with('kelas')->limit(5)->get();

        $sanksiMonitoring = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa'])
            ->whereIn('status', ['dikerjakan', 'terlambat'])
            ->limit(5)
            ->get();

        // Data kelas
        $kelasList = Kelas::all();

        // Data untuk chart
        $chartLabels = [];
        $chartPelanggaran = [];
        $chartSanksiTuntas = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->format('M Y');
            
            $chartPelanggaran[] = Pelanggaran::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->count();
                
            $chartSanksiTuntas[] = PelaksanaanSanksi::where('status', 'tuntas')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        // Tingkat kedisiplinan per kelas
        $kedisiplinanKelas = Kelas::withCount(['siswa', 'siswa as pelanggaran_count' => function($q) {
            $q->whereHas('pelanggaran', function($query) {
                $query->whereMonth('tanggal', Carbon::now()->month);
            });
        }])->get()->map(function($kelas) {
            $tingkat = $kelas->siswa_count > 0 
                ? round((($kelas->siswa_count - $kelas->pelanggaran_count) / $kelas->siswa_count) * 100)
                : 100;
            $kelas->tingkat_kedisiplinan = $tingkat;
            return $kelas;
        });

        return view('kesiswaan.laporan', compact(
            'stats', 
            'siswaPembinaan', 
            'sanksiMonitoring', 
            'kelasList',
            'chartLabels',
            'chartPelanggaran', 
            'chartSanksiTuntas',
            'kedisiplinanKelas'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->type;
        $format = $request->format;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $kelasId = $request->kelas_id;

        switch ($type) {
            case 'pembinaan':
                return $this->exportPembinaan($format, $dateFrom, $dateTo, $kelasId);
            case 'kedisiplinan':
                return $this->exportKedisiplinan($format, $dateFrom, $dateTo, $kelasId);
            case 'monitoring':
                return $this->exportMonitoring($format, $dateFrom, $dateTo, $kelasId);
            case 'konseling':
                return $this->exportKonseling($format, $dateFrom, $dateTo, $kelasId);
            case 'progress':
                return $this->exportProgress($format, $dateFrom, $dateTo, $kelasId);
            case 'rekap_kelas':
                return $this->exportRekapKelas($format, $dateFrom, $dateTo);
            default:
                return back()->with('error', 'Jenis laporan tidak valid');
        }
    }

    private function exportPembinaan($format, $dateFrom, $dateTo, $kelasId)
    {
        $query = Siswa::whereHas('pelanggaran', function($q) use ($dateFrom, $dateTo) {
            $q->where('status_verifikasi', 'diverifikasi');
            if ($dateFrom) $q->whereDate('tanggal', '>=', $dateFrom);
            if ($dateTo) $q->whereDate('tanggal', '<=', $dateTo);
        })->with(['kelas', 'pelanggaran' => function($q) {
            $q->where('status_verifikasi', 'diverifikasi')
              ->with('jenisPelanggaran');
        }]);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $data = $query->get();

        if ($format == 'excel') {
            return $this->generateCsvPembinaan($data);
        } else {
            return $this->generateHtmlPembinaan($data);
        }
    }

    private function generateCsvPembinaan($data)
    {
        $filename = 'laporan_pembinaan_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Jumlah Pelanggaran', 'Pelanggaran Terakhir', 'Status']);
            
            // Data
            foreach ($data as $index => $siswa) {
                fputcsv($file, [
                    $index + 1,
                    $siswa->nis,
                    $siswa->nama_siswa,
                    $siswa->kelas->nama_kelas ?? '-',
                    $siswa->pelanggaran->count(),
                    $siswa->pelanggaran->first()->jenisPelanggaran->nama_pelanggaran ?? '-',
                    'Dalam Pembinaan'
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportKedisiplinan($format, $dateFrom, $dateTo, $kelasId)
    {
        $query = Kelas::withCount(['siswa', 'siswa as pelanggaran_count' => function($q) use ($dateFrom, $dateTo) {
            $q->whereHas('pelanggaran', function($query) use ($dateFrom, $dateTo) {
                if ($dateFrom) $query->whereDate('tanggal', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('tanggal', '<=', $dateTo);
            });
        }]);

        if ($kelasId) {
            $query->where('id', $kelasId);
        }

        $data = $query->get()->map(function($kelas) {
            $tingkat = $kelas->siswa_count > 0 
                ? round((($kelas->siswa_count - $kelas->pelanggaran_count) / $kelas->siswa_count) * 100)
                : 100;
            $kelas->tingkat_kedisiplinan = $tingkat;
            return $kelas;
        });

        if ($format == 'excel') {
            return $this->generateCsvKedisiplinan($data);
        } else {
            return $this->generateHtmlKedisiplinan($data);
        }
    }

    private function generateCsvKedisiplinan($data)
    {
        $filename = 'laporan_kedisiplinan_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['No', 'Kelas', 'Jumlah Siswa', 'Siswa Bermasalah', 'Siswa Disiplin', 'Tingkat Kedisiplinan (%)']);
            
            // Data
            foreach ($data as $index => $kelas) {
                fputcsv($file, [
                    $index + 1,
                    $kelas->nama_kelas,
                    $kelas->siswa_count,
                    $kelas->pelanggaran_count,
                    $kelas->siswa_count - $kelas->pelanggaran_count,
                    $kelas->tingkat_kedisiplinan . '%'
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportMonitoring($format, $dateFrom, $dateTo, $kelasId)
    {
        $query = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas', 'sanksi.jenisSanksi', 'guruPengawas']);

        if ($dateFrom) {
            $query->whereDate('tanggal_pelaksanaan', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('tanggal_pelaksanaan', '<=', $dateTo);
        }
        if ($kelasId) {
            $query->whereHas('sanksi.pelanggaran.siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $data = $query->get();

        if ($format == 'excel') {
            return $this->generateCsvMonitoring($data);
        } else {
            return $this->generateHtmlMonitoring($data);
        }
    }

    private function generateCsvMonitoring($data)
    {
        $filename = 'laporan_monitoring_sanksi_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['No', 'Siswa', 'Kelas', 'Jenis Sanksi', 'Tanggal Pelaksanaan', 'Status', 'Guru Pengawas', 'Keterangan']);
            
            // Data
            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->sanksi->pelanggaran->siswa->nama_siswa ?? '-',
                    $item->sanksi->pelanggaran->siswa->kelas->nama_kelas ?? '-',
                    $item->sanksi->jenisSanksi->nama_sanksi ?? $item->sanksi->jenis_sanksi_manual ?? '-',
                    date('d/m/Y', strtotime($item->tanggal_pelaksanaan)),
                    ucfirst($item->status),
                    $item->guruPengawas->nama_guru ?? '-',
                    $item->keterangan ?? '-'
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Method untuk PDF
    private function generateHtmlPembinaan($data)
    {
        return view('kesiswaan.laporan-pdf.pembinaan', compact('data'));
    }

    private function generateHtmlKedisiplinan($data)
    {
        return view('kesiswaan.laporan-pdf.kedisiplinan', compact('data'));
    }

    private function generateHtmlMonitoring($data)
    {
        return view('kesiswaan.laporan-pdf.monitoring', compact('data'));
    }

    private function exportKonseling($format, $dateFrom, $dateTo, $kelasId)
    {
        // Dummy data untuk konseling
        $data = collect([
            (object)['siswa' => (object)['nama_siswa' => 'Ahmad Rizki', 'kelas' => (object)['nama_kelas' => 'XII RPL 1']], 'tanggal' => '2024-11-01', 'jenis_konseling' => 'Konseling Akademik', 'status' => 'selesai'],
            (object)['siswa' => (object)['nama_siswa' => 'Siti Nurhaliza', 'kelas' => (object)['nama_kelas' => 'XI TKJ 2']], 'tanggal' => '2024-11-02', 'jenis_konseling' => 'Konseling Perilaku', 'status' => 'proses']
        ]);
        
        return view('kesiswaan.laporan-pdf.konseling', compact('data'));
    }

    private function exportProgress($format, $dateFrom, $dateTo, $kelasId)
    {
        // Dummy data untuk progress
        $data = collect([
            (object)['nama_siswa' => 'Ahmad Rizki', 'kelas' => (object)['nama_kelas' => 'XII RPL 1'], 'progress_perilaku' => 85, 'tingkat_kedisiplinan' => 90, 'catatan_progress' => 'Perkembangan baik'],
            (object)['nama_siswa' => 'Siti Nurhaliza', 'kelas' => (object)['nama_kelas' => 'XI TKJ 2'], 'progress_perilaku' => 70, 'tingkat_kedisiplinan' => 75, 'catatan_progress' => 'Perlu pembinaan lanjutan']
        ]);
        
        return view('kesiswaan.laporan-pdf.progress', compact('data'));
    }

    private function exportRekapKelas($format, $dateFrom, $dateTo)
    {
        $data = Kelas::withCount([
            'siswa',
            'siswa as pelanggaran_count' => function($q) {
                $q->whereHas('pelanggaran');
            },
            'siswa as prestasi_count' => function($q) {
                $q->whereHas('prestasi');
            }
        ])->get()->map(function($kelas) {
            $tingkat = $kelas->siswa_count > 0 
                ? round((($kelas->siswa_count - $kelas->pelanggaran_count) / $kelas->siswa_count) * 100)
                : 100;
            $kelas->tingkat_kedisiplinan = $tingkat;
            return $kelas;
        });
        
        return view('kesiswaan.laporan-pdf.rekap_kelas', compact('data'));
    }
}