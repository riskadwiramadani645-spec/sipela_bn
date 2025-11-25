<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\JenisPrestasi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    public function index()
    {
        $user = session('user');
        $currentPrefix = request()->route()->getPrefix();
        
        // Role-based data filtering
        if ($currentPrefix === 'admin' || $user->level === 'admin') {
            // Admin: Full access
            $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])
                               ->latest()
                               ->get();
        } elseif ($currentPrefix === 'kesiswaan' || $user->level === 'kesiswaan') {
            // Kesiswaan: Only verified data
            $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])
                               ->where('status_verifikasi', '!=', 'draft')
                               ->latest()
                               ->get();
        } else {
            // Other roles: Limited access
            $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])
                               ->where('status_verifikasi', 'terverifikasi')
                               ->latest()
                               ->get();
        }
        
        return view('admin.view-data.prestasi', ['data' => $prestasi]);
    }

    public function create()
    {
        $siswa = Siswa::with('kelas')->get();
        $jenisPrestasi = JenisPrestasi::all();
        return view('admin.input-data.prestasi', compact('siswa', 'jenisPrestasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_prestasi_id' => 'required|exists:jenis_prestasi,jenis_prestasi_id',
            'tingkat' => 'required|in:Sekolah,Kabupaten,Provinsi,Nasional,Internasional',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $jenisPrestasi = JenisPrestasi::find($request->jenis_prestasi_id);
        
        $user = session('user');
        $statusVerifikasi = in_array($user->level, ['admin', 'kesiswaan']) ? 'diverifikasi' : 'menunggu';
        
        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_prestasi_id' => $request->jenis_prestasi_id,
            'tingkat' => $request->tingkat,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'poin' => $jenisPrestasi->poin,
            'guru_pencatat' => session('user')->guru_id ?? session('user')->user_id,
            'status_verifikasi' => $statusVerifikasi
        ];
        
        // Handle file upload
        if ($request->hasFile('bukti_dokumen')) {
            $file = $request->file('bukti_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/prestasi'), $filename);
            $data['bukti_dokumen'] = 'prestasi/' . $filename;
        }
        
        Prestasi::create($data);
        
        $currentPrefix = request()->route()->getPrefix();
        
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan');
        }
        
        return redirect()->route('admin.view-data.prestasi')->with('success', 'Prestasi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'tahunAjaran'])->findOrFail($id);
        $siswa = Siswa::with('kelas')->get();
        $jenisPrestasi = JenisPrestasi::all();
        
        return response()->json([
            'prestasi' => $prestasi,
            'siswa' => $siswa,
            'jenisPrestasi' => $jenisPrestasi
        ]);
    }

    public function show($id)
    {
        $prestasi = Prestasi::with(['siswa.kelas', 'jenisPrestasi', 'guruPencatat'])->findOrFail($id);
        return view('admin.view-data.prestasi-detail', compact('prestasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_prestasi_id' => 'required|exists:jenis_prestasi,jenis_prestasi_id',
            'tingkat' => 'required|in:Sekolah,Kabupaten,Provinsi,Nasional,Internasional',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'bukti_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $prestasi = Prestasi::findOrFail($id);
        $jenisPrestasi = JenisPrestasi::find($request->jenis_prestasi_id);
        
        $data = [
            'siswa_id' => $request->siswa_id,
            'jenis_prestasi_id' => $request->jenis_prestasi_id,
            'tingkat' => $request->tingkat,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'poin' => $jenisPrestasi->poin
        ];
        
        // Handle file upload
        if ($request->hasFile('bukti_dokumen')) {
            // Delete old file if exists
            if ($prestasi->bukti_dokumen && file_exists(public_path('uploads/' . $prestasi->bukti_dokumen))) {
                unlink(public_path('uploads/' . $prestasi->bukti_dokumen));
            }
            
            $file = $request->file('bukti_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/prestasi'), $filename);
            $data['bukti_dokumen'] = 'prestasi/' . $filename;
        }
        
        $prestasi->update($data);
        
        $currentPrefix = request()->route()->getPrefix();
        
        if ($currentPrefix === 'kesiswaan') {
            return redirect()->route('kesiswaan.prestasi.index')->with('success', 'Prestasi berhasil diperbarui');
        }
        
        return redirect()->route('admin.view-data.prestasi')->with('success', 'Prestasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->delete();
        
        return redirect()->back()->with('success', 'Prestasi berhasil dihapus');
    }
}