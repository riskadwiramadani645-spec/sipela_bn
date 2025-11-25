<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Admin - SIPELA</title>
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
        .print-btn { margin: 20px 0; text-align: center; }
        .print-btn button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN SISTEM ADMIN</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Type: {{ ucfirst($type ?? 'rekap') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Kategori</th>
                <th width="20%">Jumlah</th>
                <th width="50%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td><strong>Total Siswa</strong></td>
                <td class="text-center">{{ $stats['total_siswa'] ?? 0 }}</td>
                <td>Siswa aktif dalam sistem</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td><strong>Total Guru</strong></td>
                <td class="text-center">{{ $stats['total_guru'] ?? 0 }}</td>
                <td>Guru dan staff pengajar</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td><strong>Total Pelanggaran</strong></td>
                <td class="text-center">{{ $stats['total_pelanggaran'] ?? 0 }}</td>
                <td>Pelanggaran yang tercatat</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td><strong>Total Prestasi</strong></td>
                <td class="text-center">{{ $stats['total_prestasi'] ?? 0 }}</td>
                <td>Prestasi yang tercatat</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td><strong>Total Sanksi</strong></td>
                <td class="text-center">{{ $stats['total_sanksi'] ?? 0 }}</td>
                <td>Sanksi yang diberikan</td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Sistem Admin</h3>
        <ul>
            <li><strong>Status Sistem:</strong> Aktif dan berjalan normal</li>
            <li><strong>Data Terverifikasi:</strong> {{ $stats['verified_data'] ?? 0 }} record</li>
            <li><strong>Data Pending:</strong> {{ $stats['pending_data'] ?? 0 }} record</li>
            <li><strong>Tingkat Verifikasi:</strong> {{ $stats['verification_rate'] ?? 0 }}%</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ date('d F Y') }}</p>
        <p>Administrator Sistem</p>
        <br><br><br>
        <p>_________________________</p>
        <p>Admin SIPELA</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>