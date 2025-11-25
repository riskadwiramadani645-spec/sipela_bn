<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use App\Models\BimbinganKonseling;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepalaSekolahController extends Controller
{
    /**
     * Executive Dashboard - Monitoring All
     */
    public function dashboard()
    {
        // Key Metrics
        $totalSiswa = Siswa::count();
        $totalPelanggaran = Pelanggaran::count();
        $totalPrestasi = Prestasi::count();
        $totalSanksi = Sanksi::count();
        
        // Monthly Data
        $pelanggaranBulanIni = Pelanggaran::whereMonth('tanggal', now()->month)
                                        ->whereYear('tanggal', now()->year)
                                        ->count();
        
        $prestasiSemester = Prestasi::whereMonth('tanggal', '>=', now()->month - 6)
                                   ->count();
        
        // Recent Activities
        $recentPelanggaran = Pelanggaran::with(['siswa', 'jenisPelanggaran'])
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)
                                       ->get();
        
        $recentPrestasi = Prestasi::with(['siswa', 'jenisPrestasi'])
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();
        
        // Chart Data - Top 5 Pelanggaran
        $topPelanggaran = DB::table('pelanggaran')
            ->join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.jenis_pelanggaran_id')
            ->select('jenis_pelanggaran.nama_pelanggaran', DB::raw('count(*) as total'))
            ->groupBy('jenis_pelanggaran.jenis_pelanggaran_id', 'jenis_pelanggaran.nama_pelanggaran')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        return view('kepala-sekolah.dashboard', compact(
            'totalSiswa', 'totalPelanggaran', 'totalPrestasi', 'totalSanksi',
            'pelanggaranBulanIni', 'prestasiSemester',
            'recentPelanggaran', 'recentPrestasi', 'topPelanggaran'
        ));
    }
    
    /**
     * Monitoring All - Comprehensive School Overview
     */
    public function monitoring()
    {
        // Comprehensive Statistics
        $stats = [
            'siswa' => [
                'total' => Siswa::count(),
                'aktif' => Siswa::where('status_kesiswaan', 'aktif')->count(),
                'per_kelas' => Kelas::withCount('siswa')->get()
            ],
            'pelanggaran' => [
                'total' => Pelanggaran::count(),
                'bulan_ini' => Pelanggaran::whereMonth('tanggal', now()->month)->count(),
                'belum_sanksi' => Pelanggaran::whereDoesntHave('sanksi')->count(),
                'by_kategori' => DB::table('pelanggaran')
                    ->join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.jenis_pelanggaran_id')
                    ->select('jenis_pelanggaran.kategori', DB::raw('count(*) as total'))
                    ->groupBy('jenis_pelanggaran.kategori')
                    ->get()
            ],
            'prestasi' => [
                'total' => Prestasi::count(),
                'semester_ini' => Prestasi::whereMonth('tanggal', '>=', now()->month - 6)->count(),
                'by_tingkat' => DB::table('prestasi')
                    ->select('prestasi.tingkat', DB::raw('count(*) as total'))
                    ->whereNotNull('prestasi.tingkat')
                    ->groupBy('prestasi.tingkat')
                    ->get()
            ],
            'bk' => [
                'total' => BimbinganKonseling::count(),
                'bulan_ini' => BimbinganKonseling::whereMonth('tanggal_konseling', now()->month)->count()
            ]
        ];
        
        return view('kepala-sekolah.monitoring', compact('stats'));
    }
    
    /**
     * Laporan Interface - Executive Reports
     */
    public function laporan()
    {
        // Available report types
        $reportTypes = [
            'executive_summary' => 'Executive Summary',
            'pelanggaran_detail' => 'Laporan Pelanggaran Detail',
            'prestasi_detail' => 'Laporan Prestasi Detail',
            'sanksi_detail' => 'Laporan Sanksi Detail',
            'progress_siswa' => 'Progress Siswa',
            'rekap_kelas' => 'Rekap Per Kelas',
            'kebijakan_efektivitas' => 'Efektivitas Kebijakan Disiplin'
        ];
        
        // Recent exports
        $recentExports = collect(); // Placeholder for export history
        
        return view('kepala-sekolah.laporan', compact('reportTypes', 'recentExports'));
    }
    
    /**
     * Export Laporan - Generate Executive Reports
     */
    public function exportLaporan(Request $request)
    {
        $type = $request->input('type', 'executive_summary');
        $periode = $request->input('periode', 'bulan_ini');
        $format = $request->input('format', 'excel');
        
        // Generate report data based on type
        $data = $this->generateReportData($type, $periode);
        
        // Generate filename
        $filename = 'Laporan_KepalaSekolah_' . ucfirst($type) . '_' . $periode . '_' . date('Y-m-d_H-i-s');
        
        // Return appropriate format
        if ($format === 'pdf') {
            return $this->exportToPDF($data, $filename);
        } else {
            return $this->exportToExcel($data, $filename);
        }
    }
    
    /**
     * Generate report data based on type and period
     */
    private function generateReportData($type, $periode)
    {
        $dateRange = $this->getDateRange($periode);
        
        switch ($type) {
            case 'executive_summary':
                return [
                    'title' => 'Executive Summary - SIPELA',
                    'periode' => $periode,
                    'summary' => [
                        'total_siswa' => Siswa::count(),
                        'total_pelanggaran' => Pelanggaran::whereBetween('tanggal', $dateRange)->count(),
                        'total_prestasi' => Prestasi::whereBetween('tanggal', $dateRange)->count(),
                        'efektivitas_sanksi' => $this->calculateSanksiEfektivitas($dateRange)
                    ],
                    'data' => Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
                                        ->whereBetween('tanggal', $dateRange)
                                        ->orderBy('tanggal', 'desc')
                                        ->limit(50)
                                        ->get()
                ];
            
            case 'pelanggaran_detail':
                return [
                    'title' => 'Laporan Pelanggaran Detail',
                    'periode' => $periode,
                    'data' => Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
                                        ->whereBetween('tanggal', $dateRange)
                                        ->orderBy('tanggal', 'desc')
                                        ->get()
                ];
            
            case 'prestasi_detail':
                return [
                    'title' => 'Laporan Prestasi Detail',
                    'periode' => $periode,
                    'data' => Prestasi::with(['siswa.kelas', 'jenisPrestasi'])
                                     ->whereBetween('tanggal', $dateRange)
                                     ->orderBy('tanggal', 'desc')
                                     ->get()
                ];
            
            case 'sanksi_detail':
                return [
                    'title' => 'Laporan Sanksi Detail',
                    'periode' => $periode,
                    'data' => Sanksi::with(['pelanggaran.siswa.kelas', 'jenisSanksi'])
                                   ->whereBetween('tanggal_mulai', $dateRange)
                                   ->orderBy('tanggal_mulai', 'desc')
                                   ->get()
                ];
            
            case 'progress_siswa':
                return [
                    'title' => 'Progress Siswa',
                    'periode' => $periode,
                    'data' => Siswa::with(['kelas', 'pelanggaran', 'prestasi'])
                                  ->get()
                ];
            
            case 'rekap_kelas':
                return [
                    'title' => 'Rekap Per Kelas',
                    'periode' => $periode,
                    'data' => \App\Models\Kelas::with(['siswa.pelanggaran', 'siswa.prestasi'])
                                               ->get()
                ];
            
            default:
                return ['title' => 'Report', 'data' => []];
        }
    }
    
    /**
     * Get date range based on period
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
     * Calculate sanksi effectiveness
     */
    private function calculateSanksiEfektivitas($dateRange)
    {
        $totalPelanggaran = Pelanggaran::whereBetween('tanggal', $dateRange)->count();
        $pelanggaranDenganSanksi = Pelanggaran::whereBetween('tanggal', $dateRange)
                                             ->whereHas('sanksi')
                                             ->count();
        
        return $totalPelanggaran > 0 ? round(($pelanggaranDenganSanksi / $totalPelanggaran) * 100, 2) : 0;
    }
    
    /**
     * Export to Excel
     */
    private function exportToExcel($data, $filename)
    {
        // Create CSV content
        $csvContent = $this->generateCSVContent($data);
        
        // Return download response
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    
    /**
     * Export to PDF
     */
    private function exportToPDF($data, $filename)
    {
        $type = request('type', 'executive_summary');
        
        // Use specific template based on report type
        switch ($type) {
            case 'progress_siswa':
                return view('kepala-sekolah.laporan-pdf.progress', compact('data', 'filename'));
            case 'pelanggaran_detail':
                return view('kepala-sekolah.laporan-pdf.pelanggaran-detail', compact('data', 'filename'));
            case 'prestasi_detail':
                return view('kepala-sekolah.laporan-pdf.prestasi-detail', compact('data', 'filename'));
            case 'sanksi_detail':
                return view('kepala-sekolah.laporan-pdf.sanksi', compact('data', 'filename'));
            case 'rekap_kelas':
                return view('kepala-sekolah.laporan-pdf.rekap-kelas', compact('data', 'filename'));
            case 'kebijakan_efektivitas':
                return view('kepala-sekolah.laporan-pdf.kebijakan-efektivitas', compact('data', 'filename'));
            default:
                return view('kepala-sekolah.laporan-pdf.executive-summary', compact('data', 'filename'));
        }
    }
    
    /**
     * Generate CSV content
     */
    private function generateCSVContent($data)
    {
        $csv = "LAPORAN KEPALA SEKOLAH - SIPELA\n";
        $csv .= "Generated: " . now()->format('d/m/Y H:i:s') . "\n\n";
        
        $csv .= "EXECUTIVE SUMMARY\n";
        $csv .= "================\n";
        $csv .= "Total Siswa," . $data['summary']['total_siswa'] . "\n";
        $csv .= "Total Pelanggaran," . $data['summary']['total_pelanggaran'] . "\n";
        $csv .= "Total Prestasi," . $data['summary']['total_prestasi'] . "\n";
        $csv .= "Efektivitas Sanksi," . $data['summary']['efektivitas_sanksi'] . "%\n\n";
        
        if (isset($data['data']) && $data['data']->count() > 0) {
            $csv .= "DETAIL DATA\n";
            $csv .= "===========\n";
            $csv .= "No,Nama Siswa,Kelas,Jenis,Tanggal,Status\n";
            
            $no = 1;
            foreach ($data['data'] as $item) {
                $csv .= $no . ",";
                $csv .= ($item->siswa->nama_siswa ?? 'N/A') . ",";
                $csv .= ($item->siswa->kelas->nama_kelas ?? 'N/A') . ",";
                $csv .= ($item->jenisPelanggaran->nama_pelanggaran ?? $item->jenisPrestasi->nama_prestasi ?? 'N/A') . ",";
                $csv .= $item->tanggal . ",";
                $csv .= $item->status_verifikasi . "\n";
                $no++;
            }
        }
        
        return $csv;
    }
    
    /**
     * Generate PDF content
     */
    private function generatePDFContent($data)
    {
        $html = '<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title>' . $data['title'] . '</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
h2 { color: #34495e; margin-top: 30px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
th { background-color: #f8f9fa; font-weight: bold; }
.header { text-align: center; margin-bottom: 30px; }
.summary-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
</style>
</head><body>';
        
        $html .= '<div class="header">';
        $html .= '<h1>SIPELA - ' . $data['title'] . '</h1>';
        $html .= '<p><strong>SMK Bakti Nusantara 666</strong></p>';
        $html .= '<p>Generated: ' . now()->format('d F Y, H:i:s') . ' WIB</p>';
        $html .= '<p>Periode: ' . ucfirst(str_replace('_', ' ', $data['periode'])) . '</p>';
        $html .= '</div>';
        
        $html .= '<div class="summary-box">';
        $html .= '<h2>Executive Summary</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Indikator</th><th>Jumlah</th><th>Keterangan</th></tr>';
        $html .= '<tr><td>Total Siswa</td><td>' . number_format($data['summary']['total_siswa']) . '</td><td>Siswa aktif di sekolah</td></tr>';
        $html .= '<tr><td>Total Pelanggaran</td><td>' . number_format($data['summary']['total_pelanggaran']) . '</td><td>Pelanggaran dalam periode</td></tr>';
        $html .= '<tr><td>Total Prestasi</td><td>' . number_format($data['summary']['total_prestasi']) . '</td><td>Prestasi dalam periode</td></tr>';
        $html .= '<tr><td>Efektivitas Sanksi</td><td>' . $data['summary']['efektivitas_sanksi'] . '%</td><td>Tingkat keberhasilan sanksi</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Add detailed data if available
        if (isset($data['data']) && $data['data']->count() > 0) {
            $html .= '<h2>Detail Data</h2>';
            $html .= '<table>';
            $html .= '<tr><th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Jenis</th><th>Tanggal</th><th>Status</th></tr>';
            
            $no = 1;
            foreach ($data['data']->take(50) as $item) { // Limit to 50 records for PDF
                $html .= '<tr>';
                $html .= '<td>' . $no . '</td>';
                $html .= '<td>' . ($item->siswa->nama_siswa ?? 'N/A') . '</td>';
                $html .= '<td>' . ($item->siswa->kelas->nama_kelas ?? 'N/A') . '</td>';
                $html .= '<td>' . ($item->jenisPelanggaran->nama_pelanggaran ?? $item->jenisPrestasi->nama_prestasi ?? 'N/A') . '</td>';
                $html .= '<td>' . date('d/m/Y', strtotime($item->tanggal)) . '</td>';
                $html .= '<td>' . ucfirst($item->status_verifikasi) . '</td>';
                $html .= '</tr>';
                $no++;
            }
            $html .= '</table>';
            
            if ($data['data']->count() > 50) {
                $html .= '<p><em>Menampilkan 50 data teratas dari total ' . $data['data']->count() . ' data.</em></p>';
            }
        }
        
        $html .= '<div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">';
        $html .= '<p>Laporan ini dibuat secara otomatis oleh Sistem SIPELA</p>';
        $html .= '<p>SMK Bakti Nusantara 666 - ' . date('Y') . '</p>';
        $html .= '</div>';
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Create PDF from HTML (simplified PDF generation)
     */
    private function createPDF($html)
    {
        // Simple PDF header
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n";
        $pdf .= "2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n";
        $pdf .= "3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n";
        
        // Convert HTML to simple text for PDF
        $text = strip_tags($html);
        $text = html_entity_decode($text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        $pdf .= "4 0 obj\n<<\n/Length " . strlen($text) . "\n>>\nstream\n";
        $pdf .= "BT\n/F1 12 Tf\n50 750 Td\n";
        
        // Split text into lines for PDF
        $lines = explode("\n", wordwrap($text, 80, "\n"));
        $y = 750;
        foreach ($lines as $line) {
            if ($y < 50) break; // Stop if we reach bottom of page
            $pdf .= "(" . addslashes($line) . ") Tj\n";
            $pdf .= "0 -15 Td\n";
            $y -= 15;
        }
        
        $pdf .= "ET\nendstream\nendobj\n";
        $pdf .= "xref\n0 5\n0000000000 65535 f \n0000000010 00000 n \n0000000079 00000 n \n0000000173 00000 n \n0000000301 00000 n \n";
        $pdf .= "trailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n" . strlen($pdf) . "\n%%EOF";
        
        return $pdf;
    }
}