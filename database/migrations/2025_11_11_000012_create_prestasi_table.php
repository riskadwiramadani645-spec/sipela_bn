<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id('prestasi_id');
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('guru_pencatat');
            $table->unsignedBigInteger('jenis_prestasi_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->integer('poin');
            $table->text('keterangan')->nullable();
            $table->enum('tingkat', ['Sekolah', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'])->nullable();
            $table->string('penghargaan', 100)->nullable();
            $table->string('bukti_dokumen', 255)->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'diverifikasi', 'ditolak', 'revisi'])->default('menunggu');
            $table->unsignedBigInteger('guru_verifikator')->nullable();
            $table->date('tanggal');
            $table->timestamps();
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa');
            $table->foreign('guru_pencatat')->references('guru_id')->on('guru');
            $table->foreign('jenis_prestasi_id')->references('jenis_prestasi_id')->on('jenis_prestasi');
            $table->foreign('tahun_ajaran_id')->references('tahun_ajaran_id')->on('tahun_ajaran');
            $table->foreign('guru_verifikator')->references('guru_id')->on('guru');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};