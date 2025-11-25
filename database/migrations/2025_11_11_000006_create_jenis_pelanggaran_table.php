<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id('jenis_pelanggaran_id');
            $table->string('nama_pelanggaran', 100);
            $table->enum('kategori', ['ringan', 'sedang', 'berat', 'sangat_berat']);
            $table->integer('poin');
            $table->text('deskripsi')->nullable();
            $table->string('sanksi_rekomendasi', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran');
    }
};