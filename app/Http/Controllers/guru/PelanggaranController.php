<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\Guru;

class PelanggaranController extends Controller
{
    public function inputPelanggaran()
    {
        $user = session('user');
        $guru = $user->guru;
        $isWaliKelas = $guru && $guru->kelas()->exists();
        
        // Semua guru bisa input untuk semua siswa
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        
        $jenisPelanggaran = JenisPelanggaran::all();
        $tahunAjaran = \App\Models\TahunAjaran::all();
        
        return view('guru.input-pelanggaran', compact('siswa', 'jenisPelanggaran', 'tahunAjaran', 'isWaliKelas'));
    }

    public function storePelanggaran(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:5120'
        ]);

        $user = session('user');
        
        // Validasi user memiliki guru_id
        if (!$user->guru_id) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan data guru. Silakan hubungi admin.');
        }
        
        // Gunakan guru_id dari user yang login
        $guruId = $user->guru_id;
        $guru = $user->guru;
        
        $tahunAjaran = \App\Models\TahunAjaran::first();
        $tahunAjaranId = $tahunAjaran ? $tahunAjaran->tahun_ajaran_id : 1;
        
        $jenisPelanggaran = JenisPelanggaran::find($request->jenis_pelanggaran_id);
        $poin = $jenisPelanggaran ? $jenisPelanggaran->poin : 0;
        
        // Tentukan nama pencatat
        $pencatatNama = $guru ? $guru->nama_guru : 'Guru';
        
        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'guru_pencatat' => $guruId,
            'catatan_verifikasi' => 'Pencatat: ' . $pencatatNama,
            'tahun_ajaran_id' => $tahunAjaranId,
            'poin' => $poin,
            'status_verifikasi' => 'menunggu'
        ];
        
        // Handle file upload
        if ($request->hasFile('bukti_foto')) {
            $data['bukti_foto'] = $request->file('bukti_foto')->store('pelanggaran', 'public');
        }

        try {
            $pelanggaran = Pelanggaran::create($data);
            
            return redirect()->route('guru.data-pelanggaran')->with('success', 'Pelanggaran berhasil ditambahkan, menunggu verifikasi admin');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pelanggaran: ' . $e->getMessage())->withInput();
        }
    }

    public function dataPelanggaran()
    {
        $user = session('user');
        $guru = $user->guru;
        $guruId = $user->guru_id;
        $isWaliKelas = $guru && $guru->kelas()->exists();
        
        $query = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
            ->where('guru_pencatat', $guruId);
        
        $filter = null; // Tidak ada filter toggle
        
        // Filter berdasarkan tingkat
        if (request('tingkat')) {
            $query->whereHas('siswa.kelas', function($q) {
                $tingkat = request('tingkat');
                $q->where('nama_kelas', 'LIKE', $tingkat . '%');
            });
        }
        
        // Filter berdasarkan jurusan
        if (request('jurusan')) {
            $query->whereHas('siswa.kelas', function($q) {
                $jurusan = request('jurusan');
                $q->where('nama_kelas', 'LIKE', '%' . $jurusan . '%');
            });
        }
        
        // Filter berdasarkan status
        if (request('status')) {
            $query->where('status_verifikasi', request('status'));
        }
        
        $data = $query->latest()->get();
        
        // Data untuk filter
        $tingkatList = \App\Models\Kelas::select('nama_kelas')
            ->get()
            ->map(function($kelas) {
                return substr($kelas->nama_kelas, 0, strpos($kelas->nama_kelas, ' ') ?: 1);
            })
            ->unique()
            ->sort()
            ->values();
            
        $jurusanList = \App\Models\Kelas::select('nama_kelas')
            ->get()
            ->map(function($kelas) {
                $parts = explode(' ', $kelas->nama_kelas);
                return count($parts) > 1 ? $parts[1] : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
        
        return view('guru.data-pelanggaran', compact('data', 'isWaliKelas', 'filter', 'tingkatList', 'jurusanList'));
    }

    public function show($id)
    {
        $user = session('user');
        $guru = $user->guru;
        $guruId = $user->guru_id;
        
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'guruPencatat'])
            ->where('pelanggaran_id', $id)
            ->where('guru_pencatat', $guruId)
            ->firstOrFail();
        
        return response()->json([
            'pelanggaran' => $pelanggaran,
            'siswa_nama' => $pelanggaran->siswa->nama_siswa ?? '-',
            'kelas_nama' => $pelanggaran->siswa->kelas->nama_kelas ?? '-',
            'jenis_pelanggaran' => $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-',
            'tanggal' => $pelanggaran->tanggal ? \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') : '-',
            'poin' => $pelanggaran->poin,
            'status' => ucfirst($pelanggaran->status_verifikasi),
            'keterangan' => $pelanggaran->keterangan,
            'bukti_foto' => $pelanggaran->bukti_foto
        ]);
    }
}