<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Data Siswa - Admin</title>
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
        <h2>MASTER DATA SISWA</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</p>
    </div>

    <div class="summary">
        <h3>👥 Ringkasan Master Data Siswa</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ count($data) }} siswa</li>
            <li><strong>Siswa Aktif:</strong> {{ $data->where('status_kesiswaan', 'aktif')->count() }} siswa</li>
            <li><strong>Siswa Non-Aktif:</strong> {{ $data->where('status_kesiswaan', '!=', 'aktif')->count() }} siswa</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nis ?? '-' }}</td>
                <td>{{ $item->nama_siswa ?? '-' }}</td>
                <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                <td>{{ $item->tempat_lahir ?? '-' }}</td>
                <td>{{ $item->tanggal_lahir ?? '-' }}</td>
                <td>{{ ucfirst($item->status_kesiswaan ?? 'aktif') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data siswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Administrator SIPELA</p>
        <br><br><br>
        <p>_________________________</p>
        <p>Administrator Sistem</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>