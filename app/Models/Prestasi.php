<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $table = 'prestasi';
    protected $primaryKey = 'prestasi_id';
    public $timestamps = false;
    protected $dates = ['created_at', 'tanggal'];

    protected $fillable = [
        'siswa_id',
        'guru_pencatat',
        'jenis_prestasi_id',
        'tahun_ajaran_id',
        'poin',
        'keterangan',
        'tingkat',
        'penghargaan',
        'bukti_dokumen',
        'status_verifikasi',
        'guru_verifikator',
        'catatan_verifikasi',
        'tanggal'
    ];

    protected $casts = [
        'poin' => 'integer',
        'tanggal' => 'date'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    public function jenisPrestasi()
    {
        return $this->belongsTo(JenisPrestasi::class, 'jenis_prestasi_id', 'jenis_prestasi_id');
    }

    public function guruPencatat()
    {
        return $this->belongsTo(Guru::class, 'guru_pencatat', 'guru_id');
    }

    public function guruVerifikator()
    {
        return $this->belongsTo(Guru::class, 'guru_verifikator', 'guru_id');
    }
    
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    public function monitoringOrangtua()
    {
        return $this->hasMany(MonitoringOrangtua::class, 'prestasi_id', 'prestasi_id');
    }
}