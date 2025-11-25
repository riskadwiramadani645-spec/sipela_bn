<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Progress Siswa - Kesiswaan</title>
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
        <h2>LAPORAN PROGRESS SISWA</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} siswa</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">Progress Perilaku</th>
                <th width="15%">Kedisiplinan</th>
                <th width="30%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $siswa)
            @php
                $totalPelanggaran = $siswa->pelanggaran->count();
                $totalPrestasi = $siswa->prestasi->count();
                $kedisiplinan = $totalPelanggaran > 0 ? max(0, 100 - ($totalPelanggaran * 10)) : 100;
                $progress = $totalPrestasi > 0 ? min(100, 60 + ($totalPrestasi * 10)) : ($kedisiplinan >= 80 ? 80 : 60);
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $siswa->nama_siswa }}</strong></td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $progress >= 80 ? '#28a745' : ($progress >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $progress }}%
                    </span>
                </td>
                <td class="text-center">
                    <span style="background: {{ $kedisiplinan >= 80 ? '#28a745' : ($kedisiplinan >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $kedisiplinan }}%
                    </span>
                </td>
                <td>{{ $totalPelanggaran > 0 ? 'Perlu pembinaan' : ($totalPrestasi > 0 ? 'Berprestasi' : 'Dalam monitoring') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Progress Siswa</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ count($data) }} siswa</li>
            <li><strong>Siswa Berprestasi:</strong> {{ collect($data)->filter(function($s) { return $s->prestasi->count() > 0; })->count() }} siswa</li>
            <li><strong>Siswa Bermasalah:</strong> {{ collect($data)->filter(function($s) { return $s->pelanggaran->count() > 0; })->count() }} siswa</li>
            <li><strong>Siswa Normal:</strong> {{ collect($data)->filter(function($s) { return $s->pelanggaran->count() == 0 && $s->prestasi->count() == 0; })->count() }} siswa</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ date('d F Y') }}</p>
        <p>Bagian Kesiswaan</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ session('user')->nama ?? 'Kesiswaan' }}</p>
        <p>Koordinator Kesiswaan</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>