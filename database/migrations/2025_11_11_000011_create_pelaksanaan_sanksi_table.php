<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelaksanaan_sanksi', function (Blueprint $table) {
            $table->id('pelaksanaan_sanksi_id');
            $table->unsignedBigInteger('sanksi_id');
            $table->date('tanggal_pelaksanaan');
            $table->text('deskripsi_pelaksanaan')->nullable();
            $table->string('bukti_pelaksanaan', 255)->nullable();
            $table->enum('status', ['terjadwal', 'dikerjakan', 'tuntas', 'terlambat', 'perpanjangan'])->default('terjadwal');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('guru_pengawas')->nullable();
            $table->timestamps();
            $table->foreign('sanksi_id')->references('sanksi_id')->on('sanksi');
            $table->foreign('guru_pengawas')->references('guru_id')->on('guru');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelaksanaan_sanksi');
    }
};