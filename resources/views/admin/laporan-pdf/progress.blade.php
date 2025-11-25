<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Progress Sistem - Admin</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PROGRESS SISTEM</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} aktivitas</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">Jenis Aktivitas</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Status</th>
                <th width="15%">Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td>{{ $item->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $item->jenis_aktivitas ?? 'N/A' }}</td>
                <td class="text-center">{{ $item->tanggal ? date('d/m/Y', strtotime($item->tanggal)) : '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $item->status == 'selesai' ? '#28a745' : ($item->status == 'proses' ? '#ffc107' : ($item->status == 'pending' ? '#17a2b8' : '#dc3545')) }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ ucfirst(str_replace('_', ' ', $item->status ?? 'pending')) }}
                    </span>
                </td>
                <td class="text-center">{{ $item->progress ?? '0' }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Progress Sistem</h3>
        <ul>
            <li><strong>Total Aktivitas:</strong> {{ count($data) }} aktivitas</li>
            <li><strong>Aktivitas Selesai:</strong> {{ collect($data)->where('status', 'selesai')->count() }} aktivitas</li>
            <li><strong>Sedang Proses:</strong> {{ collect($data)->where('status', 'proses')->count() }} aktivitas</li>
            <li><strong>Rata-rata Progress:</strong> {{ count($data) > 0 ? round(collect($data)->avg('progress'), 1) : 0 }}%</li>
        </ul>
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