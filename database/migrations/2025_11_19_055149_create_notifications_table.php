<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'sanksi_followup', 'bk_completed', etc
            $table->unsignedBigInteger('user_id'); // penerima notifikasi
            $table->unsignedBigInteger('sanksi_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Tidak menggunakan foreign key constraint untuk menghindari error
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('sanksi_id')->references('sanksi_id')->on('sanksi');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};