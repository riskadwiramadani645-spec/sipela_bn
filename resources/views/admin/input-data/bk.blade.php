@extends('layouts.app')

@section('title', 'Input Bimbingan Konseling')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Input Data - Bimbingan Konseling</h6>
                    <p class="mb-0">Input dan pencatatan bimbingan konseling siswa di SIPELA</p>
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
                    $viewDataRoute = 'konselor-bk.data-bk-saya';
                    $storeRoute = $currentPrefix === 'konselor-bk' ? 'konselor-bk.input-bk.store' : 'admin.input-data.bk.store';
                @endphp
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0">Form Input Bimbingan Konseling</h6>
                    <div>
                        <div class="btn-group me-2" role="group">
                            <input type="radio" class="btn-check" name="mode" id="mode_sebelum" value="sebelum" checked>
                            <label class="btn btn-outline-warning" for="mode_sebelum">
                                <i class="fa fa-bell"></i> Panggil Siswa
                            </label>
                            <input type="radio" class="btn-check" name="mode" id="mode_sesudah" value="sesudah">
                            <label class="btn btn-outline-success" for="mode_sesudah">
                                <i class="fa fa-check"></i> Input Hasil
                            </label>
                        </div>
                        <a href="{{ route($viewDataRoute) }}" class="btn btn-info">
                            <i class="fa fa-list"></i> Lihat Data
                        </a>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_mode" id="form_mode" value="sebelum">
                    @if(isset($followupData))
                        <input type="hidden" name="sanksi_id" value="{{ $followupData['sanksi_id'] }}">
                        <input type="hidden" name="notification_id" value="{{ $followupData['notification_id'] }}">
                        <input type="hidden" name="is_followup" value="1">
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required {{ isset($followupData) ? 'readonly' : '' }}>
                                <option value="">Pilih Siswa</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->siswa_id }}" 
                                        {{ isset($followupData) && $followupData['siswa_id'] == $s->siswa_id ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }} - {{ $s->kelas->nama_kelas ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if(isset($followupData))
                                <small class="text-info">* Auto-selected dari notifikasi follow-up sanksi</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Layanan</label>
                            <select name="jenis_layanan" class="form-select" required>
                                <option value="">Pilih Jenis Layanan</option>
                                <option value="Individu">Individu</option>
                                <option value="Kelompok">Kelompok</option>
                                <option value="Klasikal">Klasikal</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Konseling</label>
                            <div class="input-group">
                                <input type="date" name="tanggal_konseling" class="form-control" value="{{ date('Y-m-d') }}" required id="tanggal_konseling">
                                <button type="button" class="btn btn-outline-light" onclick="document.getElementById('tanggal_konseling').showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mode-sesudah" style="display: none;">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="terdaftar">Terdaftar</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="tindak_lanjut">Tindak Lanjut</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Topik Konseling</label>
                        <input type="text" name="topik" class="form-control" 
                               placeholder="Masalah akademik, sosial, pribadi, dll" 
                               value="{{ isset($followupData) ? $followupData['topik_default'] : '' }}" 
                               required>
                        @if(isset($followupData))
                            <small class="text-info">* Topik default untuk follow-up sanksi, dapat diubah sesuai kebutuhan</small>
                        @endif
                    </div>
                    
                    <div class="mb-3 mode-sesudah" style="display: none;">
                        <label class="form-label">Keluhan/Masalah</label>
                        <textarea name="keluhan_masalah" class="form-control" rows="3" placeholder="Jelaskan keluhan atau masalah yang dihadapi siswa..."></textarea>
                    </div>
                    
                    <div class="mb-3 mode-sesudah" style="display: none;">
                        <label class="form-label">Tindakan/Solusi</label>
                        <textarea name="tindakan_solusi" class="form-control" rows="3" placeholder="Jelaskan tindakan konseling yang dilakukan..."></textarea>
                    </div>
                    
                    <div class="mb-3 mode-sesudah" style="display: none;">
                        <label class="form-label">Hasil Evaluasi</label>
                        <textarea name="hasil_evaluasi" class="form-control" rows="3" placeholder="Hasil evaluasi dari konseling yang dilakukan..."></textarea>
                    </div>
                    
                    <input type="hidden" name="guru_konselor" value="{{ session('user')->user_id ?? 1 }}">
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">Panggil Siswa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeSebelum = document.getElementById('mode_sebelum');
    const modeSesudah = document.getElementById('mode_sesudah');
    const formMode = document.getElementById('form_mode');
    const submitBtn = document.getElementById('submitBtn');
    const sesudahFields = document.querySelectorAll('.mode-sesudah');
    
    function toggleMode() {
        if (modeSebelum.checked) {
            formMode.value = 'sebelum';
            submitBtn.textContent = 'Panggil Siswa';
            submitBtn.className = 'btn btn-warning';
            sesudahFields.forEach(field => {
                field.style.display = 'none';
                const textarea = field.querySelector('textarea');
                if (textarea) textarea.removeAttribute('required');
            });
        } else {
            formMode.value = 'sesudah';
            submitBtn.textContent = 'Simpan Hasil Konseling';
            submitBtn.className = 'btn btn-success';
            sesudahFields.forEach(field => {
                field.style.display = 'block';
                const textarea = field.querySelector('textarea[name="tindakan_solusi"]');
                if (textarea) textarea.setAttribute('required', 'required');
            });
        }
    }
    
    modeSebelum.addEventListener('change', toggleMode);
    modeSesudah.addEventListener('change', toggleMode);
    
    toggleMode(); // Initialize
});
</script>
@endsection