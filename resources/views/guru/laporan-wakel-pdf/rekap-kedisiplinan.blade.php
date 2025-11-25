<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kedisiplinan - Wali Kelas</title>
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
        <h2>REKAP KEDISIPLINAN - {{ $kelas->nama_kelas ?? 'Kelas' }}</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
        <p>Wali Kelas: {{ $guru->nama_guru ?? 'N/A' }}</p>
    </div>

    <div class="summary">
        <h3>📊 Rekap Kedisiplinan Kelas {{ $kelas->nama_kelas }}</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ $exportData['stats']['total_siswa'] ?? 0 }} siswa</li>
            <li><strong>Total Pelanggaran:</strong> {{ $exportData['stats']['total_pelanggaran'] ?? 0 }} kasus</li>
            <li><strong>Siswa Bermasalah:</strong> {{ $exportData['stats']['siswa_bermasalah'] ?? 0 }} siswa</li>
            <li><strong>Tingkat Kedisiplinan:</strong> {{ ($exportData['stats']['total_siswa'] ?? 0) > 0 ? round(((($exportData['stats']['total_siswa'] ?? 0) - ($exportData['stats']['siswa_bermasalah'] ?? 0)) / ($exportData['stats']['total_siswa'] ?? 1)) * 100) : 100 }}%</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Total Pelanggaran</th>
                <th>Kedisiplinan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($exportData['siswa'] ?? []) as $index => $siswa)
            @php
                $totalPelanggaran = $siswa->pelanggaran()->count();
                $kedisiplinan = $totalPelanggaran > 0 ? max(0, 100 - ($totalPelanggaran * 10)) : 100;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $siswa->nis ?? '-' }}</td>
                <td>{{ $siswa->nama_siswa ?? '-' }}</td>
                <td class="text-center">{{ $totalPelanggaran }}</td>
                <td class="text-center">
                    <span style="background: {{ $kedisiplinan >= 80 ? '#28a745' : ($kedisiplinan >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $kedisiplinan }}%
                    </span>
                </td>
                <td>{{ $totalPelanggaran > 3 ? 'Bermasalah' : 'Normal' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data siswa</td>
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