<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Prestasi Kelas - Wali Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #666; }
        .header p { margin: 10px 0; color: #888; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 11px; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PRESTASI KELAS {{ $kelas->nama_kelas ?? 'N/A' }}</h2>
        <p>Wali Kelas: {{ $guru->nama_guru ?? 'N/A' }} | Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</p>
        <p>Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="section">
        <h3>DATA PRESTASI SISWA</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Jenis Prestasi</th>
                    <th>Tanggal</th>
                    <th>Tingkat</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exportData ?? [] as $index => $prestasi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $prestasi->siswa->nis ?? '-' }}</td>
                    <td>{{ $prestasi->siswa->nama_siswa ?? '-' }}</td>
                    <td>{{ $prestasi->jenisPrestasi->nama_prestasi ?? '-' }}</td>
                    <td>{{ $prestasi->tanggal ?? '-' }}</td>
                    <td>{{ $prestasi->tingkat ?? '-' }}</td>
                    <td>{{ $prestasi->poin ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data prestasi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h4>RINGKASAN:</h4>
        <p>Total Prestasi: {{ count($exportData ?? []) }} prestasi</p>
        <p>Periode Laporan: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Wali Kelas {{ $kelas->nama_kelas ?? 'N/A' }}</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ $guru->nama_guru ?? 'Wali Kelas' }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>