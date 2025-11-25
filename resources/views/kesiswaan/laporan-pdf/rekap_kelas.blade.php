<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Per Kelas - Kesiswaan</title>
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
        <h2>REKAP KEDISIPLINAN PER KELAS</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} kelas</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Kelas</th>
                <th width="15%">Jumlah Siswa</th>
                <th width="15%">Pelanggaran</th>
                <th width="15%">Prestasi</th>
                <th width="25%">Tingkat Kedisiplinan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $kelas)
            @php
                $siswaCount = $kelas->siswa->count();
                $pelanggaranCount = $kelas->siswa->sum(function($s) { return $s->pelanggaran->count(); });
                $prestasiCount = $kelas->siswa->sum(function($s) { return $s->prestasi->count(); });
                $kedisiplinan = $siswaCount > 0 ? max(0, 100 - (($pelanggaranCount / $siswaCount) * 10)) : 100;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                <td class="text-center">{{ $siswaCount }}</td>
                <td class="text-center">{{ $pelanggaranCount }}</td>
                <td class="text-center">{{ $prestasiCount }}</td>
                <td class="text-center">
                    <span style="background: {{ $kedisiplinan >= 80 ? '#28a745' : ($kedisiplinan >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ round($kedisiplinan) }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Rekap Per Kelas</h3>
        <ul>
            <li><strong>Total Kelas:</strong> {{ count($data) }} kelas</li>
            <li><strong>Total Siswa:</strong> {{ collect($data)->sum(function($k) { return $k->siswa->count(); }) }} siswa</li>
            <li><strong>Total Pelanggaran:</strong> {{ collect($data)->sum(function($k) { return $k->siswa->sum(function($s) { return $s->pelanggaran->count(); }); }) }} kasus</li>
            <li><strong>Total Prestasi:</strong> {{ collect($data)->sum(function($k) { return $k->siswa->sum(function($s) { return $s->prestasi->count(); }); }) }} prestasi</li>
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