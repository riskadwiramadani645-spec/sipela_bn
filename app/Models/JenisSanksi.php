<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSanksi extends Model
{
    protected $table = 'jenis_sanksi';
    protected $primaryKey = 'jenis_sanksi_id';
    public $timestamps = false;
    protected $dates = ['created_at'];
    
    protected $fillable = [
        'nama_sanksi',
        'kategori',
        'deskripsi'
    ];

    public function sanksi()
    {
        return $this->hasMany(Sanksi::class, 'jenis_sanksi_id', 'jenis_sanksi_id');
    }
}