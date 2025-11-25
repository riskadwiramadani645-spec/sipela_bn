<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Pelanggaran;

return new class extends Migration
{
    public function up()
    {
        // Update semua record yang menggunakan sample-bukti.jpg menjadi null
        Pelanggaran::where('bukti_foto', 'pelanggaran/sample-bukti.jpg')
                   ->update(['bukti_foto' => null]);
    }

    public function down()
    {
        // Rollback tidak diperlukan karena ini adalah pembersihan data
    }
};