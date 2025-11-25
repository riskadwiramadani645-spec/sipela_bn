@extends('layouts.app')

@section('title', 'Data Kelas Saya - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Dashboard Wali Kelas</h6>
                    <p class="mb-0">{{ $kelas->nama_kelas ?? 'Kelas' }} - {{ $guru->nama_guru ?? 'Guru' }}</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1" id="current-date">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock" id="current-time">{{ now()->format('H:i') }} WIB</div>
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="bg-primary rounded p-3 text-white text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h5>{{ $totalSiswa ?? 0 }}</h5>
                            <small>Total Siswa</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-danger rounded p-3 text-white text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h5>{{ $siswaBerisiko ?? 0 }}</h5>
                            <small>Siswa Berisiko</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success rounded p-3 text-white text-center">
                            <i class="fas fa-trophy fa-2x mb-2"></i>
                            <h5>{{ $siswaBerprestasi ?? 0 }}</h5>
                            <small>Siswa Berprestasi</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info rounded p-3 text-white text-center">
                            <i class="fas fa-percentage fa-2x mb-2"></i>
                            <h5>{{ $tingkatKedisiplinan ?? 0 }}%</h5>
                            <small>Tingkat Kedisiplinan</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="data-kelas-walikelasTable" class="table table-hover" data-datatable data-page-size="10">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Total Pelanggaran</th>
                                <th>Total Prestasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaList as $index => $siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->jenis_kelamin }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $siswa->pelanggaran_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $siswa->prestasi_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if(($siswa->pelanggaran_count ?? 0) > 3)
                                        <span class="badge bg-danger">Berisiko</span>
                                    @elseif(($siswa->prestasi_count ?? 0) > 0)
                                        <span class="badge bg-success">Berprestasi</span>
                                    @else
                                        <span class="badge bg-secondary">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetail({{ $siswa->siswa_id }})">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data siswa</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Siswa -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewDetail(siswaId) {
    $('#detailModal').modal('show');
    $('#detailContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    
    // Load detail siswa via AJAX (optional)
    // You can implement this later
}
</script>

@endsection

@push('scripts')
<script>
function updateDateTime() {
    const now = new Date();
    
    const timeOptions = {
        timeZone: 'Asia/Jakarta',
        hour12: false,
        hour: '2-digit',
        minute: '2-digit'
    };
    const timeString = now.toLocaleTimeString('id-ID', timeOptions);
    const clockElements = document.querySelectorAll('.real-time-clock');
    clockElements.forEach(el => el.textContent = timeString + ' WIB');
    
    const dateOptions = {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    };
    const dateString = now.toLocaleDateString('id-ID', dateOptions);
    const dateElements = document.querySelectorAll('#current-date');
    dateElements.forEach(el => el.textContent = dateString);
}

setInterval(updateDateTime, 1000);
updateDateTime();
</script>
@endpush