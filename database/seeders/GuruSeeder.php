<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $guru = [
            [
                'nip' => '000000000000000001',
                'nama_guru' => 'Dini Susanti',
                'jenis_kelamin' => 'Perempuan',
                'bidang_studi' => 'Administrasi Kesiswaan',
                'no_telp' => '081234567899',
                'email' => 'kesiswaan@smkbn666.sch.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '196801011990031001',
                'nama_guru' => 'Drs. Ahmad Suryadi, M.Pd',
                'jenis_kelamin' => 'Laki-laki',
                'bidang_studi' => 'Matematika',
                'no_telp' => '081234567890',
                'email' => 'ahmad.suryadi@smkbn666.sch.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '197205151995122001',
                'nama_guru' => 'Siti Nurhaliza, S.Pd',
                'jenis_kelamin' => 'Perempuan',
                'bidang_studi' => 'Bahasa Indonesia',
                'no_telp' => '081234567891',
                'email' => 'siti.nurhaliza@smkbn666.sch.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '198003201998031002',
                'nama_guru' => 'Budi Santoso, S.Kom',
                'jenis_kelamin' => 'Laki-laki',
                'bidang_studi' => 'Teknik Komputer Jaringan',
                'no_telp' => '081234567892',
                'email' => 'budi.santoso@smkbn666.sch.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '198506102010012014',
                'nama_guru' => 'Rina Wulandari, S.Pd',
                'jenis_kelamin' => 'Perempuan',
                'bidang_studi' => 'Bimbingan Konseling',
                'no_telp' => '081234567893',
                'email' => 'rina.wulandari@smkbn666.sch.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '199001152015031003',
                'nama_guru' => 'Agus Prasetyo, S.Pd',
                'jenis_kelamin' => 'Laki-laki',
                'bidang_studi' => 'Pendidikan Jasmani',
                'no_telp' => '081234567894',
                'email' => 'agus.prasetyo@smkbn666.sch.id',
                'status' => 'Aktif'
            ]
        ];

        foreach ($guru as $data) {
            Guru::create($data);
        }
    }
}