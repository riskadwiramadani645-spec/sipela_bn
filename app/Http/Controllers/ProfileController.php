<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\OrangTua;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Force refresh user data dari database dengan relasi
        $user = User::with(['guru', 'siswa', 'orangTua'])->find($user->user_id);
        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan');
        }
        
        // Force update session dengan data terbaru
        session()->forget('user');
        session(['user' => $user]);

        $profileData = $this->getProfileData($user);
        
        return view('profile.index', compact('user', 'profileData'));
    }

    public function updateProfile(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // Validasi input
            $rules = ['username' => 'required|unique:users,username,' . $user->user_id . ',user_id'];
            
            // Tambah validasi berdasarkan level
            if ($user->level === 'admin') {
                $rules['nama_lengkap'] = 'nullable|string|max:100';
            } elseif (in_array($user->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah', 'wali_kelas'])) {
                $rules['nama_guru'] = 'required|string|max:100';
                $rules['jenis_kelamin'] = 'required|in:Laki-laki,Perempuan';
                $rules['bidang_studi'] = 'nullable|string|max:100';
                $rules['no_telp'] = 'nullable|string|max:15';
                $rules['email'] = 'nullable|email|max:100';
            } elseif ($user->level === 'siswa') {
                $rules['nama_siswa'] = 'required|string|max:100';
                $rules['jenis_kelamin'] = 'required|in:Laki-laki,Perempuan';
                $rules['tempat_lahir'] = 'nullable|string|max:50';
                $rules['tanggal_lahir'] = 'nullable|date';
                $rules['no_telp'] = 'nullable|string|max:15';
                $rules['alamat'] = 'nullable|string|max:255';
            } elseif ($user->level === 'orang_tua') {
                $rules['nama_orang_tua'] = 'required|string|max:100';
                $rules['hubungan'] = 'nullable|in:Ayah,Ibu,Wali';
                $rules['pekerjaan'] = 'nullable|string|max:100';
                $rules['pendidikan'] = 'nullable|string|max:100';
                $rules['no_telp'] = 'nullable|string|max:15';
                $rules['alamat'] = 'nullable|string|max:255';
            }
            
            $request->validate($rules);

            DB::beginTransaction();

            // Update username
            DB::table('users')->where('user_id', $user->user_id)->update([
                'username' => $request->username
            ]);

            // Update data spesifik berdasarkan level
            $this->updateSpecificData($user, $request);

            DB::commit();

            // Update session dengan relasi
            $updatedUser = User::with(['guru', 'siswa', 'orangTua'])->find($user->user_id);
            session(['user' => $updatedUser]);

            return redirect()->route('profile.index')->with('success', 'Profile berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function updatePassword(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ], [
                'current_password.required' => 'Password lama harus diisi',
                'new_password.required' => 'Password baru harus diisi',
                'new_password.min' => 'Password baru minimal 6 karakter',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            ]);

            // Refresh user data dari database
            $currentUser = User::find($user->user_id);
            if (!$currentUser) {
                return back()->with('error', 'User tidak ditemukan');
            }

            // Verifikasi password lama
            if (!Hash::check($request->current_password, $currentUser->password)) {
                return back()->with('error', 'Password lama tidak sesuai');
            }

            // Update password
            DB::table('users')->where('user_id', $user->user_id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('profile.index')->with('success', 'Password berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePhoto(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $request->validate([
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'profile_photo.required' => 'Foto harus dipilih',
                'profile_photo.image' => 'File harus berupa gambar',
                'profile_photo.mimes' => 'Format foto harus jpeg, png, jpg, atau gif',
                'profile_photo.max' => 'Ukuran foto maksimal 2MB'
            ]);

            $currentUser = User::find($user->user_id);
            if (!$currentUser) {
                return back()->with('error', 'User tidak ditemukan');
            }

            // Hapus foto lama jika ada
            if ($currentUser->profile_photo && file_exists(storage_path('app/public/' . $currentUser->profile_photo))) {
                unlink(storage_path('app/public/' . $currentUser->profile_photo));
            }

            // Upload foto baru
            $fileName = 'profile_' . $user->user_id . '_' . time() . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $path = $request->file('profile_photo')->storeAs('profile-photos', $fileName, 'public');

            // Update database
            DB::table('users')->where('user_id', $user->user_id)->update([
                'profile_photo' => $path
            ]);

            // Update session dengan relasi
            $updatedUser = User::with(['guru', 'siswa', 'orangTua'])->find($user->user_id);
            session(['user' => $updatedUser]);

            return redirect()->route('profile.index')->with('success', 'Foto profile berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getProfileData($user)
    {
        switch ($user->level) {
            case 'admin':
                return (object) [
                    'nama_admin' => $user->nama_lengkap ?? $user->username,
                    'username' => $user->username,
                    'level' => $user->level
                ];
            case 'guru':
            case 'kesiswaan':
            case 'konselor_bk':
            case 'kepala_sekolah':
            case 'wali_kelas':
                return DB::table('guru')->where('guru_id', $user->guru_id ?? 0)->first();
            case 'siswa':
                return DB::table('siswa')
                    ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.kelas_id')
                    ->where('siswa.siswa_id', $user->siswa_id ?? 0)
                    ->select('siswa.*', 'kelas.nama_kelas')
                    ->first();
            case 'orang_tua':
                return DB::table('orang_tua')
                    ->leftJoin('siswa', 'orang_tua.siswa_id', '=', 'siswa.siswa_id')
                    ->where('orang_tua.ortu_id', $user->ortu_id ?? 0)
                    ->select('orang_tua.*', 'siswa.nama_siswa')
                    ->first();
            default:
                return null;
        }
    }

    private function updateSpecificData($user, $request)
    {
        switch ($user->level) {
            case 'admin':
                // Admin hanya update nama_lengkap di tabel users
                if ($request->filled('nama_lengkap')) {
                    DB::table('users')->where('user_id', $user->user_id)->update([
                        'nama_lengkap' => $request->nama_lengkap
                    ]);
                }
                break;
            case 'guru':
            case 'kesiswaan':
            case 'konselor_bk':
            case 'kepala_sekolah':
            case 'wali_kelas':
                // Cari guru berdasarkan guru_id atau username
                $guruToUpdate = null;
                if ($user->guru_id) {
                    $guruToUpdate = DB::table('guru')->where('guru_id', $user->guru_id)->first();
                }
                
                if (!$guruToUpdate) {
                    $guruToUpdate = DB::table('guru')
                        ->where('nip', $user->username)
                        ->orWhere('email', $user->username)
                        ->first();
                }
                
                if ($guruToUpdate) {
                    // Pastikan jenis_kelamin tidak null untuk guru
                    $jenisKelamin = $request->jenis_kelamin;
                    if (is_null($jenisKelamin) || $jenisKelamin === '' || $jenisKelamin === 'null') {
                        $jenisKelamin = $guruToUpdate->jenis_kelamin ?? 'Laki-laki';
                    }
                    
                    // Update guru yang sudah ada
                    DB::table('guru')->where('guru_id', $guruToUpdate->guru_id)->update([
                        'nama_guru' => $request->nama_guru,
                        'jenis_kelamin' => $jenisKelamin,
                        'bidang_studi' => $request->bidang_studi ?? $guruToUpdate->bidang_studi,
                        'no_telp' => $request->no_telp ?? $guruToUpdate->no_telp,
                        'email' => $request->email ?? $guruToUpdate->email,
                    ]);
                    
                    // Update guru_id di user jika belum ada
                    if (!$user->guru_id) {
                        DB::table('users')->where('user_id', $user->user_id)->update([
                            'guru_id' => $guruToUpdate->guru_id
                        ]);
                    }
                } else {
                    // Pastikan jenis_kelamin untuk guru baru
                    $jenisKelamin = $request->jenis_kelamin;
                    if (is_null($jenisKelamin) || $jenisKelamin === '' || $jenisKelamin === 'null') {
                        $jenisKelamin = 'Laki-laki';
                    }
                    
                    // Buat record guru baru jika tidak ditemukan
                    $newGuruId = DB::table('guru')->insertGetId([
                        'nama_guru' => $request->nama_guru,
                        'nip' => $user->username,
                        'jenis_kelamin' => $jenisKelamin,
                        'bidang_studi' => $request->bidang_studi ?? 'Mata Pelajaran',
                        'no_telp' => $request->no_telp ?? null,
                        'email' => $request->email ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Update guru_id di user
                    DB::table('users')->where('user_id', $user->user_id)->update([
                        'guru_id' => $newGuruId
                    ]);
                }
                break;
            case 'siswa':
                if ($user->siswa_id) {
                    // Pastikan jenis_kelamin tidak null
                    $jenisKelamin = $request->jenis_kelamin;
                    if (is_null($jenisKelamin) || $jenisKelamin === '' || $jenisKelamin === 'null') {
                        $currentSiswa = DB::table('siswa')->where('siswa_id', $user->siswa_id)->first();
                        $jenisKelamin = $currentSiswa->jenis_kelamin ?? 'Laki-laki';
                    }
                    
                    DB::table('siswa')->where('siswa_id', $user->siswa_id)->update([
                        'nama_siswa' => $request->nama_siswa,
                        'jenis_kelamin' => $jenisKelamin,
                        'tempat_lahir' => $request->tempat_lahir ?? null,
                        'tanggal_lahir' => $request->tanggal_lahir ?? null,
                        'alamat' => $request->alamat ?? null,
                        'no_telp' => $request->no_telp ?? null,
                        'updated_at' => now()
                    ]);
                }
                break;
            case 'orang_tua':
                if ($user->ortu_id) {
                    DB::table('orang_tua')->where('ortu_id', $user->ortu_id)->update([
                        'nama_orangtua' => $request->nama_orang_tua,
                        'hubungan' => $request->hubungan ?? null,
                        'pekerjaan' => $request->pekerjaan ?? null,
                        'pendidikan' => $request->pendidikan ?? null,
                        'no_telp' => $request->no_telp ?? null,
                        'alamat' => $request->alamat ?? null,
                        'updated_at' => now()
                    ]);
                }
                break;
        }
    }
}