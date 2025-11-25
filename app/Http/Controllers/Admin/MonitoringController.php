<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\Kelas;

class MonitoringController extends Controller
{
    public function index()
    {
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])->get();
        $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])->get();
        
        return view('admin.verifikasi-monitoring.monitoring', compact('pelanggaran', 'prestasi'));
    }
}