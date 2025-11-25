@extends('layouts.app')

@section('title', 'Data Anak - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Data Anak - {{ $anak->nama_siswa }}</h6>
                    <p class="mb-0">Kelas {{ $anak->kelas->nama_kelas ?? 'N/A' }} - NIS: {{ $anak->nis }}</p>
                </div>
                <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Orang Tua -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Informasi:</strong> Anda sedang melihat data lengkap {{ $anak->nama_siswa }} sebagai 
            <strong>{{ $anak->orangTua->first()->hubungan ?? 'Orang Tua' }}</strong>. 
            Data ini mencakup riwayat pelanggaran, prestasi, dan sanksi.
        </div>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="row g-4 mt-1">
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                <h4>{{ $pelanggaran->count() }}</h4>
                <p class="mb-0">Total Pelanggaran</p>
                <small>{{ $pelanggaran->where('status_verifikasi', 'diverifikasi')->count() }} Terverifikasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fa fa-trophy fa-3x mb-3"></i>
                <h4>{{ $prestasi->count() }}</h4>
                <p class="mb-0">Total Prestasi</p>
                <small>{{ $prestasi->sum('poin') }} Total Poin</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fa fa-gavel fa-3x mb-3"></i>
                <h4>{{ $sanksi->count() }}</h4>
                <p class="mb-0">Total Sanksi</p>
                <small>{{ $sanksi->where('status', '!=', 'selesai')->count() }} Masih Aktif</small>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="dataTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pelanggaran-tab" data-bs-toggle="tab" data-bs-target="#pelanggaran" type="button" role="tab">
                            <i class="fa fa-exclamation-triangle me-2"></i>Riwayat Pelanggaran
                            @if($pelanggaran->count() > 0)
                            <span class="badge bg-danger ms-2">{{ $pelanggaran->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prestasi-tab" data-bs-toggle="tab" data-bs-target="#prestasi" type="button" role="tab">
                            <i class="fa fa-trophy me-2"></i>Riwayat Prestasi
                            @if($prestasi->count() > 0)
                            <span class="badge bg-success ms-2">{{ $prestasi->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sanksi-tab" data-bs-toggle="tab" data-bs-target="#sanksi" type="button" role="tab">
                            <i class="fa fa-gavel me-2"></i>Status Sanksi
                            @if($sanksi->where('status', '!=', 'selesai')->count() > 0)
                            <span class="badge bg-warning ms-2">{{ $sanksi->where('status', '!=', 'selesai')->count() }} Aktif</span>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="dataTabContent">
                    <!-- Tab Pelanggaran -->
                    <div class="tab-pane fade show active" id="pelanggaran" role="tabpanel">
                        @if($pelanggaran->count() > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Informasi:</strong> Berikut adalah riwayat pelanggaran {{ $anak->nama_siswa }}. 
                            Sebagai orang tua, mohon bantu anak untuk memperbaiki perilaku.
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="pelanggaranAnakTable" class="table table-striped table-hover" data-datatable data-page-size="10">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Tanggal</th>
                                        <th width="25%">Jenis Pelanggaran</th>
                                        <th width="8%">Poin</th>
                                        <th width="20%">Guru Pencatat</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pelanggaran as $index => $p)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $p->tanggal ? date('d/m/Y', strtotime($p->tanggal)) : 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $p->tanggal ? date('H:i', strtotime($p->tanggal)) : '' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</strong>
                                            @if($p->jenisPelanggaran->kategori ?? false)
                                            <br><small class="text-muted">{{ $p->jenisPelanggaran->kategori }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-danger fs-6">{{ $p->poin ?? 0 }}</span>
                                        </td>
                                        <td>{{ $p->guruPencatat->nama_guru ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColor = match($p->status_verifikasi ?? 'pending') {
                                                    'diverifikasi' => 'success',
                                                    'ditolak' => 'danger',
                                                    default => 'warning'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($p->status_verifikasi ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($p->keterangan ?? '-', 50) }}</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fa fa-smile fa-2x text-success mb-2"></i>
                                            <br><strong class="text-success">Tidak ada pelanggaran!</strong>
                                            <br><small class="text-muted">{{ $anak->nama_siswa }} adalah siswa yang baik</small>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Tab Prestasi -->
                    <div class="tab-pane fade" id="prestasi" role="tabpanel">
                        @if($prestasi->count() > 0)
                        <div class="alert alert-success mb-3">
                            <i class="fa fa-trophy me-2"></i>
                            <strong>Selamat!</strong> {{ $anak->nama_siswa }} telah meraih {{ $prestasi->count() }} prestasi 
                            dengan total {{ $prestasi->sum('poin') }} poin. Terus dukung dan motivasi anak!
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="prestasiAnakTable" class="table table-striped table-hover" data-datatable data-page-size="10">
                                <thead class="table-success">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Tanggal</th>
                                        <th width="25%">Jenis Prestasi</th>
                                        <th width="15%">Tingkat</th>
                                        <th width="10%">Poin</th>
                                        <th width="33%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prestasi as $index => $p)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $p->tanggal ? date('d/m/Y', strtotime($p->tanggal)) : 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $p->jenisPrestasi->nama_prestasi ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $p->tingkat ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success fs-6">+{{ $p->poin ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $p->keterangan ?? '-' }}</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fa fa-star fa-2x text-warning mb-2"></i>
                                            <br><strong>Belum ada prestasi</strong>
                                            <br><small class="text-muted">Dukung {{ $anak->nama_siswa }} untuk berprestasi!</small>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Tab Sanksi -->
                    <div class="tab-pane fade" id="sanksi" role="tabpanel">
                        @php $sanksiAktif = $sanksi->where('status', '!=', 'selesai'); @endphp
                        @if($sanksiAktif->count() > 0)
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> {{ $anak->nama_siswa }} memiliki {{ $sanksiAktif->count() }} sanksi aktif 
                            yang perlu diselesaikan. Mohon bantu anak untuk menyelesaikan tepat waktu.
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="sanksiAnakTable" class="table table-striped table-hover" data-datatable data-page-size="10">
                                <thead class="table-warning">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Pelanggaran</th>
                                        <th width="20%">Jenis Sanksi</th>
                                        <th width="12%">Mulai</th>
                                        <th width="12%">Selesai</th>
                                        <th width="15%">Status</th>
                                        <th width="16%">Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sanksi as $index => $s)
                                    <tr class="{{ $s->status != 'selesai' ? 'table-warning' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $s->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $s->pelanggaran->poin ?? 0 }} poin</small>
                                        </td>
                                        <td>{{ $s->jenisSanksi->nama_sanksi ?? $s->jenis_sanksi_manual ?? 'N/A' }}</td>
                                        <td>{{ $s->tanggal_mulai ? date('d/m/Y', strtotime($s->tanggal_mulai)) : 'N/A' }}</td>
                                        <td>{{ $s->tanggal_selesai ? date('d/m/Y', strtotime($s->tanggal_selesai)) : 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColor = match($s->status ?? 'pending') {
                                                    'selesai' => 'success',
                                                    'dikerjakan' => 'info',
                                                    'terlambat' => 'danger',
                                                    default => 'warning'
                                                };
                                                $statusIcon = match($s->status ?? 'pending') {
                                                    'selesai' => 'check-circle',
                                                    'dikerjakan' => 'clock',
                                                    'terlambat' => 'exclamation-triangle',
                                                    default => 'hourglass-half'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                <i class="fa fa-{{ $statusIcon }} me-1"></i>
                                                {{ ucfirst($s->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($s->status == 'selesai')
                                                <span class="text-success"><i class="fa fa-check me-1"></i>Selesai</span>
                                            @elseif($s->status == 'terlambat')
                                                <span class="text-danger"><i class="fa fa-clock me-1"></i>Terlambat</span>
                                            @else
                                                <span class="text-warning"><i class="fa fa-hourglass-half me-1"></i>Dalam Proses</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fa fa-thumbs-up fa-2x text-success mb-2"></i>
                                            <br><strong class="text-success">Tidak ada sanksi!</strong>
                                            <br><small class="text-muted">{{ $anak->nama_siswa }} tidak memiliki sanksi</small>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert untuk Sanksi Aktif -->
@if($sanksi->where('status', '!=', 'selesai')->count() > 0)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Perhatian:</strong> Anak Anda memiliki {{ $sanksi->where('status', '!=', 'selesai')->count() }} sanksi yang perlu diselesaikan. 
            Mohon bantu anak untuk menyelesaikan sanksi tersebut.
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
.rounded-circle {
    border-radius: 50% !important;
    object-fit: cover;
}
</style>
@endpush