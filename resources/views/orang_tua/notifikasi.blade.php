@extends('layouts.app')

@section('title', 'Notifikasi - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <h6 class="mb-4">Notifikasi Orang Tua</h6>
            <p>Pantau semua notifikasi terkait anak Anda di sekolah</p>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-bell fa-3x mb-2"></i>
                        <h6>Notifikasi Aktif</h6>
                        <small>{{ $notifikasi->where('is_read', false)->count() }} belum dibaca</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-child fa-3x mb-2"></i>
                        <h6>Monitoring Anak</h6>
                        <small>{{ $anak->nama_siswa ?? 'N/A' }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-envelope fa-3x mb-2"></i>
                        <h6>Total Notifikasi</h6>
                        <small>{{ $notifikasi->count() }} pesan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0">Daftar Notifikasi</h6>
                    @if($notifikasi->where('is_read', false)->count() > 0)
                    <button class="btn btn-sm btn-success" onclick="markAllAsRead()">
                        <i class="fa fa-check-double"></i> Tandai Semua Dibaca
                    </button>
                    @endif
                </div>
                
                <div class="row">
                    @forelse($notifikasi as $notif)
                    <div class="col-12 mb-3">
                        <div class="card {{ $notif->is_read ? 'bg-dark' : 'bg-warning' }} border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            @if($notif->type == 'bk_call_parent')
                                                <i class="fa fa-user-md text-info me-2"></i>
                                            @elseif($notif->type == 'pelanggaran')
                                                <i class="fa fa-exclamation-triangle text-danger me-2"></i>
                                            @elseif($notif->type == 'prestasi')
                                                <i class="fa fa-trophy text-success me-2"></i>
                                            @else
                                                <i class="fa fa-bell text-primary me-2"></i>
                                            @endif
                                            <h6 class="mb-0 {{ $notif->is_read ? 'text-white' : 'text-dark' }}">
                                                {{ $notif->title }}
                                            </h6>
                                            @if(!$notif->is_read)
                                                <span class="badge bg-danger ms-2">Baru</span>
                                            @endif
                                        </div>
                                        <p class="mb-2 {{ $notif->is_read ? 'text-light' : 'text-dark' }}">
                                            {{ $notif->message }}
                                        </p>
                                        <small class="{{ $notif->is_read ? 'text-muted' : 'text-secondary' }}">
                                            <i class="fa fa-clock me-1"></i>
                                            {{ $notif->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        @if(!$notif->is_read)
                                        <button class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $notif->id }})">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification({{ $notif->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fa fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Notifikasi</h5>
                            <p class="text-muted">Belum ada notifikasi untuk Anda</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/orang-tua/notifikasi/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/orang-tua/notifikasi/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(id) {
    if (confirm('Yakin ingin menghapus notifikasi ini?')) {
        fetch(`/orang-tua/notifikasi/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>

@endsection