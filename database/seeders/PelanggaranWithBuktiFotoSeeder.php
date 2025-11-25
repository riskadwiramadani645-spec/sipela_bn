<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\TahunAjaran;

class PelanggaranWithBuktiFotoSeeder extends Seeder
{
    public function run()
    {
        // Ambil data yang diperlukan
        $siswa = Siswa::first();
        $jenisPelanggaran = JenisPelanggaran::first();
        $tahunAjaran = TahunAjaran::first();

        if ($siswa && $jenisPelanggaran && $tahunAjaran) {
            // Buat data pelanggaran dengan bukti foto dummy
            Pelanggaran::create([
                'siswa_id' => $siswa->siswa_id,
                'guru_pencatat' => 1,
                'jenis_pelanggaran_id' => $jenisPelanggaran->jenis_pelanggaran_id,
                'tahun_ajaran_id' => $tahunAjaran->tahun_ajaran_id,
                'poin' => $jenisPelanggaran->poin,
                'keterangan' => 'Pelanggaran dengan bukti foto untuk testing',
                'bukti_foto' => 'pelanggaran/sample-bukti.jpg', // Path dummy
                'status_verifikasi' => 'diverifikasi',
                'guru_verifikator' => 1,
                'catatan_verifikasi' => 'Pencatat: Admin',
                'tanggal' => now()->format('Y-m-d')
            ]);
        }
    }
}