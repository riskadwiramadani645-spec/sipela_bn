<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\BimbinganKonseling;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\Sanksi;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BimbinganKonselingController extends Controller
{
    public function index()
    {
        $data = BimbinganKonseling::with(['siswa.kelas', 'guruKonselor', 'sanksi'])
            ->latest()
            ->get();
            
        $siswa = Siswa::with('kelas')->get();
        $guru = Guru::where('jabatan', 'like', '%konselor%')
            ->orWhere('jabatan', 'like', '%bk%')
            ->get();
        $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        
        // Sanksi yang perlu follow-up
        $sanksiFollowup = Sanksi::with(['siswa.kelas', 'pelanggaran.jenisPelanggaran'])
            ->where('assigned_to_bk', true)
            ->where('followup_status', 'pending')
            ->get();
        
        return view('kesiswaan.bimbingan-konseling.index', compact(
            'data', 'siswa', 'guru', 'tahunAjaran', 'sanksiFollowup'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_layanan' => 'required|string',
            'topik' => 'required|string',
            'keluhan_masalah' => 'required|string',
            'tindakan_solusi' => 'required|string',
            'tanggal_konseling' => 'required|date',
            'sanksi_id' => 'nullable|exists:sanksi,sanksi_id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $request->all();
                $user = session('user');
                $data['guru_konselor'] = $user->guru_id ?? 1; // Default guru konselor
                $data['tahun_ajaran_id'] = TahunAjaran::where('status', 'aktif')->first()->tahun_ajaran_id ?? 1;
                $data['status'] = 'selesai';
                
                if ($request->sanksi_id) {
                    $data['is_followup'] = true;
                }
                
                $bk = BimbinganKonseling::create($data);
                
                // Jika ini follow-up sanksi
                if ($request->sanksi_id) {
                    $sanksi = Sanksi::find($request->sanksi_id);
                    $sanksi->update(['followup_status' => 'completed']);
                    
                    // Kirim notifikasi ke kesiswaan
                    $kesiswaan = User::where('level', 'kesiswaan')->get();
                    foreach ($kesiswaan as $user) {
                        Notification::create([
                            'type' => 'bk_completed',
                            'user_id' => $user->id,
                            'sanksi_id' => $sanksi->sanksi_id,
                            'title' => 'Follow-up Sanksi Selesai',
                            'message' => 'BK telah melakukan ' . $request->jenis_layanan . ' untuk siswa ' . ($sanksi->siswa->nama_siswa ?? 'N/A') . ' pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling))
                        ]);
                    }
                    
                    // Kirim notifikasi ke siswa
                    $siswaUser = User::where('siswa_id', $request->siswa_id)->first();
                    if ($siswaUser) {
                        Notification::create([
                            'type' => 'bk_siswa',
                            'user_id' => $siswaUser->id,
                            'sanksi_id' => $sanksi->sanksi_id,
                            'title' => 'Pemanggilan BK',
                            'message' => 'Anda dipanggil ke ruang BK untuk ' . $request->jenis_layanan . ' terkait follow-up sanksi pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling)) . '. Harap datang tepat waktu.'
                        ]);
                    }
                    
                    // Mark notification as read
                    $user = session('user');
                    if ($user && $user->id) {
                        Notification::where('sanksi_id', $request->sanksi_id)
                            ->where('user_id', $user->id)
                            ->where('type', 'sanksi_followup')
                            ->update(['is_read' => true]);
                    }
                }
            });
            
            return redirect()->back()->with('success', 'Data bimbingan konseling berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    public function followup($sanksiId)
    {
        $sanksi = Sanksi::with(['siswa.kelas', 'pelanggaran.jenisPelanggaran'])
            ->findOrFail($sanksiId);
            
        return response()->json([
            'sanksi' => $sanksi,
            'siswa' => $sanksi->siswa
        ]);
    }

    public function edit($id)
    {
        $bk = BimbinganKonseling::with(['siswa.kelas'])->findOrFail($id);
        return response()->json($bk);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'jenis_layanan' => 'required|string',
            'topik' => 'required|string',
            'keluhan_masalah' => 'required|string',
            'tindakan_solusi' => 'required|string',
            'tanggal_konseling' => 'required|date'
        ]);

        try {
            $bk = BimbinganKonseling::findOrFail($id);
            $bk->update($request->all());
            
            return redirect()->back()->with('success', 'Data bimbingan konseling berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $bk = BimbinganKonseling::findOrFail($id);
            $bk->delete();
            
            return redirect()->back()->with('success', 'Data bimbingan konseling berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}