<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\JenisPelanggaran;
use App\Models\JenisPrestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputDataController extends Controller
{
    public function pelanggaran()
    {
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'guru', 'jenisPelanggaran'])->latest()->get();
        $siswa = Siswa::with('kelas')->get();
        $guru = Guru::all();
        $jenisPelanggaran = JenisPelanggaran::all();
        
        return view('admin.input-data.pelanggaran', compact('pelanggaran', 'siswa', 'guru', 'jenisPelanggaran'));
    }

    public function storePelanggaran(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'guru_id' => 'required|exists:guru,guru_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tanggal_pelanggaran' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $data = $request->all();
            
            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pelanggaran'), $filename);
                $data['bukti_foto'] = $filename;
            }

            Pelanggaran::create($data);
            return redirect()->back()->with('success', 'Data pelanggaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    public function editPelanggaran($id)
    {
        $pelanggaran = Pelanggaran::with(['siswa', 'guru', 'jenisPelanggaran'])->findOrFail($id);
        $siswa = Siswa::with('kelas')->get();
        $guru = Guru::all();
        $jenisPelanggaran = JenisPelanggaran::all();
        
        return response()->json([
            'pelanggaran' => $pelanggaran,
            'siswa' => $siswa,
            'guru' => $guru,
            'jenisPelanggaran' => $jenisPelanggaran
        ]);
    }

    public function updatePelanggaran(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'guru_id' => 'required|exists:guru,guru_id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,jenis_pelanggaran_id',
            'tanggal_pelanggaran' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $pelanggaran = Pelanggaran::findOrFail($id);
            $data = $request->all();
            
            if ($request->hasFile('bukti_foto')) {
                // Delete old file
                if ($pelanggaran->bukti_foto && file_exists(public_path('uploads/pelanggaran/' . $pelanggaran->bukti_foto))) {
                    unlink(public_path('uploads/pelanggaran/' . $pelanggaran->bukti_foto));
                }
                
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pelanggaran'), $filename);
                $data['bukti_foto'] = $filename;
            }

            $pelanggaran->update($data);
            return redirect()->back()->with('success', 'Data pelanggaran berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroyPelanggaran($id)
    {
        try {
            $pelanggaran = Pelanggaran::findOrFail($id);
            
            // Delete file if exists
            if ($pelanggaran->bukti_foto && file_exists(public_path('uploads/pelanggaran/' . $pelanggaran->bukti_foto))) {
                unlink(public_path('uploads/pelanggaran/' . $pelanggaran->bukti_foto));
            }
            
            $pelanggaran->delete();
            return redirect()->back()->with('success', 'Data pelanggaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function prestasi()
    {
        $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi'])->latest()->get();
        $siswa = Siswa::with('kelas')->get();
        $jenisPrestasi = JenisPrestasi::all();
        
        return view('admin.input-data.prestasi', compact('prestasi', 'siswa', 'jenisPrestasi'));
    }

    public function storePrestasi(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_prestasi_id' => 'required|exists:jenis_prestasi,jenis_prestasi_id',
            'tanggal_prestasi' => 'required|date',
            'deskripsi' => 'required|string',
            'tingkat' => 'required|in:sekolah,kecamatan,kabupaten,provinsi,nasional,internasional',
            'peringkat' => 'nullable|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $data = $request->all();
            
            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/prestasi'), $filename);
                $data['bukti_foto'] = $filename;
            }

            Prestasi::create($data);
            return redirect()->back()->with('success', 'Data prestasi berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    public function editPrestasi($id)
    {
        $prestasi = Prestasi::with(['siswa', 'jenisPrestasi'])->findOrFail($id);
        $siswa = Siswa::with('kelas')->get();
        $jenisPrestasi = JenisPrestasi::all();
        
        return response()->json([
            'prestasi' => $prestasi,
            'siswa' => $siswa,
            'jenisPrestasi' => $jenisPrestasi
        ]);
    }

    public function updatePrestasi(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_prestasi_id' => 'required|exists:jenis_prestasi,jenis_prestasi_id',
            'tanggal_prestasi' => 'required|date',
            'deskripsi' => 'required|string',
            'tingkat' => 'required|in:sekolah,kecamatan,kabupaten,provinsi,nasional,internasional',
            'peringkat' => 'nullable|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $prestasi = Prestasi::findOrFail($id);
            $data = $request->all();
            
            if ($request->hasFile('bukti_foto')) {
                // Delete old file
                if ($prestasi->bukti_foto && file_exists(public_path('uploads/prestasi/' . $prestasi->bukti_foto))) {
                    unlink(public_path('uploads/prestasi/' . $prestasi->bukti_foto));
                }
                
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/prestasi'), $filename);
                $data['bukti_foto'] = $filename;
            }

            $prestasi->update($data);
            return redirect()->back()->with('success', 'Data prestasi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroyPrestasi($id)
    {
        try {
            $prestasi = Prestasi::findOrFail($id);
            
            // Delete file if exists
            if ($prestasi->bukti_foto && file_exists(public_path('uploads/prestasi/' . $prestasi->bukti_foto))) {
                unlink(public_path('uploads/prestasi/' . $prestasi->bukti_foto));
            }
            
            $prestasi->delete();
            return redirect()->back()->with('success', 'Data prestasi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}