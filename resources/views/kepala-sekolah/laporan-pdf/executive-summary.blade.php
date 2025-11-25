<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Summary - Kepala Sekolah</title>
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
        <h2>EXECUTIVE SUMMARY</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ isset($data['periode']) ? ucfirst(str_replace('_', ' ', $data['periode'])) : 'Semua Data' }}</p>
    </div>

    <div class="summary">
        <h3>📊 Executive Summary</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ isset($data['summary']['total_siswa']) ? number_format($data['summary']['total_siswa']) : 0 }} siswa</li>
            <li><strong>Total Pelanggaran:</strong> {{ isset($data['summary']['total_pelanggaran']) ? number_format($data['summary']['total_pelanggaran']) : 0 }} kasus</li>
            <li><strong>Total Prestasi:</strong> {{ isset($data['summary']['total_prestasi']) ? number_format($data['summary']['total_prestasi']) : 0 }} prestasi</li>
            <li><strong>Efektivitas Sanksi:</strong> {{ isset($data['summary']['efektivitas_sanksi']) ? $data['summary']['efektivitas_sanksi'] : 0 }}%</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($data['data']) ? $data['data'] : []) as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->siswa->nama_siswa ?? 'N/A' }}</td>
                <td>{{ $item->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $item->jenisPelanggaran->nama_pelanggaran ?? $item->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                <td>{{ $item->tanggal ?? '-' }}</td>
                <td>{{ ucfirst($item->status_verifikasi ?? 'pending') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ session('user')->nama ?? 'Kepala Sekolah' }}</p>
        <p>Kepala Sekolah</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>