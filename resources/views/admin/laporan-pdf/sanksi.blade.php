<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sanksi - Admin</title>
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
        <h2>LAPORAN SANKSI SISWA</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="summary">
        <h3>⚖️ Ringkasan Laporan Sanksi</h3>
        <ul>
            <li><strong>Total Sanksi:</strong> {{ count($data) }} sanksi</li>
            <li><strong>Status Selesai:</strong> {{ $data->where('status', 'selesai')->count() }} sanksi</li>
            <li><strong>Status Berlangsung:</strong> {{ $data->where('status', 'berjalan')->count() }} sanksi</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Sanksi</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tanggal_mulai ?? '-' }}</td>
                <td>{{ $item->pelanggaran->siswa->nis ?? '-' }}</td>
                <td>{{ $item->pelanggaran->siswa->nama_siswa ?? '-' }}</td>
                <td>{{ $item->pelanggaran->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $item->jenis_sanksi ?? '-' }}</td>
                <td>{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($item->tanggal_selesai)) : '-' }} hari</td>
                <td>{{ ucfirst($item->status ?? 'berlangsung') }}</td>
                <td>{{ $item->deskripsi_sanksi ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">Tidak ada data sanksi</td>
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