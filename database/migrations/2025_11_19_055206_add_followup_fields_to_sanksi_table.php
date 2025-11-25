<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sanksi', function (Blueprint $table) {
            $table->boolean('assigned_to_bk')->default(false);
            $table->enum('followup_status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->unsignedBigInteger('bk_user_id')->nullable();
            
            // Tidak menggunakan foreign key constraint
            // $table->foreign('bk_user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('sanksi', function (Blueprint $table) {
            $table->dropColumn(['assigned_to_bk', 'followup_status', 'bk_user_id']);
        });
    }
};