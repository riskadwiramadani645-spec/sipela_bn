<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Siswa Bermasalah - BK</title>
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
        <h2>LAPORAN SISWA BERMASALAH</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} siswa</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="10%">Total Pelanggaran</th>
                <th width="15%">Konseling Terakhir</th>
                <th width="15%">Status BK</th>
                <th width="20%">Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $siswa)
            @php
                $totalPelanggaran = $siswa->pelanggaran->count();
                $konselingTerakhir = \App\Models\BimbinganKonseling::where('siswa_id', $siswa->siswa_id)->orderBy('tanggal_konseling', 'desc')->first();
                $statusBK = $konselingTerakhir ? $konselingTerakhir->status : 'Belum Konseling';
                $rekomendasi = $totalPelanggaran >= 5 ? 'Konseling Intensif' : ($totalPelanggaran >= 3 ? 'Konseling Rutin' : 'Monitoring');
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $siswa->nama_siswa }}</strong></td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $totalPelanggaran >= 5 ? '#dc3545' : ($totalPelanggaran >= 3 ? '#ffc107' : '#28a745') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $totalPelanggaran }}
                    </span>
                </td>
                <td class="text-center">{{ $konselingTerakhir ? date('d/m/Y', strtotime($konselingTerakhir->tanggal_konseling)) : '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $statusBK == 'selesai' ? '#28a745' : ($statusBK == 'diproses' ? '#ffc107' : ($statusBK == 'tindak_lanjut' ? '#17a2b8' : '#dc3545')) }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ ucfirst(str_replace('_', ' ', $statusBK)) }}
                    </span>
                </td>
                <td>{{ $rekomendasi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Siswa Bermasalah</h3>
        <ul>
            <li><strong>Total Siswa Bermasalah:</strong> {{ count($data) }} siswa</li>
            <li><strong>Perlu Konseling Intensif:</strong> {{ collect($data)->filter(function($s) { return $s->pelanggaran->count() >= 5; })->count() }} siswa</li>
            <li><strong>Perlu Konseling Rutin:</strong> {{ collect($data)->filter(function($s) { return $s->pelanggaran->count() >= 3 && $s->pelanggaran->count() < 5; })->count() }} siswa</li>
            <li><strong>Perlu Monitoring:</strong> {{ collect($data)->filter(function($s) { return $s->pelanggaran->count() < 3; })->count() }} siswa</li>
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