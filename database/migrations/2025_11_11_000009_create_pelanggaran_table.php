<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id('pelanggaran_id');
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('guru_pencatat');
            $table->unsignedBigInteger('jenis_pelanggaran_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->integer('poin');
            $table->text('keterangan')->nullable();
            $table->string('bukti_foto', 255)->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'diverifikasi', 'ditolak', 'revisi'])->default('menunggu');
            $table->unsignedBigInteger('guru_verifikator')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->date('tanggal');
            $table->timestamps();
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa');
            $table->foreign('guru_pencatat')->references('guru_id')->on('guru');
            $table->foreign('jenis_pelanggaran_id')->references('jenis_pelanggaran_id')->on('jenis_pelanggaran');
            $table->foreign('tahun_ajaran_id')->references('tahun_ajaran_id')->on('tahun_ajaran');
            $table->foreign('guru_verifikator')->references('guru_id')->on('guru');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran');
    }
};