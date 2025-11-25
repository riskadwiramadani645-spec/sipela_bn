@extends('layouts.app')

@section('title', 'Detail Pelanggaran - SIPELA')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="mb-0">Detail Pelanggaran</h6>
                        <small class="text-muted">Informasi lengkap pelanggaran yang saya laporkan</small>
                    </div>
                    <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Informasi Pelanggaran</h6>
                            </div>
                            <div class="card-body">
                                <table id="detailTable" class="table table-borderless" data-datatable data-page-size="10">
                                    <tr>
                                        <td width="200"><strong>Tanggal Kejadian</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Siswa</strong></td>
                                        <td>: {{ $pelanggaran->siswa->nama_siswa ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kelas</strong></td>
                                        <td>: {{ $pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Pelanggaran</strong></td>
                                        <td>: {{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Poin</strong></td>
                                        <td>: 
                                            <span class="badge bg-{{ $pelanggaran->poin >= 50 ? 'danger' : ($pelanggaran->poin >= 25 ? 'warning' : 'info') }}">
                                                {{ $pelanggaran->poin }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Keterangan</strong></td>
                                        <td>: {{ $pelanggaran->keterangan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Guru Pencatat</strong></td>
                                        <td>: {{ $pelanggaran->guruPencatat->nama_guru ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tahun Ajaran</strong></td>
                                        <td>: {{ $pelanggaran->tahunAjaran->tahun_ajaran ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Status Verifikasi</h6>
                            </div>
                            <div class="card-body text-center">
                                @if($pelanggaran->status_verifikasi == 'menunggu')
                                    <i class="fa fa-clock fa-3x text-warning mb-3"></i>
                                    <h6 class="text-warning">Menunggu Verifikasi</h6>
                                    <p class="text-muted small">Pelanggaran sedang menunggu verifikasi dari kesiswaan</p>
                                @elseif($pelanggaran->status_verifikasi == 'diverifikasi')
                                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                                    <h6 class="text-success">Diverifikasi</h6>
                                    <p class="text-muted small">Pelanggaran telah diverifikasi oleh kesiswaan</p>
                                    @if($pelanggaran->guruVerifikator)
                                        <small class="text-muted">Diverifikasi oleh: {{ $pelanggaran->guruVerifikator->nama_guru }}</small>
                                    @endif
                                @else
                                    <i class="fa fa-times-circle fa-3x text-danger mb-3"></i>
                                    <h6 class="text-danger">Ditolak</h6>
                                    <p class="text-muted small">Pelanggaran ditolak oleh kesiswaan</p>
                                @endif
                            </div>
                        </div>

                        @if($pelanggaran->bukti_foto)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Bukti Foto</h6>
                            </div>
                            <div class="card-body">
                                <img src="{{ asset('storage/' . $pelanggaran->bukti_foto) }}" 
                                     class="img-fluid rounded" alt="Bukti Foto">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection