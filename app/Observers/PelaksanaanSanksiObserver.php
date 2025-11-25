<?php

namespace App\Observers;

use App\Models\PelaksanaanSanksi;

class PelaksanaanSanksiObserver
{
    public function updated(PelaksanaanSanksi $pelaksanaanSanksi)
    {
        $this->updateSanksiStatus($pelaksanaanSanksi);
    }

    public function created(PelaksanaanSanksi $pelaksanaanSanksi)
    {
        // Update status sanksi menjadi dijadwalkan ketika pelaksanaan dibuat
        $pelaksanaanSanksi->sanksi->update(['status' => 'dijadwalkan']);
    }

    private function updateSanksiStatus(PelaksanaanSanksi $pelaksanaanSanksi)
    {
        $sanksi = $pelaksanaanSanksi->sanksi;
        
        // Update status sanksi berdasarkan status pelaksanaan
        switch ($pelaksanaanSanksi->status) {
            case 'dikerjakan':
                $sanksi->update(['status' => 'berlangsung']);
                break;
            case 'tuntas':
                $sanksi->update(['status' => 'selesai']);
                break;
            case 'terlambat':
            case 'perpanjangan':
                $sanksi->update(['status' => 'tindak_lanjut']);
                break;
        }
    }
}