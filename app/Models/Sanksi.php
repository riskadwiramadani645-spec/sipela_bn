<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sanksi extends Model
{
    protected $table = 'sanksi';
    protected $primaryKey = 'sanksi_id';
    public $timestamps = true;
    protected $dates = ['created_at', 'updated_at', 'tanggal_mulai', 'tanggal_selesai'];

    protected $fillable = [
        'pelanggaran_id',
        'jenis_sanksi_id',
        'jenis_sanksi_manual',
        'deskripsi_sanksi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'bobot',
        'status_intern',
        'catatan_pelaksanaan',
        'guru_penanggungjawab',
        'assigned_to_bk',
        'followup_status',
        'bk_user_id'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    public function pelanggaran()
    {
        return $this->belongsTo(Pelanggaran::class, 'pelanggaran_id', 'pelanggaran_id');
    }

    public function jenisSanksi()
    {
        return $this->belongsTo(JenisSanksi::class, 'jenis_sanksi_id', 'jenis_sanksi_id');
    }

    public function guruPenanggungjawab()
    {
        return $this->belongsTo(Guru::class, 'guru_penanggungjawab', 'guru_id');
    }

    public function pelaksanaanSanksi()
    {
        return $this->hasMany(PelaksanaanSanksi::class, 'sanksi_id', 'sanksi_id');
    }

    // Helper method untuk mendapatkan siswa melalui pelanggaran
    public function siswa()
    {
        return $this->hasOneThrough(Siswa::class, Pelanggaran::class, 'pelanggaran_id', 'siswa_id', 'pelanggaran_id', 'siswa_id');
    }

    public function bkUser()
    {
        return $this->belongsTo(User::class, 'bk_user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'sanksi_id', 'sanksi_id');
    }

    public function inputBk()
    {
        return $this->hasMany(BimbinganKonseling::class, 'sanksi_id', 'sanksi_id');
    }
}