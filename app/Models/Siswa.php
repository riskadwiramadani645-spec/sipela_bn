<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'siswa_id';
    public $timestamps = true;
    protected $dates = ['tanggal_lahir'];

    protected $fillable = [
        'nis',
        'nisn',
        'nama_siswa',
        'jenis_kelamin',
        'status_kesiswaan',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_telp',
        'kelas_id',
        'foto'
    ];
    
    protected $attributes = [
        'jenis_kelamin' => 'Laki-laki',
        'status_kesiswaan' => 'aktif'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'siswa_id', 'siswa_id');
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'siswa_id', 'siswa_id');
    }
    
    public function bimbinganKonseling()
    {
        return $this->hasMany(BimbinganKonseling::class, 'siswa_id', 'siswa_id');
    }
    
    public function orangTua()
    {
        return $this->hasMany(OrangTua::class, 'siswa_id', 'siswa_id');
    }

    public function pelaksanaanSanksi()
    {
        return $this->hasMany(PelaksanaanSanksi::class, 'siswa_id', 'siswa_id');
    }
    
    public function user()
    {
        return $this->hasOne(User::class, 'siswa_id', 'siswa_id');
    }
}