@extends('layouts.app')

@section('title', 'View Data Sendiri - Siswa')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1">Riwayat Data Pribadi</h5>
                    <p class="mb-0">{{ $siswa->nama_siswa }} - {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small">NIS: {{ $siswa->nis }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <ul class="nav nav-tabs" id="dataTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pelanggaran-tab" data-bs-toggle="tab" data-bs-target="#pelanggaran" type="button">
                        <i class="fa fa-exclamation-triangle me-2"></i>Riwayat Pelanggaran ({{ $pelanggaran->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="prestasi-tab" data-bs-toggle="tab" data-bs-target="#prestasi" type="button">
                        <i class="fa fa-trophy me-2"></i>Riwayat Prestasi ({{ $prestasi->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sanksi-tab" data-bs-toggle="tab" data-bs-target="#sanksi" type="button">
                        <i class="fa fa-gavel me-2"></i>Riwayat Sanksi ({{ $sanksi->count() }})
                    </button>
                </li>
            </ul>
            
            <div class="tab-content mt-4" id="dataTabContent">
                <!-- Riwayat Pelanggaran -->
                <div class="tab-pane fade show active" id="pelanggaran" role="tabpanel">
                    @if($pelanggaran->count() > 0)
                    <div class="table-responsive">
                        <table id="pelanggaranSiswaTable" class="table table-striped" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Kategori</th>
                                    <th>Poin</th>
                                    <th>Guru Pencatat</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pelanggaran as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $p->tanggal ? date('d/m/Y', strtotime($p->tanggal)) : '-' }}</td>
                                    <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            ($p->jenisPelanggaran->kategori ?? '') == 'ringan' ? 'success' : 
                                            (($p->jenisPelanggaran->kategori ?? '') == 'sedang' ? 'warning' : 'danger')
                                        }}">
                                            {{ ucfirst($p->jenisPelanggaran->kategori ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>{{ $p->jenisPelanggaran->poin ?? 0 }}</td>
                                    <td>{{ $p->guruPencatat->nama_guru ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $p->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                                            {{ ucfirst($p->status_verifikasi) }}
                                        </span>
                                    </td>
                                    <td>{{ $p->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                        <h5>Tidak Ada Pelanggaran</h5>
                        <p class="text-muted">Anda belum memiliki riwayat pelanggaran. Pertahankan kedisiplinan!</p>
                    </div>
                    @endif
                </div>
                
                <!-- Riwayat Prestasi -->
                <div class="tab-pane fade" id="prestasi" role="tabpanel">
                    @if($prestasi->count() > 0)
                    <div class="table-responsive">
                        <table id="prestasiSiswaTable" class="table table-striped" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Prestasi</th>
                                    <th>Tingkat</th>
                                    <th>Peringkat</th>
                                    <th>Poin</th>
                                    <th>Penyelenggara</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prestasi as $index => $pr)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pr->tanggal ? date('d/m/Y', strtotime($pr->tanggal)) : '-' }}</td>
                                    <td>{{ $pr->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            ($pr->tingkat ?? '') == 'sekolah' ? 'info' : 
                                            (($pr->tingkat ?? '') == 'kota' ? 'success' : 
                                            (($pr->tingkat ?? '') == 'provinsi' ? 'warning' : 
                                            (($pr->tingkat ?? '') == 'nasional' ? 'danger' : 'secondary')))
                                        }}">
                                            {{ ucfirst($pr->tingkat ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pr->peringkat)
                                            @if($pr->peringkat <= 3)
                                                <span class="badge bg-warning">
                                                    {{ $pr->peringkat == 1 ? '🥇' : ($pr->peringkat == 2 ? '🥈' : '🥉') }} {{ $pr->peringkat }}
                                                </span>
                                            @else
                                                {{ $pr->peringkat }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $pr->jenisPrestasi->poin ?? 0 }}</td>
                                    <td>{{ $pr->penyelenggara ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $pr->status_verifikasi == 'diverifikasi' ? 'success' : 'warning' }}">
                                            {{ ucfirst($pr->status_verifikasi) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fa fa-trophy fa-4x text-warning mb-3"></i>
                        <h5>Belum Ada Prestasi</h5>
                        <p class="text-muted">Terus berprestasi dan raih pencapaian terbaik!</p>
                    </div>
                    @endif
                </div>
                
                <!-- Riwayat Sanksi -->
                <div class="tab-pane fade" id="sanksi" role="tabpanel">
                    @if($sanksi->count() > 0)
                    <div class="table-responsive">
                        <table id="sanksiSiswaTable" class="table table-striped" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pelanggaran</th>
                                    <th>Jenis Sanksi</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sanksi as $index => $s)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $s->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                    <td>{{ $s->jenisSanksi->nama_sanksi ?? 'N/A' }}</td>
                                    <td>{{ $s->tanggal_mulai ? date('d/m/Y', strtotime($s->tanggal_mulai)) : '-' }}</td>
                                    <td>{{ $s->tanggal_selesai ? date('d/m/Y', strtotime($s->tanggal_selesai)) : '-' }}</td>
                                    <td>
                                        @if($s->tanggal_selesai)
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($s->tanggal_mulai)
                                            <span class="badge bg-warning">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $s->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fa fa-gavel fa-4x text-info mb-3"></i>
                        <h5>Tidak Ada Sanksi</h5>
                        <p class="text-muted">Anda belum memiliki riwayat sanksi.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection