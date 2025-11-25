@extends('layouts.app')

@section('title', 'Input Prestasi')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Input Data - Prestasi</h6>
                    <p class="mb-0">Input dan pencatatan prestasi siswa di SIPELA</p>
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
                    $storeRoute = $currentPrefix === 'kesiswaan' ? 'kesiswaan.input-data.prestasi.store' : 'admin.input-data.prestasi.store';
                @endphp
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0">Form Input Prestasi</h6>
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
                            <label class="form-label">Jenis Prestasi</label>
                            <select name="jenis_prestasi_id" class="form-select" required>
                                <option value="">Pilih Jenis Prestasi</option>
                                @foreach($jenisPrestasi as $jp)
                                    <option value="{{ $jp->jenis_prestasi_id }}" data-poin="{{ $jp->poin }}">{{ $jp->nama_prestasi }} ({{ $jp->poin }} poin)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($tahunAjaran as $ta)
                                    <option value="{{ $ta->tahun_ajaran_id }}" {{ $ta->status_aktif ? 'selected' : '' }}>{{ $ta->tahun_ajaran }} - {{ $ta->semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Poin</label>
                            <input type="number" name="poin" class="form-control" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <div class="input-group">
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required id="tanggal_prestasi">
                                <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_prestasi').showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kabupaten">Kabupaten</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bukti Dokumen (Opsional)</label>
                        <input type="file" name="bukti_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 5MB</small>
                    </div>
                    
                    <input type="hidden" name="guru_pencatat" value="{{ session('user')->user_id ?? 1 }}">
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                
                <script>
                document.querySelector('select[name="jenis_prestasi_id"]').addEventListener('change', function() {
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