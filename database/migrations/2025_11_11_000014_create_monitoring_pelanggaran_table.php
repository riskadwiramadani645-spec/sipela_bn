<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_pelanggaran', function (Blueprint $table) {
            $table->id('monitoring_id');
            $table->unsignedBigInteger('pelanggaran_id');
            $table->unsignedBigInteger('guru_kepsek');
            $table->enum('status_monitoring', ['Menunggu', 'Diproses', 'Selesai'])->default('Menunggu');
            $table->text('catatan_monitoring')->nullable();
            $table->date('tanggal_monitoring');
            $table->string('tindak_lanjut', 255)->nullable();
            $table->datetime('created_at')->nullable();
            $table->foreign('pelanggaran_id')->references('pelanggaran_id')->on('pelanggaran');
            $table->foreign('guru_kepsek')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_pelanggaran');
    }
};