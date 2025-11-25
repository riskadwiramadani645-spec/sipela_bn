<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ViewDataAnakController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas', 'pelanggaran.jenisPelanggaran', 'prestasi.jenisPrestasi', 'bimbinganKonseling']);
        
        // Filter pencarian
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter kelas
        if ($request->kelas) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('nama_kelas', $request->kelas);
            });
        }
        
        $siswa = $query->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $selectedSiswa = $siswa->first();
        
        return view('admin.view-data.anak', compact('siswa', 'kelasList', 'selectedSiswa'));
    }
    
    public function show($id)
    {
        $siswa = Siswa::with([
            'kelas',
            'pelanggaran.jenisPelanggaran',
            'prestasi.jenisPrestasi', 
            'bimbinganKonseling'
        ])->findOrFail($id);
        
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        return view('admin.view-data.anak', compact('siswa', 'kelasList'))->with('selectedSiswa', $siswa);
    }
}