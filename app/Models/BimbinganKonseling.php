<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BimbinganKonseling extends Model
{
    protected $table = 'bimbingan_konseling';
    protected $primaryKey = 'bk_id';
    public $timestamps = true;
    protected $dates = ['created_at', 'updated_at', 'tanggal_konseling', 'tanggal_tindak_lanjut'];

    protected $fillable = [
        'siswa_id',
        'guru_konselor',
        'tahun_ajaran_id',
        'jenis_layanan',
        'topik',
        'keluhan_masalah',
        'tindakan_solusi',
        'status',
        'tanggal_konseling',
        'tanggal_tindak_lanjut',
        'hasil_evaluasi',
        'sanksi_id',
        'is_followup'
    ];

    protected $casts = [
        'tanggal_konseling' => 'date',
        'tanggal_tindak_lanjut' => 'date'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    public function guruKonselor()
    {
        return $this->belongsTo(Guru::class, 'guru_konselor', 'guru_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    // Accessor untuk compatibility dengan view
    public function getIdAttribute()
    {
        return $this->bk_id;
    }

    public function getTindakanAttribute()
    {
        return $this->tindakan_solusi;
    }

    public function getKonselorAttribute()
    {
        return $this->guruKonselor;
    }

    public function sanksi()
    {
        return $this->belongsTo(Sanksi::class, 'sanksi_id', 'sanksi_id');
    }
}