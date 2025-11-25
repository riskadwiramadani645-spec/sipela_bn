@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">View Data - Anak</h6>
                    <p class="mb-0">Lihat data lengkap siswa untuk preview orang tua di SIPELA</p>
                </div>
                <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">View Data Anak (Preview untuk Orang Tua)</h6>
                
                <!-- Pencarian Siswa -->
                <form method="GET" action="{{ route('admin.view-data.anak') }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Cari nama siswa atau NIS..." value="{{ $search ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->nama_kelas }}" {{ ($kelasFilter ?? '') == $kelas->nama_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="siswa_id" onchange="this.form.submit()">
                                <option value="">Pilih Siswa untuk Detail</option>
                                @foreach($siswaList as $siswa)
                                    <option value="{{ $siswa->siswa_id }}" {{ request('siswa_id') == $siswa->siswa_id ? 'selected' : '' }}>
                                        {{ $siswa->nama_siswa }} - {{ $siswa->kelas->nama_kelas ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                @if($selectedSiswa)
                <!-- Info Siswa -->
                <div class="card mb-4 bg-secondary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Data Siswa</h6>
                                <p><strong>Nama:</strong> {{ $selectedSiswa->nama_siswa }}</p>
                                <p><strong>NIS:</strong> {{ $selectedSiswa->nis }}</p>
                                <p><strong>Kelas:</strong> {{ $selectedSiswa->kelas->nama_kelas ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Ringkasan</h6>
                                <p><strong>Total Pelanggaran:</strong> <span class="badge bg-danger">{{ $statistics['pelanggaran'] }}</span></p>
                                <p><strong>Total Prestasi:</strong> <span class="badge bg-success">{{ $statistics['prestasi'] }}</span></p>
                                <p><strong>Poin Saat Ini:</strong> <span class="badge bg-{{ $statistics['poin'] > 0 ? 'danger' : 'success' }}">{{ $statistics['poin'] }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Data -->
                <ul class="nav nav-tabs" id="dataTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pelanggaran-tab" data-bs-toggle="tab" data-bs-target="#pelanggaran" type="button">Pelanggaran</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prestasi-tab" data-bs-toggle="tab" data-bs-target="#prestasi" type="button">Prestasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bk-tab" data-bs-toggle="tab" data-bs-target="#bk" type="button">Bimbingan Konseling</button>
                    </li>
                </ul>

                <div class="tab-content" id="dataTabContent">
                    <!-- Tab Pelanggaran -->
                    <div class="tab-pane fade show active" id="pelanggaran" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table id="anakTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pelanggaranData as $pelanggaran)
                                    <tr>
                                        <td>{{ $pelanggaran->tanggal ? date('d/m/Y', strtotime($pelanggaran->tanggal)) : '-' }}</td>
                                        <td>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                        <td><span class="badge bg-danger">{{ $pelanggaran->poin }}</span></td>
                                        <td>{{ $pelanggaran->keterangan ?? '-' }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($pelanggaran->status_verifikasi) }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data pelanggaran</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Prestasi -->
                    <div class="tab-pane fade" id="prestasi" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table id="anakTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Prestasi</th>
                                        <th>Tingkat</th>
                                        <th>Poin</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prestasiData as $prestasi)
                                    <tr>
                                        <td>{{ $prestasi->tanggal ? date('d/m/Y', strtotime($prestasi->tanggal)) : '-' }}</td>
                                        <td>{{ $prestasi->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ $prestasi->tingkat ?? '-' }}</span></td>
                                        <td><span class="badge bg-success">{{ $prestasi->poin }}</span></td>
                                        <td>{{ $prestasi->keterangan ?? '-' }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($prestasi->status_verifikasi) }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data prestasi</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab BK -->
                    <div class="tab-pane fade" id="bk" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table id="anakTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Topik</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bkData as $bk)
                                    <tr>
                                        <td>{{ $bk->tanggal ? date('d/m/Y', strtotime($bk->tanggal)) : '-' }}</td>
                                        <td>{{ $bk->topik ?? 'N/A' }}</td>
                                        <td>{{ $bk->tindakan ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $bk->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($bk->status ?? 'Proses') }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada data bimbingan konseling</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Silakan pilih siswa dari dropdown di atas untuk melihat detail data.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$selectedSiswa && $siswaList->count() > 0)
<!-- Daftar Siswa -->
<div class="container-fluid px-4 pb-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Daftar Siswa ({{ $siswaList->count() }} siswa)</h6>
                <div class="table-responsive">
                    <table id="anakTable" class="table table-striped" data-datatable data-page-size="10">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Prestasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $key => $siswa)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                <td><span class="badge bg-danger">{{ $siswa->pelanggaran->where('status_verifikasi', 'diverifikasi')->count() }}</span></td>
                                <td><span class="badge bg-success">{{ $siswa->prestasi->where('status_verifikasi', 'diverifikasi')->count() }}</span></td>
                                <td>
                                    <a href="{{ route('admin.view-data.anak', ['siswa_id' => $siswa->siswa_id] + request()->only(['search', 'kelas'])) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection