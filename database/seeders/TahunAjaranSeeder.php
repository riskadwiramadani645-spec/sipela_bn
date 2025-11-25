<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tahun_ajaran')->insert([
            [
                'kode_tahun' => '2024/2025-1',
                'tahun_ajaran' => '2024/2025',
                'semester' => 'Ganjil',
                'status_aktif' => true,
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-12-31',
                'created_at' => now()
            ],
            [
                'kode_tahun' => '2023/2024-2',
                'tahun_ajaran' => '2023/2024',
                'semester' => 'Genap',
                'status_aktif' => false,
                'tanggal_mulai' => '2024-01-01',
                'tanggal_selesai' => '2024-06-30',
                'created_at' => now()
            ]
        ]);
    }
}