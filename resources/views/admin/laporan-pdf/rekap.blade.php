<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Keseluruhan - Admin</title>
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
        .section { margin: 20px 0; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>REKAP KESELURUHAN SISTEM</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="section">
        <h3>STATISTIK UMUM</h3>
        <table style="width: 60%;">
            <tr>
                <td><strong>Total Siswa</strong></td>
                <td>{{ $stats['total_siswa'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Total Guru</strong></td>
                <td>{{ $stats['total_guru'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Total Kelas</strong></td>
                <td>{{ $stats['total_kelas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Total Pelanggaran</strong></td>
                <td>{{ $stats['total_pelanggaran'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Total Prestasi</strong></td>
                <td>{{ $stats['total_prestasi'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Total Sanksi</strong></td>
                <td>{{ $stats['total_sanksi'] ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>PELANGGARAN TERBANYAK</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Jumlah Kasus</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPelanggaran ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_pelanggaran ?? '-' }}</td>
                    <td>{{ $item->total_kasus ?? 0 }}</td>
                    <td>{{ $item->poin ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>PRESTASI TERBANYAK</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Prestasi</th>
                    <th>Jumlah</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPrestasi ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_prestasi ?? '-' }}</td>
                    <td>{{ $item->total_prestasi ?? 0 }}</td>
                    <td>{{ $item->poin ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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