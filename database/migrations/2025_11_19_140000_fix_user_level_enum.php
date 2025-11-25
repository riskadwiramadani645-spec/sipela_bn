<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('level', 20)->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level', ['admin', 'kesiswaan', 'guru', 'konselor_bk', 'kepala_sekolah', 'siswa', 'orang_tua'])->change();
        });
    }
};