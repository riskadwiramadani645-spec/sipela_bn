<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaksanaanSanksi extends Model
{
    protected $table = 'pelaksanaan_sanksi';
    protected $primaryKey = 'pelaksanaan_sanksi_id';
    public $timestamps = true;

    protected $fillable = [
        'sanksi_id',
        'tanggal_pelaksanaan',
        'deskripsi_pelaksanaan',
        'bukti_pelaksanaan',
        'status',
        'catatan',
        'guru_pengawas'
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date'
    ];

    public function sanksi()
    {
        return $this->belongsTo(Sanksi::class, 'sanksi_id', 'sanksi_id');
    }

    public function guruPengawas()
    {
        return $this->belongsTo(Guru::class, 'guru_pengawas', 'guru_id');
    }

    // Helper method untuk mendapatkan siswa melalui sanksi->pelanggaran
    public function getSiswaAttribute()
    {
        return $this->sanksi->pelanggaran->siswa ?? null;
    }
}