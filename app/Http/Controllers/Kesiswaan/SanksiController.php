<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SanksiController extends Controller
{
    public function index()
    {
        $sanksi = Sanksi::with([
            'pelanggaran.siswa.kelas', 
            'pelanggaran.jenisPelanggaran',
            'guruPenanggungjawab',
            'bkUser'
        ])->latest()->get();
        
        // Filter sanksi yang perlu follow-up BK
        $sanksiFollowup = $sanksi->where('assigned_to_bk', true);
        
        return view('kesiswaan.view-data.sanksi', compact('sanksi', 'sanksiFollowup'));
    }

    public function markNotificationRead($sanksiId)
    {
        $user = session('user');
        if ($user && $user->user_id) {
            Notification::where('sanksi_id', $sanksiId)
                ->where('user_id', $user->user_id)
                ->where('type', 'bk_completed')
                ->update(['is_read' => true]);
        }
            
        return response()->json(['success' => true]);
    }
}