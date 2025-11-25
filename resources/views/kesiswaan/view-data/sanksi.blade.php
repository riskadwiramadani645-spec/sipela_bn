@extends('layouts.app')

@section('title', 'Dashboard Kesiswaan - SIPELA')

@section('content')

@push('styles')
<style>
/* Override sidebar active state untuk halaman ini */
.sidebar .nav-link.active {
    background-color: transparent !important;
}
.sidebar .nav-link[href*="dashboard"] {
    background-color: #dc3545 !important;
    color: white !important;
}
</style>
@endpush

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Follow-up Sanksi BK</h6>
                    <p class="mb-0">Monitoring sanksi yang ditangani BK</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mt-1">
    <div class="col-md-4">
        <div class="bg-warning rounded p-4 text-dark">
            <div class="d-flex align-items-center">
                <i class="fa fa-clock fa-2x me-3"></i>
                <div>
                    <h4 class="mb-0">{{ $sanksiFollowup->where('followup_status', 'pending')->count() }}</h4>
                    <small>Perlu Follow-up</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg-success rounded p-4 text-white">
            <div class="d-flex align-items-center">
                <i class="fa fa-check-circle fa-2x me-3"></i>
                <div>
                    <h4 class="mb-0">{{ $sanksiFollowup->where('followup_status', 'completed')->count() }}</h4>
                    <small>Sudah Selesai</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg-info rounded p-4 text-white">
            <div class="d-flex align-items-center">
                <i class="fa fa-users fa-2x me-3"></i>
                <div>
                    <h4 class="mb-0">{{ $sanksiFollowup->pluck('siswa_id')->unique()->count() }}</h4>
                    <small>Siswa Terlibat</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sanksi Perlu Follow-up -->
@if($sanksiFollowup->where('followup_status', 'pending')->count() > 0)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-warning rounded h-100 p-4">
            <div class="mb-4">
                <h6 class="mb-0 text-dark"><i class="fa fa-exclamation-triangle me-2"></i>Sanksi Perlu Follow-up BK</h6>
                <p class="text-dark mb-0">{{ $sanksiFollowup->where('followup_status', 'pending')->count() }} sanksi menunggu tindak lanjut dari BK</p>
            </div>
            
            <div class="table-responsive">
                <table id="sanksiTable" class="table table-dark table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Pelanggaran</th>
                            <th>Sanksi</th>
                            <th>Guru PJ</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sanksiFollowup->where('followup_status', 'pending') as $sanksi)
                        <tr>
                            <td>
                                <strong>{{ $sanksi->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $sanksi->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                            <td>{{ Str::limit($sanksi->deskripsi_sanksi, 50) }}</td>
                            <td>{{ $sanksi->guruPenanggungjawab->nama_guru ?? 'N/A' }}</td>
                            <td>{{ $sanksi->tanggal_mulai ? \Carbon\Carbon::parse($sanksi->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-warning">Menunggu BK</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sanksi Sudah Selesai Follow-up -->
@if($sanksiFollowup->where('followup_status', 'completed')->count() > 0)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-success rounded h-100 p-4">
            <div class="mb-4">
                <h6 class="mb-0 text-white"><i class="fa fa-check-circle me-2"></i>Follow-up Selesai dari BK</h6>
                <p class="text-white mb-0">{{ $sanksiFollowup->where('followup_status', 'completed')->count() }} sanksi telah ditindaklanjuti BK</p>
            </div>
            
            <div class="table-responsive">
                <table id="sanksiTable" class="table table-dark table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Pelanggaran</th>
                            <th>Sanksi</th>
                            <th>Follow-up BK</th>
                            <th>Tanggal Follow-up</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sanksiFollowup->where('followup_status', 'completed') as $sanksi)
                        <tr>
                            <td>
                                <strong>{{ $sanksi->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $sanksi->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                            <td>{{ Str::limit($sanksi->deskripsi_sanksi, 50) }}</td>
                            <td>
                                <span class="badge bg-info">Follow-up BK</span><br>
                                <small class="text-muted">Sudah ditindaklanjuti</small>
                            </td>
                            <td>
                                {{ $sanksi->updated_at ? $sanksi->updated_at->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge bg-success">Selesai</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Jika tidak ada sanksi follow-up -->
@if($sanksiFollowup->count() == 0)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4 text-center">
            <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
            <h5 class="text-white">Tidak Ada Sanksi Follow-up</h5>
            <p class="text-muted">Semua sanksi dalam kondisi normal, tidak ada yang memerlukan follow-up BK</p>
        </div>
    </div>
</div>
@endif

<div class="row g-4 mt-1">
    <div class="col-12 text-center">
        <a href="{{ route('kesiswaan.dashboard') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

@endsection