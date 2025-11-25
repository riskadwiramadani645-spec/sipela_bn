@extends('layouts.app')

@section('title', 'Edit Prestasi')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Edit Prestasi Siswa</h6>
                
                <form action="{{ route('admin.prestasi.update', $prestasi->prestasi_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <option value="">Pilih Siswa</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->id }}" {{ $prestasi->siswa_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Prestasi</label>
                            <select name="jenis_prestasi_id" class="form-select" required>
                                <option value="">Pilih Jenis Prestasi</option>
                                @foreach($jenisPrestasi as $jp)
                                    <option value="{{ $jp->id }}" {{ $prestasi->jenis_prestasi_id == $jp->id ? 'selected' : '' }}>
                                        {{ $jp->nama_prestasi }} ({{ $jp->poin }} poin)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tingkat</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="Sekolah" {{ $prestasi->tingkat == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                                <option value="Kabupaten" {{ $prestasi->tingkat == 'Kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                <option value="Provinsi" {{ $prestasi->tingkat == 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                                <option value="Nasional" {{ $prestasi->tingkat == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                                <option value="Internasional" {{ $prestasi->tingkat == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <div class="input-group">
                                <input type="date" name="tanggal" class="form-control" value="{{ $prestasi->tanggal }}" required id="tanggal_prestasi_edit">
                                <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_prestasi_edit').showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required>{{ $prestasi->keterangan }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Prestasi</button>
                        <a href="{{ route('admin.prestasi.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection