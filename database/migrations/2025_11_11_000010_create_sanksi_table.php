<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanksi', function (Blueprint $table) {
            $table->id('sanksi_id');
            $table->unsignedBigInteger('pelanggaran_id');
            $table->unsignedBigInteger('jenis_sanksi_id');
            $table->text('deskripsi_sanksi')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['terdaftar', 'dijadwalkan', 'berlangsung', 'selesai', 'tindak_lanjut'])->default('terdaftar');
            $table->text('catatan_pelaksanaan')->nullable();
            $table->unsignedBigInteger('guru_penanggungjawab')->nullable();
            $table->timestamps();
            
            $table->foreign('pelanggaran_id')->references('pelanggaran_id')->on('pelanggaran');
            $table->foreign('jenis_sanksi_id')->references('jenis_sanksi_id')->on('jenis_sanksi');
            $table->foreign('guru_penanggungjawab')->references('guru_id')->on('guru');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanksi');
    }
};