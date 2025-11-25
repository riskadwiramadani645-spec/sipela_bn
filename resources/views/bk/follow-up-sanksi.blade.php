@extends('layouts.app')

@section('title', 'Follow-up Sanksi - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Follow-up Sanksi</h6>
                    <p class="mb-0">Monitoring dan Tindak Lanjut Sanksi Siswa</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 mb-1">{{ now()->format('d M Y') }}</div>
                    <div class="small real-time-clock">{{ now()->format('H:i') }} WIB</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card bg-secondary">
            <div class="card-header">
                <h6 class="mb-0"><i class="fa fa-sync-alt me-2"></i>Sanksi Perlu Follow-up</h6>
            </div>
            <div class="card-body">
                @if($sanksiFollowUp->count() > 0)
                    <div class="table-responsive">
                        <table id="follow-up-sanksiTable" class="table table-striped text-white" data-datatable data-page-size="10">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Sanksi</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sanksiFollowUp as $sanksi)
                                <tr>
                                    <td>
                                        <strong>{{ $sanksi->sanksi->siswa->nama_siswa ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $sanksi->sanksi->siswa->nis ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $sanksi->sanksi->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                    <td>{{ $sanksi->sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                                    <td>{{ $sanksi->sanksi->jenis_sanksi ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ $sanksi->status }}</span>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($sanksi->created_at)) }}</td>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="completeFollowup({{ $sanksi->sanksi_id }})">
                                            <i class="fa fa-check"></i> Selesai
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">Tidak ada sanksi yang perlu follow-up saat ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function completeFollowup(sanksiId) {
    if (confirm('Apakah Anda yakin follow-up sanksi ini sudah selesai?')) {
        fetch(`/bk/followup-complete/${sanksiId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Follow-up berhasil diselesaikan');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}
</script>
@endsection