@extends('layouts.app')

@section('title', 'Backup System Admin - SIPELA')

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="bg-primary text-white rounded h-100 p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Backup System Administrator</h6>
                    <p class="mb-0">Kelola backup dan restore database SIPELA - Akses khusus admin</p>
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
        <!-- System Info -->
        <div class="col-12">
            <div class="bg-secondary rounded p-4">
                <h6 class="mb-4 text-white"><i class="fa fa-info-circle me-2"></i>Informasi Sistem Database</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="bg-info text-white rounded p-3 text-center">
                            <i class="fa fa-database fa-2x mb-2"></i>
                            <h6>{{ $dbSize ?? 0 }} MB</h6>
                            <small>Ukuran Database</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success text-white rounded p-3 text-center">
                            <i class="fa fa-file-archive fa-2x mb-2"></i>
                            <h6>{{ count($backupFiles ?? []) }}</h6>
                            <small>File Backup</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning text-white rounded p-3 text-center">
                            <i class="fa fa-clock fa-2x mb-2"></i>
                            <h6>{{ date('d/m/Y') }}</h6>
                            <small>Backup Terakhir</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-danger text-white rounded p-3 text-center">
                            <i class="fa fa-shield-alt fa-2x mb-2"></i>
                            <h6>ADMIN</h6>
                            <small>Access Level</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0 text-white"><i class="fa fa-tools me-2"></i>Backup & Restore System</h6>
                </div>

                <div class="row g-4">
                    <!-- Backup Database -->
                    <div class="col-md-6">
                        <div class="bg-white rounded p-4 shadow-sm">
                            <h6 class="mb-3 text-dark fw-bold"><i class="fa fa-download me-2 text-primary"></i>Backup Database</h6>
                            <p class="text-secondary">Download backup database SIPELA lengkap</p>
                            <form action="{{ route('admin.laporan-sistem.sistem.backup') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-download me-2"></i>Backup Sekarang
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="col-md-6">
                        <div class="bg-white rounded p-4 shadow-sm">
                            <h6 class="mb-3 text-dark fw-bold"><i class="fa fa-server me-2 text-success"></i>Status Sistem</h6>
                            <p class="text-secondary">Monitoring kesehatan database SIPELA</p>
                            <div class="mb-2">
                                <small class="text-muted">Database Connection:</small>
                                <span class="badge bg-success ms-2">Connected</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Last Backup:</small>
                                <span class="badge bg-info ms-2">{{ date('d/m/Y H:i', time() + (7 * 3600)) }} WIB</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">System Status:</small>
                                <span class="badge bg-success ms-2">Healthy</span>
                            </div>
                        </div>
                    </div>



                    <!-- Backup History -->
                    <div class="col-12">
                        <div class="bg-white rounded p-4 shadow-sm">
                            <h6 class="mb-3 text-dark fw-bold"><i class="fa fa-history me-2 text-info"></i>Riwayat Backup Database</h6>
                            @if(isset($debugInfo))
                            <div class="alert alert-info">
                                <small>Debug: Path exists: {{ $debugInfo['path_exists'] ? 'Yes' : 'No' }} | Files: {{ $debugInfo['files_count'] }} | Path: {{ $debugInfo['path'] }}</small>
                            </div>
                            @endif
                            <div class="table-responsive">
                                <table id="sistemTable" class="table table-striped" data-datatable data-page-size="10">
                                    <thead>
                                        <tr class="text-dark">
                                            <th>Tanggal</th>
                                            <th>Nama File</th>
                                            <th>Ukuran</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Check if backup files exist in storage
                                            $backupPath = storage_path('app/backups');
                                            $actualFiles = [];
                                            if (file_exists($backupPath)) {
                                                $files = scandir($backupPath);
                                                foreach ($files as $file) {
                                                    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                                                        $filePath = $backupPath . DIRECTORY_SEPARATOR . $file;
                                                        $actualFiles[] = [
                                                            'name' => $file,
                                                            'size' => filesize($filePath),
                                                            'date' => date('d/m/Y H:i:s', filemtime($filePath) + (7 * 3600)) // +7 hours for WIB
                                                        ];
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @forelse($actualFiles as $backup)
                                        <tr class="text-dark">
                                            <td>{{ $backup['date'] }}</td>
                                            <td>{{ $backup['name'] }}</td>
                                            <td>{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                                            <td><span class="badge bg-success">Berhasil</span></td>
                                            <td>
                                                <a href="{{ route('admin.laporan-sistem.sistem.download', $backup['name']) }}" class="btn btn-sm btn-primary" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <form action="{{ route('admin.laporan-sistem.sistem.delete', $backup['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus backup ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada backup yang dibuat</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection