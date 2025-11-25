<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use App\Models\BimbinganKonseling;
use Illuminate\Http\Request;

class ViewDataController extends Controller
{
    public function anak()
    {
        $siswa = Siswa::with(['kelas', 'orangTua'])
            ->withCount(['pelanggaran', 'prestasi', 'sanksi', 'bimbinganKonseling'])
            ->latest()
            ->get();
            
        return view('admin.view-data.anak', compact('siswa'));
    }

    public function pelanggaran()
    {
        $pelanggaran = Pelanggaran::with([
            'siswa.kelas', 
            'guru', 
            'jenisPelanggaran',
            'sanksi.jenisSanksi'
        ])->latest()->get();
        
        return view('admin.view-data.pelanggaran', compact('pelanggaran'));
    }

    public function prestasi()
    {
        $prestasi = Prestasi::with([
            'siswa.kelas', 
            'jenisPrestasi'
        ])->latest()->get();
        
        return view('admin.view-data.prestasi', compact('prestasi'));
    }
}