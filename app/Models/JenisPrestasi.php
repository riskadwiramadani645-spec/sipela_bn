<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPrestasi extends Model
{
    protected $table = 'jenis_prestasi';
    protected $primaryKey = 'jenis_prestasi_id';
    public $timestamps = false;
    protected $dates = ['created_at'];

    protected $fillable = [
        'nama_prestasi',
        'poin',
        'kategori',
        'deskripsi',
        'reward'
    ];

    protected $casts = [
        'poin' => 'integer'
    ];
    
    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'jenis_prestasi_id', 'jenis_prestasi_id');
    }
}