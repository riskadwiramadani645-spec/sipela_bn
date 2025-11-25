<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Follow-up Sanksi - BK</title>
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
        <h2>LAPORAN FOLLOW-UP SANKSI</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} sanksi</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="20%">Jenis Sanksi</th>
                <th width="15%">Tanggal Mulai</th>
                <th width="15%">Status</th>
                <th width="10%">Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $pelaksanaan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $pelaksanaan->sanksi->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td>{{ $pelaksanaan->sanksi->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $pelaksanaan->sanksi->jenis_sanksi ?? 'N/A' }}</td>
                <td class="text-center">{{ $pelaksanaan->tanggal_mulai ? date('d/m/Y', strtotime($pelaksanaan->tanggal_mulai)) : '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $pelaksanaan->status == 'Selesai' ? '#28a745' : ($pelaksanaan->status == 'Diproses' ? '#ffc107' : ($pelaksanaan->status == 'Tindak_lanjut' ? '#17a2b8' : '#dc3545')) }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ str_replace('_', ' ', $pelaksanaan->status ?? 'Terdaftar') }}
                    </span>
                </td>
                <td class="text-center">
                    @php
                        $progress = $pelaksanaan->status == 'Selesai' ? 100 : ($pelaksanaan->status == 'Diproses' ? 50 : ($pelaksanaan->status == 'Tindak_lanjut' ? 75 : 0));
                    @endphp
                    {{ $progress }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Follow-up Sanksi</h3>
        <ul>
            <li><strong>Total Sanksi:</strong> {{ count($data) }} sanksi</li>
            <li><strong>Sanksi Selesai:</strong> {{ collect($data)->where('status', 'Selesai')->count() }} sanksi</li>
            <li><strong>Sanksi Dalam Proses:</strong> {{ collect($data)->where('status', 'Diproses')->count() }} sanksi</li>
            <li><strong>Sanksi Tindak Lanjut:</strong> {{ collect($data)->where('status', 'Tindak_lanjut')->count() }} sanksi</li>
            <li><strong>Sanksi Terdaftar:</strong> {{ collect($data)->where('status', 'Terdaftar')->count() }} sanksi</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ date('d F Y') }}</p>
        <p>Konselor BK</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ session('user')->guru->nama_guru ?? 'Konselor BK' }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>