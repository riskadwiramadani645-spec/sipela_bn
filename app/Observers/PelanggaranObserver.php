<?php

namespace App\Observers;

use App\Models\Pelanggaran;
use App\Models\Sanksi;
use App\Models\JenisSanksi;
use App\Models\User;

class PelanggaranObserver
{
    public function updated(Pelanggaran $pelanggaran)
    {
        // DISABLED: Auto-generate sanksi - Sekarang manual create sanksi
        // Kesiswaan harus manual buat sanksi setelah verifikasi
        
        // Log untuk tracking verifikasi
        if ($pelanggaran->isDirty('status_verifikasi') && $pelanggaran->status_verifikasi === 'diverifikasi') {
            \Log::info('Pelanggaran diverifikasi - Manual sanksi required', [
                'pelanggaran_id' => $pelanggaran->pelanggaran_id,
                'siswa' => $pelanggaran->siswa->nama_siswa ?? 'N/A'
            ]);
        }
    }

    public function created(Pelanggaran $pelanggaran)
    {
        // DISABLED: Auto-generate sanksi untuk admin/kesiswaan
        // Sekarang semua harus manual create sanksi
        
        \Log::info('Pelanggaran created - Manual verification & sanksi required', [
            'pelanggaran_id' => $pelanggaran->pelanggaran_id,
            'status' => $pelanggaran->status_verifikasi
        ]);
    }

    private function generateSanksi(Pelanggaran $pelanggaran)
    {
        // Cek apakah sanksi sudah ada
        if ($pelanggaran->sanksi) {
            return;
        }

        // Tentukan jenis sanksi berdasarkan poin
        $jenisSanksi = $this->getJenisSanksiByPoin($pelanggaran->poin);
        
        if ($jenisSanksi) {
            Sanksi::create([
                'pelanggaran_id' => $pelanggaran->pelanggaran_id,
                'jenis_sanksi_id' => $jenisSanksi->jenis_sanksi_id,
                'deskripsi_sanksi' => $this->generateDeskripsiSanksi($pelanggaran, $jenisSanksi),
                'status' => 'terdaftar',
                'guru_penanggungjawab' => $pelanggaran->guru_verifikator ?? $pelanggaran->guru_pencatat
            ]);
        }
    }

    private function getJenisSanksiByPoin($poin)
    {
        // Logic untuk menentukan jenis sanksi berdasarkan poin
        if ($poin >= 100) {
            return JenisSanksi::where('kategori', 'berat')->first();
        } elseif ($poin >= 50) {
            return JenisSanksi::where('kategori', 'sedang')->first();
        } else {
            return JenisSanksi::where('kategori', 'ringan')->first();
        }
    }

    private function generateDeskripsiSanksi(Pelanggaran $pelanggaran, JenisSanksi $jenisSanksi)
    {
        return "Sanksi {$jenisSanksi->nama_sanksi} untuk pelanggaran {$pelanggaran->jenisPelanggaran->nama_pelanggaran} dengan poin {$pelanggaran->poin}";
    }
}