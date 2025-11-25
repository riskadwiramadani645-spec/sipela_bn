<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'guru_id';
    public $timestamps = true;
    
    protected $fillable = [
        'nip',
        'nama_guru',
        'jenis_kelamin',
        'bidang_studi',
        'no_telp',
        'email',
        'status'
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id', 'guru_id');
    }
    
    public function pelanggaranPencatat()
    {
        return $this->hasMany(Pelanggaran::class, 'guru_pencatat', 'guru_id');
    }

    public function pelanggaranVerifikator()
    {
        return $this->hasMany(Pelanggaran::class, 'guru_verifikator', 'guru_id');
    }

    public function prestasiPencatat()
    {
        return $this->hasMany(Prestasi::class, 'guru_pencatat', 'guru_id');
    }

    public function prestasiVerifikator()
    {
        return $this->hasMany(Prestasi::class, 'guru_verifikator', 'guru_id');
    }

    public function bimbinganKonseling()
    {
        return $this->hasMany(BimbinganKonseling::class, 'guru_konselor', 'guru_id');
    }

    public function sanksiPenanggungjawab()
    {
        return $this->hasMany(Sanksi::class, 'pic_penanggungjawab', 'guru_id');
    }

    public function pelaksanaanSanksi()
    {
        return $this->hasMany(PelaksanaanSanksi::class, 'guru_pengawas', 'guru_id');
    }
}