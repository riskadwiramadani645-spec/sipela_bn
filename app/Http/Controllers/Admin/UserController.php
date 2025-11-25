<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\OrangTua;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['guru', 'siswa.kelas', 'orangTua']);
        
        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'Guru') {
                $query->where('level', 'Guru');
            } elseif ($request->role === 'Siswa') {
                $query->where('level', 'Siswa');
                
                // Sub-filter by tingkat kelas
                if ($request->filled('tingkat')) {
                    $query->whereHas('siswa.kelas', function($q) use ($request) {
                        $q->where('nama_kelas', 'LIKE', $request->tingkat . '%');
                    });
                }
            }
        }
        
        $users = $query->latest()->get();
        
        // Get profiles that don't have users yet - simplified approach
        $usedGuruIds = User::whereNotNull('guru_id')->pluck('guru_id')->toArray();
        $usedSiswaIds = User::whereNotNull('siswa_id')->pluck('siswa_id')->toArray();
        $usedOrtuIds = User::whereNotNull('ortu_id')->pluck('ortu_id')->toArray();
        
        $availableGuru = Guru::whereNotIn('guru_id', $usedGuruIds)->get();
        $availableSiswa = Siswa::with('kelas')->whereNotIn('siswa_id', $usedSiswaIds)->get();
        $availableOrangTua = OrangTua::whereNotIn('ortu_id', $usedOrtuIds)->get();
        
        // For debugging - let's also get all profiles
        $allGuru = Guru::all();
        $allSiswa = Siswa::with('kelas')->get();
        $allOrangTua = OrangTua::all();
        
        // Get all for new profile creation
        $allKelas = \App\Models\Kelas::all();
        
        // Get all kelas for filter options
        $allKelas = \App\Models\Kelas::all();
        
        return view('admin.master-data.users', compact('users', 'availableGuru', 'availableSiswa', 'availableOrangTua', 'allKelas', 'allGuru', 'allSiswa', 'allOrangTua'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'level' => 'required|string',
            'input_mode' => 'required|in:existing,new'
        ];

        // Add validation based on input mode and level
        if ($request->input_mode === 'existing') {
            if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                $validationRules['guru_id'] = 'required|exists:guru,guru_id';
            } elseif ($request->level === 'siswa') {
                $validationRules['siswa_id'] = 'required|exists:siswa,siswa_id';
            } elseif ($request->level === 'orang_tua') {
                $validationRules['ortu_id'] = 'required|exists:orang_tua,ortu_id';
            }
        } else {
            // New profile validation
            if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                $validationRules['nama_guru'] = 'required|string|max:100';
                $validationRules['nip'] = 'required|string|unique:guru,nip';
                $validationRules['mata_pelajaran'] = 'nullable|string|max:100';
                $validationRules['no_hp_guru'] = 'nullable|string|max:15';
            } elseif ($request->level === 'siswa') {
                $validationRules['nama_siswa'] = 'required|string|max:100';
                $validationRules['nis'] = 'required|string|unique:siswa,nis';
                $validationRules['kelas_id'] = 'required|exists:kelas,kelas_id';
                $validationRules['tahun_masuk'] = 'required|integer|min:2000|max:' . (date('Y') + 1);
                $validationRules['no_hp_siswa'] = 'nullable|string|max:15';
            } elseif ($request->level === 'orang_tua') {
                $validationRules['nama_orangtua'] = 'required|string|max:100';
                $validationRules['pekerjaan'] = 'nullable|string|max:100';
                $validationRules['no_hp_ortu'] = 'nullable|string|max:15';
                $validationRules['alamat'] = 'nullable|string';
            }
        }

        $request->validate($validationRules);

        try {
            \DB::transaction(function () use ($request) {
                $userData = [
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'nama_lengkap' => $request->nama_lengkap,
                    'level' => $request->level,
                    'can_verify' => $request->has('can_verify'),
                    'is_active' => true,
                    'guru_id' => null,
                    'siswa_id' => null,
                    'ortu_id' => null
                ];

                if ($request->input_mode === 'existing') {
                    // Link to existing profile
                    if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                        $userData['guru_id'] = $request->guru_id;
                    } elseif ($request->level === 'siswa') {
                        $userData['siswa_id'] = $request->siswa_id;
                    } elseif ($request->level === 'orang_tua') {
                        $userData['ortu_id'] = $request->ortu_id;
                    }
                } else {
                    // Create new profile first
                    if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                        $guru = Guru::create([
                            'nama_guru' => $request->nama_guru,
                            'nip' => $request->nip,
                            'mata_pelajaran' => $request->mata_pelajaran,
                            'no_hp' => $request->no_hp_guru
                        ]);
                        $userData['guru_id'] = $guru->guru_id;
                    } elseif ($request->level === 'siswa') {
                        $siswa = Siswa::create([
                            'nama_siswa' => $request->nama_siswa,
                            'nis' => $request->nis,
                            'kelas_id' => $request->kelas_id,
                            'tahun_masuk' => $request->tahun_masuk,
                            'no_hp' => $request->no_hp_siswa
                        ]);
                        $userData['siswa_id'] = $siswa->siswa_id;
                    } elseif ($request->level === 'orang_tua') {
                        $orangTua = OrangTua::create([
                            'nama_orangtua' => $request->nama_orangtua,
                            'pekerjaan' => $request->pekerjaan,
                            'no_hp' => $request->no_hp_ortu,
                            'alamat' => $request->alamat
                        ]);
                        $userData['ortu_id'] = $orangTua->ortu_id;
                    }
                }

                User::create($userData);
            });

            return redirect()->route('admin.master-data.users')->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.master-data.users')->with('error', 'Gagal menambahkan user: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::with(['guru', 'siswa.kelas', 'orangTua'])->findOrFail($id);
        
        // Jika request AJAX, return JSON
        if (request()->wantsJson() || request()->ajax()) {
            $guru = Guru::all();
            $siswa = Siswa::with('kelas')->get();
            $orangTua = OrangTua::all();
            return response()->json(['user' => $user, 'guru' => $guru, 'siswa' => $siswa, 'orangTua' => $orangTua]);
        }
        
        // Jika bukan AJAX, redirect ke index
        return redirect()->route('admin.master-data.users');
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
            'username' => 'required|string|unique:users,username,' . $id . ',user_id',
            'level' => 'required|string|in:admin,guru,siswa,orang_tua,kesiswaan,konselor_bk,kepala_sekolah,wali_kelas'
        ];

        // Add validation based on input mode and level if not admin
        if ($request->level !== 'admin' && $request->has('input_mode')) {
            $validationRules['input_mode'] = 'required|in:existing,new';
            
            if ($request->input_mode === 'existing') {
                if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                    $validationRules['guru_id'] = 'required|exists:guru,guru_id';
                } elseif ($request->level === 'siswa') {
                    $validationRules['siswa_id'] = 'required|exists:siswa,siswa_id';
                } elseif ($request->level === 'orang_tua') {
                    $validationRules['ortu_id'] = 'required|exists:orang_tua,ortu_id';
                }
            } else {
                // New profile validation
                if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                    $validationRules['nama_guru'] = 'required|string|max:100';
                    $validationRules['nip'] = 'required|string|unique:guru,nip';
                    $validationRules['mata_pelajaran'] = 'nullable|string|max:100';
                    $validationRules['no_hp_guru'] = 'nullable|string|max:15';
                } elseif ($request->level === 'siswa') {
                    $validationRules['nama_siswa'] = 'required|string|max:100';
                    $validationRules['nis'] = 'required|string|unique:siswa,nis';
                    $validationRules['kelas_id'] = 'required|exists:kelas,kelas_id';
                    $validationRules['tahun_masuk'] = 'required|integer|min:2000|max:' . (date('Y') + 1);
                    $validationRules['no_hp_siswa'] = 'nullable|string|max:15';
                } elseif ($request->level === 'orang_tua') {
                    $validationRules['nama_orangtua'] = 'required|string|max:100';
                    $validationRules['pekerjaan'] = 'nullable|string|max:100';
                    $validationRules['no_hp_ortu'] = 'nullable|string|max:15';
                    $validationRules['alamat'] = 'nullable|string';
                }
            }
        }

        $request->validate($validationRules);

        try {
            DB::transaction(function () use ($request, $id) {
                $user = User::findOrFail($id);
                
                $userData = [
                    'username' => $request->username,
                    'nama_lengkap' => $request->nama_lengkap,
                    'level' => $request->level,
                    'can_verify' => $request->has('can_verify'),
                    'is_active' => $request->has('is_active'),
                    'guru_id' => null,
                    'siswa_id' => null,
                    'ortu_id' => null
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                // Handle profile linking for non-admin users
                if ($request->level !== 'admin' && $request->has('input_mode')) {
                    if ($request->input_mode === 'existing') {
                        // Link to existing profile
                        if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                            $userData['guru_id'] = $request->guru_id;
                        } elseif ($request->level === 'siswa') {
                            $userData['siswa_id'] = $request->siswa_id;
                        } elseif ($request->level === 'orang_tua') {
                            $userData['ortu_id'] = $request->ortu_id;
                        }
                    } else {
                        // Create new profile first
                        if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                            $guru = Guru::create([
                                'nama_guru' => $request->nama_guru,
                                'nip' => $request->nip,
                                'mata_pelajaran' => $request->mata_pelajaran,
                                'no_hp' => $request->no_hp_guru
                            ]);
                            $userData['guru_id'] = $guru->guru_id;
                        } elseif ($request->level === 'siswa') {
                            $siswa = Siswa::create([
                                'nama_siswa' => $request->nama_siswa,
                                'nis' => $request->nis,
                                'kelas_id' => $request->kelas_id,
                                'tahun_masuk' => $request->tahun_masuk,
                                'no_hp' => $request->no_hp_siswa
                            ]);
                            $userData['siswa_id'] = $siswa->siswa_id;
                        } elseif ($request->level === 'orang_tua') {
                            $orangTua = OrangTua::create([
                                'nama_orangtua' => $request->nama_orangtua,
                                'pekerjaan' => $request->pekerjaan,
                                'no_hp' => $request->no_hp_ortu,
                                'alamat' => $request->alamat
                            ]);
                            $userData['ortu_id'] = $orangTua->ortu_id;
                        }
                    }
                } else {
                    // For admin or legacy update without input_mode
                    if (in_array($request->level, ['guru', 'kesiswaan', 'konselor_bk', 'kepala_sekolah'])) {
                        $userData['guru_id'] = $request->guru_id;
                    } elseif ($request->level === 'siswa') {
                        $userData['siswa_id'] = $request->siswa_id;
                    } elseif ($request->level === 'orang_tua') {
                        $userData['ortu_id'] = $request->ortu_id;
                    }
                }

                $user->update($userData);
            });

            return redirect()->route('admin.master-data.users')->with('success', 'User berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('admin.master-data.users')->with('error', 'Gagal memperbarui user: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.master-data.users')->with('success', 'User berhasil dihapus');
    }
}