<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'guru_id')) {
                $table->unsignedBigInteger('guru_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('users', 'siswa_id')) {
                $table->unsignedBigInteger('siswa_id')->nullable()->after('guru_id');
            }
            if (!Schema::hasColumn('users', 'ortu_id')) {
                $table->unsignedBigInteger('ortu_id')->nullable()->after('siswa_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'guru_id')) {
                $table->dropColumn('guru_id');
            }
            if (Schema::hasColumn('users', 'siswa_id')) {
                $table->dropColumn('siswa_id');
            }
            if (Schema::hasColumn('users', 'ortu_id')) {
                $table->dropColumn('ortu_id');
            }
        });
    }
};