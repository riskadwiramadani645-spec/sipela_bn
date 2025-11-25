<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing data first
        DB::table('sanksi')->where('status', 'Terdaftar')->update(['status' => 'terdaftar']);
        DB::table('sanksi')->where('status', 'Diproses')->update(['status' => 'berlangsung']);
        DB::table('sanksi')->where('status', 'Selesai')->update(['status' => 'selesai']);
        DB::table('sanksi')->where('status', 'Tindak_lanjut')->update(['status' => 'tindak_lanjut']);
        
        // Seed default jenis sanksi
        $this->seedDefaultJenisSanksi();
        
        // Add jenis_sanksi_id column
        if (!Schema::hasColumn('sanksi', 'jenis_sanksi_id')) {
            Schema::table('sanksi', function (Blueprint $table) {
                $table->unsignedBigInteger('jenis_sanksi_id')->nullable()->after('pelanggaran_id');
            });
        }
        
        // Update existing sanksi with default jenis_sanksi_id
        $defaultJenisSanksi = DB::table('jenis_sanksi')->where('kategori', 'ringan')->first();
        if ($defaultJenisSanksi) {
            DB::table('sanksi')->whereNull('jenis_sanksi_id')->update([
                'jenis_sanksi_id' => $defaultJenisSanksi->jenis_sanksi_id
            ]);
        }
        
        // Add foreign key
        Schema::table('sanksi', function (Blueprint $table) {
            $table->foreign('jenis_sanksi_id')->references('jenis_sanksi_id')->on('jenis_sanksi');
        });
        
        // Drop old jenis_sanksi column if exists
        if (Schema::hasColumn('sanksi', 'jenis_sanksi')) {
            Schema::table('sanksi', function (Blueprint $table) {
                $table->dropColumn('jenis_sanksi');
            });
        }
        
        // Remove siswa_id from pelaksanaan_sanksi
        if (Schema::hasColumn('pelaksanaan_sanksi', 'siswa_id')) {
            Schema::table('pelaksanaan_sanksi', function (Blueprint $table) {
                $table->dropForeign(['siswa_id']);
                $table->dropColumn('siswa_id');
            });
        }
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
            DB::table('jenis_sanksi')->updateOrInsert(
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
        });

        Schema::table('pelaksanaan_sanksi', function (Blueprint $table) {
            $table->unsignedBigInteger('siswa_id')->after('pelaksanaan_sanksi_id');
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa');
        });
    }
};