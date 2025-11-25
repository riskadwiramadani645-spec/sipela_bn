<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Sanksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function followUpSanksi()
    {
        $user = session('user');
        
        // Get sanksi yang perlu follow-up
        $sanksiFollowUp = \App\Models\PelaksanaanSanksi::with(['sanksi.siswa.kelas', 'sanksi.pelanggaran.jenisPelanggaran'])
            ->where('status', 'Dalam Proses')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('bk.follow-up-sanksi', compact('sanksiFollowUp'));
    }
    
    public function index()
    {
        $user = session('user');
        
        // Debug: Log user info
        \Log::info('BK Notifikasi Debug', [
            'user_id' => $user->user_id,
            'username' => $user->username,
            'level' => $user->level
        ]);
        
        // Ambil semua notifikasi untuk BK (tidak hanya sanksi_followup)
        $notifications = Notification::where('user_id', $user->user_id)
            ->whereIn('type', ['sanksi_followup', 'bk_panggilan', 'bk_hasil', 'bk_reminder'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        \Log::info('Notifications found: ' . $notifications->count());
            
        return view('bk.notifikasi', compact('notifications'));
    }
    
    public function markAsRead($id)
    {
        try {
            $user = session('user');
            
            $notification = Notification::where('id', $id)
                ->where('user_id', $user->user_id)
                ->first();
                
            if ($notification) {
                $notification->update(['is_read' => true]);
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function completeFollowup($sanksiId, Request $request)
    {
        try {
            DB::transaction(function () use ($sanksiId, $request) {
                $user = session('user');
                
                // Update status sanksi
                $sanksi = Sanksi::findOrFail($sanksiId);
                $sanksi->update(['followup_status' => 'completed']);
                
                // Mark notification as read
                if ($request->notification_id) {
                    Notification::where('id', $request->notification_id)
                        ->where('user_id', $user->user_id)
                        ->update(['is_read' => true]);
                }
                
                // Kirim notifikasi ke kesiswaan bahwa follow-up sudah selesai
                $kesiswaan = \App\Models\User::where('level', 'kesiswaan')->get();
                foreach ($kesiswaan as $kesiswaanUser) {
                    Notification::create([
                        'type' => 'bk_completed',
                        'user_id' => $kesiswaanUser->user_id,
                        'sanksi_id' => $sanksiId,
                        'title' => 'Follow-up Sanksi Selesai',
                        'message' => 'BK telah menyelesaikan follow-up sanksi untuk siswa ' . ($sanksi->siswa->nama_siswa ?? 'N/A') . ' pada tanggal ' . now()->format('d/m/Y')
                    ]);
                }
            });
            
            return response()->json(['success' => true, 'message' => 'Follow-up berhasil diselesaikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}