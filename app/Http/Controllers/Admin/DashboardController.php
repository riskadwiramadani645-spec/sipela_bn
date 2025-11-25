<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use App\Models\BimbinganKonseling;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $data = [
                'totalUsers' => User::count(),
                'totalSiswa' => Siswa::count() ?? 0,
                'totalGuru' => Guru::count() ?? 0,
                'totalKelas' => Kelas::count() ?? 0,
                'totalPelanggaran' => Pelanggaran::count() ?? 0,
                'totalPrestasi' => Prestasi::count() ?? 0,
                'sanksiAktif' => Sanksi::where('status', 'berjalan')->count() ?? 0,
                'totalBK' => BimbinganKonseling::count() ?? 0
            ];
        } catch (\Exception $e) {
            $data = [
                'totalUsers' => User::count(),
                'totalSiswa' => 0,
                'totalGuru' => 0,
                'totalKelas' => 0,
                'totalPelanggaran' => 0,
                'totalPrestasi' => 0,
                'sanksiAktif' => 0,
                'totalBK' => 0
            ];
        }

        return view('admin.dashboard', $data);
    }
}