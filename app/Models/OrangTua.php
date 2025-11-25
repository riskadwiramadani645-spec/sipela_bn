<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';
    protected $primaryKey = 'ortu_id';
    public $timestamps = true;
    
    protected $fillable = [
        'siswa_id',
        'hubungan',
        'nama_orangtua',
        'pekerjaan',
        'pendidikan',
        'no_telp',
        'alamat'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    public function monitoringOrangtua()
    {
        return $this->hasMany(MonitoringOrangtua::class, 'ortu_id', 'ortu_id');
    }
    
    public function user()
    {
        return $this->hasOne(User::class, 'ortu_id', 'ortu_id');
    }
}