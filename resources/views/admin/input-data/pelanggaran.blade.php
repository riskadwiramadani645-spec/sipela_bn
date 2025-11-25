@extends('layouts.app')

@section('title', 'Input Pelanggaran')

@section('content')
<!-- Header Banner -->
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Input Data - Pelanggaran</h6>
                    <p class="mb-0">Input dan pencatatan pelanggaran siswa di SIPELA</p>
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
                @php
                    $currentPrefix = request()->route()->getPrefix();
                    $viewDataRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.pelanggaran.index' : 'admin.view-data.pelanggaran';
                    $storeRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.input-pelanggaran.store' : 'admin.input-data.pelanggaran.store';
                @endphp
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0">Form Input Pelanggaran</h6>
                    <a href="{{ route($viewDataRoute) }}" class="btn btn-info">
                        <i class="fa fa-list"></i> Lihat Data
                    </a>
                </div>

                <form method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <option value="">Pilih Siswa</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->siswa_id }}">{{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pelanggaran</label>
                            <select name="jenis_pelanggaran_id" class="form-select" required>
                                <option value="">Pilih Jenis Pelanggaran</option>
                                @foreach($jenisPelanggaran as $jp)
                                    <option value="{{ $jp->jenis_pelanggaran_id }}" data-poin="{{ $jp->poin }}">{{ $jp->nama_pelanggaran }} ({{ $jp->poin }} poin)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Poin</label>
                            <input type="number" name="poin" class="form-control" readonly>
                        </div>
                    </div>
                    

                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bukti Foto (Opsional)</label>
                        <input type="file" name="bukti_foto" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                    </div>
                    
                    <input type="hidden" name="guru_pencatat" value="{{ session('user')->user_id ?? 1 }}">
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                
                <script>
                // Auto fill poin
                document.querySelector('select[name="jenis_pelanggaran_id"]').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const poin = selectedOption.getAttribute('data-poin') || 0;
                    document.querySelector('input[name="poin"]').value = poin;
                });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.real-time-clock {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const timeOptions = {
        timeZone: 'Asia/Jakarta',
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    const timeString = now.toLocaleTimeString('id-ID', timeOptions);
    const clockElements = document.querySelectorAll('.real-time-clock');
    clockElements.forEach(el => el.textContent = timeString + ' WIB');
}

setInterval(updateClock, 1000);
updateClock();
</script>
@endpush