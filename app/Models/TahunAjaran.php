<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'tahun_ajaran_id';
    public $timestamps = true;
    protected $dates = ['tanggal_mulai', 'tanggal_selesai'];
    
    protected $fillable = [
        'kode_tahun',
        'tahun_ajaran', 
        'semester',
        'status_aktif',
        'tanggal_mulai',
        'tanggal_selesai'
    ];
    
    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    public function bimbinganKonseling()
    {
        return $this->hasMany(BimbinganKonseling::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }
}