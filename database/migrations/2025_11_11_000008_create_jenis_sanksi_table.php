<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_sanksi', function (Blueprint $table) {
            $table->id('jenis_sanksi_id');
            $table->string('nama_sanksi');
            $table->string('kategori', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_sanksi');
    }
};