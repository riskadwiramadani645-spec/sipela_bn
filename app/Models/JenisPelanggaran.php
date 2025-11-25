<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran';
    protected $primaryKey = 'jenis_pelanggaran_id';
    public $timestamps = false;
    protected $dates = ['created_at'];

    protected $fillable = [
        'nama_pelanggaran',
        'kategori',
        'poin',
        'deskripsi',
        'sanksi_rekomendasi'
    ];

    protected $casts = [
        'poin' => 'integer'
    ];
    
    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'jenis_pelanggaran_id', 'jenis_pelanggaran_id');
    }
}