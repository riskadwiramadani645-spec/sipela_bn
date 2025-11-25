<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasData = [
            // Kelas X
            ['nama_kelas' => 'X PPLG 1', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'X PPLG 2', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'X AKT', 'jurusan' => 'AKUTANSI', 'kapasitas' => 36],
            ['nama_kelas' => 'X DKV', 'jurusan' => 'DESAIN KOMUNIKASI VISUAL', 'kapasitas' => 36],
            ['nama_kelas' => 'X ANM', 'jurusan' => 'ANIMASI', 'kapasitas' => 36],
            ['nama_kelas' => 'X PMS', 'jurusan' => 'PEMASARAN', 'kapasitas' => 36],
            
            // Kelas XI
            ['nama_kelas' => 'XI PPLG 1', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'XI PPLG 2', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'XI AKT', 'jurusan' => 'AKUTANSI', 'kapasitas' => 36],
            ['nama_kelas' => 'XI DKV', 'jurusan' => 'DESAIN KOMUNIKASI VISUAL', 'kapasitas' => 36],
            ['nama_kelas' => 'XI ANM', 'jurusan' => 'ANIMASI', 'kapasitas' => 36],
            ['nama_kelas' => 'XI PMS', 'jurusan' => 'PEMASARAN', 'kapasitas' => 36],
            
            // Kelas XII
            ['nama_kelas' => 'XII PPLG 1', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'XII PPLG 2', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'XII PPLG 3', 'jurusan' => 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'kapasitas' => 36],
            ['nama_kelas' => 'XII AKT 1', 'jurusan' => 'AKUTANSI', 'kapasitas' => 36],
            ['nama_kelas' => 'XII AKT 2', 'jurusan' => 'AKUTANSI', 'kapasitas' => 36],
            ['nama_kelas' => 'XII DKV', 'jurusan' => 'DESAIN KOMUNIKASI VISUAL', 'kapasitas' => 36],
            ['nama_kelas' => 'XII ANM', 'jurusan' => 'ANIMASI', 'kapasitas' => 36],
            ['nama_kelas' => 'XII PMS', 'jurusan' => 'PEMASARAN', 'kapasitas' => 36],
        ];

        foreach ($kelasData as $kelas) {
            \DB::table('kelas')->insert([
                'nama_kelas' => $kelas['nama_kelas'],
                'jurusan' => $kelas['jurusan'],
                'kapasitas' => $kelas['kapasitas'],
                'created_at' => now()
            ]);
        }
    }
}
