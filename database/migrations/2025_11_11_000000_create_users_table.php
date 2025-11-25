<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->unsignedBigInteger('siswa_id')->nullable();
            $table->unsignedBigInteger('ortu_id')->nullable();
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('nama_lengkap', 100)->nullable();
            $table->enum('level', ['admin', 'kesiswaan', 'guru', 'konselor_bk', 'kepala_sekolah', 'siswa', 'orang_tua']);
            $table->boolean('can_verify')->default(false);
            $table->boolean('is_active')->default(true);
            $table->datetime('last_login')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};