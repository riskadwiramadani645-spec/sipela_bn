<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Guru first
        $guru1 = DB::table('guru')->insertGetId(['nip' => '123456789', 'nama_guru' => 'Admin Guru', 'bidang_studi' => 'Administrasi', 'status' => 'Aktif', 'created_at' => now()]);
        $guru2 = DB::table('guru')->insertGetId(['nip' => '987654321', 'nama_guru' => 'Guru BK', 'bidang_studi' => 'Bimbingan Konseling', 'status' => 'Aktif', 'created_at' => now()]);
        $guru3 = DB::table('guru')->insertGetId(['nip' => '111222333', 'nama_guru' => 'Kepala Sekolah', 'bidang_studi' => 'Kepala Sekolah', 'status' => 'Aktif', 'created_at' => now()]);
        $guru4 = DB::table('guru')->insertGetId(['nip' => '444555666', 'nama_guru' => 'Guru Matematika', 'bidang_studi' => 'Matematika', 'status' => 'Aktif', 'created_at' => now()]);

        // Insert Users with different roles
        DB::table('users')->insert([
            [
                'guru_id' => $guru1,
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'level' => 'admin',
                'can_verify' => true,
                'created_at' => now()
            ],
            [
                'guru_id' => null,
                'username' => 'kesiswaan',
                'password' => Hash::make('kesiswaan123'),
                'level' => 'kesiswaan',
                'can_verify' => true,
                'created_at' => now()
            ],
            [
                'guru_id' => $guru4,
                'username' => 'guru',
                'password' => Hash::make('guru123'),
                'level' => 'guru',
                'can_verify' => false,
                'created_at' => now()
            ],
            [
                'guru_id' => $guru2,
                'username' => 'konselor_bk',
                'password' => Hash::make('bk123'),
                'level' => 'konselor_bk',
                'can_verify' => true,
                'created_at' => now()
            ],
            [
                'guru_id' => $guru3,
                'username' => 'kepala_sekolah',
                'password' => Hash::make('kepsek123'),
                'level' => 'kepala_sekolah',
                'can_verify' => true,
                'created_at' => now()
            ],
            [
                'guru_id' => null,
                'username' => 'siswa',
                'password' => Hash::make('siswa123'),
                'level' => 'siswa',
                'can_verify' => false,
                'created_at' => now()
            ],
            [
                'guru_id' => null,
                'username' => 'orang_tua',
                'password' => Hash::make('ortu123'),
                'level' => 'orang_tua',
                'can_verify' => false,
                'created_at' => now()
            ]
        ]);
    }
}