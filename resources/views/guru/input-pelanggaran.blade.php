@extends('layouts.app')

@section('title', 'Input Pelanggaran - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Input Pelanggaran {{ $isWaliKelas ? '(Wali Kelas)' : '(Guru)' }}</h6>
                    <p class="mb-0">{{ $isWaliKelas ? 'Input pelanggaran siswa di kelas yang Anda ampu' : 'Input pelanggaran siswa yang Anda temukan' }}</p>
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
                <h6 class="mb-0 text-white">Form Input Pelanggaran</h6>
                <a href="{{ route('guru.data-pelanggaran') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-list"></i> Lihat Data Pelanggaran
                </a>
            </div>
            
            <form action="{{ route('guru.input-pelanggaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-white">Siswa <span class="text-danger">*</span></label>
                            <select class="form-control bg-dark text-white border-primary" name="siswa_id" required>
                                <option value="">Pilih Siswa</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->siswa_id }}">{{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? '' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Anda dapat menginput pelanggaran untuk semua siswa</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-white">Jenis Pelanggaran <span class="text-danger">*</span></label>
                            <select class="form-control bg-dark text-white border-primary" name="jenis_pelanggaran_id" required>
                                <option value="">Pilih Jenis Pelanggaran</option>
                                @foreach($jenisPelanggaran as $jp)
                                    <option value="{{ $jp->jenis_pelanggaran_id }}">{{ $jp->nama_pelanggaran }} ({{ $jp->poin }} poin)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-white">Tanggal Kejadian <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="date" class="form-control bg-dark text-white border-primary" name="tanggal" value="{{ date('Y-m-d') }}" required id="tanggal_pelanggaran">
                                <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_pelanggaran').showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-white">Status</label>
                            <input type="text" class="form-control bg-dark text-white border-primary" value="Menunggu Verifikasi" readonly>
                            <small class="text-muted">Pelanggaran akan diverifikasi oleh admin/kesiswaan</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-white">Keterangan/Deskripsi Pelanggaran <span class="text-danger">*</span></label>
                    <textarea class="form-control bg-dark text-white border-primary" name="keterangan" rows="4" placeholder="Jelaskan detail pelanggaran yang terjadi..." required></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-white">Bukti Foto (Opsional)</label>
                    <input type="file" class="form-control bg-dark text-white border-primary" name="bukti_foto" accept=".jpg,.jpeg,.png">
                    <small class="text-muted">Format: JPG, PNG. Maksimal 5MB</small>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="bg-info text-white rounded p-3">
            <h6 class="mb-2"><i class="fas fa-info-circle"></i> Informasi Penting</h6>
            <ul class="mb-0 small">
                <li>Semua pelanggaran yang Anda input akan menunggu verifikasi dari admin atau kesiswaan</li>
                <li>Pastikan data yang diinput sudah benar karena akan mempengaruhi poin siswa</li>
                <li>Anda dapat menginput pelanggaran untuk semua siswa di sekolah</li>
                <li>Poin pelanggaran akan otomatis diambil dari jenis pelanggaran yang dipilih</li>
            </ul>
        </div>
    </div>
</div>

@endsection