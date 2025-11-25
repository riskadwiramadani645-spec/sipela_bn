<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Sanksi - Kesiswaan</title>
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
        <h2>LAPORAN MONITORING SANKSI</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} sanksi</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="25%">Jenis Sanksi</th>
                <th width="15%">Deadline</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $sanksi)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $sanksi->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td>{{ $sanksi->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $sanksi->jenis_sanksi ?? 'N/A' }}</td>
                <td class="text-center">{{ $sanksi->tanggal_selesai ? date('d/m/Y', strtotime($sanksi->tanggal_selesai)) : '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $sanksi->status == 'Selesai' ? '#28a745' : ($sanksi->status == 'Diproses' ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $sanksi->status ?? 'Terdaftar' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Monitoring Sanksi</h3>
        <ul>
            <li><strong>Total Sanksi:</strong> {{ count($data) }} sanksi</li>
            <li><strong>Sanksi Selesai:</strong> {{ collect($data)->where('status', 'Selesai')->count() }} sanksi</li>
            <li><strong>Sanksi Dalam Proses:</strong> {{ collect($data)->where('status', 'Diproses')->count() }} sanksi</li>
            <li><strong>Sanksi Terdaftar:</strong> {{ collect($data)->where('status', 'Terdaftar')->count() }} sanksi</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ date('d F Y') }}</p>
        <p>Bagian Kesiswaan</p>
        <br><br><br>
        <p>_________________________</p>
        <p>Koordinator Kesiswaan</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>