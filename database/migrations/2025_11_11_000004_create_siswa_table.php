<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id('siswa_id');
            $table->string('nis', 20)->unique();
            $table->string('nisn', 20)->unique()->nullable();
            $table->string('nama_siswa', 100);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('status_kesiswaan', ['aktif', 'lulus', 'pindah', 'drop_out', 'cuti'])->default('aktif');
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamps();
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};