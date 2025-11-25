<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update tabel sanksi
        Schema::table('sanksi', function (Blueprint $table) {
            // Drop kolom jenis_sanksi lama jika ada
            if (Schema::hasColumn('sanksi', 'jenis_sanksi')) {
                $table->dropColumn('jenis_sanksi');
            }
            
            // Tambah jenis_sanksi_id jika belum ada
            if (!Schema::hasColumn('sanksi', 'jenis_sanksi_id')) {
                $table->unsignedBigInteger('jenis_sanksi_id')->nullable()->after('pelanggaran_id');
            }
            
            // Update enum status
            $table->enum('status', ['terdaftar', 'dijadwalkan', 'berlangsung', 'selesai', 'tindak_lanjut'])->default('terdaftar')->change();
        });
        
        // Seed default jenis sanksi jika belum ada
        $this->seedDefaultJenisSanksi();
        
        // Tambah foreign key setelah data ada
        Schema::table('sanksi', function (Blueprint $table) {
            if (Schema::hasColumn('sanksi', 'jenis_sanksi_id')) {
                $table->foreign('jenis_sanksi_id')->references('jenis_sanksi_id')->on('jenis_sanksi');
            }
        });

        // Update tabel pelaksanaan_sanksi
        Schema::table('pelaksanaan_sanksi', function (Blueprint $table) {
            // Drop siswa_id jika ada
            if (Schema::hasColumn('pelaksanaan_sanksi', 'siswa_id')) {
                $table->dropForeign(['siswa_id']);
                $table->dropColumn('siswa_id');
            }
        });
    }
    
    private function seedDefaultJenisSanksi()
    {
        $jenisSanksi = [
            ['nama_sanksi' => 'Teguran Lisan', 'kategori' => 'ringan', 'deskripsi' => 'Teguran lisan dari guru'],
            ['nama_sanksi' => 'Teguran Tertulis', 'kategori' => 'ringan', 'deskripsi' => 'Teguran tertulis dan surat pernyataan'],
            ['nama_sanksi' => 'Kerja Sosial', 'kategori' => 'sedang', 'deskripsi' => 'Membersihkan lingkungan sekolah'],
            ['nama_sanksi' => 'Skorsing', 'kategori' => 'berat', 'deskripsi' => 'Tidak boleh masuk sekolah sementara'],
        ];
        
        foreach ($jenisSanksi as $sanksi) {
            \DB::table('jenis_sanksi')->updateOrInsert(
                ['nama_sanksi' => $sanksi['nama_sanksi']],
                $sanksi
            );
        }
    }
    
    public function down(): void
    {
        Schema::table('sanksi', function (Blueprint $table) {
            $table->dropForeign(['jenis_sanksi_id']);
            $table->dropColumn('jenis_sanksi_id');
            $table->string('jenis_sanksi', 255)->after('pelanggaran_id');
            $table->enum('status', ['Terdaftar', 'Diproses', 'Selesai', 'Tindak_lanjut'])->default('Terdaftar')->change();
        });

        Schema::table('pelaksanaan_sanksi', function (Blueprint $table) {
            $table->unsignedBigInteger('siswa_id')->after('pelaksanaan_sanksi_id');
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa');
        });
    }
};