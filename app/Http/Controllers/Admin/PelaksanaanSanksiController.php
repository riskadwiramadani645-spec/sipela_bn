<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PelaksanaanSanksi;
use App\Models\Sanksi;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;

class PelaksanaanSanksiController extends Controller
{
    public function index()
    {
        $user = session('user');
        $currentPrefix = request()->route()->getPrefix();
        
        // Role-based data filtering
        if ($currentPrefix === 'admin' || $user->level === 'admin') {
            // Admin: Full access
            $data = PelaksanaanSanksi::with(['siswa.kelas', 'sanksi', 'guruPengawas'])->get();
            $sanksi = Sanksi::all();
        } elseif ($currentPrefix === 'kesiswaan' || $user->level === 'kesiswaan') {
            // Kesiswaan: Only active sanctions
            $data = PelaksanaanSanksi::with(['siswa.kelas', 'sanksi', 'guruPengawas'])
                                    ->whereHas('sanksi', function($query) {
                                        $query->whereIn('status_sanksi', ['direncanakan', 'berjalan', 'selesai']);
                                    })
                                    ->get();
            $sanksi = Sanksi::whereIn('status_sanksi', ['direncanakan', 'berjalan'])->get();
        } else {
            // Other roles: Limited access
            $data = PelaksanaanSanksi::with(['siswa.kelas', 'sanksi', 'guruPengawas'])
                                    ->whereHas('sanksi', function($query) {
                                        $query->where('status_sanksi', 'selesai');
                                    })
                                    ->get();
            $sanksi = collect(); // Empty collection
        }
        
        $siswa = Siswa::with('kelas')->where('status_kesiswaan', 'aktif')->get();
        $guru = Guru::all();
        return view('admin.sanksi.pelaksanaan', compact('data', 'sanksi', 'siswa', 'guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'sanksi_id' => 'required|exists:sanksi,sanksi_id',
            'tanggal_pelaksanaan' => 'required|date',
            'status' => 'required|in:terjadwal,dikerjakan,tuntas,terlambat,perpanjangan',
            'bukti_pelaksanaan' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        $data = $request->all();
        
        if ($request->hasFile('bukti_pelaksanaan')) {
            $data['bukti_pelaksanaan'] = $request->file('bukti_pelaksanaan')->store('pelaksanaan_sanksi', 'public');
        }

        PelaksanaanSanksi::create($data);
        return redirect()->back()->with('success', 'Pelaksanaan sanksi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = PelaksanaanSanksi::where('pelaksanaan_sanksi_id', $id)->firstOrFail();
        $sanksi = Sanksi::all();
        $siswa = Siswa::with('kelas')->get();
        $guru = Guru::all();
        return response()->json(['data' => $data, 'sanksi' => $sanksi, 'siswa' => $siswa, 'guru' => $guru]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
            'sanksi_id' => 'required|exists:sanksi,sanksi_id',
            'tanggal_pelaksanaan' => 'required|date',
            'status' => 'required|in:terjadwal,dikerjakan,tuntas,terlambat,perpanjangan',
            'bukti_pelaksanaan' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        $pelaksanaan = PelaksanaanSanksi::where('pelaksanaan_sanksi_id', $id)->firstOrFail();
        $data = $request->all();
        
        if ($request->hasFile('bukti_pelaksanaan')) {
            $data['bukti_pelaksanaan'] = $request->file('bukti_pelaksanaan')->store('pelaksanaan_sanksi', 'public');
        }

        $pelaksanaan->update($data);
        return redirect()->back()->with('success', 'Pelaksanaan sanksi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pelaksanaan = PelaksanaanSanksi::where('pelaksanaan_sanksi_id', $id)->firstOrFail();
        $pelaksanaan->delete();
        return redirect()->back()->with('success', 'Pelaksanaan sanksi berhasil dihapus');
    }
}