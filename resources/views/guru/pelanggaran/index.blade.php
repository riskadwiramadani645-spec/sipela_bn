@extends('layouts.app')

@section('title', 'Data Pelanggaran Saya - SIPELA')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="mb-0">Data Pelanggaran yang Saya Input</h6>
                        <small class="text-muted">Pelanggaran yang telah saya laporkan dan status verifikasinya</small>
                    </div>
                    <a href="{{ route('guru.input-data.pelanggaran') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>Input Pelanggaran Baru
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="indexTable" class="table table-striped" data-datatable data-page-size="10">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin</th>
                                <th>Status Verifikasi</th>
                                <th>Status Sanksi</th>
                                <th>Status Pelaksanaan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggaran as $index => $item)
                            <tr>
                                <td>{{ $pelanggaran->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $item->siswa->nama_siswa ?? 'N/A' }}</td>
                                <td>{{ $item->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                <td>{{ $item->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->poin >= 50 ? 'danger' : ($item->poin >= 25 ? 'warning' : 'info') }}">
                                        {{ $item->poin }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status_verifikasi == 'menunggu')
                                        <span class="badge bg-warning">
                                            <i class="fa fa-clock me-1"></i>Menunggu Verifikasi
                                        </span>
                                    @elseif($item->status_verifikasi == 'diverifikasi')
                                        <span class="badge bg-success">
                                            <i class="fa fa-check me-1"></i>Diverifikasi
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fa fa-times me-1"></i>Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->sanksi)
                                        @php
                                            $statusColors = [
                                                'terdaftar' => 'secondary',
                                                'dijadwalkan' => 'info', 
                                                'berlangsung' => 'warning',
                                                'selesai' => 'success',
                                                'tindak_lanjut' => 'primary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$item->sanksi->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->sanksi->status)) }}
                                        </span>
                                        <br><small class="text-muted">{{ $item->sanksi->jenisSanksi->nama_sanksi ?? 'N/A' }}</small>
                                    @else
                                        <span class="badge bg-light text-dark">Belum Ada Sanksi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->sanksi && $item->sanksi->pelaksanaanSanksi->count() > 0)
                                        @php $pelaksanaan = $item->sanksi->pelaksanaanSanksi->first(); @endphp
                                        @php
                                            $pelaksanaanColors = [
                                                'terjadwal' => 'secondary',
                                                'dikerjakan' => 'warning',
                                                'tuntas' => 'success',
                                                'terlambat' => 'danger',
                                                'perpanjangan' => 'info'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $pelaksanaanColors[$pelaksanaan->status] ?? 'secondary' }}">
                                            {{ ucfirst($pelaksanaan->status) }}
                                        </span>
                                        @if($pelaksanaan->tanggal_pelaksanaan)
                                            <br><small class="text-muted">{{ $pelaksanaan->tanggal_pelaksanaan->format('d/m/Y') }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-dark">Belum Dijadwalkan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('guru.pelanggaran.show', $item->pelanggaran_id) }}" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if($item->status_verifikasi == 'menunggu')
                                            <a href="{{ route('guru.pelanggaran.edit', $item->pelanggaran_id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('guru.pelanggaran.destroy', $item->pelanggaran_id) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus pelanggaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data pelanggaran yang Anda input</p>
                                    <a href="{{ route('guru.input-data.pelanggaran') }}" class="btn btn-primary">
                                        Input Pelanggaran Pertama
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $pelanggaran->links() }}
            </div>
        </div>
    </div>
</div>
@endsection