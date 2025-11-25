<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id('guru_id');
            $table->string('nip', 20)->unique();
            $table->string('nama_guru', 100);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('bidang_studi', 50)->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Pensiun'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};