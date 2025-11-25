@extends('layouts.app')

@section('title', 'Master Data - Jenis Sanksi')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Master Data - Jenis Sanksi</h6>
                    <p class="mb-0">Kelola jenis sanksi dan tindakan disiplin di SIPELA</p>
                </div>
                  <div class="text-end">
                    <div class="small text-light">SMK Bakti Nusantara 666</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-secondary rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0">Master Data - Jenis Sanksi</h6>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table id="jenis-sanksiTable" class="table table-striped" data-datatable data-page-size="10">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sanksi</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nama_sanksi }}</td>
                            <td><span class="badge bg-info">{{ $item->kategori }}</span></td>
                            <td>{{ $item->deskripsi }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <form action="{{ route('admin.master-data.jenis-sanksi.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-primary">
                    <h5 class="modal-title text-white">Tambah Jenis Sanksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Nama Sanksi</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="nama_sanksi" placeholder="Teguran Lisan, Teguran Tertulis, Skorsing" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Kategori</label>
                        <input type="text" class="form-control bg-dark text-white border-primary" name="kategori" placeholder="Ringan, Sedang, Berat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Deskripsi</label>
                        <textarea class="form-control bg-dark text-white border-primary" name="deskripsi" rows="4" placeholder="Deskripsi detail cara pelaksanaan sanksi"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-primary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<script>
function updateClock() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
    const timeStr = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }) + ' WIB';
    
    document.getElementById('current-date').textContent = dateStr;
    document.getElementById('current-time').textContent = timeStr;
}

updateClock();
setInterval(updateClock, 1000);
</script>