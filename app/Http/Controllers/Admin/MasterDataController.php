<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\JenisPrestasi;
use App\Models\JenisSanksi;
use App\Models\OrangTua;

use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index()
    {
        return view('admin.master-data.index');
    }

    // TAHUN AJARAN
    public function tahunAjaran()
    {
        $data = TahunAjaran::all();
        return view('admin.master-data.tahun-ajaran', compact('data'));
    }

    public function storeTahunAjaran(Request $request)
    {
        try {
            $request->validate([
                'kode_tahun' => 'required|unique:tahun_ajaran',
                'tahun_ajaran' => 'required',
                'semester' => 'required|in:Ganjil,Genap'
            ]);

            TahunAjaran::create([
                'kode_tahun' => $request->kode_tahun,
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => $request->semester,
                'status_aktif' => $request->has('status_aktif')
            ]);
            
            return redirect()->back()->with('success', 'Data tahun ajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    // GURU
    public function guru()
    {
        $data = Guru::all();
        return view('admin.master-data.guru', compact('data'));
    }
    
    public function dataGuru()
    {
        $data = Guru::all();
        return view('admin.master-data.guru', compact('data'));
    }

    public function storeGuru(Request $request)
    {
        try {
            $request->validate([
                'nama_guru' => 'required|string|max:100',
                'nip' => 'required|string|unique:guru|max:20',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'email' => 'nullable|email|unique:guru|max:100',
                'no_telp' => 'nullable|string|max:15',
                'bidang_studi' => 'nullable|string|max:50'
            ]);

            Guru::create($request->only([
                'nama_guru', 'nip', 'jenis_kelamin', 'email', 'no_telp', 'bidang_studi'
            ]));
            
            return redirect()->back()->with('success', 'Data guru berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    // KELAS
    public function kelas()
    {
        $data = Kelas::with('waliKelas')->get();
        $guru = Guru::all();
        return view('admin.master-data.kelas', compact('data', 'guru'));
    }
    
    public function dataKelas()
    {
        $data = Kelas::with('waliKelas')->get();
        $guru = Guru::all();
        return view('admin.master-data.kelas', compact('data', 'guru'));
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas',
            'jurusan' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|integer|min:1',
            'wali_kelas_id' => 'nullable|exists:guru,guru_id'
        ]);

        Kelas::create($request->only(['nama_kelas', 'jurusan', 'kapasitas', 'wali_kelas_id']));
        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan');
    }

    // SISWA
    public function siswa()
    {
        $data = Siswa::with('kelas')->get();
        $kelas = Kelas::all();
        return view('admin.master-data.siswa', compact('data', 'kelas'));
    }
    
    public function dataSiswa()
    {
        $data = Siswa::with('kelas')->get();
        $kelas = Kelas::all();
        return view('admin.master-data.siswa', compact('data', 'kelas'));
    }

    public function storeSiswa(Request $request)
    {
        try {
            $request->validate([
                'nis' => 'required|string|unique:siswa|max:20',
                'nisn' => 'nullable|string|unique:siswa|max:20',
                'nama_siswa' => 'required|string|max:100',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'status_kesiswaan' => 'nullable|in:aktif,lulus,pindah,drop_out,cuti',
                'tanggal_lahir' => 'nullable|date',
                'no_telp' => 'nullable|string|max:15',
                'kelas_id' => 'nullable|exists:kelas,kelas_id'
            ]);

            Siswa::create($request->only([
                'nis', 'nisn', 'nama_siswa', 'jenis_kelamin', 'status_kesiswaan',
                'tanggal_lahir', 'tempat_lahir', 'alamat', 'no_telp', 'kelas_id'
            ]));
            
            return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    // JENIS PELANGGARAN
    public function jenisPelanggaran()
    {
        $data = JenisPelanggaran::all();
        return view('admin.master-data.jenis-pelanggaran', compact('data'));
    }

    public function storeJenisPelanggaran(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required',
            'poin' => 'required|integer'
        ]);

        JenisPelanggaran::create($request->all());
        return redirect()->back()->with('success', 'Data jenis pelanggaran berhasil ditambahkan');
    }

    // JENIS PRESTASI
    public function jenisPrestasi()
    {
        $data = JenisPrestasi::all();
        return view('admin.master-data.jenis-prestasi', compact('data'));
    }

    public function storeJenisPrestasi(Request $request)
    {
        $request->validate([
            'nama_prestasi' => 'required',
            'poin' => 'required|integer'
        ]);

        JenisPrestasi::create($request->all());
        return redirect()->back()->with('success', 'Data jenis prestasi berhasil ditambahkan');
    }

    // JENIS SANKSI
    public function jenisSanksi()
    {
        $data = JenisSanksi::all();
        return view('admin.master-data.jenis-sanksi', compact('data'));
    }

    public function storeJenisSanksi(Request $request)
    {
        $request->validate([
            'nama_sanksi' => 'required'
        ]);

        JenisSanksi::create($request->all());
        return redirect()->back()->with('success', 'Data jenis sanksi berhasil ditambahkan');
    }



    // EDIT & DELETE METHODS
    public function editSiswa($id)
    {
        $data = Siswa::findOrFail($id);
        $kelas = Kelas::all();
        return response()->json(['data' => $data, 'kelas' => $kelas]);
    }

    public function updateSiswa(Request $request, $id)
    {
        $jenisKelamin = $request->jenis_kelamin;
        if (is_null($jenisKelamin) || $jenisKelamin === '' || $jenisKelamin === 'null') {
            $jenisKelamin = 'Laki-laki';
        }
        
        \DB::table('siswa')
            ->where('siswa_id', $id)
            ->update([
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'nama_siswa' => $request->nama_siswa,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'kelas_id' => $request->kelas_id,
                'status_kesiswaan' => $request->status_kesiswaan ?: 'aktif',
                'updated_at' => now()
            ]);
        
        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroySiswa($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);
            
            // Cek apakah siswa memiliki data terkait
            $pelanggaranCount = \App\Models\Pelanggaran::where('siswa_id', $id)->count();
            $prestasiCount = \App\Models\Prestasi::where('siswa_id', $id)->count();
            $orangTuaCount = \App\Models\OrangTua::where('siswa_id', $id)->count();
            
            if ($pelanggaranCount > 0 || $prestasiCount > 0 || $orangTuaCount > 0) {
                $message = 'Siswa tidak dapat dihapus karena masih memiliki data terkait: ';
                $details = [];
                if ($pelanggaranCount > 0) $details[] = "$pelanggaranCount pelanggaran";
                if ($prestasiCount > 0) $details[] = "$prestasiCount prestasi";
                if ($orangTuaCount > 0) $details[] = "$orangTuaCount data orang tua";
                $message .= implode(', ', $details) . '. Hapus data terkait terlebih dahulu.';
                
                return redirect()->back()->with('error', $message);
            }
            
            $siswa->delete();
            return redirect()->back()->with('success', 'Data siswa berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }

    public function editGuru($id)
    {
        $data = Guru::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function updateGuru(Request $request, $id)
    {
        $request->validate([
            'nama_guru' => 'required',
            'nip' => 'required|unique:guru,nip,' . $id . ',guru_id',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'email' => 'nullable|email|unique:guru,email,' . $id . ',guru_id',
            'no_telp' => 'nullable|string|max:15'
        ]);

        $guru = Guru::findOrFail($id);
        $updateData = $request->only([
            'nama_guru', 'nip', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
            'alamat', 'no_telp', 'email', 'bidang_studi', 'status'
        ]);
        
        // Pastikan jenis_kelamin tidak null
        if (empty($updateData['jenis_kelamin'])) {
            $updateData['jenis_kelamin'] = $guru->jenis_kelamin;
        }
        
        $guru->update($updateData);
        return redirect()->back()->with('success', 'Data guru berhasil diperbarui');
    }

    public function destroyGuru($id)
    {
        try {
            $guru = Guru::findOrFail($id);
            
            // Cek apakah guru memiliki data terkait
            $kelasCount = \App\Models\Kelas::where('wali_kelas_id', $id)->count();
            $pelanggaranCount = \App\Models\Pelanggaran::where('guru_pencatat', $id)->count();
            $prestasiCount = \App\Models\Prestasi::where('guru_pencatat', $id)->count();
            $bkCount = \App\Models\BimbinganKonseling::where('guru_konselor', $id)->count();
            $sanksiCount = \App\Models\Sanksi::where('guru_penanggungjawab', $id)->count();
            $userCount = \App\Models\User::where('guru_id', $id)->count();
            
            if ($kelasCount > 0 || $pelanggaranCount > 0 || $prestasiCount > 0 || $bkCount > 0 || $sanksiCount > 0 || $userCount > 0) {
                $message = 'Guru tidak dapat dihapus karena masih memiliki data terkait: ';
                $details = [];
                if ($kelasCount > 0) $details[] = "wali kelas di $kelasCount kelas";
                if ($pelanggaranCount > 0) $details[] = "pencatat $pelanggaranCount pelanggaran";
                if ($prestasiCount > 0) $details[] = "pencatat $prestasiCount prestasi";
                if ($bkCount > 0) $details[] = "konselor di $bkCount bimbingan";
                if ($sanksiCount > 0) $details[] = "penanggung jawab $sanksiCount sanksi";
                if ($userCount > 0) $details[] = "$userCount akun user";
                $message .= implode(', ', $details) . '. Hapus data terkait terlebih dahulu.';
                
                return redirect()->back()->with('error', $message);
            }
            
            $guru->delete();
            return redirect()->back()->with('success', 'Data guru berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }
    }

    public function editKelas($id)
    {
        $data = Kelas::findOrFail($id);
        $guru = Guru::all();
        return response()->json(['data' => $data, 'guru' => $guru]);
    }

    public function updateKelas(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $id . ',kelas_id',
            'jurusan' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|integer|min:1',
            'wali_kelas_id' => 'nullable|exists:guru,guru_id'
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->only(['nama_kelas', 'jurusan', 'kapasitas', 'wali_kelas_id']));
        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui');
    }

    public function destroyKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->back()->with('success', 'Data kelas berhasil dihapus');
    }

    public function editJenisPelanggaran($id)
    {
        $data = JenisPelanggaran::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function updateJenisPelanggaran(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggaran' => 'required',
            'poin' => 'required|integer'
        ]);

        $jenisPelanggaran = JenisPelanggaran::findOrFail($id);
        $jenisPelanggaran->update($request->all());
        return redirect()->back()->with('success', 'Data jenis pelanggaran berhasil diperbarui');
    }

    public function destroyJenisPelanggaran($id)
    {
        $jenisPelanggaran = JenisPelanggaran::findOrFail($id);
        $jenisPelanggaran->delete();
        return redirect()->back()->with('success', 'Data jenis pelanggaran berhasil dihapus');
    }

    public function editJenisPrestasi($id)
    {
        $data = JenisPrestasi::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function updateJenisPrestasi(Request $request, $id)
    {
        $request->validate([
            'nama_prestasi' => 'required',
            'poin' => 'required|integer'
        ]);

        $jenisPrestasi = JenisPrestasi::findOrFail($id);
        $jenisPrestasi->update($request->all());
        return redirect()->back()->with('success', 'Data jenis prestasi berhasil diperbarui');
    }

    public function destroyJenisPrestasi($id)
    {
        $jenisPrestasi = JenisPrestasi::findOrFail($id);
        $jenisPrestasi->delete();
        return redirect()->back()->with('success', 'Data jenis prestasi berhasil dihapus');
    }

    // JENIS SANKSI CRUD
    public function editJenisSanksi($id)
    {
        $data = JenisSanksi::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function updateJenisSanksi(Request $request, $id)
    {
        $request->validate([
            'nama_sanksi' => 'required'
        ]);

        $jenisSanksi = JenisSanksi::findOrFail($id);
        $jenisSanksi->update($request->all());
        return redirect()->back()->with('success', 'Data jenis sanksi berhasil diperbarui');
    }

    public function destroyJenisSanksi($id)
    {
        $jenisSanksi = JenisSanksi::findOrFail($id);
        $jenisSanksi->delete();
        return redirect()->back()->with('success', 'Data jenis sanksi berhasil dihapus');
    }

    // TAHUN AJARAN CRUD
    public function editTahunAjaran($id)
    {
        $data = TahunAjaran::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function updateTahunAjaran(Request $request, $id)
    {
        $request->validate([
            'kode_tahun' => 'required|unique:tahun_ajaran,kode_tahun,' . $id . ',tahun_ajaran_id',
            'tahun_ajaran' => 'required',
            'semester' => 'required'
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif');
        $tahunAjaran->update($data);
        return redirect()->back()->with('success', 'Data tahun ajaran berhasil diperbarui');
    }

    public function destroyTahunAjaran($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();
        return redirect()->back()->with('success', 'Data tahun ajaran berhasil dihapus');
    }

    // ORANG TUA CRUD
    public function dataOrangTua(Request $request)
    {
        $query = OrangTua::with('siswa.kelas');
        
        // Filter berdasarkan hubungan
        if ($request->filled('hubungan')) {
            $query->where('hubungan', $request->hubungan);
        }
        
        // Filter berdasarkan kelas siswa
        if ($request->filled('kelas')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas);
            });
        }
        
        // Filter berdasarkan pencarian nama
        if ($request->filled('search')) {
            $query->where('nama_orangtua', 'like', '%' . $request->search . '%');
        }
        
        $data = $query->get();
        $siswa = Siswa::with('kelas')->get();
        $kelas = Kelas::all();
        
        return view('admin.master-data.orang-tua', compact('data', 'siswa', 'kelas'));
    }

    public function storeOrangTua(Request $request)
    {
        $request->validate([
            'nama_orangtua' => 'required',
            'hubungan' => 'required|in:Ayah,Ibu,Wali',
            'siswa_id' => 'required|exists:siswa,siswa_id'
        ]);

        OrangTua::create($request->only([
            'nama_orangtua', 'hubungan', 'siswa_id', 
            'pekerjaan', 'pendidikan', 'no_telp', 'alamat'
        ]));
        return redirect()->back()->with('success', 'Data orang tua berhasil ditambahkan');
    }

    public function editOrangTua($id)
    {
        $data = OrangTua::findOrFail($id);
        $siswa = Siswa::with('kelas')->get();
        return response()->json(['data' => $data, 'siswa' => $siswa]);
    }

    public function updateOrangTua(Request $request, $id)
    {
        $request->validate([
            'nama_orangtua' => 'required',
            'hubungan' => 'required|in:Ayah,Ibu,Wali',
            'siswa_id' => 'required|exists:siswa,siswa_id'
        ]);

        $orangTua = OrangTua::findOrFail($id);
        $orangTua->update($request->only([
            'nama_orangtua', 'hubungan', 'siswa_id', 'pekerjaan',
            'pendidikan', 'no_telp', 'alamat'
        ]));
        return redirect()->back()->with('success', 'Data orang tua berhasil diperbarui');
    }

    public function destroyOrangTua($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $orangTua->delete();
        return redirect()->back()->with('success', 'Data orang tua berhasil dihapus');
    }
}