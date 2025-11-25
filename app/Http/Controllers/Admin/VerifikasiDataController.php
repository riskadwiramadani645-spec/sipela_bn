<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiDataController extends Controller
{
    public function index()
    {
        $pelanggaran = DB::table('pelanggaran as p')
            ->join('siswa as s', 'p.siswa_id', '=', 's.siswa_id')
            ->join('kelas as k', 's.kelas_id', '=', 'k.kelas_id')
            ->join('tahun_ajaran as ta', 'p.tahun_ajaran_id', '=', 'ta.tahun_ajaran_id')
            ->select('p.*', 's.nama_siswa', 'k.nama_kelas', 'ta.tahun_ajaran', 'ta.semester')
            ->where('p.status_verifikasi', 'pending')
            ->orderBy('p.tanggal_pelanggaran', 'desc')
            ->get();

        $prestasi = DB::table('prestasi as pr')
            ->join('siswa as s', 'pr.siswa_id', '=', 's.siswa_id')
            ->join('kelas as k', 's.kelas_id', '=', 'k.kelas_id')
            ->join('tahun_ajaran as ta', 'pr.tahun_ajaran_id', '=', 'ta.tahun_ajaran_id')
            ->select('pr.*', 's.nama_siswa', 'k.nama_kelas', 'ta.tahun_ajaran', 'ta.semester')
            ->where('pr.status_verifikasi', 'pending')
            ->orderBy('pr.tanggal_prestasi', 'desc')
            ->get();

        return view('admin.verifikasi-data.index', compact('pelanggaran', 'prestasi'));
    }

    public function verifikasiPelanggaran(Request $request, $id)
    {
        DB::table('pelanggaran')
            ->where('pelanggaran_id', $id)
            ->update([
                'status_verifikasi' => $request->status,
                'catatan_verifikasi' => $request->catatan,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Data pelanggaran berhasil diverifikasi');
    }

    public function verifikasiPrestasi(Request $request, $id)
    {
        DB::table('prestasi')
            ->where('prestasi_id', $id)
            ->update([
                'status_verifikasi' => $request->status,
                'catatan_verifikasi' => $request->catatan,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Data prestasi berhasil diverifikasi');
    }
}