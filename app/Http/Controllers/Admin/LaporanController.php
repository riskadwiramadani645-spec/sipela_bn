<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa' => Siswa::count(),
            'total_pelanggaran' => Pelanggaran::count(),
            'total_prestasi' => Prestasi::count(),
            'total_sanksi' => Sanksi::count(),
            'pelanggaran_bulan_ini' => Pelanggaran::whereMonth('created_at', date('m'))->count(),
            'prestasi_bulan_ini' => Prestasi::whereMonth('created_at', date('m'))->count(),
        ];
        
        $recentPelanggaran = Pelanggaran::with(['siswa', 'jenisPelanggaran'])->latest()->take(5)->get();
        $recentPrestasi = Prestasi::with(['siswa', 'jenisPrestasi'])->latest()->take(5)->get();
        
        return view('admin.laporan-sistem.laporan', compact('stats', 'recentPelanggaran', 'recentPrestasi'));
    }

    public function sistem()
    {
        $dbSize = $this->getDatabaseSize();
        $backupFiles = $this->getBackupFiles();
        
        // Debug info
        $backupPath = storage_path('app/backups');
        $debugInfo = [
            'path_exists' => file_exists($backupPath),
            'path' => $backupPath,
            'files_count' => count($backupFiles)
        ];
        
        return view('admin.laporan-sistem.sistem', compact('dbSize', 'backupFiles', 'debugInfo'));
    }

    public function backup()
    {
        try {
            // Set timezone Indonesia
            date_default_timezone_set('Asia/Jakarta');
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Create backup directory if not exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Get database configuration
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            // Create mysqldump command
            $command = "mysqldump --host={$dbHost} --user={$dbUser} --password={$dbPass} {$dbName} > {$path}";
            
            // Execute backup
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                return redirect()->route('admin.laporan-sistem.sistem')->with('success', 'Backup database berhasil dibuat: ' . $filename);
            } else {
                return redirect()->back()->with('error', 'Gagal membuat backup database');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pelanggaran,prestasi,sanksi,siswa,guru,kelas,rekap',
            'format' => 'required|in:excel,pdf',
            'periode' => 'nullable|in:hari_ini,minggu_ini,bulan_ini,semester_ini',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        try {
            $data = $this->getExportData($request->type, $request->date_from, $request->date_to);
            
            if ($request->format === 'excel') {
                return $this->exportToExcel($data, $request->type);
            } else {
                return $this->exportToPdf($data, $request->type);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');
        
        // Create backup directory if not exists
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
            return [];
        }
        
        $files = scandir($backupPath);
        $backups = [];
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $filePath = $backupPath . DIRECTORY_SEPARATOR . $file;
                $backups[] = [
                    'name' => $file,
                    'size' => filesize($filePath),
                    'date' => date('d/m/Y H:i:s', filemtime($filePath))
                ];
            }
        }
        
        // Sort by date (newest first)
        usort($backups, function($a, $b) {
            return filemtime(storage_path('app/backups/' . $b['name'])) - filemtime(storage_path('app/backups/' . $a['name']));
        });
        
        return $backups;
    }

    public function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan');
        }
        
        return response()->download($filePath);
    }

    public function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan');
        }
        
        if (unlink($filePath)) {
            return redirect()->back()->with('success', 'File backup berhasil dihapus: ' . $filename);
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus file backup');
        }
    }

    private function getExportData($type, $dateFrom = null, $dateTo = null)
    {
        // Handle periode parameter
        $periode = request('periode');
        if ($periode && !$dateFrom && !$dateTo) {
            switch ($periode) {
                case 'hari_ini':
                    $dateFrom = $dateTo = now()->format('Y-m-d');
                    break;
                case 'minggu_ini':
                    $dateFrom = now()->startOfWeek()->format('Y-m-d');
                    $dateTo = now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'bulan_ini':
                    $dateFrom = now()->startOfMonth()->format('Y-m-d');
                    $dateTo = now()->endOfMonth()->format('Y-m-d');
                    break;
                case 'semester_ini':
                    $dateFrom = now()->month >= 7 ? now()->year . '-07-01' : (now()->year - 1) . '-07-01';
                    $dateTo = now()->month >= 7 ? (now()->year + 1) . '-01-31' : now()->year . '-06-30';
                    break;
            }
        }
        
        switch ($type) {
            case 'pelanggaran':
                $query = Pelanggaran::with(['siswa.kelas', 'guruPencatat', 'jenisPelanggaran']);
                if ($dateFrom) $query->whereDate('tanggal', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('tanggal', '<=', $dateTo);
                return $query->get();
                
            case 'prestasi':
                $query = Prestasi::with(['siswa.kelas', 'jenisPrestasi']);
                if ($dateFrom) $query->whereDate('tanggal', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('tanggal', '<=', $dateTo);
                return $query->get();
                
            case 'sanksi':
                $query = Sanksi::with(['pelanggaran.siswa', 'jenisSanksi']);
                if ($dateFrom) $query->whereDate('tanggal_mulai', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('tanggal_mulai', '<=', $dateTo);
                return $query->get();
                
            case 'siswa':
                return Siswa::with(['kelas'])->get();
                
            case 'guru':
                return \App\Models\Guru::all();
                
            case 'kelas':
                return \App\Models\Kelas::with(['siswa'])->get();
                
            case 'rekap':
                return [
                    'pelanggaran' => Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])->get(),
                    'prestasi' => Prestasi::with(['siswa.kelas', 'jenisPrestasi'])->get(),
                    'sanksi' => Sanksi::with(['pelanggaran.siswa'])->get(),
                    'siswa' => Siswa::with(['kelas'])->get()
                ];
                
            default:
                return collect();
        }
    }

    private function exportToExcel($data, $type)
    {
        // HTML format for Excel (same as PDF)
        $filename = $type . '_' . date('Y-m-d_H-i-s') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = $this->generateHtmlReport($data, $type);
        return response($html, 200, $headers);
    }

    private function exportToPdf($data, $type)
    {
        // Gunakan template Blade untuk PDF dengan auto print
        $periode = request('periode', 'Semua Data');
        
        // Siapkan data statistik untuk rekap
        if ($type === 'rekap') {
            $stats = [
                'total_siswa' => count($data['siswa']),
                'total_pelanggaran' => count($data['pelanggaran']),
                'total_prestasi' => count($data['prestasi']),
                'total_sanksi' => count($data['sanksi'])
            ];
            
            $topPelanggaran = collect($data['pelanggaran'])
                ->groupBy('jenis_pelanggaran_id')
                ->map(function($items) {
                    $first = $items->first();
                    return (object) [
                        'nama_pelanggaran' => $first->jenisPelanggaran->nama_pelanggaran ?? 'N/A',
                        'total_kasus' => $items->count(),
                        'poin' => $first->jenisPelanggaran->poin ?? 0
                    ];
                })
                ->sortByDesc('total_kasus')
                ->take(5)
                ->values();
                
            $topPrestasi = collect($data['prestasi'])
                ->groupBy('jenis_prestasi_id')
                ->map(function($items) {
                    $first = $items->first();
                    return (object) [
                        'nama_prestasi' => $first->jenisPrestasi->nama_prestasi ?? 'N/A',
                        'total_prestasi' => $items->count(),
                        'poin' => $first->jenisPrestasi->poin ?? 0
                    ];
                })
                ->sortByDesc('total_prestasi')
                ->take(5)
                ->values();
                
            return view('admin.laporan-pdf.rekap', compact('data', 'periode', 'stats', 'topPelanggaran', 'topPrestasi'));
        }
        
        return view('admin.laporan-pdf.' . $type, compact('data', 'periode'));
    }

    private function generateHtmlReport($data, $type)
    {
        $html = '<html><head><title>Laporan ' . ucfirst($type) . '</title>';
        $html .= '<style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}</style>';
        $html .= '</head><body>';
        $html .= '<h2>Laporan ' . ucfirst($type) . ' - SIPELA</h2>';
        $html .= '<p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>';
        
        switch ($type) {
            case 'pelanggaran':
                $html .= '<table><tr><th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Jenis Pelanggaran</th><th>Tanggal</th><th>Poin</th><th>Status</th></tr>';
                foreach ($data as $index => $item) {
                    $html .= '<tr>';
                    $html .= '<td>' . ($index + 1) . '</td>';
                    $html .= '<td>' . ($item->siswa->nama_siswa ?? '') . '</td>';
                    $html .= '<td>' . ($item->siswa->kelas->nama_kelas ?? '') . '</td>';
                    $html .= '<td>' . ($item->jenisPelanggaran->nama_pelanggaran ?? '') . '</td>';
                    $html .= '<td>' . ($item->tanggal ?? '') . '</td>';
                    $html .= '<td>' . ($item->poin ?? '') . '</td>';
                    $html .= '<td>' . ($item->status_verifikasi ?? '') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
                break;
                
            case 'prestasi':
                $html .= '<table><tr><th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Jenis Prestasi</th><th>Tanggal</th><th>Tingkat</th></tr>';
                foreach ($data as $index => $item) {
                    $html .= '<tr>';
                    $html .= '<td>' . ($index + 1) . '</td>';
                    $html .= '<td>' . ($item->siswa->nama_siswa ?? '') . '</td>';
                    $html .= '<td>' . ($item->siswa->kelas->nama_kelas ?? '') . '</td>';
                    $html .= '<td>' . ($item->jenisPrestasi->nama_prestasi ?? '') . '</td>';
                    $html .= '<td>' . ($item->tanggal ?? '') . '</td>';
                    $html .= '<td>' . ($item->tingkat ?? '') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
                break;
                
            case 'rekap':
                $html .= '<table><tr><th>Jenis Data</th><th>Total</th></tr>';
                $html .= '<tr><td>Pelanggaran</td><td>' . count($data['pelanggaran']) . '</td></tr>';
                $html .= '<tr><td>Prestasi</td><td>' . count($data['prestasi']) . '</td></tr>';
                $html .= '<tr><td>Sanksi</td><td>' . count($data['sanksi']) . '</td></tr>';
                $html .= '<tr><td>Siswa</td><td>' . count($data['siswa']) . '</td></tr>';
                $html .= '</table>';
                break;
                
            default:
                $html .= '<p>Data tidak tersedia untuk jenis laporan ini.</p>';
        }
        
        $html .= '</body></html>';
        return $html;
    }
}