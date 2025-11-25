<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Models\PelaksanaanSanksi;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SanksiController extends Controller
{
    public function index()
    {
        $sanksi = Sanksi::with(['pelanggaran.siswa.kelas', 'pelanggaran.jenisPelanggaran'])->latest()->get();
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])->whereDoesntHave('sanksi')->get();
        
        return view('admin.sanksi.index', compact('sanksi', 'pelanggaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggaran_id' => 'required|exists:pelanggaran,pelanggaran_id|unique:sanksi,pelanggaran_id',
            'jenis_sanksi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'deskripsi_sanksi' => 'required|string',
            'status' => 'required|in:Terdaftar,Diproses,Selesai,Tindak_lanjut',
            'guru_penanggungjawab' => 'required|exists:guru,guru_id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $request->all();
                
                // Cek apakah guru penanggung jawab adalah konselor BK
                $guru = Guru::find($request->guru_penanggungjawab);
                $bkUser = User::where('level', 'konselor_bk')->first();
                
                if ($guru && (stripos($guru->jabatan, 'konselor') !== false || stripos($guru->jabatan, 'bk') !== false)) {
                    $data['assigned_to_bk'] = true;
                    $data['followup_status'] = 'pending';
                    $data['bk_user_id'] = $bkUser ? $bkUser->user_id : null;
                }
                
                $sanksi = Sanksi::create($data);
                
                // Kirim notifikasi jika assigned ke BK
                if ($sanksi->assigned_to_bk && $bkUser) {
                    Notification::create([
                        'type' => 'sanksi_followup',
                        'user_id' => $bkUser->user_id,
                        'sanksi_id' => $sanksi->sanksi_id,
                        'title' => 'Notifikasi Follow-up Sanksi',
                        'message' => 'Siswa ' . ($sanksi->siswa->nama_siswa ?? 'N/A') . ' memerlukan follow-up BK untuk sanksi pelanggaran ' . ($sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A')
                    ]);
                }
            });
            
            return redirect()->back()->with('success', 'Data sanksi berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $sanksi = Sanksi::with(['pelanggaran.siswa.kelas'])->findOrFail($id);
        $pelanggaran = Pelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])->get();
        
        return response()->json([
            'sanksi' => $sanksi,
            'pelanggaran' => $pelanggaran
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pelanggaran_id' => 'required|exists:pelanggaran,pelanggaran_id|unique:sanksi,pelanggaran_id,' . $id . ',sanksi_id',
            'jenis_sanksi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'deskripsi_sanksi' => 'required|string',
            'status' => 'required|in:Terdaftar,Diproses,Selesai,Tindak_lanjut'
        ]);

        try {
            $sanksi = Sanksi::findOrFail($id);
            $sanksi->update($request->all());
            return redirect()->back()->with('success', 'Data sanksi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $sanksi = Sanksi::findOrFail($id);
                
                // Hapus pelaksanaan sanksi terlebih dahulu
                PelaksanaanSanksi::where('sanksi_id', $id)->delete();
                
                // Hapus notifikasi terkait
                if (class_exists('App\Models\Notification')) {
                    \App\Models\Notification::where('sanksi_id', $id)->delete();
                }
                
                // Hapus bimbingan konseling terkait
                if (class_exists('App\Models\BimbinganKonseling')) {
                    \App\Models\BimbinganKonseling::where('sanksi_id', $id)->delete();
                }
                
                // Hapus sanksi
                $sanksi->delete();
            });
            
            return redirect()->back()->with('success', 'Data sanksi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function pelaksanaan()
    {
        $pelaksanaanSanksi = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas'])->latest()->get();
        $sanksi = Sanksi::with(['pelanggaran.siswa.kelas'])->where('status', '!=', 'Selesai')->get();
        
        return view('admin.sanksi.pelaksanaan', compact('pelaksanaanSanksi', 'sanksi'));
    }

    public function storePelaksanaan(Request $request)
    {
        $request->validate([
            'sanksi_id' => 'required|exists:sanksi,sanksi_id',
            'tanggal_pelaksanaan' => 'required|date',
            'deskripsi_pelaksanaan' => 'required|string',
            'status_pelaksanaan' => 'required|in:terlaksana,tidak_terlaksana,sebagian',
            'catatan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $data = $request->all();
            
            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pelaksanaan_sanksi'), $filename);
                $data['bukti_foto'] = $filename;
            }

            DB::transaction(function () use ($data, $request) {
                PelaksanaanSanksi::create($data);
                
                // Update status sanksi jika pelaksanaan terlaksana
                if ($request->status_pelaksanaan === 'terlaksana') {
                    Sanksi::find($request->sanksi_id)->update(['status' => 'Selesai']);
                }
            });

            return redirect()->back()->with('success', 'Data pelaksanaan sanksi berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    public function editPelaksanaan($id)
    {
        $pelaksanaan = PelaksanaanSanksi::with(['sanksi.pelanggaran.siswa.kelas'])->findOrFail($id);
        $sanksi = Sanksi::with(['pelanggaran.siswa.kelas'])->get();
        
        return response()->json([
            'pelaksanaan' => $pelaksanaan,
            'sanksi' => $sanksi
        ]);
    }

    public function updatePelaksanaan(Request $request, $id)
    {
        $request->validate([
            'sanksi_id' => 'required|exists:sanksi,sanksi_id',
            'tanggal_pelaksanaan' => 'required|date',
            'deskripsi_pelaksanaan' => 'required|string',
            'status_pelaksanaan' => 'required|in:terlaksana,tidak_terlaksana,sebagian',
            'catatan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $pelaksanaan = PelaksanaanSanksi::findOrFail($id);
            $data = $request->all();
            
            if ($request->hasFile('bukti_foto')) {
                // Delete old file
                if ($pelaksanaan->bukti_foto && file_exists(public_path('uploads/pelaksanaan_sanksi/' . $pelaksanaan->bukti_foto))) {
                    unlink(public_path('uploads/pelaksanaan_sanksi/' . $pelaksanaan->bukti_foto));
                }
                
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pelaksanaan_sanksi'), $filename);
                $data['bukti_foto'] = $filename;
            }

            DB::transaction(function () use ($pelaksanaan, $data, $request) {
                $pelaksanaan->update($data);
                
                // Update status sanksi
                if ($request->status_pelaksanaan === 'terlaksana') {
                    Sanksi::find($request->sanksi_id)->update(['status' => 'Selesai']);
                } else {
                    Sanksi::find($request->sanksi_id)->update(['status' => 'Diproses']);
                }
            });

            return redirect()->back()->with('success', 'Data pelaksanaan sanksi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroyPelaksanaan($id)
    {
        try {
            $pelaksanaan = PelaksanaanSanksi::findOrFail($id);
            
            // Delete file if exists
            if ($pelaksanaan->bukti_foto && file_exists(public_path('uploads/pelaksanaan_sanksi/' . $pelaksanaan->bukti_foto))) {
                unlink(public_path('uploads/pelaksanaan_sanksi/' . $pelaksanaan->bukti_foto));
            }
            
            // Reset sanksi status
            Sanksi::find($pelaksanaan->sanksi_id)->update(['status' => 'Terdaftar']);
            
            $pelaksanaan->delete();
            return redirect()->back()->with('success', 'Data pelaksanaan sanksi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}