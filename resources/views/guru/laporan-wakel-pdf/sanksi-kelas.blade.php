<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sanksi Kelas - Wali Kelas</title>
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
        <h2>SANKSI KELAS - {{ $kelas->nama_kelas ?? 'Kelas' }}</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
        <p>Wali Kelas: {{ $guru->nama_guru ?? 'N/A' }}</p>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Sanksi Kelas</h3>
        <ul>
            <li><strong>Total Sanksi:</strong> {{ count($exportData) }} sanksi</li>
            <li><strong>Status Selesai:</strong> {{ $exportData->where('status', 'selesai')->count() }} sanksi</li>
            <li><strong>Status Berlangsung:</strong> {{ $exportData->where('status', 'belum_dilaksanakan')->count() }} sanksi</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Jenis Sanksi</th>
                <th>Status</th>
                <th>Tanggal Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exportData as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tanggal_mulai ?? '-' }}</td>
                <td>{{ $item->pelanggaran->siswa->nis ?? '-' }}</td>
                <td>{{ $item->pelanggaran->siswa->nama_siswa ?? '-' }}</td>
                <td>{{ $item->jenisSanksi->nama_sanksi ?? '-' }}</td>
                <td>{{ ucfirst($item->status ?? 'pending') }}</td>
                <td>{{ $item->tanggal_selesai ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data sanksi</td>
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