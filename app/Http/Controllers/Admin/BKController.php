<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BimbinganKonseling;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;

class BKController extends Controller
{
    public function index()
    {
        $bk = BimbinganKonseling::with(['siswa.kelas', 'guruKonselor'])->latest()->get();
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        return view('admin.input-data.bk', compact('bk', 'siswa'));
    }

    public function create()
    {
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $bk = BimbinganKonseling::with(['siswa.kelas', 'guruKonselor'])->latest()->get();
        return view('admin.input-data.bk', compact('siswa', 'bk'));
    }

    public function store(Request $request)
    {
        $mode = $request->form_mode;
        
        // Handle follow-up sanksi notification
        if ($request->has('is_followup') && $request->notification_id) {
            $user = session('user');
            \App\Models\Notification::where('id', $request->notification_id)
                ->where('user_id', $user->user_id)
                ->update(['is_read' => true]);
        }
        
        if ($mode === 'sebelum') {
            // Mode panggil siswa - hanya validasi basic
            $request->validate([
                'siswa_id' => 'required|exists:siswa,siswa_id',
                'topik' => 'required|string|max:255',
                'jenis_layanan' => 'required|string',
                'tanggal_konseling' => 'required|date'
            ]);
            
            $user = session('user');
            
            $bk = BimbinganKonseling::create([
                'siswa_id' => $request->siswa_id,
                'topik' => $request->topik,
                'jenis_layanan' => $request->jenis_layanan,
                'tanggal_konseling' => $request->tanggal_konseling,
                'status' => 'terdaftar',
                'guru_konselor' => $user->guru_id ?? 1,
                'tahun_ajaran_id' => 1
            ]);
            
            // Kirim notifikasi ke siswa (jika punya akun user)
            $siswa = \App\Models\Siswa::find($request->siswa_id);
            $userSiswa = \App\Models\User::where('siswa_id', $siswa->siswa_id)->first();
            \Log::info('BK Notification Debug', [
                'siswa_id' => $siswa->siswa_id,
                'user_found' => $userSiswa ? 'Yes' : 'No',
                'user_id' => $userSiswa->user_id ?? 'N/A'
            ]);
            if ($userSiswa && $userSiswa->user_id) {
                \App\Models\Notification::create([
                    'type' => 'bk_panggilan',
                    'user_id' => $userSiswa->user_id,
                    'title' => 'Panggilan Konseling BK',
                    'message' => 'Anda dipanggil untuk konseling BK dengan topik: ' . $request->topik . ' pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling))
                ]);
                \Log::info('Notification sent to user_id: ' . $userSiswa->user_id);
            } else {
                \Log::warning('No user found for siswa_id: ' . $siswa->siswa_id);
            }
            
            // TAMBAHAN: Kirim notifikasi ke BK sendiri sebagai reminder
            $bkUsers = \App\Models\User::where('level', 'konselor_bk')->get();
            foreach ($bkUsers as $bkUser) {
                \App\Models\Notification::create([
                    'type' => 'bk_reminder',
                    'user_id' => $bkUser->user_id,
                    'title' => 'Reminder Konseling BK',
                    'message' => 'Anda telah memanggil siswa ' . $siswa->nama_siswa . ' untuk konseling BK dengan topik: ' . $request->topik . ' pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling))
                ]);
            }
            
            // Kirim notifikasi ke orang tua
            if ($siswa && $siswa->orang_tua_id) {
                $orangTua = \App\Models\User::where('orang_tua_id', $siswa->orang_tua_id)->first();
                if ($orangTua) {
                    \App\Models\Notification::create([
                        'type' => 'bk_panggilan_ortu',
                        'user_id' => $orangTua->user_id,
                        'title' => 'Panggilan Konseling BK Anak',
                        'message' => 'Anak Anda ' . $siswa->nama_siswa . ' dipanggil untuk konseling BK dengan topik: ' . $request->topik . ' pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling))
                    ]);
                }
            }
            
            $successMessage = 'Panggilan konseling berhasil dibuat dan notifikasi telah dikirim';
            if ($request->has('is_followup')) {
                $successMessage = 'Follow-up sanksi berhasil diproses. Panggilan konseling telah dibuat dan notifikasi dikirim ke siswa.';
            }
            
            return redirect()->back()->with('success', $successMessage);
            
        } else {
            // Mode input hasil - validasi lengkap
            $request->validate([
                'siswa_id' => 'required|exists:siswa,siswa_id',
                'topik' => 'required|string|max:255',
                'jenis_layanan' => 'required|string',
                'tanggal_konseling' => 'required|date',
                'tindakan_solusi' => 'required|string',
                'keluhan_masalah' => 'nullable|string',
                'hasil_evaluasi' => 'nullable|string',
                'status' => 'nullable|string'
            ]);

            $user = session('user');
            
            $bk = BimbinganKonseling::create([
                'siswa_id' => $request->siswa_id,
                'topik' => $request->topik,
                'jenis_layanan' => $request->jenis_layanan,
                'tanggal_konseling' => $request->tanggal_konseling,
                'keluhan_masalah' => $request->keluhan_masalah,
                'tindakan_solusi' => $request->tindakan_solusi,
                'hasil_evaluasi' => $request->hasil_evaluasi,
                'status' => $request->status ?? 'selesai',
                'guru_konselor' => $user->guru_id ?? 1,
                'tahun_ajaran_id' => 1
            ]);
            
            // Kirim notifikasi hasil ke siswa (jika punya akun user)
            $siswa = \App\Models\Siswa::find($request->siswa_id);
            $userSiswa = \App\Models\User::where('siswa_id', $siswa->siswa_id)->first();
            if ($userSiswa && $userSiswa->user_id) {
                \App\Models\Notification::create([
                    'type' => 'bk_hasil',
                    'user_id' => $userSiswa->user_id,
                    'title' => 'Hasil Konseling BK',
                    'message' => 'Konseling BK Anda dengan topik: ' . $request->topik . ' telah selesai pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling))
                ]);
            }
            
            // Kirim notifikasi ke orang tua
            if ($siswa && $siswa->orang_tua_id) {
                $orangTua = \App\Models\User::where('orang_tua_id', $siswa->orang_tua_id)->first();
                if ($orangTua) {
                    \App\Models\Notification::create([
                        'type' => 'bk_hasil_ortu',
                        'user_id' => $orangTua->user_id,
                        'title' => 'Hasil Konseling BK Anak',
                        'message' => 'Anak Anda ' . $siswa->nama_siswa . ' telah menjalani konseling BK dengan topik: ' . $request->topik . ' pada tanggal ' . date('d/m/Y', strtotime($request->tanggal_konseling))
                    ]);
                }
            }
            
            // Kirim notifikasi ke kesiswaan
            $kesiswaan = \App\Models\User::where('level', 'kesiswaan')->get();
            foreach ($kesiswaan as $kesiswaanUser) {
                \App\Models\Notification::create([
                    'type' => 'bk_hasil_kesiswaan',
                    'user_id' => $kesiswaanUser->user_id,
                    'title' => 'Hasil Konseling BK',
                    'message' => 'BK telah menyelesaikan konseling dengan siswa ' . $siswa->nama_siswa . ' dengan topik: ' . $request->topik
                ]);
            }
            
            return redirect()->back()->with('success', 'Hasil konseling berhasil disimpan dan notifikasi telah dikirim');
        }
    }

    public function edit($id)
    {
        $bk = BimbinganKonseling::with(['siswa.kelas'])->findOrFail($id);
        $siswa = Siswa::with('kelas')->get();
        $konselor = User::where('level', 'konselor_bk')->get();
        
        return response()->json([
            'bk' => $bk,
            'siswa' => $siswa,
            'konselor' => $konselor
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'topik' => 'required|string|max:255',
            'tindakan' => 'required|string',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $bk = BimbinganKonseling::findOrFail($id);
        $bk->update($request->only([
            'siswa_id', 'topik', 'tindakan', 'tanggal', 'keterangan'
        ]));
        
        return redirect()->route('admin.bk')->with('success', 'Data BK berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bk = BimbinganKonseling::findOrFail($id);
        $bk->delete();
        
        return redirect()->back()->with('success', 'Data BK berhasil dihapus');
    }
}