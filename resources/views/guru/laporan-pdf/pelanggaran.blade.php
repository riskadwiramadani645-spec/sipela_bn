<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelanggaran - Guru</title>
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
        .text-center { text-align: center; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PELANGGARAN - DATA SAYA</h2>
        <p>Guru: {{ $guru->nama_guru ?? 'N/A' }} | Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</p>
        <p>Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="section">
        <h3>DATA PELANGGARAN YANG SAYA INPUT</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Tanggal</th>
                    <th>Poin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data ?? [] as $index => $pelanggaran)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pelanggaran->siswa->nis ?? '-' }}</td>
                    <td>{{ $pelanggaran->siswa->nama_siswa ?? '-' }}</td>
                    <td>{{ $pelanggaran->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                    <td>{{ $pelanggaran->tanggal ?? '-' }}</td>
                    <td>{{ $pelanggaran->poin ?? 0 }}</td>
                    <td>{{ ucfirst($pelanggaran->status_verifikasi ?? 'pending') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data pelanggaran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h4>RINGKASAN:</h4>
        <p>Total Pelanggaran: {{ count($data ?? []) }} kasus</p>
        <p>Periode Laporan: {{ $periode ?? 'Semua Data' }}</p>
        <p><strong>Catatan:</strong> Laporan ini hanya menampilkan data pelanggaran yang Anda input sendiri</p>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Guru Pencatat</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ $guru->nama_guru ?? 'Guru' }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>