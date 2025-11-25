<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $table = 'pelanggaran';
    protected $primaryKey = 'pelanggaran_id';
    public $timestamps = true;
    protected $dates = ['created_at', 'tanggal'];

    protected $fillable = [
        'siswa_id',
        'guru_pencatat',
        'jenis_pelanggaran_id',
        'tahun_ajaran_id',
        'poin',
        'keterangan',
        'bukti_foto',
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

    public function jenisPelanggaran()
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id', 'jenis_pelanggaran_id');
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
        return $this->hasMany(MonitoringOrangtua::class, 'pelanggaran_id', 'pelanggaran_id');
    }

    public function sanksi()
    {
        return $this->hasOne(Sanksi::class, 'pelanggaran_id', 'pelanggaran_id');
    }
}