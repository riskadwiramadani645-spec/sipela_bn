<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id('ortu_id');
            $table->unsignedBigInteger('siswa_id');
            $table->enum('hubungan', ['Ayah', 'Ibu', 'Wali']);
            $table->string('nama_orangtua', 100);
            $table->string('pekerjaan', 50)->nullable();
            $table->string('pendidikan', 50)->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};