<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_prestasi', function (Blueprint $table) {
            $table->id('jenis_prestasi_id');
            $table->string('nama_prestasi', 100);
            $table->integer('poin');
            $table->enum('kategori', ['Akademik', 'Non-Akademik']);
            $table->text('deskripsi')->nullable();
            $table->string('reward', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_prestasi');
    }
};