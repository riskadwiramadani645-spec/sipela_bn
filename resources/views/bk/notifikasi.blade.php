@extends('layouts.app')

@section('title', 'Notifikasi Follow-up Sanksi - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Notifikasi Follow-up Sanksi</h6>
                    <p class="mb-0">Daftar sanksi yang memerlukan bimbingan konseling lanjutan</p>
                </div>
                <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="mb-4">
                <h6 class="mb-0">Daftar Notifikasi Follow-up Sanksi</h6>
                <small class="text-muted">Sanksi yang memerlukan tindak lanjut bimbingan konseling</small>
            </div>
            
            <div class="table-responsive">
                <table id="notifikasiTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Jenis/Tipe</th>
                            <th>Tanggal</th>
                            <th>Pesan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $key => $notifikasi)
                        <tr class="{{ !$notifikasi->is_read ? 'table-warning' : '' }}">
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($notifikasi->type == 'sanksi_followup' && $notifikasi->sanksi)
                                    <strong>{{ $notifikasi->sanksi->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $notifikasi->sanksi->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                @else
                                    <strong>{{ $notifikasi->title ?? 'Notifikasi BK' }}</strong><br>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $notifikasi->type)) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($notifikasi->type == 'sanksi_followup')
                                    {{ $notifikasi->sanksi->jenis_sanksi ?? 'N/A' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $notifikasi->type)) }}
                                @endif
                            </td>
                            <td>
                                @if($notifikasi->type == 'sanksi_followup' && $notifikasi->sanksi)
                                    {{ $notifikasi->sanksi->tanggal_mulai ? \Carbon\Carbon::parse($notifikasi->sanksi->tanggal_mulai)->format('d/m/Y') : '-' }}
                                @else
                                    {{ $notifikasi->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>{{ $notifikasi->message }}</td>
                            <td>
                                @if($notifikasi->is_read)
                                    <span class="badge bg-success">Sudah Dibaca</span>
                                @else
                                    <span class="badge bg-warning">Belum Dibaca</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(!$notifikasi->is_read && $notifikasi->type == 'sanksi_followup')
                                        <button class="btn btn-sm btn-success" 
                                                onclick="followupSanksi({{ $notifikasi->sanksi_id }}, {{ $notifikasi->id }}, {{ $notifikasi->sanksi->pelanggaran->siswa_id ?? 0 }})" 
                                                title="Proses Follow-up">
                                            <i class="fa fa-check"></i> Proses
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-info" onclick="markAsRead({{ $notifikasi->id }})" title="Tandai Sudah Dibaca">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada notifikasi follow-up sanksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function followupSanksi(sanksiId, notificationId, siswaId) {
    // Redirect ke halaman input BK dengan parameter
    window.location.href = `/konselor-bk/input-bk?siswa_id=${siswaId}&sanksi_id=${sanksiId}&notification_id=${notificationId}&followup=1`;
}

function markAsRead(notificationId) {
    fetch(`/konselor-bk/notification/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal menandai notifikasi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menandai notifikasi');
    });
}
</script>

@endsection