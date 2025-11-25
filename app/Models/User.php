<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;
    protected $dates = ['last_login'];

    protected $fillable = [
        'guru_id',
        'siswa_id',
        'ortu_id',
        'username',
        'password',
        'profile_photo',
        'nama_lengkap',
        'level',
        'can_verify',
        'is_active',
        'last_login'
    ];

    // Level constants
    const LEVEL_ADMIN = 'admin';
    const LEVEL_KESISWAAN = 'kesiswaan';
    const LEVEL_GURU = 'guru';
    const LEVEL_KONSELOR_BK = 'konselor_bk';
    const LEVEL_KEPALA_SEKOLAH = 'kepala_sekolah';
    const LEVEL_SISWA = 'siswa';
    const LEVEL_ORANG_TUA = 'orang_tua';

    public static function getLevels()
    {
        return [
            self::LEVEL_ADMIN => 'Admin',
            self::LEVEL_KESISWAAN => 'Kesiswaan',
            self::LEVEL_GURU => 'Guru',
            self::LEVEL_KONSELOR_BK => 'Konselor BK',
            self::LEVEL_KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::LEVEL_SISWA => 'Siswa',
            self::LEVEL_ORANG_TUA => 'Orang Tua'
        ];
    }

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'can_verify' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'last_login' => 'datetime'
    ];

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    // Relasi dengan tabel profil
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'ortu_id', 'ortu_id');
    }

    // Helper method untuk mendapatkan nama user
    public function getNamaAttribute()
    {
        switch ($this->level) {
            case self::LEVEL_GURU:
            case self::LEVEL_KESISWAAN:
            case self::LEVEL_KONSELOR_BK:
            case self::LEVEL_KEPALA_SEKOLAH:
                return $this->guru->nama_guru ?? $this->nama_lengkap;
            case self::LEVEL_SISWA:
                return $this->siswa->nama_siswa ?? $this->nama_lengkap;
            case self::LEVEL_ORANG_TUA:
                return $this->orangTua->nama_orangtua ?? $this->nama_lengkap;
            default:
                return $this->nama_lengkap ?? $this->username;
        }
    }

    // Helper method untuk mendapatkan foto profile
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo && file_exists(storage_path('app/public/' . $this->profile_photo))) {
            return asset('storage/' . $this->profile_photo);
        }
        return asset('img/default-avatar.svg');
    }

    // Helper method untuk mendapatkan kelas siswa
    public function getKelasAttribute()
    {
        if ($this->level === 'siswa' && $this->siswa) {
            return $this->siswa->kelas;
        }
        return null;
    }

    // Helper method untuk mendapatkan nama dengan kelas
    public function getNamaWithKelasAttribute()
    {
        if ($this->level === 'siswa' && $this->siswa && $this->siswa->kelas) {
            return $this->siswa->nama_siswa . ' - ' . $this->siswa->kelas->nama_kelas;
        }
        return $this->nama;
    }
    
    // Helper method untuk orang tua mendapatkan data anak
    public function getDataAnak()
    {
        if ($this->level === 'orang_tua' && $this->orangTua) {
            return $this->orangTua->siswa;
        }
        return null;
    }
}