@extends('layouts.app')

@section('title', 'Riwayat Notifikasi - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1"><i class="fa fa-bell me-2"></i>Riwayat Notifikasi</h6>
                    <p class="mb-0">{{ $siswa->nama_siswa }} - Kelas {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
                </div>
                <div class="text-end">
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-light btn-sm">
                        <i class="fa fa-arrow-left me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0"><i class="fa fa-list me-2"></i>Semua Notifikasi ({{ $notifications->total() }})</h6>
                <div class="text-muted small">
                    Menampilkan {{ $notifications->count() }} dari {{ $notifications->total() }} notifikasi
                </div>
            </div>

            @if($notifications->count() > 0)
                <div class="row">
                    @foreach($notifications as $notif)
                    <div class="col-12 mb-3">
                        <div class="alert alert-{{ $notif->is_read ? 'secondary' : 'info' }} border-start border-4 border-{{ $notif->is_read ? 'secondary' : 'primary' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="me-2">{{ $notif->title }}</strong>
                                        @if(!$notif->is_read)
                                            <span class="badge bg-primary">Baru</span>
                                        @endif
                                    </div>
                                    <p class="mb-2">{{ $notif->message }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fa fa-clock me-1"></i>
                                            {{ $notif->created_at->format('d M Y, H:i') }} WIB
                                            ({{ $notif->created_at->diffForHumans() }})
                                        </small>
                                        <small class="text-muted">
                                            Status: {{ $notif->is_read ? 'Sudah dibaca' : 'Belum dibaca' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Notifikasi</h5>
                    <p class="text-muted">Belum ada notifikasi yang diterima.</p>
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
                        <i class="fa fa-home me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.alert {
    transition: all 0.3s ease;
}
.alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush