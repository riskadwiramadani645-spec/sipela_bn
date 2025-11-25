<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelanggaran Anak - Orang Tua</title>
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
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 10px 0; border-radius: 5px; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PELANGGARAN ANAK</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</p>
        <p>Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="warning">
        <strong>⚠️ LIMITED ACCESS:</strong> Laporan ini hanya menampilkan data pelanggaran anak Anda sendiri.
    </div>

    <div style="margin-bottom: 20px;">
        <h3>INFORMASI ANAK</h3>
        <table style="width: 60%;">
            <tr>
                <td><strong>Nama Siswa</strong></td>
                <td>{{ $anak->nama_siswa ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>NIS</strong></td>
                <td>{{ $anak->nis ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>{{ $anak->kelas->nama_kelas ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>DATA PELANGGARAN</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Poin</th>
                    <th>Guru Pencatat</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data ?? [] as $index => $pelanggaran)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pelanggaran->tanggal ?? '-' }}</td>
                    <td>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                    <td>{{ $pelanggaran->poin ?? 0 }}</td>
                    <td>{{ $pelanggaran->guruPencatat->nama_guru ?? '-' }}</td>
                    <td>{{ ucfirst($pelanggaran->status_verifikasi ?? 'pending') }}</td>
                    <td>{{ $pelanggaran->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data pelanggaran pada periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h4>RINGKASAN:</h4>
        <p>Total Pelanggaran: {{ count($data ?? []) }} kasus</p>
        <p>Periode Laporan: {{ $periode ?? 'Semua Data' }}</p>
        <p><strong>Catatan untuk Orang Tua:</strong> Mohon bantu anak untuk memperbaiki perilaku dan menghindari pelanggaran serupa di masa mendatang.</p>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Orang Tua Siswa</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ $anak->orangTua->first()->nama_orangtua ?? 'Orang Tua' }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>