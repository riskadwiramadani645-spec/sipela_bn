<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sanksi Anak - Orang Tua</title>
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
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; padding: 10px; margin: 10px 0; border-radius: 5px; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN SANKSI ANAK</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</p>
        <p>Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="info">
        <strong>ℹ️ LIMITED ACCESS:</strong> Laporan ini hanya menampilkan data sanksi anak Anda sendiri.
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
        <h3>DATA SANKSI</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pelanggaran</th>
                    <th>Jenis Sanksi</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data ?? [] as $index => $sanksi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                    <td>{{ $sanksi->jenisSanksi->nama_sanksi ?? $sanksi->jenis_sanksi_manual ?? '-' }}</td>
                    <td>{{ $sanksi->tanggal_mulai ?? '-' }}</td>
                    <td>{{ $sanksi->tanggal_selesai ?? '-' }}</td>
                    <td>{{ ucfirst($sanksi->status ?? 'pending') }}</td>
                    <td>{{ $sanksi->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data sanksi pada periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h4>RINGKASAN:</h4>
        <p>Total Sanksi: {{ count($data ?? []) }} sanksi</p>
        <p>Periode Laporan: {{ $periode ?? 'Semua Data' }}</p>
        @php
            $sanksiAktif = collect($data ?? [])->where('status', '!=', 'selesai')->count();
        @endphp
        @if($sanksiAktif > 0)
        <p><strong>⚠️ Perhatian:</strong> Terdapat {{ $sanksiAktif }} sanksi yang masih aktif. Mohon bantu anak untuk menyelesaikan sanksi tersebut tepat waktu.</p>
        @else
        <p><strong>✅ Baik:</strong> Semua sanksi telah diselesaikan dengan baik.</p>
        @endif
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