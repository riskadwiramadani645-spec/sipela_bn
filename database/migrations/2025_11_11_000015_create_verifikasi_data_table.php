<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_data', function (Blueprint $table) {
            $table->id();
            $table->string('tabel_terkait', 100);
            $table->integer('id_terkait');
            $table->unsignedBigInteger('guru_verifikator');
            $table->foreign('guru_verifikator')->references('user_id')->on('users');
            $table->enum('status', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_data');
    }
};