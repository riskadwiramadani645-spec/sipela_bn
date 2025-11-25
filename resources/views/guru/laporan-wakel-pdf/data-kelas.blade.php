<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Kelas Lengkap - Wali Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #666; }
        .header p { margin: 10px 0; color: #888; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .summary { margin-top: 30px; padding: 20px; background-color: #e9ecef; border-radius: 5px; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>DATA KELAS LENGKAP - {{ $kelas->nama_kelas ?? 'Kelas' }}</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
        <p>Wali Kelas: {{ $guru->nama_guru ?? 'N/A' }}</p>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Data Kelas</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ count($exportData) }} siswa</li>
            <li><strong>Siswa Bermasalah:</strong> {{ $exportData->filter(function($s) { return $s->pelanggaran_count > 3; })->count() }} siswa</li>
            <li><strong>Siswa Berprestasi:</strong> {{ $exportData->filter(function($s) { return $s->prestasi_count > 0; })->count() }} siswa</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Jenis Kelamin</th>
                <th>Total Pelanggaran</th>
                <th>Total Prestasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exportData as $index => $siswa)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $siswa->nis ?? '-' }}</td>
                <td>{{ $siswa->nama_siswa ?? '-' }}</td>
                <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                <td class="text-center">{{ $siswa->pelanggaran_count ?? 0 }}</td>
                <td class="text-center">{{ $siswa->prestasi_count ?? 0 }}</td>
                <td>{{ $siswa->pelanggaran_count > 3 ? 'Bermasalah' : 'Normal' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data siswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Wali Kelas {{ $kelas->nama_kelas }}</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ $guru->nama_guru ?? 'Wali Kelas' }}</p>
        <p>NIP: {{ $guru->nip ?? '-' }}</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>