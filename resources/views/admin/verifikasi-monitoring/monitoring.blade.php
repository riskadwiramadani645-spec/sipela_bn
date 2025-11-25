@extends('layouts.app')

@section('title', 'Monitoring All')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <h6 class="mb-4">Monitoring All</h6>
            <p>Pantau semua aktivitas sistem secara real-time dan komprehensif</p>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-chart-line fa-3x mb-2"></i>
                        <h6>Real-time Monitor</h6>
                        <small>Pantau aktivitas terkini</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-tachometer-alt fa-3x mb-2"></i>
                        <h6>Dashboard Statistik</h6>
                        <small>Lihat ringkasan data</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fa fa-eye fa-3x mb-2"></i>
                        <h6>Comprehensive View</h6>
                        <small>Monitoring menyeluruh</small>
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
                <h6 class="mb-4">Monitoring Sistem SIPELA</h6>
                
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="bg-primary text-white rounded p-3 text-center">
                            <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                            <h5>{{ $pelanggaran->count() }}</h5>
                            <small>Total Pelanggaran</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success text-white rounded p-3 text-center">
                            <i class="fa fa-trophy fa-2x mb-2"></i>
                            <h5>{{ $prestasi->count() }}</h5>
                            <small>Total Prestasi</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning text-white rounded p-3 text-center">
                            <i class="fa fa-clock fa-2x mb-2"></i>
                            <h5>{{ $pelanggaran->where('status_verifikasi', 'menunggu')->count() + $prestasi->where('status_verifikasi', 'menunggu')->count() }}</h5>
                            <small>Pending Verifikasi</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info text-white rounded p-3 text-center">
                            <i class="fa fa-gavel fa-2x mb-2"></i>
                            <h5>{{ $pelanggaran->where('status_verifikasi', 'diverifikasi')->count() }}</h5>
                            <small>Sanksi Diperlukan</small>
                        </div>
                    </div>
                </div>
                
                @if(session('user') && session('user')->level === 'kesiswaan')
                <!-- Kesiswaan Alert Panel -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="bg-danger text-white rounded p-3">
                            <h6 class="mb-3"><i class="fa fa-bell me-2"></i>Alert Kesiswaan - Koordinator Disiplin</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fa fa-fire fa-2x mb-2"></i>
                                        <h6>Kasus Prioritas</h6>
                                        <span class="badge bg-warning">{{ $pelanggaran->filter(function($p) { return isset($p->jenisPelanggaran->poin) && $p->jenisPelanggaran->poin >= 50; })->count() }}</span>
                                        <small class="d-block">Poin ≥ 50</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                                        <h6>Perlu Verifikasi</h6>
                                        <span class="badge bg-warning">{{ $pelanggaran->where('status_verifikasi', 'menunggu')->count() }}</span>
                                        <small class="d-block">Menunggu Tindakan</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fa fa-calendar-times fa-2x mb-2"></i>
                                        <h6>Hari Ini</h6>
                                        <span class="badge bg-info">{{ $pelanggaran->where('tanggal', '>=', now()->format('Y-m-d'))->count() }}</span>
                                        <small class="d-block">Pelanggaran Baru</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#pelanggaran">
                            <i class="fa fa-exclamation-triangle me-2"></i>Pelanggaran Terbaru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#prestasi">
                            <i class="fa fa-trophy me-2"></i>Prestasi Terbaru
                        </a>
                    </li>
                    @if(session('user') && session('user')->level === 'kesiswaan')
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#verifikasi">
                            <i class="fa fa-check-circle me-2"></i>Perlu Verifikasi
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#sanksi">
                            <i class="fa fa-gavel me-2"></i>Sanksi Aktif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#bk">
                            <i class="fa fa-user-md me-2"></i>Sesi BK
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Pelanggaran Tab -->
                    <div class="tab-pane fade show active" id="pelanggaran">
                        <div class="table-responsive">
                            <table id="pelanggaranTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Pencatat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pelanggaran->take(10) as $p)
                                    <tr>
                                        <td>
                                            <strong>{{ $p->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $p->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ isset($p->jenisPelanggaran->poin) && $p->jenisPelanggaran->poin >= 50 ? 'danger' : ($p->jenisPelanggaran->poin >= 25 ? 'warning' : 'info') }}">
                                                {{ $p->jenisPelanggaran->poin ?? 0 }} poin
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($p->status_verifikasi == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($p->status_verifikasi == 'diverifikasi')
                                                <span class="badge bg-success">Diverifikasi</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($p->status_verifikasi) }}</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $p->guruPencatat->nama_guru ?? 'N/A' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data pelanggaran</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Prestasi Tab -->
                    <div class="tab-pane fade" id="prestasi">
                        <div class="table-responsive">
                            <table id="prestasiTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Jenis Prestasi</th>
                                        <th>Poin</th>
                                        <th>Tingkat</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Pencatat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prestasi->take(10) as $p)
                                    <tr>
                                        <td>
                                            <strong>{{ $p->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $p->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $p->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                                        <td><span class="badge bg-success">{{ $p->jenisPrestasi->poin ?? 0 }} poin</span></td>
                                        <td><span class="badge bg-info">{{ $p->tingkat }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($p->status_verifikasi == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($p->status_verifikasi == 'diverifikasi')
                                                <span class="badge bg-success">Diverifikasi</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($p->status_verifikasi) }}</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $p->guruPencatat->nama_guru ?? 'N/A' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data prestasi</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if(session('user') && session('user')->level === 'kesiswaan')
                    <!-- Tab Verifikasi Khusus Kesiswaan -->
                    <div class="tab-pane fade" id="verifikasi">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-warning mb-3"><i class="fa fa-exclamation-triangle me-2"></i>Pelanggaran Menunggu Verifikasi</h6>
                                <div class="table-responsive">
                                    <table id="verifikasiPelanggaranTable" class="table table-sm" data-datatable data-page-size="10">
                                        <thead>
                                            <tr>
                                                <th>Siswa</th>
                                                <th>Pelanggaran</th>
                                                <th>Poin</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pelanggaran->where('status_verifikasi', 'menunggu')->take(5) as $p)
                                            <tr>
                                                <td>
                                                    <strong>{{ $p->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $p->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                                </td>
                                                <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ isset($p->jenisPelanggaran->poin) && $p->jenisPelanggaran->poin >= 50 ? 'danger' : 'warning' }}">
                                                        {{ $p->jenisPelanggaran->poin ?? 0 }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('kesiswaan.verifikasi-monitoring.verifikasi') }}" class="btn btn-sm btn-success">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-success">Semua pelanggaran sudah diverifikasi</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info mb-3"><i class="fa fa-trophy me-2"></i>Prestasi Menunggu Verifikasi</h6>
                                <div class="table-responsive">
                                    <table id="verifikasiPrestasiTable" class="table table-sm" data-datatable data-page-size="10">
                                        <thead>
                                            <tr>
                                                <th>Siswa</th>
                                                <th>Prestasi</th>
                                                <th>Tingkat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($prestasi->where('status_verifikasi', 'menunggu')->take(5) as $p)
                                            <tr>
                                                <td>
                                                    <strong>{{ $p->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $p->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                                </td>
                                                <td>{{ $p->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                                                <td><span class="badge bg-info">{{ $p->tingkat }}</span></td>
                                                <td>
                                                    <a href="{{ route('kesiswaan.verifikasi-monitoring.verifikasi') }}" class="btn btn-sm btn-success">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-success">Semua prestasi sudah diverifikasi</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Sanksi Tab -->
                    <div class="tab-pane fade" id="sanksi">
                        <div class="table-responsive">
                            <table id="sanksiTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Jenis Sanksi</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Penanggung Jawab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sanksiAktifList ?? [] as $s)
                                    <tr>
                                        <td>
                                            <strong>{{ $s->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $s->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $s->jenis_sanksi }}</td>
                                        <td>{{ \Carbon\Carbon::parse($s->tanggal_mulai)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($s->tanggal_selesai)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($s->status_sanksi == 'berjalan')
                                                <span class="badge bg-warning">Berjalan</span>
                                            @elseif($s->status_sanksi == 'selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-info">{{ ucfirst($s->status_sanksi) }}</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $s->picPenanggungjawab->nama_guru ?? 'N/A' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada sanksi aktif</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BK Tab -->
                    <div class="tab-pane fade" id="bk">
                        <div class="table-responsive">
                            <table id="bkTable" class="table table-striped" data-datatable data-page-size="10">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Jenis Layanan</th>
                                        <th>Topik</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Konselor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bkTerbaru ?? [] as $b)
                                    <tr>
                                        <td>
                                            <strong>{{ $b->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $b->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $b->jenis_layanan }}</span></td>
                                        <td>{{ $b->topik }}</td>
                                        <td>{{ \Carbon\Carbon::parse($b->tanggal_konseling)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($b->status == 'terdaftar')
                                                <span class="badge bg-secondary">Terdaftar</span>
                                            @elseif($b->status == 'diproses')
                                                <span class="badge bg-warning">Diproses</span>
                                            @elseif($b->status == 'selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-info">Tindak Lanjut</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $b->guruKonselor->nama_guru ?? 'N/A' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada sesi BK</td>
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
@endsection