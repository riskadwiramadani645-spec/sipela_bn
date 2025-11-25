<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bimbingan_konseling', function (Blueprint $table) {
            $table->unsignedBigInteger('sanksi_id')->nullable();
            $table->boolean('is_followup')->default(false);
            
            // Tidak menggunakan foreign key constraint
            // $table->foreign('sanksi_id')->references('sanksi_id')->on('sanksi');
        });
    }

    public function down()
    {
        Schema::table('bimbingan_konseling', function (Blueprint $table) {
            $table->dropColumn(['sanksi_id', 'is_followup']);
        });
    }
};