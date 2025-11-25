<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembinaan Siswa - Kesiswaan</title>
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
        <h2>LAPORAN PEMBINAAN SISWA - KESISWAAN</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} siswa</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIS</th>
                <th width="25%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="10%">Jml Pelanggaran</th>
                <th width="30%">Status Pembinaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $siswa)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $siswa->nis }}</td>
                <td><strong>{{ $siswa->nama_siswa }}</strong></td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center"><span style="background: #ffc107; padding: 2px 8px; border-radius: 3px;">{{ $siswa->pelanggaran->count() }}</span></td>
                <td>{{ $siswa->pelanggaran->count() >= 3 ? 'Pembinaan Intensif' : 'Pembinaan Ringan' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Laporan Pembinaan</h3>
        <ul>
            <li><strong>Total Siswa Dalam Pembinaan:</strong> {{ count($data) }} siswa</li>
            <li><strong>Status:</strong> Memerlukan pembinaan khusus dari bagian kesiswaan</li>
            <li><strong>Periode:</strong> {{ request('periode', 'Bulan ini') }}</li>
            <li><strong>Tindak Lanjut:</strong> Konseling berkala dan monitoring perkembangan</li>
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