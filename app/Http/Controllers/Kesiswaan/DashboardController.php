<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Sanksi;
use App\Models\PelaksanaanSanksi;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Set timezone Indonesia
        Carbon::setLocale('id');
        $currentTime = Carbon::now('Asia/Jakarta');
        
        // Data statistik utama
        $totalSiswa = Siswa::count();
        $totalPelanggaran = Pelanggaran::count();
        $totalPrestasi = Prestasi::count();
        
        // Data verifikasi - role kesiswaan sebagai verifikator
        $pelanggaranMenunggu = Pelanggaran::where('status_verifikasi', 'menunggu')->count();
        $prestasiMenunggu = Prestasi::where('status_verifikasi', 'menunggu')->count();
        
        // Data sanksi dan monitoring - menggunakan data yang tersedia
        $sanksiAktif = PelaksanaanSanksi::whereIn('status', ['dikerjakan', 'terjadwal'])->count();
        $sanksiTerlambat = PelaksanaanSanksi::where('status', 'terlambat')->count();
        $sanksiDeadline = PelaksanaanSanksi::where('status', 'terjadwal')->count();
        
        // Notifikasi follow-up sanksi untuk BK
        $user = session('user');
        $followupNotifications = 0;
        $sanksiFollowup = 0;
        
        if ($user && $user->id) {
            $followupNotifications = Notification::where('user_id', $user->id)
                ->where('type', 'sanksi_followup')
                ->where('is_read', false)
                ->count();
                
            $sanksiFollowup = Sanksi::where('assigned_to_bk', true)
                ->where('followup_status', 'pending')
                ->count();
        }
            
        // Kasus prioritas - pelanggaran menunggu verifikasi
        $kasusPrioritas = $pelanggaranMenunggu;
        $perluEskalasi = $sanksiTerlambat;
        
        // Recent data untuk monitoring (maksimal 5 item)
        $recentPelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentSanksi = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Recent prestasi (maksimal 5 item)
        $recentPrestasi = \App\Models\Prestasi::with(['siswa.kelas', 'jenisPrestasi'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Progress sanksi aktif - data sederhana
        $sanksiProgress = collect();
        
        // Statistik bulanan - data sederhana
        $monthlyStats = [];
        
        // Efektivitas sanksi
        $sanksiTuntas = PelaksanaanSanksi::where('status', 'tuntas')->count();
        $efektivitasSanksi = 85; // Default value

        return view('kesiswaan.dashboard', compact(
            'currentTime',
            'totalSiswa', 'totalPelanggaran', 'totalPrestasi', 
            'pelanggaranMenunggu', 'prestasiMenunggu',
            'sanksiAktif', 'sanksiTerlambat', 'sanksiDeadline',
            'kasusPrioritas', 'perluEskalasi',
            'recentPelanggaran', 'recentSanksi', 'recentPrestasi', 'sanksiProgress',
            'monthlyStats', 'efektivitasSanksi', 'sanksiTuntas',
            'followupNotifications', 'sanksiFollowup'
        ));
    }
}