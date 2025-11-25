<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimbingan_konseling', function (Blueprint $table) {
            $table->id('bk_id');
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('guru_konselor');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->enum('jenis_layanan', ['Individu', 'Kelompok', 'Klasikal']);
            $table->string('topik', 255);
            $table->text('keluhan_masalah')->nullable();
            $table->text('tindakan_solusi')->nullable();
            $table->enum('status', ['terdaftar', 'diproses', 'selesai', 'tindak_lanjut'])->default('terdaftar');
            $table->date('tanggal_konseling');
            $table->date('tanggal_tindak_lanjut')->nullable();
            $table->text('hasil_evaluasi')->nullable();
            $table->timestamps();
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa');
            $table->foreign('guru_konselor')->references('guru_id')->on('guru');
            $table->foreign('tahun_ajaran_id')->references('tahun_ajaran_id')->on('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingan_konseling');
    }
};