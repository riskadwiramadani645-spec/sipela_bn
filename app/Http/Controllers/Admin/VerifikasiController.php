<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index()
    {
        $user = session('user');
        
        $pelanggaranMenunggu = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
                                         ->where('status_verifikasi', 'menunggu')
                                         ->latest()
                                         ->get();
        
        // Admin tidak perlu verifikasi prestasi (langsung disetujui saat input)
        // Hanya kesiswaan yang perlu verifikasi prestasi dari admin
        if ($user->level === 'admin') {
            $prestasiMenunggu = collect(); // Admin tidak ada prestasi yang perlu diverifikasi
        } else {
            $prestasiMenunggu = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])
                                       ->where('status_verifikasi', 'menunggu')
                                       ->latest()
                                       ->get();
        }
        
        return view('admin.verifikasi-monitoring.verifikasi', compact('pelanggaranMenunggu', 'prestasiMenunggu'));
    }

    public function verifikasiPelanggaran(Request $request, $id)
    {
        $user = session('user');
        
        // Hanya admin/kesiswaan yang bisa verifikasi
        if (!in_array($user->level, ['admin', 'kesiswaan'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk verifikasi');
        }
        
        $pelanggaran = Pelanggaran::findOrFail($id);
        $pelanggaran->update([
            'status_verifikasi' => $request->status_verifikasi ?? $request->status,
            'catatan_verifikasi' => $request->catatan_verifikasi ?? $request->catatan,
            'guru_verifikator' => $user->guru_id ?? 1,
            'tanggal_verifikasi' => now()
        ]);
        
        $message = $request->status == 'diverifikasi' 
            ? 'Pelanggaran berhasil diverifikasi dan dapat dibuat sanksi' 
            : 'Pelanggaran ditolak';
            
        return redirect()->back()->with('success', $message);
    }

    public function verifikasiPrestasi(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->update([
            'status_verifikasi' => $request->status,
            'catatan_verifikasi' => $request->catatan
        ]);
        
        return redirect()->back()->with('success', 'Status verifikasi berhasil diperbarui');
    }
}