<?php

namespace App\Http\Controllers\Walikelas;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    public function index()
    {
        $user = session('user');
        
        // Tab 1: Pelanggaran yang saya input (sebagai guru)
        $pelanggaranSayaInput = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran'])
            ->where('guru_pencatat', $user->guru_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Tab 2: Pelanggaran siswa di kelas saya (dari guru manapun)
        $pelanggaranKelas = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran', 'guruPencatat'])
            ->whereHas('siswa', function($query) use ($user) {
                $query->where('kelas_id', $user->kelas_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('walikelas.pelanggaran.index', compact('pelanggaranSayaInput', 'pelanggaranKelas'));
    }
    
    public function show($id)
    {
        $user = session('user');
        
        // Walikelas bisa lihat detail pelanggaran siswa di kelasnya atau yang dia input
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat', 'guruVerifikator', 'tahunAjaran'])
            ->where(function($query) use ($user) {
                $query->whereHas('siswa', function($q) use ($user) {
                    $q->where('kelas_id', $user->kelas_id);
                })->orWhere('guru_pencatat', $user->guru_id);
            })
            ->findOrFail($id);
            
        return view('walikelas.pelanggaran.detail', compact('pelanggaran'));
    }
}