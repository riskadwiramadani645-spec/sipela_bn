<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringOrangtua extends Model
{
    protected $table = 'monitoring_orangtua';
    protected $primaryKey = 'monitoring_id';
    public $timestamps = false;
    protected $dates = ['created_at', 'tanggal_monitoring'];
    
    protected $fillable = [
        'ortu_id',
        'pelanggaran_id',
        'prestasi_id',
        'status_kontak',
        'status_monitoring',
        'tindak_lanjut',
        'tanggal_monitoring',
        'dokumen_berita'
    ];

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'ortu_id', 'ortu_id');
    }

    public function pelanggaran()
    {
        return $this->belongsTo(Pelanggaran::class, 'pelanggaran_id', 'pelanggaran_id');
    }

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'prestasi_id', 'prestasi_id');
    }
}